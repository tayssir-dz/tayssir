<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\SubmitChapterAnswersRequest;
use App\Services\Content\AnswerSubmissionService;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

#[Group('Content Management APIs', weight: 6)]
class ContentController extends BaseController
{
    /**
     * Get user content.
     *
     * This endpoint returns the content associated with the authenticated user.
     */
    public function getUserContent(Request $request)
    {
        $user = $request->user();
        if (! $user->division) {
            return $this->sendError(__('response.an_error_occurred'));
        }

        return $this->sendResponse($user->content);
    }

    /**
     * Submit chapter answers.
     *
     * This endpoint submits the answers of a chapter.
     */
    public function SubmitChapterAnswers(SubmitChapterAnswersRequest $request)
    {
        $user = $request->user();
        $chapterId = $request->chapter_id;
        $answers = $request->answers;

        try {
            $service = new AnswerSubmissionService;
            $result = $service->submit($user, $chapterId, $answers);

            return $this->sendResponse($result, __('response.answers_submitted_successfully'));
        } catch (\Exception $e) {
            Log::error('Error submitting answers: ' . $e->getMessage());

            return $this->sendError(__('response.an_error_occurred'), [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'error' => $e,
            ]);
        }
    }
}
