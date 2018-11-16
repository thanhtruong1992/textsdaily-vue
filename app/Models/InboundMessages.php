<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use function GuzzleHttp\json_decode;

class InboundMessages extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'inbound_messages_u_template';

    protected $fillable = [
            'from',
            'to',
            'message',
            'user_id',
            'keyworks',
            'message_id',
            'return_data',
            'created_by',
            'updated_by'
    ];

    protected $hidden = [];

    public $timestamps = true;

    public function getCreatedAtAttribute($value) {
        $user = Auth::user();
        $timezone = !empty($user) ? $user->time_zone : 'UTC';
        return Carbon::parse($value)->setTimezone($timezone)->toDateTimeString();
    }

    public function getUpdatedAtAttrbiute() {
        $user = Auth::user();
        $timezone = !empty($user) ? $user->time_zone : 'UTC';
        return Carbon::parse($value)->setTimezone($timezone)->toDateTimeString();
    }

    public function getReturnDataAttribute($value) {
        return json_decode($value);
    }
}
