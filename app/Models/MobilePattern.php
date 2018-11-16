<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class MobilePattern extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'mobile_pattern';

    protected $fillable = [
            'number_pattern',
            'country',
            'network',
            'created_by',
            'updated_by'
    ];

    protected $hidden = [];

    public $timestamps = true;
}
