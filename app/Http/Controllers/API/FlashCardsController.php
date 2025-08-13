<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\FlashcardGroup;
use App\Models\Flashcard;
use App\Models\Material;
use Illuminate\Http\Request;

class FlashCardsController extends BaseController
{
    public function materialsWithFlashcardGroups(Request $request)
    {
        $request->validate([
            'materials' => 'sometimes|array',
            'materials.*' => 'integer|exists:materials,id',
            'material_id' => 'sometimes|integer|exists:materials,id',
        ]);

        $query = Material::with(['flashcardGroups' => function ($query) {
            $query->withCount('flashcards');
        }])
            ->whereHas('flashcardGroups')
            ->withCount(['flashcardGroups', 'flashcardGroups as total_flashcards_count' => function ($query) {
                $query->join('flashcards', 'flashcard_groups.id', '=', 'flashcards.flashcard_group_id');
            }]);

        if ($request->has('materials') && is_array($request->materials)) {
            $query->whereIn('id', $request->materials);
        } elseif ($request->has('material_id')) {
            $query->where('id', $request->material_id);
        }

        $materials = $query->orderBy('name')->get();

        $materialsWithGroups = $materials->map(function ($material) {
            return [
                'material' => [
                    'id' => $material->id,
                    'name' => $material->name,
                    'code' => $material->code,
                    'color' => $material->color,
                    'description' => $material->description,
                    'flashcard_groups_count' => $material->flashcard_groups_count,
                    'total_flashcards_count' => $material->total_flashcards_count ?? 0,
                ],
                'flashcard_groups' => $material->flashcardGroups->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'title' => $group->title,
                        'description' => $group->description,
                        'flashcards_count' => $group->flashcards_count,
                        'created_at' => $group->created_at,
                    ];
                })->values()
            ];
        })->values();

        return $this->sendResponse([
            'materials_with_flashcard_groups' => $materialsWithGroups,
        ], __("response.materials_with_flashcard_groups_retrieved_successfully"));
    }

    public function index(Request $request)
    {
        $request->validate([
            'materials' => 'sometimes|array',
            'materials.*' => 'integer|exists:materials,id',
            'material_id' => 'sometimes|integer|exists:materials,id',
            'flashcard_groups' => 'sometimes|array',
            'flashcard_groups.*' => 'integer|exists:flashcard_groups,id',
            'flashcard_group_id' => 'sometimes|integer|exists:flashcard_groups,id',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
        ]);

        $query = Flashcard::with(['flashcardGroup', 'flashcardGroup.material']);

        if ($request->has('materials') && is_array($request->materials)) {
            $query->whereHas('flashcardGroup', function ($q) use ($request) {
                $q->whereIn('material_id', $request->materials);
            });
        } elseif ($request->has('material_id')) {
            $query->whereHas('flashcardGroup', function ($q) use ($request) {
                $q->where('material_id', $request->material_id);
            });
        }

        if ($request->has('flashcard_groups') && is_array($request->flashcard_groups)) {
            $query->whereIn('flashcard_group_id', $request->flashcard_groups);
        } elseif ($request->has('flashcard_group_id')) {
            $query->where('flashcard_group_id', $request->flashcard_group_id);
        }

        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100);

        $flashcards = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $flashcardsData = $flashcards->getCollection()->map(function ($flashcard) {
            return [
                'id' => $flashcard->id,
                'title' => $flashcard->title,
                'description' => $flashcard->description,
                'flashcard_group' => [
                    'id' => $flashcard->flashcardGroup->id,
                    'title' => $flashcard->flashcardGroup->title,
                    'description' => $flashcard->flashcardGroup->description,
                ],
                'material' => [
                    'id' => $flashcard->flashcardGroup->material->id,
                    'name' => $flashcard->flashcardGroup->material->name,
                    'code' => $flashcard->flashcardGroup->material->code,
                    'color' => $flashcard->flashcardGroup->material->color,
                    'description' => $flashcard->flashcardGroup->material->description,
                ],
                'created_at' => $flashcard->created_at,
                'updated_at' => $flashcard->updated_at,
            ];
        });

        return $this->sendResponse([
            'flashcards' => $flashcardsData,
            'pagination' => [
                'current_page' => $flashcards->currentPage(),
                'last_page' => $flashcards->lastPage(),
                'per_page' => $flashcards->perPage(),
                'total' => $flashcards->total(),
                'from' => $flashcards->firstItem(),
                'to' => $flashcards->lastItem(),
            ]
        ], __("response.flashcards_retrieved_successfully"));
    }

    public function show($id)
    {
        $flashcard = Flashcard::with(['flashcardGroup', 'flashcardGroup.material'])
            ->find($id);

        if (is_null($flashcard)) {
            return $this->sendError(__("response.flashcard_not_found"));
        }

        $flashcardData = [
            'id' => $flashcard->id,
            'title' => $flashcard->title,
            'description' => $flashcard->description,
            'flashcard_group' => [
                'id' => $flashcard->flashcardGroup->id,
                'title' => $flashcard->flashcardGroup->title,
                'description' => $flashcard->flashcardGroup->description,
            ],
            'material' => [
                'id' => $flashcard->flashcardGroup->material->id,
                'name' => $flashcard->flashcardGroup->material->name,
                'code' => $flashcard->flashcardGroup->material->code,
                'color' => $flashcard->flashcardGroup->material->color,
                'description' => $flashcard->flashcardGroup->material->description,
            ],
            'created_at' => $flashcard->created_at,
            'updated_at' => $flashcard->updated_at,
        ];

        return $this->sendResponse($flashcardData, __("response.flashcard_retrieved_successfully"));
    }

    public function content(Request $request)
    {
        $user = $request->user();
        $materials = $user?->division?->materials()->with(['flashcardGroups.flashcards'])->get() ?? collect();

        $topics = $materials->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'color' => $m->color,
                'secondary_color' => $m->secondary_color,
            ];
        })->values();

        $categories = $materials->flatMap(function ($m) {
            return $m->flashcardGroups->map(function ($g) use ($m) {
                return [
                    'id' => $g->id,
                    'topic_id' => $m->id,
                    'name' => $g->title,
                ];
            });
        })->values();

        $cards = $materials->flatMap(function ($m) {
            return $m->flashcardGroups->flatMap(function ($g) {
                return $g->flashcards->map(function ($c) use ($g) {
                    return [
                        'id' => $c->id,
                        'category_id' => $g->id,
                        'name' => $c->title,
                        'description' => $c->description,
                    ];
                });
            });
        })->values();

        return $this->sendResponse([
            'topics' => $topics,
            'categories' => $categories,
            'cards' => $cards,
        ], __('response.flashcards_retrieved_successfully'));
    }
}
