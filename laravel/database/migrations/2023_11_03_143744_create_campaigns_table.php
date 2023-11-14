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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_search_campaign');
            $table->json('special_industries');
            $table->decimal('roas_bid', 10, 2);
            $table->string('campaign_name');
            $table->string('rf_campaign_type');
            $table->timestamp('create_time')->nullable();
            $table->boolean('is_smart_performance_campaign')->nullable();
            $table->string('objective')->nullable();
            $table->string('campaign_id');
            $table->decimal('budget', 10, 2);
            $table->string('secondary_status')->nullable();
            $table->string('operation_status')->nullable();
            $table->string('advertiser_id');
            $table->string('budget_mode')->nullable();
            $table->string('app_promotion_type')->nullable();
            $table->string('deep_bid_type')->nullable();
            $table->string('objective_type')->nullable();
            $table->timestamp('modify_time')->nullable();
            $table->string('campaign_type');
            $table->boolean('is_new_structure');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
