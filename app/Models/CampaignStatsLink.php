<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class CampaignStatsLink extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'campaign_stats_link_u_template';

    protected $fillable = [
            'user_id',
            'campaign_id',
            'link_id',
            'url',
            'location',
            'ip',
            'status',
            'created_by',
            'updated_by'
    ];

    protected $hidden = [];

    public $timestamps= true;
}
