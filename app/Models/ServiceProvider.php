<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ServiceProvider extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'service_provider';

    protected $fillable = [
            'code',
            'name',
            'config_url',
            'config_username',
            'config_password',
            'config_access_key',
            'default',
            'status',
            'created_by',
            'updated_by'
    ];

    protected $hidden = [];

    public $timestamps = true;
}
