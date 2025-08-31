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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('promo_code_id')->nullable()->constrained('promo_codes')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('promocode_percentage', 5, 2)->nullable();
            $table->decimal('promocode_amount', 10, 2)->nullable();
            $table->decimal('combined_discount_percentage', 5, 2)->nullable();
            $table->decimal('combined_discount_amount', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2);
            $table->decimal('promoter_margin_percentage', 5, 2)->nullable();
            $table->decimal('promoter_margin_amount', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
