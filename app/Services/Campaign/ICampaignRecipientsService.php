<?php

namespace App\Services\Campaign;

use Illuminate\Http\Request;

interface ICampaignRecipientsService {
    /**
     *
     * @param Request $request
     */
    public function createCampaignRecipients($request);

    /**
     *
     * @param Request $request
     */
    public function getCampaignRecipientsInfoByCampaignId($id, $user_id);

    /**
     *
     * @param int $id
     */
    public function getCampaignRecipientsInfo($id);

    /**
     *
     * @param int $id
     */
    public function deleteCampaignRecipientsByListId($id, $campaign_id);

    /**
     *
     * @param int $id
     * @param Request $request
     */
    public function updateCampaignRecipients($id, Request $request);

    /**
     *
     * @param int $campaign_id
     */
    public function getRecipientsIDByCampaignId( $campaign_id, $user_id = null);
}