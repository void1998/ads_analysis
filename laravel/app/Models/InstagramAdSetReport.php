<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstagramAdSetReport extends Model
{
    protected $table = 'instagram_ad_set_reports';

    protected $fillable = [
        'campaign_id',
        'adset_id',
        'adset_name',
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
