<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TMSMSReport extends Model
{
    protected $table = 'tm_sms_reports';

    protected $fillable = [
        "return_message_id",
        "return_from",
        "return_to",
        "return_message",
        "return_mccmnc",
        "return_price",
        "return_currency",
        "return_sms_count",
        "return_error_code",
        "return_status"
    ];

    public $timestamps = true;
}
