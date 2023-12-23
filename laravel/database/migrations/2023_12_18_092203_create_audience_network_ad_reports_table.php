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
        Schema::create('audience_network_ad_reports', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_id');
            $table->string('adset_id');
            $table->string('ad_id');
            $table->string('campaign_name');
            $table->integer('impressions');
            $table->integer('clicks');
            $table->decimal('spend', 10, 2);
            $table->date('date_start');
            $table->date('date_stop');
            $table->string('publisher_platform');

            // JSON column for storing the purchase_roas array
            $table->json('purchase_roas');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audience_network_ad_reports');
    }
};
