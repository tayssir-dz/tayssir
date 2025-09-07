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
        Schema::create('user_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('chapter_id')->constrained()->onDelete('cascade');
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->integer('points_earned')->default(0);

            // Use shorter, explicit names to avoid MySQL's 64-char identifier limit
            $table->unique(
                ['user_id', 'question_id', 'chapter_id', 'unit_id', 'material_id'],
                'user_answers_user_question_chapter_unit_material_unique'
            );
            $table->index(
                ['user_id', 'question_id', 'chapter_id', 'unit_id', 'material_id'],
                'user_answers_user_question_chapter_unit_material_index'
            );
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_answers');
    }
};
