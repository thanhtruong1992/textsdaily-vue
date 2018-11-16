<?php
namespace App\Services\Campaign;

use App\Services\BaseService;
use App\Repositories\Campaign\ICampaignLinksRepository;
use Auth;

class CampaignLinkService extends BaseService implements ICampaignLinkService
{
    protected $campaignLinkRepo;
    protected $campaignStatsLinkService;

    public function __construct(ICampaignLinksRepository $campaignLinkRepo, ICampaignStatsLinkService $ICampaignStatsLinkService ) {
        $this->campaignLinkRepo = $campaignLinkRepo;
        $this->campaignStatsLinkService = $ICampaignStatsLinkService;
    }

    public function createCampaignLink($link, $campaign_id) {
        $userId = Auth::user()->id;
        $data = [
                "url" => $link,
                "campaign_id" => $campaign_id,
                "created_by" => $userId,
                "updated_by" => $userId
        ];
        return $this->campaignLinkRepo->create($data);
    }

    public function redirectLink($request) {
        $userId = $request->get('user_id');
        $id = $request->get('campaign_link_id');

        if($userId && $id) {
            $result = $this->campaignLinkRepo->findCampainLink($userId, $id);
            if($result) {
                // Update to stats link
                $result->ip = $request->ip();
                //
                if ($result->campaign_id) {
                    $this->campaignStatsLinkService->createCampaignStatsLink( $result, $userId );
                    // Get Total click
                    $linkTotals = $this->campaignStatsLinkService->getTotalsCountByCampaign( $result->campaign_id, $userId );
                    // Update total click for link
                    foreach ( (array)$linkTotals as $idLink => $totals ) {
                        $this->campaignLinkRepo->updateByUser( array( 'total_clicks' => $totals ), $idLink, $userId );
                    }
                }
                //
                return $this->success($result);
            }
        }

        return $this->fail();
    }

    /**
     * @param array $arrID : List ID of links
     * {@inheritDoc}
     * @see \App\Services\Campaign\ICampaignLinkService::updateCampaignID()
     */
    public function updateCampaignID( array $attributes, $arrID = array() )
    {
        if ( $arrID ) {
            return $this->campaignLinkRepo->updateByArrID( $attributes, $arrID );
        }
        return false;
    }

    public function updateCampaignLink($campaignLinkID, array $attributes = []) {
        if($campaignLinkID) {
            return $this->campaignLinkRepo->updateCampaignLink($campaignLinkID, $attributes);
        }

        return false;
    }

    public function findCampaignLinkWithCampaign($camapginID, $userID) {
        return $this->campaignLinkRepo->findCampaignLinkWithCampaign($camapginID, $userID);
    }

    public function deleteCampaignLink($campaignLinkID) {
        return $this->campaignLinkRepo->deleteLink($campaignLinkID);
    }

    public function deleteListCampaignLink($campaignLinkIDs) {
        return $this->campaignLinkRepo->deleteListID($campaignLinkIDs);
    }
}