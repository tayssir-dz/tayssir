<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseController;
use App\Enums\UnitVisibility;
use App\Models\Chapter;
use App\Models\Material;
use App\Models\Unit;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('Web Content APIs', weight: 13)]
class ContentWebController extends BaseController
{
    private function perPage(Request $request): int
    {
        $perPage = (int) $request->query('per_page', 15);

        return max(1, min(100, $perPage));
    }

    /**
     * Web content snapshot.
     *
     * Lightweight content tree for the authenticated user. Includes paginated materials only. Use query params per_page (1-100, default 15) and page to paginate results.
     */
    public function content(Request $request)
    {
        $user = $request->user();
        if (! $user->division) {
            return $this->sendError(__('response.an_error_occurred'));
        }

        $progressData = $user->getAllProgressData();
        $subscriptionIds = $user->subscriptions->pluck('id')->all();

        // QUERY HERE: Load all active materials from the division (no subscription filter)
        // This matches ContentService::getUserContent() behavior in V2
        $materials = $user->division
            ->materials()
            ->active()
            ->paginate($this->perPage($request));

        $materialsData = $materials->getCollection()->map(function ($material) use ($progressData) {
            return [
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
        });

        $materials->setCollection($materialsData);

        return $this->sendResponse([
            'materials' => $materials,
        ]);
    }

    /**
     * List my materials (paginated).
     *
     * Returns all active materials for the current division. Supports pagination via per_page (1-100, default 15) and page.
     * NOTE: This endpoint returns all materials regardless of subscription status, similar to V2 behavior.
     * Unit visibility (premium/available) is determined per-unit based on user subscriptions.
     */
    public function materials(Request $request)
    {
        $user = $request->user();
        $progressData = $user->getAllProgressData();

        // QUERY HERE: Load all active materials from the division (no subscription filter)
        // This matches ContentService::getUserContent() behavior in V2
        $materials = $user->division
            ->materials()
            ->active()
            ->paginate($this->perPage($request));

        $materials->setCollection(
            $materials->getCollection()->map(function ($material) use ($progressData) {
                return [
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
            })
        );

        return $this->sendResponse($materials);
    }

    /**
     * List units by material (paginated).
     *
     * Returns all units for the specified material. Includes progress, points and visibility.
     * Premium units (not in user's subscriptions) are included with premium visibility.
     * Supports pagination via per_page (1-100, default 15) and page.
     */
    public function units(Request $request, int $materialId)
    {
        $user = $request->user();
        $progressData = $user->getAllProgressData();
        $subscriptionIds = $user->subscriptions->pluck('id')->all();

        $material = Material::active()->findOrFail($materialId);

        // QUERY HERE: Load all active units from the material (no subscription filter)
        // This matches ContentService::getUserContent() behavior in V2
        $units = $material
            ->units()
            ->active()
            ->paginate($this->perPage($request));

        $units->setCollection(
            $units->getCollection()->map(function ($unit) use ($progressData, $material, $subscriptionIds) {
                // Determine if unit is available (in any of the user's subscriptions)
                $unitSubscribed = $unit->subscriptions()->whereIn('subscriptions.id', $subscriptionIds)->exists();

                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'description' => $unit->description,
                    'image' => $unit->image,
                    'direction' => $unit->getEffectiveDirection()->value,
                    'material_id' => $material->id,
                    'progress' => $progressData['units'][$unit->id] ?? 0,
                    'points' => $progressData['points']['units'][$unit->id] ?? 0,
                    'visibility' => $unitSubscribed ? UnitVisibility::AVAILABLE->value : UnitVisibility::PREMIUM->value,
                ];
            })
        );

        return $this->sendResponse($units);
    }

    /**
     * Get unit with chapters.
     *
     * Returns unit data with all chapters at once. Includes progress, points and visibility for both unit and chapters.
     * Premium chapters (not in user's subscriptions) are included with premium visibility.
     */
    public function unitWithChapters(Request $request, int $unitId)
    {
        $user = $request->user();
        $progressData = $user->getAllProgressData();
        $subscriptionIds = $user->subscriptions->pluck('id');

        $unit = Unit::active()->findOrFail($unitId);
        $material = $unit->material()->first();

        // Get chapters without pagination (including premium ones)
        $chapters = $unit
            ->chapters()
            ->active()
            ->with('chapter_level')
            ->get();

        $chapterVisibility = $user->getChapterVisibility($unit->id);

        $chaptersData = $chapters->map(function ($chapter) use ($progressData, $unit, $chapterVisibility, $subscriptionIds) {
            $chapterSubscribed = $chapter->subscriptions()->whereIn('subscriptions.id', $subscriptionIds)->exists();

            // If chapter not subscribed, mark as premium
            // Otherwise use calculated visibility (done/current/locked)
            $visibility = ! $chapterSubscribed
                ? \App\Enums\ChapterVisibility::PREMIUM->value
                : ($chapterVisibility[$chapter->id] ?? \App\Enums\ChapterVisibility::LOCKED->value);

            $bonusPoints = $progressData['points']['bonuses'][$chapter->id] ?? 0;

            return [
                'id' => $chapter->id,
                'name' => $chapter->name,
                'direction' => $chapter->getEffectiveDirection()->value,
                'description' => $chapter->description,
                'image' => $chapter->image,
                'unit_id' => $unit->id,
                'bonus_points' => $chapter->chapter_level ? $chapter->chapter_level->bonus : 0,
                'earned_bonus' => $bonusPoints,
                'progress' => $progressData['chapters'][$chapter->id] ?? 0,
                'points' => $progressData['points']['chapters'][$chapter->id] ?? 0,
                'visibility' => $visibility,
            ];
        });

        $unitData = [
            'id' => $unit->id,
            'name' => $unit->name,
            'description' => $unit->description,
            'image' => $unit->image,
            'direction' => $unit->getEffectiveDirection()->value,
            'material_id' => $material->id,
            'progress' => $progressData['units'][$unit->id] ?? 0,
            'points' => $progressData['points']['units'][$unit->id] ?? 0,
            'chapters' => $chaptersData,
        ];

        return $this->sendResponse($unitData);
    }

    /**
     * List chapters by unit (paginated).
     *
     * Returns chapters for the specified unit if accessible to the user. Includes progress, points and visibility.
     * Premium chapters (not in user's subscriptions) are included with premium visibility.
     * Supports pagination via per_page (1-100, default 15) and page.
     */
    public function chapters(Request $request, int $unitId)
    {
        $user = $request->user();
        $progressData = $user->getAllProgressData();
        $subscriptionIds = $user->subscriptions->pluck('id');

        $unit = Unit::active()->findOrFail($unitId);
        $material = $unit->material()->first();

        $chapters = $unit
            ->chapters()
            ->active()
            ->with('chapter_level')
            ->paginate($this->perPage($request));

        $chapterVisibility = $user->getChapterVisibility($unit->id);

        $chapters->setCollection(
            $chapters->getCollection()->map(function ($chapter) use ($progressData, $unit, $chapterVisibility, $subscriptionIds) {
                $chapterSubscribed = $chapter->subscriptions()->whereIn('subscriptions.id', $subscriptionIds)->exists();

                // If chapter not subscribed, mark as premium
                // Otherwise use calculated visibility (done/current/locked)
                $visibility = ! $chapterSubscribed
                    ? \App\Enums\ChapterVisibility::PREMIUM->value
                    : ($chapterVisibility[$chapter->id] ?? \App\Enums\ChapterVisibility::LOCKED->value);

                $bonusPoints = $progressData['points']['bonuses'][$chapter->id] ?? 0;

                return [
                    'id' => $chapter->id,
                    'name' => $chapter->name,
                    'direction' => $chapter->getEffectiveDirection()->value,
                    'description' => $chapter->description,
                    'image' => $chapter->image,
                    'unit_id' => $unit->id,
                    'bonus_points' => $chapter->chapter_level ? $chapter->chapter_level->bonus : 0,
                    'earned_bonus' => $bonusPoints,
                    'progress' => $progressData['chapters'][$chapter->id] ?? 0,
                    'points' => $progressData['points']['chapters'][$chapter->id] ?? 0,
                    'visibility' => $visibility,
                ];
            })
        );

        return $this->sendResponse($chapters);
    }

    /**
     * List questions by chapter (paginated).
     *
     * Returns transformed questions for the specified chapter only if the chapter is in user's subscriptions. Supports pagination via per_page (1-100, default 15) and page.
     */
    public function questions(Request $request, int $chapterId)
    {
        $user = $request->user();
        $subscriptionIds = $user->subscriptions->pluck('id');

        $chapter = Chapter::active()->findOrFail($chapterId);

        // Verify chapter belongs to an allowed subscription
        $chapterSubscribed = $chapter->subscriptions()->whereIn('subscriptions.id', $subscriptionIds)->exists();
        if (! $chapterSubscribed) {
            return $this->sendError(__('response.an_error_occurred'));
        }

        $questions = $chapter
            ->questions()
            ->paginate($this->perPage($request));

        $questions->setCollection(
            $questions->getCollection()->map(function ($question) use ($user) {
                return $user->transformQuestion($question);
            })
        );

        return $this->sendResponse($questions);
    }
}
