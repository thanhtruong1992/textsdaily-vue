<?php

namespace App\Repositories\Campaign;

interface ICampaignLinksRepository {
    public function create(array $attributes);
    public function findCampainLink($userId, $id);
    public function updateByUser( array $attributes, $id, $idUser );
    public function updateByArrID( array $attributes, array $arrID, $userId = null );
    public function updateCampaignLink($campaignLinkID, array $attributes = []);
    public function findCampaignLinkWithCampaign($camapginID, $userID);
    public function deleteLink($id);
    public function deleteListID($ids);
}