<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignsReport extends Model
{
    use HasFactory;

    public mixed $conversion_rate;
    public mixed $total_purchase_value;
    public mixed $offline_shopping_events_value;
    public mixed $impressions;
    public mixed $clicks;
    public mixed $total_onsite_shopping_value;
    public mixed $spend;
    public mixed $stat_time_day;
    public mixed $campaign_id;
    protected $table = 'campaigns_reports';

    protected $fillable = [
        'campaign_id',
        'stat_time_day',
        'spend',
        'total_onsite_shopping_value',
        'offline_shopping_events_value',
        'impressions',
        'total_purchase_value',
        'clicks',
        'conversion_rate',
        'campaign_name'
    ];
}
