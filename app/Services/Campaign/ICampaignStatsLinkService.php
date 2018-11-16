<?php
namespace App\Services\Campaign;

use App\Models\CampaignLinks;

interface ICampaignStatsLinkService
{
    public function createCampaignStatsLink( CampaignLinks $campaginLink, $idUser );
    public function getTotalsCountByCampaign( $idCampaign, $idUser );
}