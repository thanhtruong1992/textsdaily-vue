<?php

namespace App\Repositories\Campaign;

interface ICampaignRecipientsRepository {
    public function checkSubscriberListOfUserCanBeDeleted( $list_id );
    public function getListSubscriberByCampaignId($campaign_id, $user_id = null);
    public function deleteCampaignRecipientsBySubscriberListId($list_id, $campaign_id);
}