<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Transaction extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'billing_transaction';

    protected $fillable = [
            'user_id',
            'description',
            'type',
            'credits',
            'currency',
            'created_by',
            'updated_by',
    ];

    protected $hidden = [];

    public $timestamps= true;
}
