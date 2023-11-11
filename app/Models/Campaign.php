<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $table = 'campaigns'; // Specify the table name if it's different from the model name
    protected $fillable = [
        'is_search_campaign',
        'special_industries',
        'roas_bid',
        'campaign_name',
        'rf_campaign_type',
        'create_time',
        'is_smart_performance_campaign',
        'objective',
        'campaign_id',
        'budget',
        'secondary_status',
        'operation_status',
        'advertiser_id',
        'budget_mode',
        'app_promotion_type',
        'deep_bid_type',
        'objective_type',
        'modify_time',
        'campaign_type',
        'is_new_structure',
    ];
}
