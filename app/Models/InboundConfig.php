<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class InboundConfig extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'inbound_config';

    protected $fillable = [
            'number',
            'expiry_date',
            'group2_user_id',
            'group3_user_id',
            'keyworks',
            'created_by',
            'updated_by'
    ];

    protected $hidden = [];

    public $timestamps = true;
}
