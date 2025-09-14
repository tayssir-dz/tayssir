<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\API\BaseController;
use App\Models\QuestionReport;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('Moderation & Reports APIs', weight: 6)]
class ReportControllerV2 extends BaseController
{
    /**
     * Report a question.
     *
     * Creates a report entry for a question with an optional description.
     */
    public function reportQuestion(Request $request)
    {
        $validated = $request->validate([
            'question_id' => 'required|integer|exists:questions,id',
            'description' => 'nullable|string|max:2000',
        ]);

        $user = $request->user();

        $report = QuestionReport::create([
            'user_id' => $user?->id,
            'question_id' => $validated['question_id'],
            'description' => $validated['description'] ?? null,
        ]);

        return $this->sendResponse([
            'report_id' => $report->id,
        ]);
    }
}
