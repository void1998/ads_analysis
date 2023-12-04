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
            $table->string('status')->nullable();
            $table->uuid('campaign_id');
            $table->string('type')->nullable();
            $table->json('targeting')->nullable();
            $table->string('targeting_reach_status')->nullable();
            $table->string('placement')->nullable();
            $table->string('billing_event')->nullable();
            $table->boolean('auto_bid')->nullable();
            $table->boolean('target_bid')->nullable();
            $table->string('bid_strategy')->nullable();
            $table->unsignedBigInteger('daily_budget_micro')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->string('optimization_goal')->nullable();
            $table->string('conversion_window')->nullable();
            $table->uuid('pixel_id')->nullable();
            $table->string('delivery_constraint')->nullable();
            $table->string('pacing_type')->nullable();
            $table->string('child_ad_type')->nullable();
            $table->string('forced_view_setting')->nullable();
            $table->string('creation_state')->nullable();
            $table->json('delivery_status')->nullable();
            $table->json('skadnetwork_properties')->nullable();
            $table->bigInteger('delivery_properties_version')->nullable();
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
