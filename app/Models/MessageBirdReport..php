<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageBirdReport extends Model
{
    protected $table = 'messagebird_reports';

    protected $fillable = [
        "message_id",
        "return_json"
    ];

    public $timestamps = true;
}
