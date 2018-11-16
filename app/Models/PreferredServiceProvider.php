<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class PreferredServiceProvider extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'preferred_service_provider';

    protected $fillable = [
            'country',
            'network',
            'service_provider',
            'created_by',
            'updated_by'
    ];

    protected $hidden = [];

    public $timestamps = true;
}
