<?php
namespace App\Services\Campaign;

interface IQueueService {

    /**
     * FN Process replace personalize for message
     * @param string $message
     * @param array $customFields
     * @param \Model\Queue $queue
     */
    public function personalizeHandler($message, $customFields = array(), $queue, $subscriber);

    /**
     * FN CHECK CREDITS FOR SEND SMS
     * @param int $idUser
     * @param array $priceConfiguration
     * @param object $queue
     * @return boolean | int
     *  + Return false if country was disabled
     *  + Return true if account out of money
     *  + Return price of message
     */
    public function checkCreditsHandler( $user, $priceConfiguration, $queue );

    /**
     * FN CACULATE MESSAGE COUNT
     * @param string $message
     * @param string $messageType
     */
    public function calculateMessageCount( $message );

    /**
     * Get Pending campaign for send sms
     */
    public function sendSMS( $campaign, $processAmount = 100 );

    /**
     * FN Get queues data need to tracking delivery reports.
     * @param \App\Models\Campaign $campaign
     */
    public function trackingDeliveryReports( $campaign, $processAmount = 100 );

    public function getQueueByPhone($phone, $userID, $campaignID);
}