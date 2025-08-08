<?php

namespace App\Http\Controllers\API;

use App\Models\Material;
use App\Models\Unit;
use App\Models\Chapter;
use Illuminate\Http\Request;

class ContentWebController extends BaseController
{
    private function perPage(Request $request): int
    {
        $perPage = (int) $request->query('per_page', 15);
        return max(1, min(100, $perPage));
    }

    public function content(Request $request)
    {
        $user = $request->user();
        if (!$user->division) {
            return $this->sendError(__('response.an_error_occurred'));
        }

        $progressData = $user->getAllProgressData();
        $subscriptionIds = $user->subscriptions->pluck('id');

        // Minimal, paginated content snapshot for web
        $materials = $user->division
            ->materials()
            ->active()
            ->whereHas('units', function ($query) use ($subscriptionIds) {
                $query->whereHas('subscriptions', function ($subQuery) use ($subscriptionIds) {
                    $subQuery->whereIn('subscriptions.id', $subscriptionIds);
                });
            })
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

    public function materials(Request $request)
    {
        $user = $request->user();
        $progressData = $user->getAllProgressData();
        $subscriptionIds = $user->subscriptions->pluck('id');

        $materials = $user->division
            ->materials()
            ->active()
            ->whereHas('units', function ($query) use ($subscriptionIds) {
                $query->whereHas('subscriptions', function ($subQuery) use ($subscriptionIds) {
                    $subQuery->whereIn('subscriptions.id', $subscriptionIds);
                });
            })
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

    public function units(Request $request, int $materialId)
    {
        $user = $request->user();
        $progressData = $user->getAllProgressData();
        $subscriptionIds = $user->subscriptions->pluck('id');

        $material = Material::active()->findOrFail($materialId);

        $units = $material
            ->units()
            ->active()
            ->whereHas('subscriptions', function ($query) use ($subscriptionIds) {
                $query->whereIn('subscriptions.id', $subscriptionIds);
            })
            ->paginate($this->perPage($request));

        $units->setCollection(
            $units->getCollection()->map(function ($unit) use ($progressData, $material) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'description' => $unit->description,
                    'image' => $unit->image,
                    'direction' => $unit->getEffectiveDirection()->value,
                    'material_id' => $material->id,
                    'progress' => $progressData['units'][$unit->id] ?? 0,
                    'points' => $progressData['points']['units'][$unit->id] ?? 0,
                ];
            })
        );

        return $this->sendResponse($units);
    }

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
            ->whereHas('subscriptions', function ($query) use ($subscriptionIds) {
                $query->whereIn('subscriptions.id', $subscriptionIds);
            })
            ->with('chapter_level')
            ->paginate($this->perPage($request));

        $chapterVisibility = $user->getChapterVisibility($unit->id);

        $chapters->setCollection(
            $chapters->getCollection()->map(function ($chapter) use ($progressData, $unit, $chapterVisibility) {
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
                    'visibility' => $chapterVisibility[$chapter->id] ?? \App\Enums\ChapterVisibility::LOCKED->value,
                ];
            })
        );

        return $this->sendResponse($chapters);
    }

    public function questions(Request $request, int $chapterId)
    {
        $user = $request->user();

        $chapter = Chapter::active()->findOrFail($chapterId);

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
