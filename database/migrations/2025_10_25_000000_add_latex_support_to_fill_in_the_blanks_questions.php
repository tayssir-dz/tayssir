<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Updates all existing fill_in_the_blanks questions to include LaTeX toggle support.
     * Adds paragraph_is_latex to each question's options.
     * Adds is_latex toggle to each blank's correct_word.
     * Converts suggestions from TagsInput (simple array) to Repeater format with is_latex toggles.
     *
     * All LaTeX toggles are set to false for existing data (considered as non-LaTeX).
     */
    public function up(): void
    {
        DB::table('questions')
            ->where('question_type', 'fill_in_the_blanks')
            ->whereNotNull('options')
            ->orderBy('id')
            ->chunk(100, function ($questions) {
                foreach ($questions as $question) {
                    $options = json_decode($question->options, true);

                    if (is_array($options)) {
                        $updatedOptions = $this->updateOptionsWithLatex($options);

                        DB::table('questions')
                            ->where('id', $question->id)
                            ->update(['options' => json_encode($updatedOptions)]);
                    }
                }
            });
    }

    /**
     * Update options structure to include LaTeX toggles.
     * Converts old suggestions array format to new repeater format with is_latex.
     * Adds latex toggles to blanks correct_words.
     */
    private function updateOptionsWithLatex(array $options): array
    {
        // Ensure paragraph_is_latex exists
        if (!isset($options['paragraph_is_latex'])) {
            $options['paragraph_is_latex'] = false;
        }

        // Update blanks to include correct_word_is_latex
        if (isset($options['blanks']) && is_array($options['blanks'])) {
            $options['blanks'] = array_map(function ($blank) {
                if (!isset($blank['correct_word_is_latex'])) {
                    $blank['correct_word_is_latex'] = false;
                }
                return $blank;
            }, $options['blanks']);
        }

        // Convert suggestions from simple array to repeater format with is_latex
        if (isset($options['suggestions'])) {
            if (is_array($options['suggestions'])) {
                // Check if it's already in the new format (array of objects with 'value' and 'is_latex')
                if (!empty($options['suggestions']) && isset($options['suggestions'][0]['value'])) {
                    // Already in new format
                    $options['suggestions'] = array_map(function ($suggestion) {
                        if (!isset($suggestion['is_latex'])) {
                            $suggestion['is_latex'] = false;
                        }
                        return $suggestion;
                    }, $options['suggestions']);
                } else {
                    // Old format - convert simple array of strings to new format
                    $options['suggestions'] = array_map(function ($suggestion) {
                        if (is_string($suggestion)) {
                            return [
                                'value' => $suggestion,
                                'is_latex' => false,
                            ];
                        }
                        // If already an array but missing is_latex
                        if (is_array($suggestion) && isset($suggestion['value'])) {
                            if (!isset($suggestion['is_latex'])) {
                                $suggestion['is_latex'] = false;
                            }
                            return $suggestion;
                        }
                        return $suggestion;
                    }, $options['suggestions']);
                }
            }
        }

        return $options;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove LaTeX toggles from all fill_in_the_blanks questions
        DB::table('questions')
            ->where('question_type', 'fill_in_the_blanks')
            ->whereNotNull('options')
            ->orderBy('id')
            ->chunk(100, function ($questions) {
                foreach ($questions as $question) {
                    $options = json_decode($question->options, true);

                    if (is_array($options)) {
                        // Remove paragraph_is_latex
                        unset($options['paragraph_is_latex']);

                        // Remove correct_word_is_latex from blanks
                        if (isset($options['blanks']) && is_array($options['blanks'])) {
                            $options['blanks'] = array_map(function ($blank) {
                                unset($blank['correct_word_is_latex']);
                                return $blank;
                            }, $options['blanks']);
                        }

                        // Convert suggestions back to simple array format
                        if (isset($options['suggestions']) && is_array($options['suggestions'])) {
                            $options['suggestions'] = array_map(function ($suggestion) {
                                if (is_array($suggestion) && isset($suggestion['value'])) {
                                    return $suggestion['value'];
                                }
                                return $suggestion;
                            }, $options['suggestions']);
                        }

                        DB::table('questions')
                            ->where('id', $question->id)
                            ->update(['options' => json_encode($options)]);
                    }
                }
            });
    }
};
