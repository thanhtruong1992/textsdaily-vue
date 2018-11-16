<?php

namespace App\Repositories\Subscribers;

interface ISubscriberRepository{
    public function getListSubscribers($listId, $paging, $search_key, $column_sort, $orderBy, $filter, $flagFilter);
    public function readFile($file, $line, $file_terminated, $file_enclosed);
    public function getCustomFieldOfUser($userId, $listId);
    public function cloneTableSubscriber($tableName, $tableTempName);
    public function importSubscribersCSV($pathFile, $stringField, $tableTempName, $line, $fileTerminated, $fileEnclosed, $breakLine, $stringCol);
    public function moveDataSubscribers($tableTempName, $tableName, $updateIfDuplicatue, $update_fields, $list_id, $user_id, $status = null, $flagUpdate = false);
    public function exportSubscribersCSV($tableName, $arrField, $fileName, $filter, $flagFilter, $headerExport);
    public function deleteSubscribers ($ids, $tableCampaign, $tableSubscriber);
    /**
     * get count item status
     */
    public function getCountStatusItem($list_id);

    public function updateByListId( $attributes, $id, $listId );
    public function findSubscriber( $idList, $id, $columns = ['*'] );
    public function getAllTableSubscribers();
    public function getDetectData($idList, $limit, $offset = 0);
    public function countTotalsByDetectStatus($idList);
    public function deleteSubscribersWithStatus($listID, $supperssedID, $status, $flagSupperssion = false);
    public function exportSubscriberWithStatus($listID, $status, $headers, $fields, $fileName, $supperssedID);
    public function createSubscriber(array $attributes, $idList);
    public function getSubscriberByPhone($phone, $campaignID, $idList);
    public function getTotalSubscriberByListID($idList);
}