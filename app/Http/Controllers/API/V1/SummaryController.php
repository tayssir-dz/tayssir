<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseController;
use App\Models\Summary;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('Summary Management APIs', weight: 10)]
class SummaryController extends BaseController
{
    /**
     * List all active summaries grouped by materials.
     *
     * This endpoint returns all active summaries grouped by materials with optional material filtering and pagination, simple example `/api/v1/summaries?per_page=20&page=1`, filter materials : `/api/v1/summaries?materials[]=1&materials[]=3`, note that the material ids must exist in the db.
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

        $query = Summary::with('material')->where('is_active', true);

        if ($request->has('materials') && is_array($request->materials)) {
            $query->whereIn('material_id', $request->materials);
        } elseif ($request->has('material_id')) {
            $query->where('material_id', $request->material_id);
        }

        $perPage = min($request->get('per_page', 15), 100);
        $summaries = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $groupedSummaries = $summaries->groupBy('material_id')->map(function ($materialSummaries) {
            $material = $materialSummaries->first()->material;

            return [
                'material' => [
                    'id' => $material->id,
                    'name' => $material->name,
                    'code' => $material->code,
                    'color' => $material->color,
                    'description' => $material->description,
                ],
                'summaries' => $materialSummaries->map(function ($summary) {
                    return [
                        'id' => $summary->id,
                        'title' => $summary->title,
                        'description' => $summary->description,
                        'pdf_url' => $summary->pdf,
                        'created_at' => $summary->created_at,
                    ];
                })->values(),
            ];
        })->values();

        return $this->sendResponse([
            'materials_with_summaries' => $groupedSummaries,
            'pagination' => [
                'current_page' => $summaries->currentPage(),
                'last_page' => $summaries->lastPage(),
                'per_page' => $summaries->perPage(),
                'total' => $summaries->total(),
                'from' => $summaries->firstItem(),
                'to' => $summaries->lastItem(),
            ],
        ], __('response.summaries_retrieved_successfully'));
    }

    /**
     * Get summary by ID.
     *
     * This endpoint returns a specific active summary by its ID.
     */
    public function show($id)
    {
        $summary = Summary::with('material')->where('is_active', true)->find($id);
        if (is_null($summary)) {
            return $this->sendError(__('response.summary_not_found'));
        }
        $summaryData = [
            'id' => $summary->id,
            'title' => $summary->title,
            'description' => $summary->description,
            'pdf_url' => $summary->pdf,
            'material' => [
                'id' => $summary->material->id,
                'name' => $summary->material->name,
                'code' => $summary->material->code,
                'color' => $summary->material->color,
                'description' => $summary->material->description,
            ],
            'created_at' => $summary->created_at,
            'updated_at' => $summary->updated_at,
        ];

        return $this->sendResponse($summaryData, __('response.summary_retrieved_successfully'));
    }

    /**
     * Summaries content.
     *
     * Returns division materials and all active summaries as units.
     */
    public function content(Request $request)
    {
        $user = $request->user();
        $materials = $user?->division?->materials()->with(['summaries'])->get() ?? collect();

        $materialsArray = $materials->map(function ($m) {
            return [
                'id' => $m->id,
                'name' => $m->name,
                'colors' => array_values(array_filter([$m->color, $m->secondary_color])),
            ];
        })->values();

        $unitsArray = $materials->flatMap(function ($m) {
            return $m->summaries->map(function ($s) use ($m) {
                return [
                    'id' => $s->id,
                    'name' => $s->title,
                    'description' => $s->description,
                    'materialId' => $m->id,
                    'pdf' => $s->pdf,
                ];
            });
        })->values();

        return $this->sendResponse([
            'materials' => $materialsArray,
            'units' => $unitsArray,
        ], __('response.summaries_retrieved_successfully'));
    }
}
