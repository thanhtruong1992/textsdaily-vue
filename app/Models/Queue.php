<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Queue extends Model implements AuditableContract {
    use Auditable;
    protected $table = 'queue_u_template_c_template';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'queue_id',
            'sender',
            'list_id',
            'subscriber_id',
            'phone',
            'country',
            'network',
            'ported',
            'service_provider',
            'message',
            'message_count',
            'sum_price_agency',
            'sum_price_client',
            'status',
            'return_mccmnc',
            'return_price',
            'return_currency',
            'return_status',
            'return_status_message',
            'return_sms_count',
            'return_bulk_id',
            'return_message_id',
            'return_json',
            'report_updated_at',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public $timestamps = false;
}
