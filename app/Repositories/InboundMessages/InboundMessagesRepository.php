<?php

namespace App\Repositories\InboundMessages;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class InboundMessagesRepository extends BaseRepository implements IInboundMessagesRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\InboundMessages";
    }

    private function changeTableName( $idUser ) {
        $this->__changeTableName ( array (
                'u_template' => 'u_' . $idUser
        ) );
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\InboundMessages\IInboundMessagesRepository::getDataTableList()
     */
    public function getDataTableList($orderBy = 'created_at', $orderDirection = 'DESC', $request = null, $idUser = null) {
        $user = Auth::user();
        if (is_null($idUser)){
            $idUser = $user->id;
        }
        $this->changeTableName($idUser);

        $query = DB::table($this->model->getTable())
                ->select(DB::raw('*, CONVERT_TZ(created_at, "UTC", "'. $request->get('timezone', $user->time_zone) .'") AS created_at'));

        if($request->has('from')) {
            $query = $query->where('created_at', '>=', Carbon::parse($request->get('from'), $request->get('timezone', $user->time_zone))->setTimezone('UTC')->toDateTimeString());
        }

        if($request->has('to')) {
            $query = $query->where('created_at', '<=', Carbon::parse($request->get('to'), $request->get('timezone', $user->time_zone))->setTimezone('UTC')->toDateTimeString());
        }

        if($request->has('subscriber_number')) {
            $query = $query->where('from', $request->get('subscriber_number'));
        }

        if($request->has('hosted_number')) {
            $query = $query->where('to', $request->get('hosted_number'));
        }

        return $query->orderBy($orderBy, $orderDirection)->paginate(10);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\InboundMessages\IInboundMessagesRepository::createMessage()
     */
    public function createMessage(array $attributes, $idUser = null) {
        if ($idUser){
            $this->changeTableName($idUser);
        }
        return parent::create ( $attributes, $this->model->getTable () );
    }

    /**
     * fn export csv inbound message
     * {@inheritDoc}
     * @see \App\Repositories\InboundMessages\IInboundMessagesRepository::exportCSV()
     */
    public function exportCSV($request, $pathFile, $userID = null) {
        $user = Auth::user();
        if(is_null($userID)) {
            $userID = $user->id;
        }
        $this->changeTableName($userID);
        $timezone = $request->get('timezone', $user->time_zone);
        $table = $this->model->getTable();
        $query = "SELECT 'Received Date' AS create_at, 'Hosted Number' AS hosted_number, 'Subscriber Number' AS subscriber_number, 'SMS content' AS message UNION ALL SELECT DATE_FORMAT(CONVERT_TZ(created_at, 'UTC', '" . $timezone . "'), '%d-%b-%Y %H:%i') AS created_at, `to` AS hosted_number, `from` AS subscriber_number, REPLACE(REPLACE(REPLACE(message, '\r\n', ' '), '\n', ' '), '\r', ' ') AS message INTO OUTFILE '". $pathFile."' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n' FROM " . $table . " WHERE id != 0 ";

        if($request->has('from')) {
            $start = Carbon::parse($request->get('from'), $request->get('timezone', $user->time_zone))->setTimezone('UTC')->toDateTimeString();
            $query = $query . " AND `created_at` >= '" . $start . "'";
        }

        if($request->has('to')) {
            $end = Carbon::parse($request->get('to'), $request->get('timezone', $user->time_zone))->setTimezone('UTC')->toDateTimeString();
            $query = $query . " AND `created_at` <= '" . $end . "'";
        }

        if($request->has('subscriber_number')) {
            $query = $query . " AND `from` = '" . $request->get('subscriber_number') . "'";
        }

        if($request->has('hosted_number')) {
            $query = $query . " AND `to` = '" . $request->get('hosted_number') . "'";
        }

        $pdo = DB::connection()->getPdo();
        return $pdo->exec($query);
    }
}
