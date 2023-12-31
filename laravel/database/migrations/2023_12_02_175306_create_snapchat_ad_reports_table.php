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
        Schema::create('snapchat_ad_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('ad_id');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->double('impressions');
            $table->double('swipes');
            $table->double('spend');
            $table->double('conversion_purchases');
            $table->double('conversion_purchases_value');
            $table->double('conversion_add_cart');
            $table->double('conversion_add_cart_value');
            $table->double('conversion_page_views');
            $table->double('conversion_page_views_value');
            $table->double('conversion_ad_view');
            $table->double('conversion_ad_view_value');
            $table->double('conversion_rate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snapchat_ad_reports');
    }
};
