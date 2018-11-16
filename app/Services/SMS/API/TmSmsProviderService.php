<?php
namespace App\Services\SMS\API;

use Illuminate\Support\Facades\Hash;
use App\Services\SMS\IResponseSendSms;
use GuzzleHttp\Client;


class TmSmsProviderService implements ISMSServiceProvider{
    protected $tmSms;
    private $infoProvider;
    public $responseSendSms;

    public function __construct(IResponseSendSms $responseSendSms) {
        $this->responseSendSms = $responseSendSms;
    }

    /**
     * fn set authorization 
     * @param string $username
     * @param string $params
     * @param string $url
     */
    public function authorization($username, $password, $url) {
        $this->infoProvider = (object) array(
            "username" => $username,
            "password" => $password,
            "url" => $url
        );
    }

    public function sendMessage($sender, $recipients, $message, $validityPeriodHours = 48) {
        $params = array(
            "gw-username"   => $this->infoProvider->username,
            "gw-password"   => $this->infoProvider->password,
            "gw-from"       => $sender,
            "gw-to"         => $recipients,
            "gw-text"       => $message,
            "gw-validity"   => $validityPeriodHours * 3600,
            "gw-dlr-mask"   => 1,
            // "gw-charset"    => "UTF-8",
            "gw-dlr-url"    => url('/') . '/' .  env('LINK_REPORT_TM_SMS') . '?hash=' . Hash::make(env('KEY_TM_SMS'))

        );

        $string = str_replace("\r\n", "\n", $message);
        $mbDetectEncoding = mb_detect_encoding($string);
        if ($mbDetectEncoding != 'ASCII') {
            $params['gw-coding'] = 3;
            $params['gw-text'] = strtoupper(bin2hex(iconv('UTF-8', 'UTF-16BE', $message)));
        }

        return $this->executeRequest('POST', $this->infoProvider->url, null, null, $params);
    }

    /**
     * fn send sms async
     * @param string $sender
     * @param string $phone
     * @param string $message
     * @param int $validityPeriodHours
     * @param object $queue
     * @return null|object
     */
    public function sendMessageAsync($sender, $recipients, $message, $validityPeriodHours = 48, $queue) {
        $params = array(
            "gw-username"   => $this->infoProvider->username,
            "gw-password"   => $this->infoProvider->password,
            "gw-from"       => $sender,
            "gw-to"         => $recipients,
            "gw-text"       => $message,
            "gw-validity"   => $validityPeriodHours * 3600,
            "gw-dlr-mask"   => 1,
            // "gw-charset"    => "UTF-8",
            "gw-dlr-url"    => url('/') . '/' .  env('LINK_REPORT_TM_SMS') . '?hash=' . Hash::make(env('KEY_TM_SMS'))
        );

        $string = str_replace("\r\n", "\n", $message);
        $mbDetectEncoding = mb_detect_encoding($string);
        if ($mbDetectEncoding != 'ASCII') {
            $params['gw-coding'] = 3;
            $params['gw-text'] = strtoupper(bin2hex(iconv('UTF-8', 'UTF-16BE', $message)));
        }

        return $this->executeRequestAsync('POST', $this->infoProvider->url, $params, $queue);
    }

    public function getMessageInfo($messageId) {
        return $this->tmSms->messages->read($messageId);
    }

    public function getBalance() {
        return $this->tmSms->balance->read();
    }

    public function getReceivedMessages(){
        return false;
    }

    private function executeRequest($httpMethod, $url, $queryParams = null, $requestHeaders = null, $body = null) {
        if ($queryParams == null)
            $queryParams = array();

        if (!is_array($queryParams)){
            $queryParams = $this->createFieldArray($queryParams);
        }

        if ($requestHeaders == null)
            $requestHeaders = array();

        $sendHeaders = array(
                'Content-Type: application/x-www-form-urlencoded'
        );

        foreach ($requestHeaders as $key => $value) {
            $sendHeaders[] = $key . ': ' . $value;
        }

        if (sizeof($queryParams) > 0) {
            $url .= '?' . $this->buildQuery($queryParams);
        }

        $opts = array(
                CURLOPT_FRESH_CONNECT => 1,
                CURLOPT_CONNECTTIMEOUT => 60,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 3,
                CURLOPT_CUSTOMREQUEST => $httpMethod,
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => $sendHeaders
        );

        if ($body) {
            $opts[CURLOPT_POSTFIELDS] = http_build_query($body);
        }

        $curlSession = curl_init();
        curl_setopt_array($curlSession, $opts);

        $result = curl_exec($curlSession);
        $arrResult = [];
        parse_str($result, $arrResult);
        $code = curl_getinfo($curlSession, CURLINFO_HTTP_CODE);
        /* $curlError = curl_error($curlSession);
        if ($curlError !== 0) {
            throw new \RuntimeException(curl_error($curlSession), $curlError);
        }

        $isSuccess = 200 <= $code && $code < 300;

        curl_close($curlSession);

        if (!$isSuccess) {
            throw new Exception($result, $code);
        } */

        if($arrResult['status'] > 0) {
            throw new \Exception($arrResult['err_msg'], $code);
        }
        return $arrResult;
    }

    /**
     * fn run call api send sms async
     * @param string $httpMethod
     * @param string $url
     * @param $queryParams
     * @param object $queue
     */
    public function executeRequestAsync($httpMethod, $url, $queryParams = null, $queue) {
        $client = new Client([
            'timeout' => 120,
            'connect_timeout' => 60
        ]);

        if (sizeof($queryParams) > 0) {
            $url .= '?' . http_build_query($queryParams);
        }

        $promise = $client->requestAsync($httpMethod, $url);
        
        $promise->then(function ($response) use($queue) {
            // success call api
            $data = (object) [
                "data" => null,
                "dataQueue" => $queue
            ];
            $arrResult = [];
            //get response body content 
            $result = $response->getBody()->getContents();
            parse_str($result, $arrResult);
            // check success
            if(in_array($response->getStatusCode(), [200, 202]) && !empty($arrResult) && $arrResult['status'] == 0 ) {
                $data = (object) [
                    "data" => $arrResult,
                    "dataQueue" => $queue
                ];
            }
            return $this->responseSendSms->responseSentSMSAsync($data);
        }, function($error) use($queue) {
            // error call api
            return (object) [
                "data" => null,
                "dataQueue" => $queue
            ];
            return $this->responseSendSms->responseSentSMSAsync($data);
        });

        $promise->wait();
        return true;
    }
}
?>