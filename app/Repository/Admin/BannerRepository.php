<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\BannerRepositoryInterface;
use App\Models\Banner;
use App\Traits\HandlesMediaUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BannerRepository implements BannerRepositoryInterface
{
    use HandlesMediaUploads;

    public function data()
    {
        $banners = Banner::query();

        return datatables()->of($banners)
            ->addColumn('image_preview', fn ($item) => $this->imagePreview($item))
            ->editColumn('position', fn ($item) => $this->formatPosition($item))
            ->editColumn('status', fn ($item) => $this->statusBadge($item))
            ->editColumn('start_at', fn ($item) => $item->start_at ? $item->start_at->format('Y-m-d H:i') : '-')
            ->editColumn('end_at', fn ($item) => $item->end_at ? $item->end_at->format('Y-m-d H:i') : '-')
            ->addColumn('stats', fn ($item) => $this->statsBadge($item))
            ->addColumn('action', fn ($item) => $this->actions($item))
            ->rawColumns(['image_preview', 'position', 'status', 'stats', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['created_by'] = Auth::guard('admin')->id();

            $banner = Banner::create($data);

            // Handle image uploads using Spatie Media
            $this->processMedia($banner, $request, [
                ['field' => 'image', 'collection' => 'banner_image', 'multiple' => false],
                ['field' => 'mobile_image', 'collection' => 'banner_mobile_image', 'multiple' => false],
            ], 'created');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Banner created successfully'),
                'redirect' => route('admin.banners.index'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => __('Error creating banner: ').$e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        return Banner::with('creator')->findOrFail($id);
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $banner = Banner::findOrFail($id);
            $data = $request->validated();

            $banner->update($data);

            // Handle image uploads using Spatie Media
            $this->processMedia($banner, $request, [
                ['field' => 'image', 'collection' => 'banner_image', 'multiple' => false],
                ['field' => 'mobile_image', 'collection' => 'banner_mobile_image', 'multiple' => false],
            ], 'updated');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Banner updated successfully'),
                'redirect' => route('admin.banners.index'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => __('Error updating banner: ').$e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            $banner->delete();

            return response()->json([
                'success' => true,
                'message' => __('Banner deleted successfully'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting banner: ').$e->getMessage(),
            ], 500);
        }
    }

    public function trashData()
    {
        $banners = Banner::onlyTrashed();

        return datatables()->of($banners)
            ->addColumn('image_preview', fn ($item) => $this->imagePreview($item))
            ->editColumn('deleted_at', fn ($item) => $item->deleted_at->format('Y-m-d H:i'))
            ->addColumn('action', fn ($item) => $this->trashActions($item))
            ->rawColumns(['image_preview', 'action'])
            ->make(true);
    }

    public function restore($id)
    {
        try {
            $banner = Banner::withTrashed()->findOrFail($id);
            $banner->restore();

            return response()->json([
                'success' => true,
                'message' => __('Banner restored successfully'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error restoring banner: ').$e->getMessage(),
            ], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $banner = Banner::withTrashed()->findOrFail($id);

            // Clear all media collections (Spatie Media handles deletion automatically)
            $banner->clearMediaCollection('banner_image');
            $banner->clearMediaCollection('banner_mobile_image');

            $banner->forceDelete();

            return response()->json([
                'success' => true,
                'message' => __('Banner permanently deleted'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting banner: ').$e->getMessage(),
            ], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            $banner->status = ! $banner->status;
            $banner->save();

            return response()->json([
                'success' => true,
                'message' => __('Banner status updated'),
                'status' => $banner->status,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating status: ').$e->getMessage(),
            ], 500);
        }
    }

    public function incrementViews($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->incrementViews();

        return response()->json(['success' => true]);
    }

    public function incrementClicks($id)
    {
        try {
            DB::beginTransaction();

            $banner = Banner::findOrFail($id);
            $banner->incrementClicks();

            // Optional: Store detailed click information
            // Handle authenticated users and guests
            $clickableType = null;
            $clickableId = null;

            if (Auth::check()) {
                // Authenticated user - store the user model class and ID
                $clickableType = get_class(Auth::user());
                $clickableId = Auth::id();
            } else {
                // Guest user - store null for both (recommended)
                $clickableType = null;
                $clickableId = null;
            }

            $banner->clicks()->create([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'referrer' => request()->header('referer'),
                'clickable_type' => $clickableType,
                'clickable_id' => $clickableId,
                'clicked_at' => now(),
            ]);

            DB::commit();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false], 500);
        }
    }

    // Helper methods for DataTables
    private function imagePreview($item)
    {
        $image = $item->image;
        if ($image) {
            return '<img src="'.$image.'" alt="'.($item->title ?? '').'" class="img-thumbnail" style="max-width: 100px; max-height: 60px;">';
        }

        return '<span class="text-muted">'.__('No image').'</span>';
    }

    private function formatPosition($item)
    {
        // Position is dynamic, so just display it as-is with a badge
        $position = ucfirst(str_replace('_', ' ', $item->position));

        return '<span class="badge bg-info">'.$position.'</span>';
    }

    private function statusBadge($item)
    {
        $badge = $item->status ? 'success' : 'danger';
        $text = $item->status ? __('Active') : __('Inactive');

        return '<span class="badge bg-'.$badge.'">'.$text.'</span>';
    }

    private function statsBadge($item)
    {
        return '<small class="text-muted">'.
               __('Views').': '.$item->views_count.' | '.
               __('Clicks').': '.$item->clicks_count.
               '</small>';
    }

    private function actions($item)
    {
        $html = '<div class="btn-group" role="group">';

        // if (hasPermission('update banner')) {
        $html .= '<a href="'.route('admin.banners.edit', $item->id).'" class="btn btn-sm btn-primary" title="'.__('Edit').'">
                        <i class="fas fa-edit"></i>
                      </a>';
        // }

        // if (hasPermission('delete banner')) {
        $html .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="'.$item->id.'" title="'.__('Delete').'">
                        <i class="fas fa-trash"></i>
                      </button>';
        // }

        // if (hasPermission('update banner')) {
        $statusClass = $item->status ? 'btn-warning' : 'btn-success';
        $statusIcon = $item->status ? 'fa-eye-slash' : 'fa-eye';
        $statusTitle = $item->status ? __('Deactivate') : __('Activate');

        $html .= '<button type="button" class="btn btn-sm '.$statusClass.' toggle-status-btn" data-id="'.$item->id.'" title="'.$statusTitle.'">
                        <i class="fas '.$statusIcon.'"></i>
                      </button>';
        // }

        $html .= '</div>';

        return $html;
    }

    private function trashActions($item)
    {
        $html = '<div class="btn-group" role="group">';

        $html .= '<button type="button" class="btn btn-sm btn-success restore-btn" data-id="'.$item->id.'" title="'.__('Restore').'">
                    <i class="fas fa-undo"></i>
                  </button>';

        $html .= '<button type="button" class="btn btn-sm btn-danger force-delete-btn" data-id="'.$item->id.'" title="'.__('Permanently Delete').'">
                    <i class="fas fa-trash-alt"></i>
                  </button>';

        $html .= '</div>';

        return $html;
    }

    /**
     * Get available banner positions for home page sections
     * @deprecated Use App\Constants\BannerConstants::getHomePagePositions() instead
     */
    public static function getHomePagePositions(): array
    {
        return \App\Constants\BannerConstants::getHomePagePositions();
    }
}
