<?php
namespace App\Services\Campaign;

use App\Services\BaseService;
use App\Repositories\Campaign\ICampaignStatsLinkRepository;
use App\Models\CampaignLinks;

class CampaignStatsLinkService extends BaseService implements ICampaignStatsLinkService
{
    //
    protected $campaignStatsLinkRepo;

    /**
     */
    public function __construct(
            ICampaignStatsLinkRepository $ICampaignStatsLinkRepo
    ) {
        $this->campaignStatsLinkRepo = $ICampaignStatsLinkRepo;
    }

    public function createCampaignStatsLink( CampaignLinks $campaginLink, $idUser )
    {
        $campaignStatsLinkParams = array();
        $campaignStatsLinkParams['user_id'] = (int)$idUser;
        $campaignStatsLinkParams['campaign_id'] = $campaginLink->campaign_id;
        $campaignStatsLinkParams['link_id'] = $campaginLink->id;
        $campaignStatsLinkParams['url'] = $campaginLink->url;
        $campaignStatsLinkParams['ip'] = $campaginLink->ip;
        return $this->campaignStatsLinkRepo->createByUser( $campaignStatsLinkParams, $idUser );
    }

    public function getTotalsCountByCampaign( $idCampaign, $idUser )
    {
        $qrResults = $this->campaignStatsLinkRepo->countTotalsGroupByCampaign( $idCampaign, $idUser );
        //
        $results = array();
        foreach ( $qrResults as $item ) {
            $results[ $item->link_id] = $item->totals;
        }
        return $results;
    }

}