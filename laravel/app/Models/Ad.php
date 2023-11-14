<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;
    protected $fillable = [
        'brand_safety_postbid_partner',
        'card_id',
        'ad_name',
        'app_name',
        'secondary_status',
        'playable_url',
        'identity_id',
        'is_aco',
        'video_id',
        'ad_ref_pixel_id',
        'adgroup_name',
        'modify_time',
        'ad_texts',
        'ad_text',
        'landing_page_url',
        'ad_id',
        'fallback_type',
        'campaign_id',
        'landing_page_urls',
        'create_time',
        'image_ids',
        'music_id',
        'is_new_structure',
        'advertiser_id',
        'profile_image_url',
        'creative_type',
        'ad_format',
        'click_tracking_url',
        'identity_type',
        'impression_tracking_url',
        'adgroup_id',
        'avatar_icon_web_uri',
        'creative_authorized',
        'deeplink',
        'call_to_action_id',
        'campaign_name',
        'viewability_vast_url',
        'page_id',
        'viewability_postbid_partner',
        'brand_safety_vast_url',
        'tracking_pixel_id',
        'operation_status',
        'vast_moat_enabled',
        'display_name',
        'deeplink_type',
        'optimization_event',
    ];

    protected $casts = [
        'is_aco' => 'boolean',
        'is_new_structure' => 'boolean',
        'creative_authorized' => 'boolean',
        'image_ids' => 'json',
    ];
}
