<?php

namespace App\Services\Content;

use App\Enums\ChapterVisibility;
use App\Enums\UnitVisibility;
use App\Models\Chapter;
use App\Models\Unit;
use App\Models\User;

class ContentService
{
    /**
     * Build user content payload for V2 endpoints.
     * Differences from InteractsWithContent:
     * - Include units not in user's subscriptions (marked as premium)
     * - Include chapters not in user's subscriptions if their unit is in user's subscriptions (marked as premium)
     * - Keep existing progress and points calculations based on user's accessible content
     */
    public function getUserContent(User $user): array
    {
        $subscriptionIds = $user->subscriptions->pluck('id')->all();

        // Preload progress and points for accessible content
        $progressData = $user->getAllProgressData();

        // Load all materials with units (no subscription filter), and chapters for units
        $division = $user->division->load([
            'materials' => function ($q) {
                $q->where('active', true)
                    ->with([
                        'units' => function ($uq) {
                            $uq->where('active', true)
                                ->with([
                                    'chapters' => function ($cq) {
                                        $cq->where('active', true);
                                    },
                                ]);
                        },
                    ]);
            },
        ]);

        $materials = $division->materials; // already filtered active

        $modules = [];
        $units = [];
        $chapters = [];
        $exercices = [];

        foreach ($materials as $material) {
            $modules[] = [
                'id' => $material->id,
                'name' => $material->name,
                'code' => $material->code,
                'direction' => $material->direction->value,
                'division_id' => $material->division_id,
                'color' => $material->color,
                'secondary_color' => $material->secondary_color,
                'description' => $material->description,
                'image' => $material->image,
                'image_grid' => $material->image_grid,
                'progress' => $progressData['materials'][$material->id] ?? 0,
                'points' => $progressData['points']['materials'][$material->id] ?? 0,
            ];

            foreach ($material->units as $unit) {
                // Determine if unit is available (in any of the user's subscriptions)
                $unitSubscribed = $unit->subscriptions()->whereIn('subscriptions.id', $subscriptionIds)->exists();

                $units[] = [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'description' => $unit->description,
                    // 'image' => $unit->image
                    'image' => $unit->image_thumb,
                    'direction' => $unit->getEffectiveDirection()->value,
                    'material_id' => $material->id,
                    'progress' => $progressData['units'][$unit->id] ?? 0,
                    'points' => $progressData['points']['units'][$unit->id] ?? 0,
                    'visibility' => $unitSubscribed ? UnitVisibility::AVAILABLE->value : UnitVisibility::PREMIUM->value,
                ];

                // Only include chapter entries for units available to the user
                if (! $unitSubscribed) {
                    continue;
                }

                // Chapter visibility within this unit
                $chapterVisibilityMap = $user->getChapterVisibility($unit->id);

                foreach ($unit->chapters as $chapter) {
                    // Determine if chapter is in user's subscriptions
                    $chapterSubscribed = $chapter->subscriptions()->whereIn('subscriptions.id', $subscriptionIds)->exists();

                    // If unit is subscribed but chapter isn't, mark as premium
                    // Else use calculated visibility (done/current/locked)
                    $visibility = ($unitSubscribed && ! $chapterSubscribed)
                        ? ChapterVisibility::PREMIUM->value
                        : ($chapterVisibilityMap[$chapter->id] ?? ChapterVisibility::LOCKED->value);

                    $bonusPoints = $progressData['points']['bonuses'][$chapter->id] ?? 0;

                    $chapters[] = [
                        'id' => $chapter->id,
                        'name' => $chapter->name,
                        'direction' => $chapter->getEffectiveDirection()->value,
                        'description' => $chapter->description,
                        // 'image' => $chapter->image,
                        'image' => $chapter->image_thumb,
                        'unit_id' => $unit->id,
                        'bonus_points' => $chapter->chapter_level ? $chapter->chapter_level->bonus : 0,
                        'earned_bonus' => $bonusPoints,
                        // Progress & points were missing causing frontend to treat all chapters uniformly
                        // For non-subscribed (premium) chapters, progressData won't have entries => default 0
                        'progress' => $progressData['chapters'][$chapter->id] ?? 0,
                        'points' => $progressData['points']['chapters'][$chapter->id] ?? 0,
                        'visibility' => $visibility,
                    ];

                    // Exercises/questions are included only for subscribed chapters (to avoid leaking answers)
                    if ($chapterSubscribed && isset($chapter->questions)) {
                        $transformedQuestions = [];
                        foreach ($chapter->questions as $question) {
                            $transformedQuestions[] = $user->transformQuestion($question);
                        }
                        if (! empty($transformedQuestions)) {
                            $exercices[] = [
                                'chapter_id' => $chapter->id,
                                'questions' => $transformedQuestions,
                            ];
                        }
                    }
                }
            }
        }

        return [
            'modules' => $modules,
            'units' => $units,
            'chapters' => $chapters,
            'exercices' => $exercices,
            'total_points' => $progressData['points']['total'] ?? 0,
        ];
    }
}
