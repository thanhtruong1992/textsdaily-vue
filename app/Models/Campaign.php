<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Campaign extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'campaign_u_template';

    protected $fillable = [
            'user_id',
            'name',
            'status',
            'is_api',
            'sender',
            'language',
            'message',
            'valid_period',
            'estimated_cost',
            'schedule_type',
            'send_time',
            'send_timezone',
            'send_process_started_on',
            'send_process_finished_on',
            'total_recipients',
            'total_sent',
            'total_failed',
            'total_clicks',
            'unique_clicks',
            'benchmark_per_second',
            'notification_emails',
            'tracking_delivery_report',
            'tracking_delivery_report_update_at',
            'backend_statistic_report',
            'backend_statistic_report_updated_at',
            'created_by',
            'updated_by'
    ];

    protected $hidden = [];

    public $timestamps= true;
}
