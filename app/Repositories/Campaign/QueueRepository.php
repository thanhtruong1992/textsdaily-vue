<?php

namespace App\Repositories\Campaign;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class QueueRepository extends BaseRepository implements IQueueRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\Queue";
    }

    private function changeTableName( $userID, $campaignID )
    {
        $this->__changeTableName ( array( 'u_template' => 'u_' . $userID, 'c_template' => 'c_' . $campaignID ) );
    }

    /**
     * CUSTOM UPDATE FUNCTION TO CHANGE TABLE NAME BY USER
     * {@inheritDoc}
     * @see \App\Repositories\Campaign\IQueueRepository::updateQueue()
     */
    public function updateQueue( array $attributes, $id, $userID, $campaignID, $skipUpdatedAt = false )
    {
        // $this->changeTableName($userID, $campaignID);
        // if (!$skipUpdatedAt) {
        //     $attributes['updated_at'] = date('Y-m-d H:i:s');
        // }
        // //
        // return parent::update($attributes, $id, $this->model->getTable ());
        
        if (!$skipUpdatedAt) {
            $attributes['updated_at'] = date('Y-m-d H:i:s');
        }

        return DB::table("queue_u_" . $userID . "_c_" . $campaignID)
            ->where('id', $id)
            ->update($attributes);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Campaign\IQueueRepository::generateQueueTable()
     */
    public function generateQueueTable( $userID, $campaignID, $listID = array(), $idGlobalSuppressionList )
    {
        try {
            // $this->changeTableName($userID, $campaignID);
            //
            $recipients = implode( ',', array_values( $listID ));
            DB::statement("CALL generateQueueTableByCampaign( $userID, $campaignID, '$recipients', $idGlobalSuppressionList ); ");
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Campaign\IQueueRepository::getPendingTotals()
     */
    public function getPendingTotals( $userID, $campaignID )
    {
        $results = DB::table('queue_u_' . $userID . '_c_' . $campaignID)
            ->select(DB::raw('COUNT(1) AS totals'))
            ->whereIn('status', ['PENDING', 'SENDING'])
            ->get();
            
        if ( isset( $results[0] ) ) {
            return $results[0]->totals;
        }
        return 0;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Campaign\IQueueRepository::getTotalsByStatus()
     */
    public function getTotalsByStatus( $userID, $campaignID )
    {
        // $this->changeTableName($userID, $campaignID);
        //
        $qrResults = DB::table('queue_u_' . $userID . '_c_' . $campaignID)->select(DB::raw('status, COUNT(1) AS totals'))->groupBy('status')->get();

        $results = array( 'PENDING' => 0, 'SENDING' => 0, 'SENT' => 0, 'FAILED' => 0, 'TOTALS' => 0 );
        foreach ( $qrResults as $item ) {
            $results[ $item->status ] = $item->totals;
            $results['TOTALS'] += $item->totals;
        }
        return $results;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Campaign\IQueueRepository::getTotalsByReturnStatus()
     */
    public function getTotalsByReturnStatus( $userID, $campaignID )
    {
        // $this->changeTableName($userID, $campaignID);
        //
        $qrResults = DB::table('queue_u_' . $userID . '_c_' . $campaignID)->select(DB::raw('return_status, COUNT(1) AS totals'))->where('status', 'SENT')->groupBy('return_status')->get();

        $results = array( 'PENDING' => 0, 'DELIVERED' => 0, 'EXPIRED' => 0, 'FAILED' => 0, 'TOTALS' => 0 );
        foreach ( $qrResults as $item ) {
            $results[ $item->return_status ] = $item->totals;
            $results['TOTALS'] += $item->totals;
        }
        return $results;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Campaign\IQueueRepository::getPendingQueues()
     */
    public function getPendingQueues( $userID, $campaignID, $limit = 0, $offset = 0 )
    {
        //
        $qr = DB::table( "queue_u_" . $userID . "_c_" . $campaignID )->where('status', '=', 'PENDING');
        if ( $offset ) {
            $qr->offset( $offset );
        }
        if ( $limit ) {
            $qr->take( $limit );
        }
        $query = clone $qr;
        $result = $qr->get();
        if ( count($result) > 0) {
            $queueIDs = collect($result)->implode('id', ',');
            $pdo = DB::connection()->getPdo();
            $pdo->exec("UPDATE queue_u_" . $userID . "_c_" . $campaignID . " SET status = 'SENDING', updated_at = '". date('Y-m-d H:i:s') ."' WHERE id IN (" . $queueIDs . ");");
            // Update status to SENDING
            // $updateResults = $query->update([ 'status' => 'SENDING', 'updated_at' => date('Y-m-d H:i:s')]);
            return $result;
        } else {
            return false;
        }
    }

    /**
     * fn get list queue by array id
     * 
     */
    public function getQueueByIDs($userID, $campaignID, $queueIDs) {

        $result = DB::table( "queue_u_" . $userID . "_c_" . $campaignID )
                    ->whereIn('id', $queueIDs)
                    ->where('status', 'SENDING')
                    ->get();
       
        if(count($result) > 0) {
            return $result;
        }

        return [];
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Campaign\IQueueRepository::getTrackingTotals()
     */
    public function getTrackingTotals( $userID, $campaignID )
    {
        // $this->changeTableName($userID, $campaignID);
        //
        $results = DB::table('queue_u_' . $userID . '_c_' . $campaignID)
            ->select(DB::raw('COUNT(1) AS totals'))
            ->whereIn('return_status', ['PENDING'])
            ->get()
        ;
        if ( isset( $results[0] ) ) {
            return $results[0]->totals;
        }
        return 0;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Campaign\IQueueRepository::getTrackingQueues()
     */
    public function getTrackingQueues( $userID, $campaignID, $limit = 0, $offset = 0 )
    {
        // $this->changeTableName( $userID, $campaignID );
        //
        $qr = DB::table('queue_u_' . $userID . '_c_' . $campaignID)
            ->select('id', 'list_id', 'subscriber_id', 'country', 'network', 'service_provider', 'return_bulk_id', 'return_message_id', 'return_sms_count', 'updated_at' )
            ->where('return_status','PENDING')
            ->where('status', 'SENT')
            ->orderBy('report_updated_at', 'ASC');
        if ( $offset ) {
            $qr->offset( $offset );
        }
        if ( $limit ) {
            $qr->limit( $limit );
        }
        return $qr->get();
    }

    /**
     * fn get queue by report status pending
     * @param string $userID
     * @param string $campaignID
     * @param string $limit
     * @param string $offset
     * @return array
     */
    public function getPendingReportQueue( $userID, $campaignID, $limit = 0, $offset = 0 )
    {
        //
        $qr = DB::table('queue_u_' . $userID . '_c_' . $campaignID)
            ->select('id', 'list_id', 'subscriber_id', 'country', 'network', 'service_provider', 'return_bulk_id', 'return_message_id', 'return_sms_count', 'updated_at' )
            ->where('return_status','PENDING')
            ->where('report_status', 'PENDING')
            ->where('status', 'SENT')
            ->orderBy('report_updated_at' , 'ASC');

        if ( $offset ) {
            $qr->offset( $offset );
        }
        if ( $limit ) {
            $qr->limit( $limit );
        }
        $update = clone $qr;
        $result = $qr->get();
        if ( count($result) > 0) {
            $queueIDs = collect($result)->implode('id', ',');
            $pdo = DB::connection()->getPdo();
            $pdo->exec("UPDATE queue_u_" . $userID . "_c_" . $campaignID . " SET report_status = 'REPORTING', report_updated_at = '". date('Y-m-d H:i:s') ."' WHERE id IN (" . $queueIDs . ");");
            // Update retport status to REPORTING
            // $update->update([ 'report_status' => 'REPORTING', 'report_updated_at' => date('Y-m-d H:i:s')]);
        } 
        return $result;
    }

    /**
     * fn get total queue by report status pending or reporting
     * @param string $userID
     * @param string $campaignID
     * @return int
     * 
     */
    public function getTotalByReportStatus($userID, $campaignID) {
        $results = DB::table( "queue_u_" . $userID . "_c_" . $campaignID )
            ->select(DB::raw('COUNT(1) AS totals'))
            ->whereIn('report_status', ['PENDING', 'REPORTING'])
            ->orWhere('return_status', 'PENDING')
            ->get();

        if ( isset( $results[0] ) ) {
            return $results[0]->totals;
        }
        return 0;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Campaign\IQueueRepository::updateAllFailed()
     */
    public function updateAllFailed( $userID, $campaignID, $msg = null, array $status = ['PENDING','SENDING']) {
        // $this->changeTableName($userID, $campaignID);
        //
        DB::beginTransaction();
        try {
            // DB::table('queue_u_' . $userID . '_c_' . $campaignID)->whereIn('status', $status)->update([
            //         'status' => 'FAILED',
            //         'return_status' => 'FAILED',
            //         'return_status_message' => $msg,
            //         'updated_at' => date('Y-m-d H:i:s')
            // ]);
            // return true;
            $pdo = DB::connection()->getPdo();
           
            $query = "UPDATE queue_u_" . $userID . "_c_" . $campaignID ;
            $query .= " SET status = 'FAILED', return_status = 'FAILED',";
            $query .= " return_status_message = '" . $msg . "', updated_at = '" . date('Y-m-d H:i:s') . "'";
            $query .= " WHERE status IN ('" .implode( "','", $status ) . "');";
            $pdo->exec($query);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            Log::error ( 'Rollback Fn updateAllFailed ' . $e->getMessage());
            DB::rollBack();
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Campaign\IQueueRepository::updateFailed()
     */
    public function updateFailed( $userID, $campaignID, $id, $msg = null ) {
        return $this->updateQueue ( [
                'status' => 'FAILED',
                'return_status' => 'FAILED',
                'return_status_message' => $msg
        ], $id, $userID, $campaignID);
    }

    /**
     *  update status pending of queue
     * @param unknown $userID
     * @param unknown $campaignID
     * @return mixed|\Illuminate\Support\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function updatePendingAllQueue($userID, $campaignID) {
        // $this->changeTableName($userID, $campaignID);
        try {
            DB::table('queue_u_' . $userID . '_c_' . $campaignID)->where('status', 'SENDING')->update([
                    'status' => 'PENDING',
                    'updated_at' => date('Y-m-d H:i:s')
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * fn get queue by phone
     * @param unknown $phone
     * @param unknown $userID
     * @param unknown $campaignID
     * @return mixed|\Illuminate\Support\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getQueueByPhone($phone, $userID, $campaignID) {
        // $this->changeTableName($userID, $campaignID);
        // //
        // return parent::findWhere( ['phone' => $phone, 'status' => 'SENT'] )->last();
        return DB::table('queue_u_' . $userID . '_c_' . $campaignID)
                ->where('phone', $phone)
                ->where('status', 'SENT')
                ->orderBy('id', 'DESC')
                ->first();
    }

    /**
     * fn detect country network and service provider of phone 
     */
    public function detectCountryNetworkServiceProviderOfPhone($phone, $userID, $parentID, $defaultPrice, $priceParent) {
        return DB::select("call detectCountryNetworkServiceProdiverOfPhone(?,?,?,?,?)", array($phone, $userID, $parentID, $defaultPrice, $priceParent));
    }

    /**
     * fn get queue pending report 
     */
    public function getQueuePendingReport($userID, $campaignID, $arrQueueID) {
        return DB::table( "queue_u_" . $userID . "_c_" . $campaignID )
                    ->whereIn('id', $arrQueueID)
                    ->where('report_status', 'REPORTING')
                    ->where('return_status', 'PENDING')
                    ->get();
    }
    
    /**
     * fn create queue api account
     */
    public function createQueueApi($allQueues, $userID, $campaignID) {
        // $this->changeTableName($userID, $campaignID);
        return DB::table('queue_u_' . $userID . '_c_' . $campaignID)->insert($allQueues);
    }

    /**
     * fn get report api account
     */
    public function getReportApi($uuid, $userID, $campaignID) {
        return DB::table( "queue_u_" . $userID . "_c_" . $campaignID )
                    ->where('queue_id', $uuid)
                    ->first();
    }

    /**
     * fn insert or update multiple row queue
     * @param string $query
     * @return boolean
     */
    public function insertOrUpdateMultipleRowQueue($query) {
        DB::beginTransaction();
        try {
            $pdo = DB::connection()->getPdo();
            $pdo->exec($query);
        
            DB::commit();
            // all good
            return true;
        } catch (\Exception $e) {
            Log::error ( 'Rollback Fn insertOrUpdateMultipleRowQueue ' . $e->getMessage());
            DB::rollBack();
        }
    }

    /**
     * fn delete report by list id
     * @param string $tableName
     * @param array $listID
     * @return bollean
     */
    public function deleteReportByListID($tableName, $field, $listID) {
        DB::table($tableName)->whereIn($field, $listID)->delete();
    }

    public function updateAgaignReport($userID, $campaignID, $listID) {
        DB::beginTransaction();
        try {
            $pdo = DB::connection()->getPdo();
            $pdo->exec("UPDATE queue_u_" . $userID . "_c_" . $campaignID . " SET report_status = 'PENDING', report_updated_at = '". date('Y-m-d H:i:s') ."' WHERE id IN (" . implode(',', $listID) . ");");
        
            DB::commit();
            // all good
            return true;
        } catch (\Exception $e) {
            Log::error ( 'Rollback Fn updateAgaignReport ' . $e->getMessage());
            DB::rollBack();
        }
        
    }

    public function checkExistsTableQueue($userID, $campaignID) {
        return Schema::hasTable( "queue_u_" . $userID . "_c_" . $campaignID);
    }
}