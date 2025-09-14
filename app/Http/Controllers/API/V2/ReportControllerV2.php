<?php

namespace App\Http\Controllers\API\V2;

use App\Http\Controllers\API\BaseController;
use App\Models\ContactForm;
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

    /**
     * Submit a contact form.
     *
     * Accepts name, email, subject, message.
     */
    public function submitContactForm(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $user = $request->user();

        $contact = ContactForm::create([
            'user_id' => $user?->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
        ]);

        return $this->sendResponse([
            'contact_id' => $contact->id,
        ]);
    }
}
