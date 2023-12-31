<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessengerAdSetReport extends Model
{
    protected $table = 'messenger_ad_set_reports';

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
