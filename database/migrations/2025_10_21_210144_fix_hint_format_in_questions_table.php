<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ensures all hints are stored in the correct JSON format:
     * [{"value": "hint text", "is_latex": false}, ...]
     *
     * This fixes the issue where some hints were stored as plain strings
     * instead of the standardized object format.
     */
    public function up(): void
    {
        // Get all questions with hints that need fixing
        DB::table('questions')
            ->whereNotNull('hint')
            ->where('hint', '!=', '[]')
            ->orderBy('id')
            ->chunk(100, function ($questions) {
                foreach ($questions as $question) {
                    $hint = json_decode($question->hint, true);
                    $normalizedHint = $this->normalizeHint($hint);

                    DB::table('questions')
                        ->where('id', $question->id)
                        ->update(['hint' => json_encode($normalizedHint)]);
                }
            });
    }

    /**
     * Normalize hint data to ensure consistent array of objects format.
     * Handles cases where hints might be stored as plain strings or already as proper objects.
     */
    private function normalizeHint($hint)
    {
        // If no hint or empty array, return empty array
        if (empty($hint)) {
            return [];
        }

        // If it's already an array
        if (is_array($hint)) {
            // If it's an empty array, return it
            if (count($hint) === 0) {
                return [];
            }

            // If first element is already an object with 'value' key, return as-is
            if (isset($hint[0]) && is_array($hint[0]) && isset($hint[0]['value'])) {
                return $hint;
            }

            // If array contains plain strings, convert each to proper object
            $normalized = [];
            foreach ($hint as $item) {
                if (is_string($item) && !empty($item)) {
                    $normalized[] = [
                        'value' => $item,
                        'is_latex' => false,
                    ];
                } elseif (is_array($item) && isset($item['value'])) {
                    $normalized[] = $item;
                }
            }
            return $normalized;
        }

        // If it's a plain string, wrap it in the proper format
        if (is_string($hint) && !empty($hint)) {
            return [
                [
                    'value' => $hint,
                    'is_latex' => false,
                ],
            ];
        }

        return [];
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration needed - we're only normalizing existing data
        // to a better format, no schema changes
    }
};
