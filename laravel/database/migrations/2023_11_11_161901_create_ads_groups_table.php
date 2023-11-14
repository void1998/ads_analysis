<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ads_groups', function (Blueprint $table) {
            $table->id();
            $table->text('pacing')->nullable();
            $table->text('deep_bid_type')->nullable();
            $table->timestamp('modify_time')->nullable();
            $table->text('spending_power')->nullable();
            $table->decimal('budget', 8, 2)->nullable();
            $table->json('schedule_infos')->nullable();
            $table->json('keywords')->nullable();
            $table->text('advertiser_id');
            $table->json('purchase_intention_keyword_ids')->nullable();
            $table->text('creative_material_mode')->nullable();
            $table->text('app_download_url')->nullable();
            $table->json('actions')->nullable();
            $table->boolean('auto_targeting_enabled')->nullable();
            $table->text('promotion_website_type')->nullable();
            $table->json('languages')->nullable();
            $table->text('promotion_type')->nullable();
            $table->timestamp('schedule_start_time')->nullable();
            $table->json('frequency_schedule')->nullable();
            $table->boolean('skip_learning_phase')->nullable();
            $table->json('household_income')->nullable();
            $table->text('optimization_goal')->nullable();
            $table->text('pixel_id')->nullable();
            $table->decimal('conversion_bid_price', 8, 2)->nullable();
            $table->text('schedule_type')->nullable();
            $table->json('adgroup_app_profile_page_state')->nullable();
            $table->json('excluded_custom_actions')->nullable();
            $table->json('isp_ids')->nullable();
            $table->text('operation_status')->nullable();
            $table->timestamp('schedule_end_time')->nullable();
            $table->boolean('inventory_filter_enabled')->nullable();
            $table->decimal('deep_cpa_bid', 8, 2)->nullable();
            $table->json('category_exclusion_ids')->nullable();
            $table->text('feed_type')->nullable();
            $table->json('device_model_ids')->nullable();
            $table->text('rf_purchased_type')->nullable();
            $table->json('conversion_window')->nullable();
            $table->json('purchased_reach')->nullable();
            $table->json('operating_systems')->nullable();
            $table->text('brand_safety_partner')->nullable();
            $table->text('dayparting')->nullable();
            $table->text('adgroup_id')->nullable();
            $table->json('audience_ids')->nullable();
            $table->text('campaign_name')->nullable();
            $table->boolean('is_smart_performance_campaign')->nullable();
            $table->boolean('is_hfss')->nullable();
            $table->json('device_price_ranges')->nullable();
            $table->text('gender')->nullable();
            $table->json('rf_estimated_frequency')->nullable();
            $table->text('campaign_id');
            $table->json('delivery_mode')->nullable();
            $table->json('network_types')->nullable();
            $table->boolean('share_disabled')->nullable();
            $table->text('budget_mode')->nullable();
            $table->json('rf_estimated_cpr')->nullable();
            $table->json('placements')->nullable();
            $table->boolean('video_download_disabled')->nullable();
            $table->text('billing_event')->nullable();
            $table->json('statistic_type')->nullable();
            $table->json('frequency')->nullable();
            $table->text('category_id')->nullable();
            $table->decimal('scheduled_budget', 8, 2)->nullable();
            $table->json('zipcode_ids')->nullable();
            $table->boolean('search_result_enabled')->nullable();
            $table->text('adgroup_name')->nullable();
            $table->text('placement_type')->nullable();
            $table->json('contextual_tag_ids')->nullable();
            $table->json('next_day_retention')->nullable();
            $table->json('targeting_expansion')->nullable();
            $table->timestamp('create_time')->nullable();
            $table->text('secondary_status')->nullable();
            $table->json('purchased_impression')->nullable();
            $table->boolean('comment_disabled')->nullable();
            $table->text('app_id')->nullable();
            $table->json('interest_category_ids')->nullable();
            $table->json('age_groups')->nullable();
            $table->text('brand_safety_type')->nullable();
            $table->text('optimization_event')->nullable();
            $table->json('interest_keyword_ids')->nullable();
            $table->text('bid_display_mode')->nullable();
            $table->decimal('bid_price', 8, 2)->nullable();
            $table->json('excluded_audience_ids')->nullable();
            $table->text('secondary_optimization_event')->nullable();
            $table->boolean('is_new_structure')->nullable();
            $table->json('location_ids')->nullable();
            $table->json('included_custom_actions')->nullable();
            $table->text('bid_type')->nullable();
            $table->text('app_type')->nullable();
            $table->text('ios14_quota_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads_groups');
    }
};
