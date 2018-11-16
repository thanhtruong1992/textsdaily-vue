<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class CustomField extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'custom_fields';

    protected $fillable = [
            'user_id',
            'list_id',
            'field_name',
            'field_type',
            'field_default_value',
            'required',
            'unique',
            'global',
            'created_by',
            'updated_by'
    ];

    protected $hidden = [];

    public $timestamps= true;
}
