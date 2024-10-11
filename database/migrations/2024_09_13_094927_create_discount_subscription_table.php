<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('discount_subscription', function (Blueprint $table) {
            $table->id();
            $table->unsignedBiginteger('subscription_id');
            $table->unsignedBiginteger('discount_id');


            $table->foreign('subscription_id')->references('id')
                ->on('subscriptions')->onDelete('cascade');
            $table->foreign('discount_id')->references('id')
                ->on('discounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_subscription');
    }
};


