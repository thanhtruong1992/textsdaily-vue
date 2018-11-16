<?php

namespace App\Repositories\CustomFields;

use App\Repositories\CustomFields\ICustomFieldRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use DB;
use App\Models\CustomField;


class CustomFieldRepository extends BaseRepository implements ICustomFieldRepository{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\CustomField";
    }

    public function addCustomfield($tableName, $columnName) {
        DB::statement("call add_customfield(?,?)", array($tableName, $columnName));
    }

    public function getCusfomFieldOfSubscriber($listId, $userId = null) {
        $query = CustomField::where('list_id', $listId);

        if($userId != "") {
            $query->where('user_id', $userId);
        }
        return $query->get();
    }

    public function  getCusfomFieldOfSubscriberByColumn($userId, array $listId, array $columnName = null) {
        if (isset($columnName) && count($columnName) > 0) {
            return DB::table('custom_fields')->select($columnName)
            ->where('user_id', $userId)
            ->whereIn('list_id', $listId)->get();
        }
        return CustomField::where('user_id', $userId)->whereIn('list_id', $listId)->get();
    }

    public function getCusfomFieldOfSubscriberByColumnForPersonalize($userId, array $listId, array $columnName = null) {
        if (isset($columnName) && count($columnName) > 0) {
//             $columnName[] = 'subscriber_lists.name';
//             return DB::table('custom_fields')->select($columnName)
//             ->leftJoin('subscriber_lists', 'custom_fields.list_id', '=', 'subscriber_lists.id')
//             ->where('custom_fields.user_id', $userId)
//             ->whereIn('custom_fields.list_id', $listId)->get();
            return DB::table('custom_fields')->select($columnName)
            ->where('user_id', $userId)
            ->whereIn('list_id', $listId)
            ->groupBy('field_name')->get();
        }
        return CustomField::where('user_id', $userId)->whereIn('list_id', $listId)->get();
    }
}
