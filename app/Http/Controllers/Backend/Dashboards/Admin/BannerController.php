<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Banner\StoreBannerRequest;
use App\Http\Requests\Admin\Banner\UpdateBannerRequest;
use App\Interfaces\Admin\BannerRepositoryInterface;
use App\Models\Banner;

class BannerController extends Controller
{
    protected $repo;

    public function __construct(BannerRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        // abort_if(!hasPermission('view banners'), 403, __('You are not authorized to view banners'));
        return view('backend.dashboards.admin.pages.banners.index');
    }

    public function data()
    {
        // abort_if(!hasPermission('view banners'), 403, __('You are not authorized to view banners'));
        return $this->repo->data();
    }

    public function create()
    {
//         abort_if(! hasPermission('create banner'), 403, __('You are not authorized to create banner'));

        return view('backend.dashboards.admin.pages.banners.create');
    }

    public function store(StoreBannerRequest $request)
    {
//         abort_if(! hasPermission('create banner'), 403, __('You are not authorized to create banner'));

        return $this->repo->store($request);
    }

    public function edit($id)
    {
//         abort_if(! hasPermission('update banner'), 403, __('You are not authorized to view banner'));
        $banner = $this->repo->show($id);

        return view('backend.dashboards.admin.pages.banners.edit', compact('banner'));
    }

    public function update(UpdateBannerRequest $request, $id)
    {
//         abort_if(! hasPermission('update banner'), 403, __('You are not authorized to update banner'));

        return $this->repo->update($request, $id);
    }

    public function destroy($id)
    {
//         abort_if(! hasPermission('delete banner'), 403, __('You are not authorized to delete banner'));

        return $this->repo->destroy($id);
    }

    public function trash()
    {
//         abort_if(! hasPermission('view banners'), 403, __('You are not authorized to view banners'));

        return view('backend.dashboards.admin.pages.banners.trash');
    }

    public function trashData()
    {
//         abort_if(! hasPermission('view banners'), 403, __('You are not authorized to view banners'));

        return $this->repo->trashData();
    }

    public function restore($id)
    {
//         abort_if(! hasPermission('delete banner'), 403, __('You are not authorized to restore banner'));

        return $this->repo->restore($id);
    }

    public function forceDelete($id)
    {
//         abort_if(! hasPermission('delete banner'), 403, __('You are not authorized to delete banner'));

        return $this->repo->forceDelete($id);
    }

    public function toggleStatus($id)
    {
//         abort_if(! hasPermission('update banner'), 403, __('You are not authorized to update banner'));

        return $this->repo->toggleStatus($id);
    }

    // Frontend API endpoints
    public function getBanners($position = null)
    {
        $banners = Banner::active()
            ->when($position, fn ($q) => $q->byPosition($position))
            ->ordered()
            ->get();

        return response()->json($banners);
    }

    public function trackView($id)
    {
        return $this->repo->incrementViews($id);
    }

    public function trackClick($id)
    {
        return $this->repo->incrementClicks($id);
    }
}
