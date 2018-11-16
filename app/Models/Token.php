<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Token extends Model {
    protected $table = 'tokens';

    protected $fillable = [
        'user_id',
        'token',
        'expired_at'
    ];

    public $timestamps = true;

    protected $appends = [
        'api_token'
    ];

    public function getApiTokenAttribute($value) {
        return 'Bearer ' . $this->token;
    }
}