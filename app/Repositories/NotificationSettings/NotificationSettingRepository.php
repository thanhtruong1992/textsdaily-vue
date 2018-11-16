<?php
namespace App\Repositories\NotificationSettings;

use App\Repositories\BaseRepository;

class NotificationSettingRepository extends BaseRepository implements INotificationSettingRepository {
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\NotificationSetting";
    }
}