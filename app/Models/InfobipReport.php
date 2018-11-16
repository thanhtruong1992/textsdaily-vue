<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfobipReport extends Model
{
    protected $table = 'infobip_reports';

    protected $fillable = [
        "return_message_id",
        "return_json"
    ];

    public $timestamps = true;
    protected $primaryKey = 'return_message_id';
}
