<?php
namespace App\Repositories\Campaign;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CampaignRecipientsRepository extends BaseRepository implements ICampaignRecipientsRepository {
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\CampaignRecipient";
    }

    /**
     * CUSTOM CREATE FUNCTION TO CHANGE TABLE NAME BY USER
     * {@inheritDoc}
     * @see \Prettus\Repository\Eloquent\BaseRepository::create()
     */
    public function create(array $attributes, $table_name = null) {
        if (Auth::user ()) {
            $this->__changeTableName(array('u_template' => 'u_' . Auth::user()->id));
        }
        return parent::create($attributes, $this->model->getTable());
    }

    /**
     *
     * {@inheritDoc}
     * @see \Prettus\Repository\Eloquent\BaseRepository::delete()
     */
    public function delete($id, $table_name = null)
    {
        if (Auth::user ()) {
            $this->__changeTableName(array('u_template' => 'u_' . Auth::user()->id));
        }
        return parent::delete($id);
    }

    public function getListSubscriberByCampaignId($campaign_id, $user_id = null ) {
        if ( $user_id ) {
            $idUser = $user_id;
        } elseif (Auth::user ()) {
            $idUser = Auth::user()->id;
        }

        $this->__changeTableName(array('u_template' => 'u_' . $idUser));

        $campaign_recipient_table_name = 'campaign_recipients_u_' . $idUser;
        $queryBuilder = DB::table ( $campaign_recipient_table_name ) ->select('list_id', 'campaign_id') ->where('campaign_id', '=', $campaign_id);
        return $queryBuilder->get();
    }

    public function deleteCampaignRecipientsBySubscriberListId($list_id, $campaign_id) {
        if (Auth::user ()) {
            $this->__changeTableName(array('u_template' => 'u_' . Auth::user()->id));
        }

        $campaign_recipient_table_name = 'campaign_recipients_u_' . Auth::user()->id;
        return $queryBuilder = DB::table ( $campaign_recipient_table_name )
                ->where('list_id', $list_id)
                ->where('campaign_id', $campaign_id)
                ->delete();
    }

    public function checkSubscriberListOfUserCanBeDeleted( $list_id )
    {
        if (Auth::user ()) {
            $this->__changeTableName(array('u_template' => 'u_' . Auth::user()->id));
        }
        $query = parent::findByField('list_id', $list_id);
        if (count($query) > 0) {
            return false; // Item cannot be deleted
        } else {
            return true; // Item can be deleted
        }
    }
}