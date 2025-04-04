<?php

namespace App\Traits;

use App\Enums\ContentDirection;
use App\Enums\QuestionType;

trait InteractsWithContent
{
    public function getContentAttribute()
    {
        $subscriptionIds = $this->subscriptions->pluck('id');

        // Get all progress data efficiently in a single call
        $progressData = $this->getAllProgressData();

        // Load all data first
        $division = $this->division->load([
            'materials.units' => function ($query) use ($subscriptionIds) {
                $query->whereHas('subscriptions', function ($subQuery) use ($subscriptionIds) {
                    $subQuery->whereIn('subscriptions.id', $subscriptionIds);
                })
                    ->where('active', true)
                    ->with([
                        'chapters' => function ($q) use ($subscriptionIds) {
                            $q->whereHas('subscriptions', function ($sub) use ($subscriptionIds) {
                                $sub->whereIn('subscriptions.id', $subscriptionIds);
                            })
                                ->where('active', true)
                                ->with('questions');
                        }
                    ]);
            }
        ]);

        // Filter out inactive materials
        $materials = $division->materials->where('active', true);

        // Prepare the four lists
        $modules = [];
        $units = [];
        $chapters = [];
        $exercices = [];

        foreach ($materials as $material) {
            // Add to modules list with progress
            $modules[] = [
                'id' => $material->id,
                'name' => $material->name,
                'code' => $material->code,
                'direction' => $material->direction->value,
                'division_id' => $material->division_id,
                'color' => $material->color,
                'secondary_color' => $material->secondary_color,
                'description' => $material->description,
                'image' => $material->image,
                'image_grid' => $material->image_grid,
                'progress' => $progressData['materials'][$material->id] ?? 0,
                'points' => $progressData['points']['materials'][$material->id] ?? 0
            ];

            foreach ($material->units as $unit) {
                // Get chapter visibility for this unit
                $chapterVisibility = $this->getChapterVisibility($unit->id);

                // Add to units list with progress
                $units[] = [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'description' => $unit->description,
                    'image' => $unit->image,
                    'direction' => $unit->getEffectiveDirection()->value,
                    'material_id' => $material->id,
                    'progress' => $progressData['units'][$unit->id] ?? 0,
                    'points' => $progressData['points']['units'][$unit->id] ?? 0
                ];

                foreach ($unit->chapters as $chapter) {
                    // Get bonus points for this chapter
                    $bonusPoints = $progressData['points']['bonuses'][$chapter->id] ?? 0;

                    // Add to chapters list (without questions) but with progress
                    $chapters[] = [
                        'id' => $chapter->id,
                        'name' => $chapter->name,
                        'direction' => $chapter->getEffectiveDirection()->value,
                        'description' => $chapter->description,
                        "image" => $chapter->image,
                        'unit_id' => $unit->id,
                        'bonus_points' => $chapter->chapter_level ? $chapter->chapter_level->bonus : 0,
                        'earned_bonus' => $bonusPoints,
                        'progress' => $progressData['chapters'][$chapter->id] ?? 0,
                        'points' => $progressData['points']['chapters'][$chapter->id] ?? 0,
                        'visibility' => $chapterVisibility[$chapter->id] ?? \App\Enums\ChapterVisibility::LOCKED->value
                    ];

                    // Transform questions
                    $transformedQuestions = [];
                    if (isset($chapter->questions)) {
                        foreach ($chapter->questions as $question) {
                            $transformedQuestions[] = $this->transformQuestion($question);
                        }
                    }

                    // Add to exercices list (only questions)
                    if (!empty($transformedQuestions)) {
                        $exercices[] = [
                            'chapter_id' => $chapter->id,
                            'questions' => $transformedQuestions,
                        ];
                    }
                }
            }
        }

        // Return structured response with total points
        return [
            'modules' => $modules,
            'units' => $units,
            'chapters' => $chapters,
            'exercices' => $exercices,
            'total_points' => $progressData['points']['total'] ?? 0
        ];
    }

