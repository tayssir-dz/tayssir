<?php

namespace App\Http\Controllers\API;

use App\Enums\ChapterVisibility;
use App\Http\Requests\API\SubmitChapterAnswersRequest;
use App\Models\Division;
use App\Models\Chapter;
use App\Models\Question;
use App\Models\UserAnswer;
use App\Models\UserChapterBonus;
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
                $chapter = Chapter::with(['unit', 'questions' => function ($query) use ($answers) {
                    // Only fetch questions that are being answered
                    $questionIds = array_column($answers, 'question_id');
                    $query->whereIn('questions.id', $questionIds);
                }, 'chapter_level'])->findOrFail($chapterId);

                $unit = $chapter->unit()->first();
                $material = $unit->material()->first();

                // Create a mapping of questions for quick lookup
                $questionsMap = $chapter->questions->keyBy('id');

                // Get existing answers for this user and chapter
                $existingAnswers = UserAnswer::where('user_id', $user->id)
                    ->where('chapter_id', $chapterId)
                    ->get()
                    ->keyBy('question_id');

                // Track correct answers to calculate bonus eligibility
                $totalSubmittedQuestions = count($answers);
                $correctAnswers = 0;
                $earnedPointsInThisSubmission = 0;

                // Prepare bulk insert/update data
                $userAnswersData = [];
                $now = now();

                foreach ($answers as $answer) {
                    $questionId = $answer['question_id'];

                    // Get question from the preloaded map
                    if (!isset($questionsMap[$questionId])) {
                        continue; // Skip if question not found or not related to chapter
                    }

                    $question = $questionsMap[$questionId];
                    // Only count if the answer is correct
                    $isCorrect = $answer['answered_correctly'];
                    if ($isCorrect) {
                        $correctAnswers++;
                    }

                    $points = $isCorrect ? $question->points : 0;

                    // If an existing answer has more points, keep it
                    if (isset($existingAnswers[$questionId]) && $existingAnswers[$questionId]->points_earned > $points) {
                        continue;
                    }

                    // Add to earned points if this is a new correct answer or an improvement
                    if ($isCorrect) {
                        $previousPoints = isset($existingAnswers[$questionId]) ? $existingAnswers[$questionId]->points_earned : 0;
                        $earnedPointsInThisSubmission += max(0, $points - $previousPoints);
                    }

                    // Delete existing answer if present
                    if (isset($existingAnswers[$questionId])) {
                        $existingAnswers[$questionId]->delete();
                    }

                    // Only add to user answers if correct
                    if ($isCorrect) {
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
                }

                // Insert new answers if any
                if (!empty($userAnswersData)) {
                    UserAnswer::insert($userAnswersData);
                }

                // Check for bonus eligibility (≥50% correct answers)
                $correctPercentage = ($correctAnswers / $totalSubmittedQuestions) * 100;
                $earnedBonusPointsInThisSubmission = 0;

                // Only award bonus if user submitted all questions in the chapter
                $allQuestionsSubmitted = $totalSubmittedQuestions === $chapter->questions()->count();

                if ($allQuestionsSubmitted && $correctPercentage >= 50 && $chapter->chapter_level) {
                    $bonusPoints = $chapter->chapter_level->bonus;

                    // Check if user already has a bonus for this chapter
                    $existingBonus = UserChapterBonus::where('user_id', $user->id)
                        ->where('chapter_id', $chapterId)
                        ->first();

                    if (!$existingBonus && $bonusPoints > 0) {
                        // Create new bonus record
                        UserChapterBonus::create([
                            'user_id' => $user->id,
                            'chapter_id' => $chapterId,
                            'bonus_points' => $bonusPoints
                        ]);

                        // Track the newly earned bonus points
                        $earnedBonusPointsInThisSubmission = $bonusPoints;
                    }
                }

                // Calculate updated progress and points
                $materialProgress = $user->materialProgress($material);
                $unitProgress = $user->unitProgress($unit);
                $chapterProgress = $user->chapterProgress($chapter);

                $materialPoints = $user->materialPoints($material);
                $unitPoints = $user->unitPoints($unit);
                $chapterPoints = $user->chapterPoints($chapter);

                // Get bonus points for this chapter
                $chapterBonusPoints = UserChapterBonus::where('user_id', $user->id)
                    ->where('chapter_id', $chapterId)
                    ->value('bonus_points') ?? 0;

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
                            'points' => $chapterPoints,
                            'bonus_points' => $chapterBonusPoints,
                            'earned_points' => $earnedPointsInThisSubmission,
                            'earned_bonus_points' => $earnedBonusPointsInThisSubmission
                        ]
                    ]
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Error submitting answers: ' . $e->getMessage());
            return $this->sendError(__('response.an_error_occurred'), [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'error' => $e
            ]);
        }
    }
}
