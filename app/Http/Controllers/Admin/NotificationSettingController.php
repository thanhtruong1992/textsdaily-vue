<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationSettings\INotificationSettingService;
use Illuminate\Support\Facades\Validator;
use Session;
use Illuminate\Support\Facades\Lang;

class NotificationSettingController extends Controller
{
    protected $notificationSettingService;
    public function __construct(INotificationSettingService $notificationSettingService) {
        $this->notificationSettingService = $notificationSettingService;
    }

    public function index() {
        $notificationSetting = $this->notificationSettingService->getNotificationOfUser();
        return view("admins.campaigns.notification-settings", ["notification" => $notificationSetting]);
    }

    public function store(Request $request) {
        $validator = Validator::make ( $request->all (), [
            "scheduled" => "emailmultiple",
            "progress" => "emailmultiple",
            "paused" => "emailmultiple",
            "finished" => "emailmultiple"
        ]);

        if ($validator->fails ()) {
            Session::flash ( 'error', Lang::get ( 'notify.input_notification_setting_error' ) );
            return redirect()->back();
        }

        $result = $this->notificationSettingService->create($request);
        if(!!$result->data) {
            Session::flash ( 'success', Lang::get ( 'notify.update_notification_success' ) );
        }else {
            Session::flash ( 'success', Lang::get ( 'notify.create_notification_success' ) );
        }
        return redirect()->route("notification-settings.index");
    }
}
