<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignPaused extends Model
{
    protected $table = "campaign_paused";
    protected $fillable = [
        'user_id',
        'campaign_id',
        'queue_id',
        'count',
        'tracking_status',
        'tracking_updated_at'
    ];
    protected $hidden = [];
    public $timestamps = true;
}
