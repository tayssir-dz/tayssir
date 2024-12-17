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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->enum('question_type', ['multiple_choices', 'fill_in_the_blanks', 'pick_the_intruder', 'true_or_false', 'match_with_arrows']);
            $table->json("options");
            $table->enum('scope', ["exercice", "lesson"]);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default("medium");
            $table->string('hint')->nullable();
            $table->text('explanation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
