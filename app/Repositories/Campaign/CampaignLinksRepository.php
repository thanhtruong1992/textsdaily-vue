<?php
namespace App\Repositories\Campaign;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Models\CampaignLinks;

class CampaignLinksRepository extends BaseRepository implements ICampaignLinksRepository {
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\CampaignLinks";
    }

    private function changeTableName( $idUser ) {
        $this->__changeTableName ( array (
                'u_template' => 'u_' . $idUser
        ) );
    }

    /**
     * CUSTOM CREATE FUNCTION TO CHANGE TABLE NAME BY USER
     * {@inheritDoc}
     * @see \Prettus\Repository\Eloquent\BaseRepository::create()
     */
    public function create(array $attributes, $table_name = null) {
        $this->changeTableName( Auth::user()->id );
        //
        return parent::create($attributes, $this->model->getTable());
    }
    public function findCampainLink($userId, $id) {
        $this->changeTableName( $userId );
        //
        return parent::find( $id );
    }

    public function updateByUser( array $attributes, $id, $idUser )
    {
        $this->changeTableName( $idUser );
        return parent::update( $attributes, $id, $this->model->getTable() );
    }

    public function updateByArrID( array $attributes, array $arrID, $userId = null ) {
        if ( is_null($userId) ) {
            $userId = Auth::user()->id;
        }
        $this->changeTableName( $userId );
        //
        return DB::table( $this->model->getTable() )->whereIn( 'id', $arrID )->update( $attributes );
    }

    public function updateCampaignLink($campaignLinkID, array $attributes = []) {
        $userID= Auth::user()->id;
        $this->changeTableName( $userID);
        //
        return DB::table( $this->model->getTable() )->where( 'id', $campaignLinkID )->update( $attributes );
    }

    public function findCampaignLinkWithCampaign($camapginID, $userID) {
        $this->changeTableName( $userID );
        //
        return parent::findByField( "campaign_id", $camapginID );
    }

    public function deleteLink($id) {
        $this->changeTableName( Auth::user()->id );
        //
        return parent::delete( $id, $this->model->getTable());
    }

    public function deleteListID($ids) {
        $this->changeTableName( Auth::user()->id );
        //
        return DB::table($this->model->getTable())->whereIn('id', $ids)->delete();
    }
}