<?php

namespace App\Repositories\Campaign;

interface ICampaignRepository {

    /**
     * @param
     *  - $search_key: string - search value
     *  - $sort_colum: string - column name
     *  - $order_by: string - sort value 'acs/desc' - default desc.
     */
    public function getCampaignByQuery($search_key, $sort_column, $order_by, $page);

    /**
     * Delete campaign item
     * @param int $campaign_id
     */
    public function deleteCampaignItem($campaign_id);

    /**
     * Amend campaign item
     * @param int $campaign_id
     */
    public function amendCampaignItem($campaign_id);

    /**
     * Get Pending campaign for send sms
     */
    public function getReadyCampaign();

    /**
     * Get Sent campaign for tracking delivery report
     */
    public function getSentCampaign();

    /**
     * FN total subscriber and total subscriber duplicate with list id subscirber
     * @param string $list_id
     */
    public function summarySubscribers($listId, $userID, $totalSMS, $defaultPriceSMS);

    /*
     * FN clone campaign
     */
    public function cloneCampaign($campaign_id, $user_id);

    /**
     * FN get total sent campaign of list user
     */
    public function totalSendCampaignOfUsers($userId, $startDate, $endDate, $filter, $timezone, $type, $currency);

    /**
     * FN get all campaign pending report
     * @param int $userID
     */
    public function getAllCampaignPendingReport($userID);

    /**
     * FN update statistic repotr of campaign
     * @param int $userID
     * @param int $campaignID
     * @param array $attributes
     */
    public function updateStatisticReportCampagin($userID, $campaignID, $attributes);

    public function getCampaignWithSubscriberList($userID, $campaignID);

    public function getCampaign($userID, $campaignID, $userType);

    public function detectCountryNetworkServiceProviderOfPhone($phone, $userID, $defaultPrice);
    
    public function getFirstCampaign();

    public function createCampaignApiAccount(array $attributes, $table_name = null);
}