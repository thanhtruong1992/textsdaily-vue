<?php

namespace App\Services\Campaign;

use App\Services\BaseService;
use App\Repositories\Campaign\ICampaignRecipientsRepository;
use Illuminate\Http\Request;
use Auth;

class CampaignRecipientsService extends BaseService implements ICampaignRecipientsService {

    //
    protected $campaignRecipientsRepo;

    /**
     */
    public function __construct( ICampaignRecipientsRepository $ICampaignRecipientsRepo ) {
        $this->campaignRecipientsRepo = $ICampaignRecipientsRepo;
    }

    /**
     *
     * @param Request $request
     */
    public function createCampaignRecipients( $request )
    {
        if( $this->campaignRecipientsRepo->create( $request ) ){
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param int $id
     */
    public function getCampaignRecipientsInfo($id)
    {
        return $this->campaignRecipientsRepo->find($id);
    }

    /**
     * get all campaign recipient
     * @param int $id
     */
    public function getCampaignRecipientsInfoByCampaignId($id, $user_id)
    {
        $results = $this->campaignRecipientsRepo->getListSubscriberByCampaignId($id, $user_id);
        return $results;
    }

    /**
     * deleteCampaignRecipientsById
     * @param int $id - id of campaign recipients
     */
    public function deleteCampaignRecipientsByListId($id, $campaign_id) {
        return $this->campaignRecipientsRepo->deleteCampaignRecipientsBySubscriberListId($id, $campaign_id);
    }

    /**
     *
     * @param int $id
     * @param Request $request
     */
    public function updateCampaignRecipients($id, Request $request){}

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Campaign\ICampaignRecipientsService::getRecipientsIDByCampaignId()
     */
    public function getRecipientsIDByCampaignId( $campaign_id, $user_id = null ) {
        $recipients = $this->campaignRecipientsRepo->getListSubscriberByCampaignId( $campaign_id, $user_id );

        $ids = array();
        foreach ( $recipients as $item ) {
            $ids[] = $item->list_id;
        }
        return $ids;
    }
}