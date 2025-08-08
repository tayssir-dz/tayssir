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
use App\Services\AnswerSubmissionService;

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
            $service = new AnswerSubmissionService();
            $result = $service->submit($user, $chapterId, $answers);

            return $this->sendResponse($result, __('response.answers_submitted_successfully'));
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
