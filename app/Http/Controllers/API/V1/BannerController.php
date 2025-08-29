<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\BaseController;
use App\Models\Banner;
use Dedoc\Scramble\Attributes\Group;

#[Group('Banner Management APIs', weight: 9)]
class BannerController extends BaseController
{
    /**
     * List all active banners.
     *
     * This endpoint returns all active banners.
     */
    public function index()
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'description' => $banner->description,
                    'action_url' => $banner->action_url,
                    'action_label' => $banner->action_label,
                    'gradient_start' => $banner->gradient_start,
                    'gradient_end' => $banner->gradient_end,
                    'image' => $banner->image,
                    'created_at' => $banner->created_at,
                ];
            });

        return $this->sendResponse($banners, __('response.banners_retrieved_successfully'));
    }

    /**
     * Get banner by ID.
     *
     * This endpoint returns a specific active banner by its ID.
     */
    public function show($id)
    {
        $banner = Banner::where('is_active', true)->find($id);

        if (is_null($banner)) {
            return $this->sendError(__('response.banner_not_found'));
        }

        $bannerData = [
            'id' => $banner->id,
            'title' => $banner->title,
            'description' => $banner->description,
            'action_url' => $banner->action_url,
            'action_label' => $banner->action_label,
            'gradient_start' => $banner->gradient_start,
            'gradient_end' => $banner->gradient_end,
            'image' => $banner->image,
            'created_at' => $banner->created_at,
        ];

        return $this->sendResponse($bannerData, __('response.banner_retrieved_successfully'));
    }
}
