<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ReportListSummary extends Model implements AuditableContract{
    use Auditable;
    protected $table = "report_list_summary_u_template";

    protected $fillable = [
        'list_id',
        'canpaign_id',
        'country',
        'network',
        'service_provider',
        'currency',
        'pending',
        'sent',
        'delivered',
        'failed',
        'expenses',
        'agency_expenses',
        'client_expenses'
    ];

    protected $hidden = [];

    public $timestamps = true;
}