    /**
     * Transform a single question based on its type.
     * Returns the transformed question.
     */
    public function transformQuestion($question)
    {
        $baseStructure = [
            'id' => $question->id,
            'type' => $question->question_type,
            'chapter_id' => $question->chapter()->first()->id ?? null,
            'image' => $question->image,
            'difficulty' => "medium", // TODO: remove this cuz its not used anymore, its here so that the mobile client will not crash
            'points' => $question->points,
            'scope' => $question->scope,
            'hint' => $question->hint ?? [],
            'explanation_text' => [
                'value' => !empty($question->explanation_text) ? $question->explanation_text : null,
                'is_latex' => $question->explanation_text_is_latex ?? false,
            ],
            'explanationVideo' => $question->explanation_asset,
            'hintImage' => $question->hint_image,
            'question' => [
                'value' => !empty($question->question) ? $question->question : null,
                'is_latex' => $question->question_is_latex ?? false,
            ],
            'direction' => $question->getEffectiveDirection()->value,
        ];

        $qType = $question->question_type->value;

        if ($qType === QuestionType::MULTIPLE_CHOICES->value) {
            return array_merge($baseStructure, $this->transformMultipleChoices($question));
        } elseif ($qType === QuestionType::FILL_IN_THE_BLANKS->value) {
            return array_merge($baseStructure, $this->transformFillInTheBlanks($question));
        } elseif ($qType === QuestionType::PICK_THE_INTRUDER->value) {
            return array_merge($baseStructure, $this->transformPickTheIntruder($question));
        } elseif ($qType === QuestionType::TRUE_OR_FALSE->value) {
            return array_merge($baseStructure, $this->transformTrueOrFalse($question));
        } elseif ($qType === QuestionType::MATCH_WITH_ARROWS->value) {
            return array_merge($baseStructure, $this->transformMatchWithArrows($question));
        }

        return $baseStructure;
    }

    // Update transformer methods to work with Question model instance
    public function transformMultipleChoices($question)
    {
        $choices = $question->options['choices'] ?? [];
        $options = [];
        $correctOptions = [];

        foreach ($choices as $index => $choice) {
            $options[] = [
                'value' => !empty($choice['option']) ? $choice['option'] : null,
                'is_latex' => $choice['option_is_latex'] ?? false,
            ];

            if (!empty($choice['is_correct'])) {
                $correctOptions[] = $index;
            }
        }

        return [
            'options' => $options,
            'correctOptions' => $correctOptions,
        ];
    }

    public function transformPickTheIntruder($question)
    {
        $wordsArr = $question->options['words'] ?? [];
        $words = [];
        $correctAnomalies = [];

        foreach ($wordsArr as $index => $item) {
            $words[] = [
                'value' => !empty($item['word']) ? $item['word'] : null,
                'is_latex' => $item['word_is_latex'] ?? false,
            ];

            if (!empty($item['is_intruder'])) {
                $correctAnomalies[] = $index;
            }
        }

        return [
            'words' => $words,
            'correctAnomalies' => $correctAnomalies,
        ];
    }

    public function transformTrueOrFalse($question)
    {
        return [
            'correctAnswer' => $question->options['correct'] ?? false,
        ];
    }

    public function transformFillInTheBlanks($question)
    {
        $data = $question->options ?? [];
        $paragraph = $data['paragraph'] ?? '';
        $blanks = $data['blanks'] ?? [];
        $suggestions = $data['suggestions'] ?? [];

        $transformedBlanks = [];

        foreach ($blanks as $blank) {
            $transformedBlanks[] = [
                'correct_word' => $blank['correct_word'],
                'position' => $blank['position'],
            ];
        }

        return [
            'paragraph' => $paragraph,
            'blanks' => $transformedBlanks,
            'suggestions' => $suggestions,
        ];
    }

    public function transformMatchWithArrows($question)
    {
        $pairsData = $question->options['pairs'] ?? [];
        $pairs = [];

        foreach ($pairsData as $pair) {
            $pairs[] = [
                'first' => [
                    'value' => !empty($pair['first']) ? $pair['first'] : null,
                    'is_latex' => $pair['first_is_latex'] ?? false,
                ],
                'second' => [
                    'value' => !empty($pair['second']) ? $pair['second'] : null,
                    'is_latex' => $pair['second_is_latex'] ?? false
                ]
            ];
        }

        return [
            'pairs' => $pairs
        ];
    }
}
