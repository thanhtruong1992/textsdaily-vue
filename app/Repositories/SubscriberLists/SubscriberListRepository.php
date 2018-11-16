<?php

namespace App\Repositories\SubscriberLists;

use App\Repositories\SubscriberLists\ISubscriberListRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\SubscriberList;
use Illuminate\Support\Facades\DB;

class SubscriberListRepository extends BaseRepository implements ISubscriberListRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\SubscriberList";
    }

    /**
     * Get all subscriberList by user id and all key filter
     *
     * @see \App\Repositories\Subscribers\ISubscriberListRepository::getSubscriberListByUser()
     */
    function getSubscriberListByUser($userID, $search_key = null, $column_sort = null, $orderBy = null) {
        $spression_invalid_list = SubscriberList::where ( 'user_id', $userID )->where('is_global', 1)->get();
        $query = SubscriberList::where ( 'user_id', $userID )->where('is_global', 0);

        // search keywork
        if (isset ( $search_key )) {
            $query->where ( function ($q) use ($search_key) {
                $q->orWhere ( 'name', "LIKE", "%$search_key%" );
            } );
        }

        // sort
        if (isset ( $column_sort ) && isset ( $orderBy )) {
            $query->orderBy ( $column_sort, $orderBy );
        }

        $subscribers = $query->orderBy('id', 'DESC')->paginate (9);
        $total = $subscribers->total() + count($spression_invalid_list);

        $result = $subscribers->items();
        if (count($result) > 0 || count($spression_invalid_list) > 0) {
            $result = collect($result)->merge($spression_invalid_list);
        }
        return (object)[
            'total' => $total,
            'data' => $result
        ];
    }

    public function createNewSubscriberListTemplate($id) {
        try {
            $query = DB::statement("call new_subscriber_template(?)", array($id));
        } catch (\Exception $e) {
            // Show error message from SQL statement
        }
    }

    /**
     *  Delete item subscriber list by id
     */
    function deleteSubscriberListItem($subscriber_list_id) {
        $result = SubscriberList::where('id', $subscriber_list_id)->delete();
        if ($result) {
            try {
                $query = DB::statement("call delete_subscriber_template(?)", array($subscriber_list_id));
            } catch (\Exception $e) {
                // Show error message from SQL statement
            }
        }
        return $result;
    }

    /**
     * FN get subscribers with list_id and user_id
     * @param string $listId
     * @param int $userID
     * @return array()
     */
    public function getListSubscribers($listId, $userID) {
        return SubscriberList::where ( 'user_id', $userID )->whereIn ( 'id', explode(',', $listId))->get();
    }

    public function updateSubscriberList( array $attributes, $id ) {
        return DB::table( 'subscriber_lists' )->where('id', $id)->update($attributes);
    }
}
