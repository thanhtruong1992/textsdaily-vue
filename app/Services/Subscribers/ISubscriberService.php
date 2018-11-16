<?php

namespace App\Services\Subscribers;

interface ISubscriberService {
    public function getListSubscribers($listId, $request);
    public function uploadCSV($request, $key = null);
    public function copyPaste($request, $key = null);
    public function getCustomFieldOfUser($listID);
    public function readFileCSV($key = null);
    public function importFileCSV($request);
    public function exportSubscribers($listId, $request);
    public function deleteSubscribers($list_id, $request);
    public function downloadCSV($hash);
    public function updateSubscriber( $attributes, $id, $listId );
    public function updateStatus($request, $key = null);
    public function getSubscriberInfo( $idList, $id );
    public function detectSubscribers( $idList );
    public function deleteSubscribersWithStatus($listID, $request);
    public function viewExport($listId);
    public function exportSubscriberWithStatus($listID, $request);
    public function createSubscriber(array $attributes, $idList);
    public function addUnsubscriber($data, $userID);
    public function addPhoneInvalid($data, $userID);
}