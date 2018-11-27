<?php
namespace App\Services\SMS\API;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Request;
use App\Services\SMS\IResponseSendSms;


class RouteMobileProviderService implements ISMSServiceProvider {
    protected $tmSms;
    private $infoProvider;
    public $responseSendSms;

    public function __construct(IResponseSendSms $responseSendSms) {
        $this->responseSendSms = $responseSendSms;
    }

    /**
     * fn set authorization
     * @param string $username
     * @param string $password
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
            "username"      => $this->infoProvider->username,
            "password"      => $this->infoProvider->password,
            "source"        => $sender,
            "destination"   => $recipients,
            "message"       => $message,
            "dlr"           => 1,
            "type"          => 0,
            "url"           => env('API_REPORT_ROUTEMOBILE'),
        );

        $string = str_replace("\r\n", "\n", $message);
        $mbDetectEncoding = mb_detect_encoding($string);
        if ($mbDetectEncoding != 'ASCII') {
            $params['type'] = 2;
            $params['message'] = strtoupper(bin2hex(iconv('UTF-8', 'UTF-16BE', $message)));
        }

        return $this->executeRequest('POST', $this->infoProvider->url, $params, null, null);
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
            "username"      => $this->infoProvider->username,
            "password"      => $this->infoProvider->password,
            "source"        => $sender,
            "destination"   => $recipients,
            "message"       => $message,
            "dlr"           => 1,
            "type"          => 0,
            "url"           => env('API_REPORT_ROUTEMOBILE'),
        );

        $string = str_replace("\r\n", "\n", $message);
        $mbDetectEncoding = mb_detect_encoding($string);
        if ($mbDetectEncoding != 'ASCII') {
            $params['type'] = 2;
            $params['message'] = strtoupper(bin2hex(iconv('UTF-8', 'UTF-16BE', $message)));
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

        $sendHeaders = [];

        foreach ($requestHeaders as $key => $value) {
            $sendHeaders[] = $key . ': ' . $value;
        }

        if (sizeof($queryParams) > 0) {
            $url .= '?' . http_build_query($queryParams);
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

        if (!empty($body)) {
            $opts[CURLOPT_POSTFIELDS] = http_build_query($body);
        }

        $curlSession = curl_init();
        curl_setopt_array($curlSession, $opts);

        $result = curl_exec($curlSession);
        $arrResult = explode('|', $result);
        $code = curl_getinfo($curlSession, CURLINFO_HTTP_CODE);

        if(!empty($arrResult) && $arrResult[0] != 1701) {
            throw new \Exception($arrResult[0], $code);
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
            'timeout' => 0,
            'connect_timeout' => 0
        ]);

        if (sizeof($queryParams) > 0) {
            $url .= '?' . http_build_query($queryParams);
        }
        
        // request async
        $promise = $client->requestAsync($httpMethod, $url);
        
        $promise->then(function ($response) use($queue) {
            // success call api
            $data = (object) [
                "data" => null,
                "dataQueue" => $queue
            ];
            // check success
            if(in_array($response->getStatusCode(), [200, 202])) {
                //get response body content
                $result = $response->getBody()->getContents();
                $data = (object) [
                    "data" => explode('|', $result),
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