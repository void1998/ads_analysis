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
        Schema::create('snapchat_adsquads', function (Blueprint $table) {

            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('status');
            $table->uuid('campaign_id');
            $table->string('type');
            $table->json('targeting');
            $table->string('targeting_reach_status');
            $table->string('placement');
            $table->string('billing_event');
            $table->boolean('auto_bid');
            $table->boolean('target_bid');
            $table->string('bid_strategy');
            $table->unsignedBigInteger('daily_budget_micro');
            $table->timestamp('start_time');
            $table->string('optimization_goal');
            $table->string('conversion_window');
            $table->uuid('pixel_id');
            $table->string('delivery_constraint');
            $table->string('pacing_type');
            $table->string('child_ad_type');
            $table->string('forced_view_setting');
            $table->string('creation_state');
            $table->json('delivery_status');
            $table->json('skadnetwork_properties');
            $table->bigInteger('delivery_properties_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snapchat_adsquads');
    }
};
