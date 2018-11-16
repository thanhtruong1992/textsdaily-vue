<?php
namespace App\Services\NotificationSettings;

use App\Services\BaseService;
use App\Repositories\NotificationSettings\INotificationSettingRepository;
use Illuminate\Support\Facades\Auth;

class NotificationSettingService extends BaseService implements INotificationSettingService {
    protected $notificationSettingRepo;
    public function __construct(INotificationSettingRepository $notificationSettingRepo) {
        $this->notificationSettingRepo = $notificationSettingRepo;
    }

    public function getNotificationOfUser($userID = null) {
        if(empty($userID)) {
            $userID = Auth::user()->id;
        }
        $notificationSetting = $this->notificationSettingRepo->findByField("user_id", $userID)->first();
        return !empty($notificationSetting) ? (object)$notificationSetting->toArray() : "";
    }

    /**
     * fn add notification setting
     * {@inheritDoc}
     * @see \App\Services\NotificationSettings\INotificationSettingService::create()
     */
    public function create($request) {
        $user = Auth::user();
        $attribute = $request->all();
        unset($attribute["_token"]);
        $data = [
            "notification" => json_encode((object)$attribute),
            "user_id" => $user->id,
            "created_by" => $user->id,
            "updated_by" => $user->id
        ];

        $notification = $this->getNotificationOfUser();
        $isUpdate = false;
        if(empty($notification)) {
            // create notification setting
            $notificationSetting = $this->notificationSettingRepo->create($data);
        }else {
            // update notification setting
            $notificationSetting = $this->notificationSettingRepo->update($data, $notification->id);
            $isUpdate = true;
        }


        return $this->success($isUpdate);
    }
}
