<?php

namespace App\Traits;

use App\Enums\QuestionType;

trait InteractsWithContent
{
    public function getContentAttribute()
    {
        $subscriptionIds = $this->subscriptions->pluck('id');

        // Load all data first
        $division = $this->division->load([
            'materials.units' => function ($query) use ($subscriptionIds) {
                $query->whereHas('subscriptions', function ($subQuery) use ($subscriptionIds) {
                    $subQuery->whereIn('subscriptions.id', $subscriptionIds);
                })->with([
                    'chapters' => function ($q) use ($subscriptionIds) {
                        $q->whereHas('subscriptions', function ($sub) use ($subscriptionIds) {
                            $sub->whereIn('subscriptions.id', $subscriptionIds);
                        })->with('questions');
                    }
                ]);
            }
        ]);

        // Prepare the four lists
        $modules = [];
        $units = [];
        $chapters = [];
        $exercices = [];

        foreach ($division->materials as $material) {
            // Add to modules list
            $modules[] = [
                'id' => $material->id,
                'name' => $material->name,
                'code' => $material->code,
                'division_id' => $material->division_id,
                'color' => $material->color,
                'secondary_color' => $material->secondary_color,
                'description' => $material->description,
                'image' => $material->image,
            ];

            foreach ($material->units as $unit) {
                // Add to units list
                $units[] = [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'description' => $unit->description,
                    'material_id' => $material->id,
                ];

                foreach ($unit->chapters as $chapter) {
                    // Add to chapters list (without questions)
                    $chapters[] = [
                        'id' => $chapter->id,
                        'name' => $chapter->name,
                        'description' => $chapter->description,
                        'unit_id' => $unit->id,
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

        // Return structured response
        return [
            'modules' => $modules,
            'units' => $units,
            'chapters' => $chapters,
            'exercices' => $exercices,
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
            'chapter_id' => $question->chapter->first()->id ?? null,
            'image' => $question->image,
            'difficulty' => $question->difficulty,
            'points' => $question->difficulty->points(),
            'scope' => $question->scope,
            'hint' => is_string($question->hint) ? [$question->hint] : $question->hint,
            'explanation_text' => $question->explanation_text,
            'explanationVideo' => $question->explanation_asset,
            'hintImage' => $question->hint_image,
            'question' => $question->question,
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
        $optionTexts = [];
        $correctOptions = [];
        foreach ($choices as $index => $choice) {
            $optionTexts[] = $choice['option'];
            if (!empty($choice['is_correct'])) {
                $correctOptions[] = $index;
            }
        }
        return [
            'options' => $optionTexts,
            'correctOptions' => $correctOptions,
        ];
    }

    public function transformPickTheIntruder($question)
    {
        $wordsArr = $question->options['words'] ?? [];
        $words = [];
        $correctAnomalies = [];
        foreach ($wordsArr as $index => $item) {
            $words[] = $item['word'];
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
        $answers = $data['answers'] ?? [];
        
        $answerValues = [];
        $placeholders = [];
        
        foreach ($answers as $answer) {
            $answerValues[] = $answer['word'];
            $placeholders[] = $answer['placeholder'];
        }
        
        return [
            'paragraph' => $paragraph,
            'answers' => $answerValues,
            'placeholders' => $placeholders,
        ];
    }

    public function transformMatchWithArrows($question)
    {
        $pairs = $question->options['pairs'] ?? [];
        $firstColumn = [];
        $secondColumn = [];
        $correctMatches = [];
        
        foreach ($pairs as $index => $pair) {
            $firstColumn[] = $pair['first'];
            $secondColumn[] = $pair['second'];
            $correctMatches[] = [$index, $index]; // Matching index with index (could be shuffled if needed)
        }
        
        return [
            'firstColumn' => $firstColumn,
            'secondColumn' => $secondColumn,
            'correctMatches' => $correctMatches,
        ];
    }
}
