<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseController;
use App\Models\Bac;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('Bac Management APIs', weight: 11)]
class BacController extends BaseController
{
    /**
     * List all active bacs grouped by materials.
     *
     * This endpoint returns all active bacs grouped by materials with optional material filtering and pagination. Examples: `/api/v1/bacs?per_page=20&page=1` for pagination, `/api/v1/bacs?material_id=1` for single material filter, `/api/v1/bacs?materials[]=1&materials[]=3` for multiple materials filter. Note that the material IDs must exist in the database.
     */
    public function index(Request $request)
    {
        $request->validate([
            'materials' => 'sometimes|array',
            'materials.*' => 'integer|exists:materials,id',
            'material_id' => 'sometimes|integer|exists:materials,id',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
        ]);

        $query = Bac::with('material')
            ->where('is_active', true);

        if ($request->has('materials') && is_array($request->materials)) {
            $query->whereIn('material_id', $request->materials);
        } elseif ($request->has('material_id')) {
            $query->where('material_id', $request->material_id);
        }

        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100);

        $bacs = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $groupedBacs = $bacs->groupBy('material_id')->map(function ($materialBacs, $materialId) {
            $material = $materialBacs->first()->material;

            return [
                'material' => [
                    'id' => $material->id,
                    'name' => $material->name,
                    'code' => $material->code,
                    'color' => $material->color,
                    'description' => $material->description,
                ],
                'bacs' => $materialBacs->map(function ($bac) {
                    return [
                        'id' => $bac->id,
                        'title' => $bac->title,
                        'description' => $bac->description,
                        'pdf_url' => $bac->pdf,
                        'created_at' => $bac->created_at,
                    ];
                })->values(),
            ];
        })->values();

        return $this->sendResponse([
            'materials_with_bacs' => $groupedBacs,
            'pagination' => [
                'current_page' => $bacs->currentPage(),
                'last_page' => $bacs->lastPage(),
                'per_page' => $bacs->perPage(),
                'total' => $bacs->total(),
                'from' => $bacs->firstItem(),
                'to' => $bacs->lastItem(),
            ],
        ], __('response.bacs_retrieved_successfully'));
    }

    /**
     * Get bac by ID.
     *
     * This endpoint returns a specific active bac by its ID.
     */
    public function show($id)
    {
        $bac = Bac::with('material')
            ->where('is_active', true)
            ->find($id);

        if (is_null($bac)) {
            return $this->sendError(__('response.bac_not_found'));
        }

        $bacData = [
            'id' => $bac->id,
            'title' => $bac->title,
            'description' => $bac->description,
            'pdf_url' => $bac->pdf,
            'material' => [
                'id' => $bac->material->id,
                'name' => $bac->material->name,
                'code' => $bac->material->code,
                'color' => $bac->material->color,
                'description' => $bac->material->description,
            ],
            'created_at' => $bac->created_at,
            'updated_at' => $bac->updated_at,
        ];

        return $this->sendResponse($bacData, __('response.bac_retrieved_successfully'));
    }

    /**
     * Bacs content.
     *
     * Returns division materials and all active bacs as units.
     */
    public function content(Request $request)
    {
        $user = $request->user();
        $materials = $user?->division?->materials()->with(['bacs'])->get() ?? collect();

        $materialsArray = $materials->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'colors' => array_values(array_filter([$m->color, $m->secondary_color])),
            ];
        })->values();

        $unitsArray = $materials->flatMap(function ($m) {
            return $m->bacs->map(function ($b) use ($m) {
                return [
                    'id' => $b->id,
                    'name' => $b->title,
                    'description' => $b->description,
                    'materialId' => $m->id,
                    'pdf' => $b->pdf,
                ];
            });
        })->values();

        return $this->sendResponse([
            'materials' => $materialsArray,
            'units' => $unitsArray,
        ], __('response.bacs_retrieved_successfully'));
    }
}
