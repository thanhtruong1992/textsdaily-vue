<?php
namespace App\Services\NotificationSettings;

interface INotificationSettingService {
    public function create($request);
    public function getNotificationOfUser($userID = null);
}