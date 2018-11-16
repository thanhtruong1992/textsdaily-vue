<?php

namespace App\Repositories\SubscriberLists;

interface ISubscriberListRepository {
    public function getSubscriberListByUser($userID, $search_key, $column_sort, $orderBy);
    public function deleteSubscriberListItem($subscriber_list_id);
    public function createNewSubscriberListTemplate($id);
    public function getListSubscribers($listId, $userID);
}