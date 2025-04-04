<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SubmitChapterAnswersRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        // Verify the chapter exists and is part of user's subscribed content
        $chapterId = $this->input('chapter_id');
        $chapter = \App\Models\Chapter::find($chapterId);
        if (!$chapter) {
            return false;
        }
        $unit = $chapter->unit()->first();
        if (!$unit) {
            return false;
        }

        // Check if user has subscription for this unit's content
        return $unit
            ->subscriptions()
            ->whereIn('subscriptions.id', $user->subscriptions->pluck('id'))
            ->exists();

        // Note: we've removed any checks for existing answers to allow resubmissions
    }

    public function rules(): array
    {
        return [
            'chapter_id' => ['required', 'integer', 'exists:chapters,id'],
            'answers' => ['required', 'array'],
            'answers.*.question_id' => [
                'required',
                'integer',
                'exists:questions,id',
                // Validate that each question belongs to the chapter
                Rule::exists('chapter_question', 'question_id')->where('chapter_id', $this->input('chapter_id'))
            ],
            'answers.*.answered_correctly' => ['required', 'boolean'],
        ];
    }
}
