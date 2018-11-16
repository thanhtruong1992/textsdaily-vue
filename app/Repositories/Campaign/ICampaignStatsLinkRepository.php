<?php

namespace App\Repositories\Campaign;

interface ICampaignStatsLinkRepository
{
    public function createByUser( $attributes, $idUser );
    public function countTotalsGroupByCampaign( $idCampaign, $idUser );
}