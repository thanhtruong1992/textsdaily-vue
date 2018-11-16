<?php
namespace App\Services\Campaign;

interface ICampaignLinkService {
    public function createCampaignLink($link, $campaign_id);
    public function redirectLink($request);
    public function updateCampaignID( array $attributes, $arrID = array() );
    public function updateCampaignLink($campaignLinkID, array $attributes = []);
    public function findCampaignLinkWithCampaign($camapginID, $userID);
    public function deleteCampaignLink($campaignLinkID);
    public function deleteListCampaignLink($campaignLinkIDs);
}