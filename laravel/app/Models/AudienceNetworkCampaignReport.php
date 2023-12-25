<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudienceNetworkCampaignReport extends Model
{
    protected $table = 'audience_network_campaign_reports';

    protected $fillable = [
        'campaign_id',
        'campaign_name',
        'impressions',
        'clicks',
        'spend',
        'date_start',
        'date_stop',
        'publisher_platform',
        'purchase_roas',
    ];
}
