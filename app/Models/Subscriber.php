<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Auth;
use Carbon\Carbon;

class Subscriber extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'subscribers_l_template';

    protected $fillable = [
            'phone',
            'country',
            'network',
            'ported',
            'mccmnc',
            'service_provider',
            'status',
            'unsubscription_date',
            'title',
            'first_name',
            'last_name',
            'detect_status',
            'detect_updated_at',
            'created_by',
            'updated_by',
            'campaign_id'
    ];

    public $hidden = ['phone'];

    public $appends = ['phone_encrypted'];

    public $timestamps = TRUE;

    public function getPhoneEncryptedAttribute() {
        $user = Auth::user();
        $phone = clone $this->phone;
        if(!empty($user) && $user->encrypted) {
            return substr($phone, 0, 3) . preg_replace("/[0-9]/i", "*", substr($phone, 3));
        }

        return $phone;
    }

    public function getUnsubscriptionDateAttribute($value) {
        $user = Auth::user();
        $timzone = !empty($user->time_zone) ? $user->time_zone : 'UTC';
        return Carbon::createFromFormat('Y-m-d H:i:s', $value, 'UTC')->setTimezone($timzone);
    }
}
