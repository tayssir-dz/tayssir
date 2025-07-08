<?php

namespace App\Http\Controllers\API;

use App\Models\Summary;
use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SummaryController extends BaseController
{
    /**
     * Display a listing of the active summaries grouped by materials with optional material filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Validate the request parameters
        $request->validate([
            'materials' => 'sometimes|array',
            'materials.*' => 'integer|exists:materials,id',
            'material_id' => 'sometimes|integer|exists:materials,id',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
        ]);

        $query = Summary::with('material')
            ->where('is_active', true);

        // Filter by materials if provided
        if ($request->has('materials') && is_array($request->materials)) {
            $query->whereIn('material_id', $request->materials);
        } elseif ($request->has('material_id')) {
            $query->where('material_id', $request->material_id);
        }

        // Set pagination parameters
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100); // Cap at 100 items per page

        // Get paginated results
        $summaries = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Group summaries by material
        $groupedSummaries = $summaries->groupBy('material_id')->map(function ($materialSummaries, $materialId) {
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
                })->values()
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
            ]
        ], __("response.summaries_retrieved_successfully"));
    }

    /**
     * Display the specified summary.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $summary = Summary::with('material')
            ->where('is_active', true)
            ->find($id);

        if (is_null($summary)) {
            return $this->sendError(__("response.summary_not_found"));
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

        return $this->sendResponse($summaryData, __("response.summary_retrieved_successfully"));
    }
}
