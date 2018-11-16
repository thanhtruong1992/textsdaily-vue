<?php

namespace App\Services\Campaign;

use Illuminate\Http\Request;
use App\Http\Requests\CreateCampaignRequest;

interface ICampaignService {
    /**
     * Get campaign item by query search, sort, data
     *
     * @ param Request $request
     */
    public function getCampaignByQuery(Request $request);

    /**
     * Delete campaign item
     * @param Request $request
     */
    public function deleteCampaign($campaign_id);

    /**
     * Amend campaign item
     * @param Request $request
     */
    public function amendCampaign($campaign_id);

    /**
     * Update campaign item status
     * @param Request $request
     */
    public function updateCampaignStatus($campaign_id);

    /**
     *
     * @param Request $request
     */
    public function createCampaign(CreateCampaignRequest $request);

    /**
     *
     * @param int $id
     */
    public function getCampaignInfo($id, $userID = null);

    /**
     *
     * @param int $id
     * @param Request $request
     */
    public function updateCampaign($id, CreateCampaignRequest $request);

    /**
     * Get Pending campaign for send sms
     */
    public function sendCampaigns($campaign = null);

    /**
     * FN Tracking status send sms
     */
    public function getDeliveryReports();

    /**
     * Summary Subscribers
     * @param Request $request
     */
    public function summarySubscribers($request);

    /**
     * FN clone campaign
     * @param Request $request
     */
    public function cloneCampaign($request);

    /**
     * FN total send campaigns
     */
    public function totalSendCampaignOfUsers($request);

    /**
     * FN get ALL canpaign pending report
     * @param int $userID
     */
    public function getAllCampaignPendingReport($userID);

    /**
     * FN update statistic report of campaign
     * @param int $userID
     * @param int $campaignID
     * @param array $attributes
     */
    public function updateStatisticReportCampaign($userID, $campaignID, $attributes);

    public function getCampaignWithSubscriberList($userID, $campaignID);

    public function getCampaign($userID, $campaignID, $userType);

    public function sendSMSTest($request);

    public function sendAgainCampaignPause();

    public function addLinkUnsubscriber($campaignID);

    public function getFirstCampaign();

    public function createCampaignApiAccount($id);
}