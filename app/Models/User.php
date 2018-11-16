<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;

class User extends Model implements Authenticatable, AuditableContract {
    use Auditable;
    use AuthenticableTrait;
    protected $blockSize = 256;
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'name',
            'username',
            'email',
            'password',
            'avatar',
            'host_name',
            'agency_id',
            'parent_id',
            'country',
            'language',
            'time_zone',
            'status',
            'type',
            'reader_id',
            'encrypted',
            'blocked',
            'billing_type',
            'credits',
            'credits_usage',
            'credits_limit',
            'currency',
            'sender',
            'default_price_sms',
            'is_tracking_link',
            'is_api',
            'created_by',
            'updated_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
            'password',
            'remember_token',
            'username',
            'email'
    ];
    private $authService;
    public $timestamps = true;

    public $appends = ["url_avatar"];

    private function checkExistedSessionOtherRole() {
        if(session('other_role')) {
            return count(session('other_role')) > 0;
        }
        return false;
    }

    public function getUrlAvatarAttribute() {
        if(!!$this->isGroup2() && $this->avatar != "") {
            return url("/client/logo");
        }elseif(!!$this->isGroup3()) {
            $userParent = $this->parentUser()->first();
            return $userParent->avatar != "" ? url("/admin/logo") : null;
        }
        return null;
    }

    public function getCreditsAttribute($value) {
        return floatval($value);
    }

    public function getCreditsUsageAttribute($value) {
        return floatval($value);
    }

    public function getCreditsLimitAttribute($value) {
        return floatval($value);
    }

    public function getHostNameAttribute($value) {
        return str_replace('www.', '', parse_url($value, PHP_URL_HOST));
    }

    public function isGroup4() {
        if ($this->checkExistedSessionOtherRole() && $this->type == "GROUP4") {
            return true;
        }

        if ($this->type == "GROUP4") {
            return true;
        }

        return false;
    }
    public function isGroup3() {
        if ($this->checkExistedSessionOtherRole() && $this->type == "GROUP3") {
            return true;
        }
        if ($this->type == "GROUP3" && $this->blocked == 0 && $this->status == 'ENABLED') {
            return true;
        }

        return false;
    }
    public function isGroup2() {
        if ($this->checkExistedSessionOtherRole() && $this->type == "GROUP2") {
            return true;
        }
        if ($this->type == "GROUP2" && $this->blocked == 0 && $this->status == 'ENABLED') {
            return true;
        }

        return false;
    }
    public function isGroup1() {
        if ($this->type == "GROUP1" && $this->blocked == 0 && $this->status == 'ENABLED') {
            return true;
        }
        return false;
    }

    public function getAccountTypeOption() {
        switch ($this->billing_type) {
            case "ONE_TIME":
                return ['ONE_TIME'];
            case "MONTHLY":
                return ['ONE_TIME', 'MONTHLY'];
            default:
                return ['ONE_TIME', 'MONTHLY', 'UNLIMITED'];
        }
    }

    public function getEncryptedAttribute($value) {
        return (boolean) $value;
    }

    public function getCredits() {
        return $this->credits;
    }

    public function getUsage() {
        return $this->credits_usage;
    }

    public function getDefaultPrice() {
        return $this->default_price_sms;
    }

    public function getBalance() {
        return $this->credits - $this->credits_usage;
    }

    public function getCreditsLimit() {
        return $this->credits_limit;
    }

    public function reportCenter() {
        return $this->hasMany("App\Models\ReportCenter", "user_id", "id");
    }

    public function parentUser() {
        return $this->belongsTo("App\Models\User", "parent_id", "id");
    }

    public function canSendCampaign() {
        if ( $this->billing_type == 'UNLIMITED' || $this->getBalance() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isApi() {
        if ($this->is_api == 1 && $this->blocked == 0 && $this->status == 'ENABLED') {
            return true;
        }

        return false;
    }

    public function parent() {
        return $this->hasOne('App\Models\User', 'id', 'parent_id');
    }
}
