<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->json('new_hint')->nullable();
        });

        // Convert existing hint data to JSON format
        DB::table('questions')->orderBy('id')->chunk(100, function ($questions) {
            foreach ($questions as $question) {
                $newHint = [];
                if (!empty($question->hint)) {
                    $newHint[] = [
                        'value' => $question->hint,
                        'is_latex' => (bool)$question->hint_is_latex,
                    ];
                }

                DB::table('questions')
                    ->where('id', $question->id)
                    ->update(['new_hint' => json_encode($newHint)]);
            }
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('hint');
            $table->dropColumn('hint_is_latex');
            $table->renameColumn('new_hint', 'hint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->text('old_hint')->nullable();
            $table->boolean('hint_is_latex')->default(false);
        });

        // Convert JSON hint back to string format
        DB::table('questions')->orderBy('id')->chunk(100, function ($questions) {
            foreach ($questions as $question) {
                $hints = json_decode($question->hint, true) ?? [];
                $firstHint = $hints[0] ?? null;

                DB::table('questions')
                    ->where('id', $question->id)
                    ->update([
                        'old_hint' => $firstHint ? $firstHint['value'] : null,
                        'hint_is_latex' => $firstHint ? $firstHint['is_latex'] : false,
                    ]);
            }
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('hint');
            $table->renameColumn('old_hint', 'hint');
        });
    }
};
