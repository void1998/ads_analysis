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
        Schema::create('ads_reports', function (Blueprint $table) {
            $table->id();
            $table->string('ad_id');
            $table->timestamp('stat_time_day');
            $table->decimal('spend', 8, 2);
            $table->decimal('total_onsite_shopping_value', 8, 2);
            $table->decimal('offline_shopping_events_value', 8, 2);
            $table->integer('impressions');
            $table->decimal('total_purchase_value', 8, 2);
            $table->integer('clicks');
            $table->decimal('conversion_rate', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads_reports');
    }
};
