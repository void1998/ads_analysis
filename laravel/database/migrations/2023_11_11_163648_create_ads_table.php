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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('brand_safety_postbid_partner')->default('UNSET');
            $table->string('card_id')->nullable();
            $table->string('ad_name');
            $table->string('app_name')->nullable();
            $table->string('secondary_status')->nullable();
            $table->string('playable_url')->nullable();
            $table->string('identity_id')->nullable();
            $table->boolean('is_aco')->nullable();
            $table->string('video_id')->nullable();
            $table->unsignedBigInteger('ad_ref_pixel_id')->nullable();
            $table->string('adgroup_name')->nullable();
            $table->timestamp('modify_time')->nullable();
            $table->text('ad_texts')->nullable();
            $table->text('ad_text');
            $table->string('landing_page_url')->nullable();
            $table->string('ad_id');
            $table->string('fallback_type')->default('UNSET');
            $table->string('campaign_id');
            $table->text('landing_page_urls')->nullable();
            $table->timestamp('create_time')->nullable();
            $table->json('image_ids')->nullable();
            $table->string('music_id')->nullable();
            $table->boolean('is_new_structure')->nullable();
            $table->string('advertiser_id');
            $table->string('profile_image_url')->nullable();
            $table->string('creative_type')->nullable();
            $table->string('ad_format')->nullable();
            $table->string('click_tracking_url')->nullable();
            $table->string('identity_type')->nullable();
            $table->string('impression_tracking_url')->nullable();
            $table->string('adgroup_id');
            $table->string('avatar_icon_web_uri')->nullable();
            $table->boolean('creative_authorized')->nullable();
            $table->string('deeplink')->nullable();
            $table->string('call_to_action_id')->nullable();
            $table->string('campaign_name')->nullable();
            $table->string('viewability_vast_url')->nullable();
            $table->string('page_id')->nullable();
            $table->string('viewability_postbid_partner')->default('UNSET');
            $table->string('brand_safety_vast_url')->nullable();
            $table->unsignedBigInteger('tracking_pixel_id')->nullable();
            $table->string('operation_status')->nullable();
            $table->boolean('vast_moat_enabled')->nullable();
            $table->string('display_name')->nullable();
            $table->string('deeplink_type')->nullable();
            $table->string('optimization_event')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
