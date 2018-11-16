<?php

namespace App\Repositories\Campaign;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CampaignRepository extends BaseRepository implements ICampaignRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\Campaign";
    }

    private function changeTableName( $idUser ) {
        $this->__changeTableName ( array (
                'u_template' => 'u_' . $idUser
        ) );
    }

    /**
     * Call store get campaign by query from current user
     *
     * {@inheritdoc}
     * @see \App\Repositories\Campaign\ICampaignRepository::getCampaignByQuery()
     */
    public function getCampaignByQuery($search_key, $sort_column, $order_by, $page) {
        if (Auth::user ()) {
            $this->__changeTableName ( array (
                    'u_template' => 'u_' . Auth::user ()->id
            ) );
        }

        $user_id = Auth::user ()->isGroup4() ? Auth::user ()->reader_id : Auth::user ()->id;
        $campaign_table_name = 'campaign_u_' . $user_id;
        $campaign_recipient_table_name = 'campaign_recipients_u_' . $user_id;
        $queryBuilder = DB::table ( $campaign_table_name )
        ->select ( $campaign_table_name . '.id', $campaign_table_name . '.name', $campaign_table_name . '.send_time', DB::raw ( 'group_concat(subscriber_lists.name separator \'; \') as subscriber_list' ), DB::raw("LCASE(`status`) as status"), $campaign_table_name . '.send_timezone', $campaign_table_name . '.schedule_type' )
            ->leftJoin ( $campaign_recipient_table_name, $campaign_table_name . '.id', '=', $campaign_recipient_table_name . '.campaign_id' )
            ->leftJoin ( 'subscriber_lists', 'subscriber_lists.id', '=', $campaign_recipient_table_name . '.list_id' )
            ->groupBy ( $campaign_table_name . '.id', $campaign_table_name . '.name', $campaign_table_name . '.send_time', $campaign_table_name . '.status', $campaign_table_name . '.send_timezone', $campaign_table_name . '.schedule_type');
        // search keywork
        if (isset ( $search_key )) {
            $queryBuilder->where ( function ($q) use ($search_key, $campaign_table_name, $campaign_recipient_table_name) {
                $q  ->orWhere ( $campaign_table_name . '.id', 'LIKE', "%$search_key%" )
                ->orWhere ( $campaign_table_name . '.name', 'LIKE', "%$search_key%" );
            } );
        }

        // sort
        if (isset ( $sort_column ) && isset ( $order_by )) {
            $queryBuilder->orderBy ( $sort_column, $order_by );
        }

        $campaignResult = $queryBuilder->paginate (10);

        return $campaignResult;
    }

    /**
     * Amend campaign
     *
     * {@inheritdoc}
     * @see \App\Repositories\Campaign\ICampaignRepository::deleteCampaignItem()
     */
    public function amendCampaignItem($campaign_id) {
        if (Auth::user ()) {
            $this->__changeTableName ( array (
                    'u_template' => 'u_' . Auth::user ()->id
            ) );
        }

        $user_id = Auth::user ()->id;
        $campaign_table_name = 'campaign_u_' . $user_id;

        $item = parent::find($campaign_id);
        if ($item->status == "READY" && $item->schedule_type == 'IMMEDIATE') {
            $queryBuilder = DB::table ( $campaign_table_name )-> where('id', $campaign_id)->update([
                    'status' => "DRAFT",
                    'send_time' => null,
                    'schedule_type' => 'NOT_SCHEDULED'
            ]);
            return ["status" => true];
        } else {
            return ["status" => false];
        }
    }

    /**
     * Delete campaign
     *
     * {@inheritdoc}
     * @see \App\Repositories\Campaign\ICampaignRepository::deleteCampaignItem()
     */
    public function deleteCampaignItem($campaign_id) {
        if (Auth::user ()) {
            $this->__changeTableName ( array (
                    'u_template' => 'u_' . Auth::user ()->id
            ) );
        }

        $item = parent::find ( $campaign_id );
        if ($item->status == "DRAFT" || ($item->status == "READY" && $item->schedule_type == "FUTURE")) {
            try {
                $query = DB::statement ( "call delete_campaign_id(?,?)", array (Auth::user ()->id,$campaign_id));
            } catch ( \Exception $e ) {
                // Show error message from SQL statement
                return [
                        "status" => false
                ];
            }
            return [
                    "status" => true
            ];
        } else {
            return [
                    "status" => false
            ];
        }
    }

    /**
     * CUSTOM CREATE FUNCTION TO CHANGE TABLE NAME BY USER
     *
     * {@inheritdoc}
     * @see \Prettus\Repository\Eloquent\BaseRepository::create()
     */
    public function create(array $attributes, $table_name = null) {
        if (Auth::user ()) {
            $this->__changeTableName ( array (
                    'u_template' => 'u_' . Auth::user ()->id
            ) );
        }
        return parent::create ( $attributes, $this->model->getTable () );
    }


    /**
     * CUSTOM UPDATE FUNCTION TO CHANGE TABLE NAME BY USER
     *
     * {@inheritdoc}
     * @see \Prettus\Repository\Eloquent\BaseRepository::create()
     */
    public function update(array $attributes, $id, $table_name = null) {
        // change table name instance
        if (Auth::user ()) {
            $this->__changeTableName ( array (
                    'u_template' => 'u_' . Auth::user ()->id
            ) );
            $attributes['updated_by'] = Auth::user ()->id;
        }

        return parent::update($attributes, $id, $this->model->getTable ());
    }

    public function updateByUser( array $attributes, $id, $idUser )
    {
        $this->changeTableName($idUser);
        return parent::update($attributes, $id, $this->model->getTable ());
    }


    /**
     * CUSTOM FIND FUNCTION TO CHANGE TABLE NAME BY USER
     *
     * {@inheritdoc}
     * @see \Prettus\Repository\Eloquent\BaseRepository::find()
     */
    public function find($id, $userID = null, $columns = ['*']) {
        if($userID != "") {
            $this->changeTableName($userID);
        }else {
            $this->changeTableName(Auth::user ()->isGroup4() ? Auth::user ()->reader_id : Auth::user ()->id);
        }

        return parent::find ( $id, $columns );
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Campaign\ICampaignRepository::getReadyCampaign()
     */
    public function getReadyCampaign(){
        $campaignQueue = DB::select('CALL getCampaignReady()');
        $campaignQueue = $campaignQueue[0];
        if (!empty($campaignQueue) && $campaignQueue->campaign_id && $campaignQueue->user_id) {
            return $this->find($campaignQueue->campaign_id, $campaignQueue->user_id);
        }
        return [];
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Campaign\ICampaignRepository::getSentCampaign()
     */
    public function getSentCampaign(){
        $campaignQueue = DB::select('CALL getCampaignSent()');
        $campaignQueue = $campaignQueue[0];
        if ($campaignQueue->campaign_id && $campaignQueue->user_id) {
            return $this->find($campaignQueue->campaign_id, $campaignQueue->user_id);
        }
        return [];
    }

    /**
     * FN total subscriber and total subscriber duplicate with list id subscirber
     * @param string $list_id
     * @return object
     */
    public function summarySubscribers($listId, $userID, $totalSMS, $defaultPriceSMS) {
        $userID = Auth::user()->id;
        $total = DB::select("call summary_subscribers(?,?,?,?)", array($listId, $userID, $totalSMS, $defaultPriceSMS));
        $data = DB::select("call get_country_network_summary_cammpaign(?,?,?,?)", array($userID, $listId, $totalSMS, $defaultPriceSMS));
        return (object) [
                "total" => $total[0] ? $total[0] : [],
                "data" => $data
        ];
    }

    /**
     * FN clone campaign
     * {@inheritDoc}
     * @see \App\Repositories\Campaign\ICampaignRepository::cloneCampaign()
     */
    public function cloneCampaign($campaign_id, $user_id) {
        return DB::statement("call clone_data_campaign(?,?)", array($campaign_id, $user_id));
    }

    /**
     * FN get total sent campaign of list user
     * @param string $userId
     * @param datetime $lastDate
     * @param datetime $nowDate
     * @return object
     */
    public function totalSendCampaignOfUsers($userId, $startDate, $endDate, $filter, $timezone, $type, $currency) {
        return DB::select("call total_campagin_of_users(?,?,?,?,?,?,?)", array($userId, $startDate, $endDate, $filter, $timezone, $type, $currency));
    }

    /**
     * fn get all campaign pending report
     * {@inheritDoc}
     * @see \Prettus\Repository\Eloquent\BaseRepository::all()
     */
    public function getAllCampaignPendingReport($userID) {
        if ( $userID) {
            $this->__changeTableName ( array (
                    'u_template' => 'u_' . $userID
            ) );
        }
        return parent::scopeQuery(function($query) {
            return $query->orderBy('backend_statistic_report_updated_at','asc');
        })->findWhere( [
                'backend_statistic_report' => 'PENDING',
                'status' => 'SENT',
                'user_id' => $userID
        ], ['id'] );
    }

    public function updateStatisticReportCampagin($userID, $campaignID, $attributes) {
        if ($userID) {
            $this->__changeTableName ( array (
                    'u_template' => 'u_' . $userID
            ) );
            $attributes['updated_by'] = $userID;
        }
        return parent::update($attributes, $campaignID, $this->model->getTable ());
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Campaign\ICampaignRepository::getCampaignWithSubscriberList()
     */
    public function getCampaignWithSubscriberList($userID, $campaignID) {
        $query = "SELECT T3.id, T3.name, T3.send_Time, T3.send_timezone, GROUP_CONCAT(T4.name SEPARATOR ', ') AS list_name FROM (" .
                " SELECT T1.id, T1.name, T2.list_id, T1.send_time, T1.send_timezone FROM campaign_u_$userID AS T1" .
                " LEFT JOIN campaign_recipients_u_$userID AS T2" .
                " ON T1.id = T2.campaign_id" .
                " WHERE T1.id = {$campaignID}) AS T3" .
                " LEFT JOIN subscriber_lists AS T4".
                " ON T3.list_id = T4.id" .
                " GROUP BY T3.id";
        return DB::select($query);
    }

    /**
     * FN get campaign report
     * {@inheritDoc}
     * @see \App\Repositories\Campaign\ICampaignRepository::getCampaign()
     */
    public function getCampaign($userID, $campaignID, $userType) {
        $query_user = "";
        if($userType == "GROUP1") {
            $query_user = "SELECT " . $userID . " AS user_id, T1.name FROM users AS T1 INNER JOIN users T2 ON T1.id = T2.parent_id AND T2.id = " . $userID;
        }else {
            $query_user = "SELECT id AS user_id, name, billing_type FROM users WHERE id = ". $userID;
        }
        $query = "SELECT T1.name, T1.send_time, T1.send_timezone, T2.name AS user_name FROM campaign_u_" . $userID . " AS T1 LEFT JOIN (" . $query_user . ") AS T2 ON T1.user_id = T2.user_id WHERE id = " . $campaignID;
        return DB::select($query);
    }

    /**
     * fn detect country network, service_provider of phone
     * @param unknown $phone
     * @param unknown $userID
     * @param unknown $defaultPrice
     * @return unknown
     */
    public function detectCountryNetworkServiceProviderOfPhone($phone, $userID, $defaultPrice) {
        return DB::select("call detect_country_network_service_provider_of_phone(?,?,?)", array($phone, $userID, $defaultPrice));
    }

    /**
     * fn get first campaign 
     */
    public function getFirstCampaign() {
        if (Auth::user ()) {
            $this->__changeTableName ( array (
                    'u_template' => 'u_' . Auth::user ()->id
            ) );
        }

        return parent::first (['*'], $this->model->getTable () );
    }

    /*
     * CUSTOM CREATE FUNCTION TO CHANGE TABLE NAME BY USER
     *
     * fn create campaign api account
     */
    public function createCampaignApiAccount(array $attributes, $table_name = null) {
        $this->__changeTableName ( array (
                'u_template' => 'u_' . $attributes['user_id']
        ) );
        return parent::create ( $attributes, $this->model->getTable () );
    }

    /**
     * fn update status campaign aapi account 
     * 
     */
    public function updateStatusCampaignApiAccount($attributes, $userID, $campaign_id) {
        $this->__changeTableName ( array (
            'u_template' => 'u_' . $userID
        ) );

        return  DB::table( $this->model->getTable() )
                ->where('id', $campaign_id )
                ->update($attributes);
    }


}
