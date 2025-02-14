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

        // Prepare the three lists
        $modules = [];
        $units = [];
        $exercices = [];

        foreach ($division->materials as $material) {
            // Add to modules list
            $modules[] = [
                'id' => $material->id,
                'name' => $material->name,
                'code' => $material->code,
                'division_id' => $material->division_id,
            ];

            foreach ($material->units as $unit) {
                // Add to units list
                $units[] = [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'material_id' => $material->id,
                ];

                foreach ($unit->chapters as $chapter) {
                    // Transform questions
                    $transformedQuestions = [];
                    if (isset($chapter->questions)) {
                        foreach ($chapter->questions as $question) {
                            $qType = $question->question_type->value;
                            if (!isset($transformedQuestions[$qType])) {
                                $transformedQuestions[$qType] = $this->transformQuestion($question);
                            }
                        }
                    }

                    // Add to exercices list
                    $exercices[] = [
                        'id' => $chapter->id,
                        'name' => $chapter->name,
                        'unit_id' => $unit->id,
                        'questions' => array_values($transformedQuestions),
                    ];
                }
            }
        }

        // Return structured response
        return [
            'modules' => $modules,
            'units' => $units,
            'exercices' => $exercices,
        ];
    }

    /**
     * Transform a single question based on its type.
     * Returns the transformed question.
     */
    public function transformQuestion($question)
    {
        $qType = $question->question_type->value;
        if ($qType === QuestionType::MULTIPLE_CHOICES->value) {
            return $this->transformMultipleChoices($question);
        } elseif ($qType === QuestionType::FILL_IN_THE_BLANKS->value) {
            return $this->transformFillInTheBlanks($question);
        } elseif ($qType === QuestionType::PICK_THE_INTRUDER->value) {
            return $this->transformPickTheIntruder($question);
        } elseif ($qType === QuestionType::TRUE_OR_FALSE->value) {
            return $this->transformTrueOrFalse($question);
        } elseif ($qType === QuestionType::MATCH_WITH_ARROWS->value) {
            return $this->transformMatchWithArrows($question);
        }
        return $question;
    }

    // Transformer for multiple_choices type:
    // Desired data: Return question data with options as an array of strings and a correctOptions array with indices.
    public function transformMultipleChoices($question)
    {
        $data = $question->toArray();
        $choices = $data['options']['choices'] ?? [];
        $optionTexts = [];
        $correctOptions = [];
        foreach ($choices as $index => $choice) {
            $optionTexts[] = $choice['option'];
            if (!empty($choice['is_correct'])) {
                $correctOptions[] = $index;
            }
        }
        $data['options'] = $optionTexts;
        $data['correctOptions'] = $correctOptions;
        return $data;
    }

    // Transformer for fill_in_the_blanks type:
    // Desired data: Return question data as is for now; transformation can be added later.
    public function transformFillInTheBlanks($question)
    {
        return $question;
    }

    // Transformer for pick_the_intruder type:
    // Desired data: Return question data with a 'words' array of strings and a 'correctAnomalies' array of indices.
    public function transformPickTheIntruder($question)
    {
        $data = $question->toArray();
        $wordsArr = $data['options']['words'] ?? [];
        $words = [];
        $correctAnomalies = [];
        foreach ($wordsArr as $index => $item) {
            $words[] = $item['word'];
            if (!empty($item['is_intruder'])) {
                $correctAnomalies[] = $index;
            }
        }
        unset($data['options']);
        $data['words'] = $words;
        $data['correctAnomalies'] = $correctAnomalies;
        return $data;
    }

    // Transformer for true_or_false type:
    // Desired data: Return question data with a 'correctAnswer' boolean and without 'options'.
    public function transformTrueOrFalse($question)
    {
        $data = $question->toArray();
        $options = $data['options'] ?? [];
        $data['correctAnswer'] = $options['correct'] ?? false;
        unset($data['options']);
        return $data;
    }

    // Transformer for match_with_arrows type:
    // Desired data: Return question data without changes for now.
    public function transformMatchWithArrows($question)
    {
        return $question;
    }
}
