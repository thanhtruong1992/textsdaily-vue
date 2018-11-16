<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $table = "notification_settings";
    protected $fillable = [
        "user_id",
        "notification",
        "created_by",
        "updated_by"
    ];
    protected $hidden = [];
    public $timestamps = true;

    public function getNotificationAttribute($value) {
        if(empty($value)) {
            $value = (object) [
                "scheduled" => "",
                "progress" => "",
                "paused" => "",
                "finsihed" => "",
                "failed" => ""
            ];
        }
        $value = json_decode($value);
        $value->scheduled = !empty($value->scheduled) ? $value->scheduled : "";
        $value->progress = !empty($value->progress) ? $value->progress : "";
        $value->paused = !empty($value->paused) ? $value->paused : "";
        $value->finished = !empty($value->finished) ? $value->finished : "";
        $value->failed = !empty($value->failed) ? $value->failed : "";
        return $value;
    }
}
