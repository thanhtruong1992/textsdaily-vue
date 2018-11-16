<?php
namespace App\Services\SMS\API;

use infobip\api\configuration\BasicAuthConfiguration;
use infobip\api\model\sms\mt\logs\GetSentSmsLogsExecuteContext;
use infobip\api\client\GetSentSmsLogs;
use infobip\api\model\sms\mt\send\Message;
use infobip\api\client\SendMultipleTextualSmsAdvanced;
use infobip\api\model\sms\mt\send\textual\SMSAdvancedTextualRequest;
use infobip\api\model\Destination;
use infobip\api\client\GetAccountBalance;
use infobip\api\client\GetReceivedMessages;
use infobip\api\model\sms\mo\reports\GetReceivedMessagesExecuteContext;
use function GuzzleHttp\json_encode;
use GuzzleHttp\Client;
use JsonMapper;
use infobip\api\model\sms\mt\send\SMSResponse;
use App\Services\SMS\IResponseSendSms;

class InfoBipProviderService implements ISMSServiceProvider
{
    public $senderCharacterLimit    = 11;
    public $messageGSMLimit         = 70;
    public $messageUnicodeLimit     = 160;
    //
    protected $auth;
    public $responseSendSms;

    public function __construct(IResponseSendSms $responseSendSms)
    {
        $this->responseSendSms = $responseSendSms;
    }

    /**
     * Fn set authorization 
     * @param string $user
     * @param string $password
     */
    public function authorization( $username, $password ) {
        $this->auth = new BasicAuthConfiguration( $username, $password );
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SMS\API\ISMSServiceProvider::sendMessage()
     */
    public function sendMessage($sender, $recipients, $message, $validityPeriodHours = 48)
    {
        // Convert hours to minutes
        $validityPeriod = $validityPeriodHours * 60;
        //
        $destination = new Destination();
        $destination->setTo($recipients);
        //
        $messageObj = new Message();
        $messageObj->setFrom($sender);
        $messageObj->setDestinations([$destination]);
        $messageObj->setText($message);
        $messageObj->setValidityPeriod($validityPeriod);
        $messageObj->setNotifyUrl(env('API_REPORT_INFOBIP'));
        //
        $requestBody = new SMSAdvancedTextualRequest();
        $requestBody->setMessages([$messageObj]);
        //
        $infoBip = new SendMultipleTextualSmsAdvanced($this->auth);

        return $infoBip->execute($requestBody);
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
        // Convert hours to minutes
        $validityPeriod = $validityPeriodHours * 60;
        //
        $destination = new Destination();
        $destination->setTo($recipients);
        //
        $messageObj = new Message();
        $messageObj->setFrom($sender);
        $messageObj->setDestinations([$destination]);
        $messageObj->setText($message);
        $messageObj->setValidityPeriod($validityPeriod);
        $messageObj->setNotifyUrl(env('API_REPORT_INFOBIP'));
        //
        $requestBody = new SMSAdvancedTextualRequest();
        $requestBody->setMessages([$messageObj]);
        
        $authToken = $this->auth->getAuthenticationHeader();
        $bodyData = json_decode(json_encode($requestBody));
        $url = $this->auth->baseUrl . 'sms/2/text/advanced';
        return $this->executeRequestAsync('POST', $url, null, $bodyData, $authToken, $queue);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SMS\API\ISMSServiceProvider::getMessageInfo()
     */
    public function getMessageInfo($messageId)
    {
        // Creating execution context
        $context = new GetSentSmsLogsExecuteContext();
        $context->setMessageId($messageId);
        // Initializing GetSentSmsDeliveryReports client with appropriate configuration
        $client = new GetSentSmsLogs($this->auth);
        // Executing request
        $response = $client->execute($context);
        //
        return $response;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SMS\API\ISMSServiceProvider::getBalance()
     */
    public function getBalance()
    {
        $client = new GetAccountBalance($this->auth);
        $response = $client->execute();
        return $response;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SMS\API\ISMSServiceProvider::getReceivedMessages()
     */
    public function getReceivedMessages()
    {
        // Initializing GetReceivedMessages client with appropriate configuration
        $client = new GetReceivedMessages($this->auth);
        // Creating execution context
        $context = new GetReceivedMessagesExecuteContext();
        // Executing request
        $response = $client->execute($context);
        //
        return $response;
    }

    /**
     * fn run call api send sms async
     * @param string $httpMethod
     * @param strign $url
     * @param $queryParams
     * @param object $queue
     */
    public function executeRequestAsync($httpMethod, $url, $queryParams = null, $body = null, $token , $queue) {
        $headers = [
            'Authorization' => $token,
            'Content-Type' => "application/json",
            'Accept' => "application/json"
        ];

        $client = new Client([
            'timeout' => 120,
            'connect_timeout' => 60,
            'headers' => $headers
        ]);

        if (sizeof($queryParams) > 0) {
            $url .= '?' . http_build_query($queryParams);
        }

        // request async
        $promise = $client->requestAsync($httpMethod, $url, [
            'body'  => json_encode($body)
        ]);

        $promise->then(function ($response) use($queue) {
            // success call api
            $data = (object) [
                "data" => null,
                "dataQueue" => $queue
            ];

            // get repsonse status code 
            $code = $response->getStatusCode();
            // check success
            if(200 <= $code && $code < 300) {
                // get response body contents
                $result = json_decode($response->getBody()->getContents());
                $className = get_class(new SMSResponse());
                $mapper = new JsonMapper();

                $data = (object) [
                    "data" => $mapper->map($result, new $className()),
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