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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->integer('price');
            $table->boolean('is_on_discount')->default(false);
            $table->integer('discount_price')->default(0);
            $table->integer('discount_percentage')->default(0);
            $table->integer('display_price')->default(0);
            $table->enum('status', ['idle', 'expired', 'active', 'done', 'problem'])->default('idle');
            $table->string('subscription_type')->default('yearly_subscription');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
