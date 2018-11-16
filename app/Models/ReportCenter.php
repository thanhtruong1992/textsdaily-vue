<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Carbon\Carbon;

class ReportCenter extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'report_center';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'user_id',
            'from',
            'to',
            'time_zone',
            'params',
            'notification_emails',
            'result',
            'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
    public $timestamps = true;
    public $appends = ['url_download'];

    /**
     * fn format data params when get
     * @param string $value
     * @return object
     */
    public function getParamsAttribute($value) {
        return json_decode($value);
    }

    /**
     * fn format data params when add or update
     * @param object $value
     */
    public function setParamsAttribute($value) {
        $this->attributes['params'] = json_encode($value);
    }

    /**
     * fn format updated_at when get
     * @param unknown $value
     * @return unknown
     */
    public function getUpdatedAtAttribute($value) {
        $timezone = Auth::user() ? Auth::user()->time_zone : '';
        $timezone = $timezone != "" ?  $timezone : "UTC";
        $data = Carbon::parse($value)->setTimezone($timezone);
        return $data->format('Y-m-d H:i:s');
    }

    /**
     * fn fromat created_at when get
     * @param unknown $value
     * @return unknown
     */
    public function getCreatedAtAttribute($value) {
        $timezone = Auth::user() ? Auth::user()->time_zone : '';
        $timezone = $timezone != "" ?  $timezone : "UTC";
        $data = Carbon::parse($value)->setTimezone($timezone);
        return $data->format('Y-m-d H:i:s');
    }

    /**
     * fn format url download file csv
     * @param unknown $value
     * @return string
     */
    public function getUrlDownloadAttribute($value) {
        $url = route("download-report-center", array("hash" => $this->result));
        return $url;
    }

    public function user() {
        return $this->belongsTo("App\Models\User");
    }
}
