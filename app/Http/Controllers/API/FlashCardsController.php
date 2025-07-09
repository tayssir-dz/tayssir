<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Models\FlashcardGroup;
use App\Models\Flashcard;
use App\Models\Material;
use Illuminate\Http\Request;

class FlashCardsController extends BaseController
{
    /**
     * Display a listing of materials with their flashcard groups and card counts.
     *
     * This endpoint returns all materials that have flashcard groups, along with
     * the flashcard groups for each material and the count of cards in each group.
     * You can optionally filter by specific materials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * @queryParam materials[] integer[] Optional. Array of material IDs to filter by. Example: [1, 2, 3]
     * @queryParam material_id integer Optional. Single material ID to filter by. Example: 1
     * 
     * @response {
     *   "success": true,
     *   "data": {
     *     "materials_with_flashcard_groups": [
     *       {
     *         "material": {
     *           "id": 1,
     *           "name": "Mathematics",
     *           "code": "MATH",
     *           "color": "#FF5733",
     *           "description": "Mathematics subject",
     *           "flashcard_groups_count": 3,
     *           "total_flashcards_count": 25
     *         },
     *         "flashcard_groups": [
     *           {
     *             "id": 1,
     *             "title": "Basic Algebra",
     *             "description": "Introduction to algebraic concepts",
     *             "flashcards_count": 10,
     *             "created_at": "2024-01-01T00:00:00.000000Z"
     *           }
     *         ]
     *       }
     *     ]
     *   },
     *   "message": "Materials with flashcard groups retrieved successfully"
     * }
     */
    public function materialsWithFlashcardGroups(Request $request)
    {
        // Validate the request parameters
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

        // Filter by materials if provided
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

    /**
     * Display a paginated listing of flashcards with optional filtering.
     *
     * This endpoint returns flashcards with pagination support. You can filter by
     * multiple materials and/or multiple flashcard groups. The results include
     * flashcard details along with their parent group and material information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * @queryParam materials[] integer[] Optional. Array of material IDs to filter by. Example: [1, 2, 3]
     * @queryParam material_id integer Optional. Single material ID to filter by. Example: 1
     * @queryParam flashcard_groups[] integer[] Optional. Array of flashcard group IDs to filter by. Example: [1, 2, 3]
     * @queryParam flashcard_group_id integer Optional. Single flashcard group ID to filter by. Example: 1
     * @queryParam per_page integer Optional. Number of items per page (1-100). Default: 15. Example: 20
     * @queryParam page integer Optional. Page number. Default: 1. Example: 2
     * 
     * @response {
     *   "success": true,
     *   "data": {
     *     "flashcards": [
     *       {
     *         "id": 1,
     *         "title": "What is algebra?",
     *         "description": "Define algebra and its basic concepts",
     *         "flashcard_group": {
     *           "id": 1,
     *           "title": "Basic Algebra",
     *           "description": "Introduction to algebraic concepts"
     *         },
     *         "material": {
     *           "id": 1,
     *           "name": "Mathematics",
     *           "code": "MATH",
     *           "color": "#FF5733",
     *           "description": "Mathematics subject"
     *         },
     *         "created_at": "2024-01-01T00:00:00.000000Z",
     *         "updated_at": "2024-01-01T00:00:00.000000Z"
     *       }
     *     ],
     *     "pagination": {
     *       "current_page": 1,
     *       "last_page": 5,
     *       "per_page": 15,
     *       "total": 75,
     *       "from": 1,
     *       "to": 15
     *     }
     *   },
     *   "message": "Flashcards retrieved successfully"
     * }
     */
    public function index(Request $request)
    {
        // Validate the request parameters
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

        // Filter by materials if provided
        if ($request->has('materials') && is_array($request->materials)) {
            $query->whereHas('flashcardGroup', function ($q) use ($request) {
                $q->whereIn('material_id', $request->materials);
            });
        } elseif ($request->has('material_id')) {
            $query->whereHas('flashcardGroup', function ($q) use ($request) {
                $q->where('material_id', $request->material_id);
            });
        }

        // Filter by flashcard groups if provided
        if ($request->has('flashcard_groups') && is_array($request->flashcard_groups)) {
            $query->whereIn('flashcard_group_id', $request->flashcard_groups);
        } elseif ($request->has('flashcard_group_id')) {
            $query->where('flashcard_group_id', $request->flashcard_group_id);
        }

        // Set pagination parameters
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100); // Cap at 100 items per page

        // Get paginated results
        $flashcards = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Transform the flashcards data
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

    /**
     * Display the specified flashcard.
     *
     * This endpoint returns detailed information about a specific flashcard,
     * including its parent group and material information.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * 
     * @urlParam id integer required The ID of the flashcard. Example: 1
     * 
     * @response {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "title": "What is algebra?",
     *     "description": "Define algebra and its basic concepts",
     *     "flashcard_group": {
     *       "id": 1,
     *       "title": "Basic Algebra",
     *       "description": "Introduction to algebraic concepts"
     *     },
     *     "material": {
     *       "id": 1,
     *       "name": "Mathematics",
     *       "code": "MATH",
     *       "color": "#FF5733",
     *       "description": "Mathematics subject"
     *     },
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   },
     *   "message": "Flashcard retrieved successfully"
     * }
     */
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
}
