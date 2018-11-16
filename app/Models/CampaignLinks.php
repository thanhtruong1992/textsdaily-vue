<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class CampaignLinks extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'campaign_links_u_template';

    protected $fillable = [
            'campaign_id',
            'url',
            'short_link',
            'total_clicks',
            'unique_clicks',
            'created_by',
            'updated_by'
    ];

    protected $hidden = [];

    public $timestamps= true;
}
