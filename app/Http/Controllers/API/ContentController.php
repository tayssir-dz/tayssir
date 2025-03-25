<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\SubmitChapterAnswersRequest;
use App\Models\Division;
use App\Models\Chapter;
use App\Models\Question;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContentController extends BaseController
{
    public function getUserContent(Request $request)
    {
        $user = $request->user();
        if (!$user->division) {
            return $this->sendError(__("response.an_error_occurred"));
        }
        return $this->sendResponse($user->content);
    }

    public function SubmitChapterAnswers(SubmitChapterAnswersRequest $request)
    {
        $user = $request->user();
        $chapterId = $request->chapter_id;
        $answers = $request->answers;

        try {
            // Use a transaction to ensure all database operations complete or none do
            return DB::transaction(function () use ($user, $chapterId, $answers) {
                // Get chapter with related data in a single query
                $chapter = Chapter::with(['chapter_units.material_units', 'questions' => function ($query) use ($answers) {
                    // Only fetch questions that are being answered
                    $questionIds = array_column($answers, 'question_id');
                    $query->whereIn('questions.id', $questionIds);
                }])->findOrFail($chapterId);

                $unit = $chapter->chapter_units()->first();
                $material = $unit->material_units()->first();

                // Create a mapping of questions for quick lookup
                $questionsMap = $chapter->questions->keyBy('id');

                // Prepare bulk insert data
                $userAnswersData = [];
                $now = now();

                foreach ($answers as $answer) {
                    $questionId = $answer['question_id'];

                    // Get question from the preloaded map
                    if (!isset($questionsMap[$questionId])) {
                        continue; // Skip if question not found or not related to chapter
                    }

                    $question = $questionsMap[$questionId];
                    $points = $answer['answered_correctly'] ? $question->points : 0;

                    $userAnswersData[] = [
                        'user_id' => $user->id,
                        'question_id' => $questionId,
                        'chapter_id' => $chapterId,
                        'unit_id' => $unit->id,
                        'material_id' => $material->id,
                        'points_earned' => $points,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Bulk insert all answers at once
                UserAnswer::insert($userAnswersData);

                // Calculate updated progress and points
                $materialProgress = $user->materialProgress($material);
                $unitProgress = $user->unitProgress($unit);
                $chapterProgress = $user->chapterProgress($chapter);

                $materialPoints = $user->materialPoints($material);
                $unitPoints = $user->unitPoints($unit);
                $chapterPoints = $user->chapterPoints($chapter);

                return $this->sendResponse([
                    'message' => __('response.answers_submitted_successfully'),
                    'total_answers' => count($userAnswersData),
                    'progress' => [
                        'material' => [
                            'id' => $material->id,
                            'progress' => $materialProgress,
                            'points' => $materialPoints
                        ],
                        'unit' => [
                            'id' => $unit->id,
                            'progress' => $unitProgress,
                            'points' => $unitPoints
                        ],
                        'chapter' => [
                            'id' => $chapter->id,
                            'progress' => $chapterProgress,
                            'points' => $chapterPoints
                        ]
                    ]
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Error submitting answers: ' . $e->getMessage());
            return $this->sendError(__('response.an_error_occurred'));
        }
    }
}
