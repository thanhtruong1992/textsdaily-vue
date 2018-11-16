<?php
namespace App\Repositories\Campaign;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CampaignStatsLinkRepository extends BaseRepository implements ICampaignStatsLinkRepository {
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\CampaignStatsLink";
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
        return parent::create( $attributes );
    }

    public function createByUser( $attributes, $idUser )
    {
        $this->changeTableName( $idUser );
        $attributes['created_by'] = $idUser;
        //
        return parent::create( $attributes, $this->model->getTable ());
    }

    public function countTotalsGroupByCampaign( $idCampaign, $idUser )
    {
        $this->changeTableName($idUser);
        return DB::table( $this->model->getTable() )->select(DB::raw('link_id, COUNT(1) AS totals'))->groupBy('link_id')->get();
    }
}