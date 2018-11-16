<?php

namespace App\Repositories\Settings;

use Prettus\Repository\Eloquent\BaseRepository;
use DB;
use Illuminate\Support\Facades\Auth;

class InboundConfigRepository extends BaseRepository implements IInboundConfigRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\InboundConfig";
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Settings\IInboundConfigRepository::getDataTableList()
     */
    public function getDataTableList($keyword, $orderBy = 'expiry_date', $orderDirection = 'ASC') {
        $currentUser = Auth::user();
        //
        $qr = DB::table( $this->model->getTable() )
        ->select('inbound_config.id', 'number', 'expiry_date', 'users.name AS user_id', 'keyworks');

        if ( $currentUser->type == 'GROUP1' ) {
            $qr->leftJoin('users', 'group2_user_id', '=', 'users.id');
        } elseif ( $currentUser->type == 'GROUP2' ) {
            $qr->leftJoin('users', 'group3_user_id', '=', 'users.id')
                ->where('group2_user_id', $currentUser->id)
            ;
        }

        //
        if ( $keyword ) {
            $qr->where('number', 'LIKE', $keyword . '%')
            ->orWhere('keyworks', 'LIKE', '%' . $keyword . '%')
            ->orWhere('name', 'LIKE', '%' . $keyword . '%')
            ;
        }
        //
        if ( $orderBy && $orderDirection ) {
            $qr->orderBy($orderBy, $orderDirection);
        } else {
            $qr->orderBy('expiry_date', 'ASC');
        }
        return $qr->paginate(10);
    }

}