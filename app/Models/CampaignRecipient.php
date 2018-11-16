<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class CampaignRecipient extends Model implements AuditableContract
{
    use Auditable;
    protected $table = 'campaign_recipients_u_template';

    protected $fillable = [
            'list_id',
            'user_id',
            'campaign_id',
            'created_by',
            'updated_by'
    ];

    protected $hidden = [];

    public $timestamps = TRUE;
    
}