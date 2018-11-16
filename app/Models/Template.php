<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Template extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'template_u_template';

    protected $fillable = [
            'name',
            'language',
            'message',
            'created_by',
            'updated_by',
    ];

    protected $hidden = [];

    public $timestamps= true;
}
