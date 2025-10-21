<?php

namespace App\Services\Analytics;

use App\Models\Material;
use App\Models\Unit;
use App\Models\UserAnswer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ContentQualityService
{
    private const CACHE_DURATION = 3600; // 1 hour in seconds
    private const CACHE_KEY_PREFIX = 'analytics.content_quality.';
    private const TOP_MATERIALS_LIMIT = 3;
    private const BOTTOM_MATERIALS_LIMIT = 3;

    /**
     * Get content quality metrics with caching
     */
    public function getMetrics(): array
    {
        $cacheKey = $this->getCacheKey();

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            return [
                'top_materials' => $this->getTopMaterials(),
                'bottom_materials' => $this->getBottomMaterials(),
            ];
        });
    }

    /**
     * Get top materials by user answers/engagement
     */
    private function getTopMaterials(): array
    {
        $topMaterialData = UserAnswer::query()
            ->select('material_id', DB::raw('COUNT(*) as engagement_count'))
            ->whereNotNull('material_id')
            ->groupBy('material_id')
            ->orderByDesc('engagement_count')
            ->limit(self::TOP_MATERIALS_LIMIT)
            ->get();

        if ($topMaterialData->isEmpty()) {
            return [];
        }

        // Get material IDs and fetch materials
        $materialIds = $topMaterialData->pluck('material_id')->toArray();
        $materials = Material::query()
            ->whereIn('id', $materialIds)
            ->get()
            ->keyBy('id');

        // Map the results
        return $topMaterialData->map(function ($record) use ($materials) {
            $material = $materials->get($record->material_id);
            return [
                'material_name' => $material?->name ?? 'Unknown',
                'engagement_count' => (int) $record->engagement_count,
            ];
        })->toArray();
    }

    /**
     * Get bottom materials by user answers/engagement (least engaged)
     */
    private function getBottomMaterials(): array
    {
        // Get all active materials
        $allMaterials = Material::query()
            ->active()
            ->get();

        // Get engagement counts for all materials
        $engagementCounts = UserAnswer::query()
            ->select('material_id', DB::raw('COUNT(*) as engagement_count'))
            ->groupBy('material_id')
            ->pluck('engagement_count', 'material_id')
            ->toArray();

        // Map materials with their engagement counts
        $materialsWithEngagement = $allMaterials->map(function ($material) use ($engagementCounts) {
            return [
                'material_id' => $material->id,
                'material_name' => $material->name,
                'engagement_count' => $engagementCounts[$material->id] ?? 0,
            ];
        })->toArray();

        // Sort by engagement count (ascending) and limit to BOTTOM_MATERIALS_LIMIT
        usort($materialsWithEngagement, function ($a, $b) {
            return $a['engagement_count'] - $b['engagement_count'];
        });

        return array_slice($materialsWithEngagement, 0, self::BOTTOM_MATERIALS_LIMIT);
    }

    /**
     * Generate cache key
     */
    private function getCacheKey(): string
    {
        return self::CACHE_KEY_PREFIX . 'metrics';
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
        Cache::forget($this->getCacheKey());
    }
}
