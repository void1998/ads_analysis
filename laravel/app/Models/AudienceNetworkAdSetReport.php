<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudienceNetworkAdSetReport extends Model
{
    protected $table = 'audience_network_ad_set_reports';

    protected $fillable = [
        'campaign_id',
        'adset_id',
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
