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
        Schema::table('questions', function (Blueprint $table) {
            $table->boolean('question_is_latex')->default(false);
            $table->boolean('hint_is_latex')->default(false);
            $table->boolean('explanation_text_is_latex')->default(false);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn([
                'question_is_latex',
                'hint_is_latex',
                'explanation_text_is_latex'
            ]);
        });
    }
};
