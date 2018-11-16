<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class PriceConfiguration extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'price_configuration_u_template';

    protected $fillable = [
            'country',
            'network',
            'price',
            'disabled',
            'created_by',
            'updated_by'
    ];

    protected $hidden = [];

    public $timestamps = true;
}
