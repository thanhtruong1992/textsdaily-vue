<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class MCCMNC extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'mccmnc';

    protected $fillable = [
            'mccmnc',
            'country',
            'network',
            'created_by',
            'updated_by'
    ];

    protected $hidden = [];

    public $timestamps = true;
}
