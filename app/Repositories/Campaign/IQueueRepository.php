<?php

namespace App\Repositories\Campaign;

interface IQueueRepository
{
    public function updateQueue( array $attributes, $id, $userID, $campaignID, $skipUpdatedAt = false );

    public function generateQueueTable( $userID, $campaignID, $listID = array(), $idGlobalSuppressionList );

    public function getPendingTotals( $userID, $campaignID );

    public function getTotalsByStatus( $userID, $campaignID );

    public function getTotalsByReturnStatus( $userID, $campaignID );

    public function getPendingQueues( $userID, $campaignID, $limit = 0, $offset = 0 );

    public function getTrackingTotals( $userID, $campaignID );

    public function getTrackingQueues( $userID, $campaignID, $limit = 0, $offset = 0 );

    public function updateAllFailed( $userID, $campaignID, $msg = null, array $status = ['PENDING','SENDING']);

    public function updateFailed( $userID, $campaignID, $id, $msg = null);

    public function updatePendingAllQueue($userID, $campaignID);

    public function getQueueByPhone($phone, $userID, $campaignID);

    public function getQueueByIDs($userID, $campaignID, $queueIDs);
}