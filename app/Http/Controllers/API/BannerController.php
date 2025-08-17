<?php

namespace App\Http\Controllers\API;

use App\Models\Banner;

class BannerController extends BaseController
{
    /**
     * Display a listing of the active banners.
     *
     * @return \Illuminate\Http\Response
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
     * Display the specified banner.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
