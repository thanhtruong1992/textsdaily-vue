<?php
namespace App\Services\SMS\API;

interface ISMSServiceProvider
{
    /**
     * FN SEND SMS
     * @param string $sender
     * @param string $recipients
     * @param string $message
     * @param integer $validityPeriod
     */
    public function sendMessage($sender, $recipients, $message, $validityPeriodHours = 48);

    /**
     * FN TRACKING STATUS SMS
     * @param string $messageId
     */
    public function getMessageInfo($messageId);

    /**
     * FN GET BALANCE
     */
    public function getBalance();

    /**
     * FN GET INBOUND RECEIVED MESSAGES
     */
    public function getReceivedMessages();
}
?>