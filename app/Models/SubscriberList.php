<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Auth;
use Carbon\Carbon;

class SubscriberList extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'subscriber_lists';

    protected $fillable = [
            'name',
            'description',
            'user_id',
            'total_subscribers',
            "is_global",
            'is_invalid',
            'detect_status',
            'detect_updated_at',
            'created_by',
            'updated_by'
    ];

    protected $hidden = [];

    public $timestamps = TRUE;

    /**
     * FN get attribute is_global
     * @param boolean $value
     */
    public function getIsGlobalAttribute($value) {
        return (boolean) $value;
    }

    public function getIsInvalidAttribute($value) {
        return (boolean) $value;
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
}
