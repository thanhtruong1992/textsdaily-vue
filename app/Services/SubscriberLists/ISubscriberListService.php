<?php

namespace App\Services\SubscriberLists;

interface ISubscriberListService {
    public function createNewSubscriberList($request);
    public function deleteSubscriberList($list_id);
    public function getAllSubscribers();
    public function getAllSubscriberListByUser($request);
    public function getSubscriberList($listId);
    public function createGlobalSupperssionList($userId);
    public function getReportListSummary($listID);
    public function getGlobalSuppressionList($idUser = null, $columns = ['*']);
    public function getDetectSubscriberList();
    public function updateSubscriberList( $idList, array $attributes );
    public function updateSubscriberListViaModel( $idList, array $attributes );
    public function getInvalidEntriesList($idUser = null, $columns = ['*']);
    public function createInvalidEntriesList($userId);
}