<?php
namespace App\Services\SMS\API;

use GuzzleHttp\Client;
use MessageBird\Client AS ClientMessageBird;
use MessageBird\Common\HttpClient;
use MessageBird\Objects\Message;
use App\Services\SMS\IResponseSendSms;


class MessageBirdProviderService extends Message implements ISMSServiceProvider
{
    const ENDPOINT = 'https://rest.messagebird.com';
    public $senderCharacterLimit    = 11;
    public $messageGSMLimit         = 70;
    public $messageUnicodeLimit     = 160;
    protected $messageBird;
    public $accessKey;
    public $responseSendSms;

    public function __construct(IResponseSendSms $responseSendSms)
    {
        $this->responseSendSms = $responseSendSms;
    }

    public function authorization( $accessKey ) {
        $this->accessKey = $accessKey;
        $this->messageBird = new ClientMessageBird($accessKey);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SMS\API\ISMSServiceProvider::sendMessage()
     */
    public function sendMessage($sender, $recipients, $message, $validityPeriodHours = 48)
    {
        // Convert hours to second
        $validityPeriod = $validityPeriodHours * 60 * 60;
        //
        $Message = new Message();
        $Message->originator = $sender;
        $Message->recipients = (array) $recipients;
        $Message->body = $message;
        $Message->validity = $validityPeriod;
        $Message->datacoding = 'auto';

        return $this->messageBird->messages->create($Message);
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
        
        // Convert hours to second
        $validityPeriod = $validityPeriodHours * 60 * 60;
        //
        $Message = new Message();
        $Message->originator = $sender;
        $Message->recipients = (array) $recipients;
        $Message->body = $message;
        $Message->validity = $validityPeriod;
        $Message->datacoding = 'auto';
        $url = self::ENDPOINT . "/messages" ;
        $token = $this->accessKey;

        return $this->executeRequestAsync('POST', $url, null, $Message, $token, $queue);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SMS\API\ISMSServiceProvider::getMessageInfo()
     */
    public function getMessageInfo($messageId)
    {
        return $this->messageBird->messages->read($messageId);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SMS\API\ISMSServiceProvider::getBalance()
     */
    public function getBalance()
    {
        return $this->messageBird->balance->read();
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SMS\API\ISMSServiceProvider::getReceivedMessages()
     */
    public function getReceivedMessages()
    {
        return false;
    }

     /**
     * fn run call api send sms async
     * @param string $httpMethod
     * @param string $url
     * @param $queryParams
     * @param object $queue
     */
    public function executeRequestAsync($httpMethod, $url, $queryParams = null, $body, $token, $queue) {
        $message = clone $body;
        $headers = [
            'Authorization' => 'AccessKey ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Accept-Charset' => 'utf-8'
        ];

        $client = new Client([
            'timeout' => 120,
            'connect_timeout' => 60,
            'headers' => $headers
        ]);

        if (sizeof($queryParams) > 0) {
            $url .= '?' . http_build_query($queryParams);
        }

        $promise = $client->requestAsync($httpMethod, $url, [
            'body' => json_encode($body)
        ]);
        
        $promise->then(function ($response) use($queue) {
           // success call api
            $data = (object) [
                "data" => null,
                "dataQueue" => $queue
            ];
            $code = $response->getStatusCode();

            // check success
            if(200 <= $code && $code < 300) {
                $result = json_decode($response->getBody()->getContents());
                $data = (object) [
                    "data" => $this->loadFromArray($result),
                    "dataQueue" => $queue
                ];
            }
            return $this->responseSendSms->responseSentSMSAsync($data);
        }, function($error) use($queue) {
            $data = (object) [
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