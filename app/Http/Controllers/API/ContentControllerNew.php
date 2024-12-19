<?php

namespace App\Http\Controllers\API;

use App\Models\Chapter;
use App\Models\Division;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContentControllerNew extends BaseController
{
    public function getUserContent(Request $request)
    {
        $user = $request->user();
        $userSubscriptions = $user->subscriptions->pluck('id');
        $division = $user->division;

        if (!$division) {
            return $this->sendError('No division found for user');
        }

        $result = $division->load([
            'materials' => function ($query) use ($userSubscriptions) {
                $query->orderBy('division_material.sort');
            },
            'materials.units' => function ($query) use ($userSubscriptions) {
                $query->whereHas('subscriptions', function ($q) use ($userSubscriptions) {
                    $q->whereIn('subscriptions.id', $userSubscriptions);
                })->with([
                            'chapters' => function ($q) use ($userSubscriptions) {
                                $q->whereHas('subscriptions', function ($sub) use ($userSubscriptions) {
                                    $sub->whereIn('subscriptions.id', $userSubscriptions);
                                })->with('questions');
                            }
                        ]);
            }
        ]);

        // Add progress information
        $result->materials->each(function ($material) use ($user) {
            $material->progress = $user->materialProgress($material);
            $material->units->each(function ($unit) use ($user) {
                $unit->progress = $user->unitProgress($unit);
                $unit->chapters->each(function ($chapter) use ($user) {
                    $chapter->progress = $user->chapterProgress($chapter);
                });
            });
        });

        return $this->sendResponse($result);
    }

    public function submitChapterAnswers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chapter_id' => 'required|exists:chapters,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = $request->user();
        $chapter = Chapter::with('questions')->findOrFail($request->chapter_id);
        $unit = $chapter->unit;
        $material = $unit->material;

        $answers = collect($request->answers)->map(function ($answer) use ($user, $chapter, $unit, $material) {
            $question = $chapter->questions->find($answer['question_id']);
            $isCorrect = $question->options['correct'] === $answer['answer'];

            return new UserAnswer([
                'user_id' => $user->id,
                'question_id' => $answer['question_id'],
                'chapter_id' => $chapter->id,
                'unit_id' => $unit->id,
                'material_id' => $material->id,
                'points_earned' => $isCorrect ? 1 : 0
            ]);
        });

        UserAnswer::insert($answers->toArray());

        return $this->sendResponse([
            'message' => 'Answers submitted successfully',
            'chapter_progress' => $user->chapterProgress($chapter)
        ]);
    }
}
