<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carousel;
use Illuminate\Http\Request;

class CarouselController extends Controller
{
    /**
     * Display the carousel management page
     */
    public function index()
    {
        $carousels = Carousel::ordered()->get();
        return view('backend.dashboards.admin.pages.carousels.index', compact('carousels'));
    }

    /**
     * Store a new carousel image
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'boolean',
        ]);

        $carousel = Carousel::create([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => Carousel::max('sort_order') + 1,
        ]);

        if ($request->hasFile('image')) {
            $carousel->addMediaFromRequest('image')
                ->toMediaCollection('carousel_image');
        }

        return response()->json([
            'status' => 'success',
            'message' => __('Carousel image uploaded successfully'),
            'carousel' => $carousel->fresh(),
        ]);
    }

    /**
     * Update a carousel item
     */
    public function update(Request $request, $id)
    {
        $carousel = Carousel::findOrFail($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_active' => 'boolean',
        ]);

        $carousel->update([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->hasFile('image')) {
            $carousel->clearMediaCollection('carousel_image');
            $carousel->addMediaFromRequest('image')
                ->toMediaCollection('carousel_image');
        }

        return response()->json([
            'status' => 'success',
            'message' => __('Carousel updated successfully'),
            'carousel' => $carousel->fresh(),
        ]);
    }

    /**
     * Toggle carousel active status
     */
    public function toggleStatus($id)
    {
        $carousel = Carousel::findOrFail($id);
        $carousel->update(['is_active' => !$carousel->is_active]);

        return response()->json([
            'status' => 'success',
            'message' => __('Carousel status updated successfully'),
            'is_active' => $carousel->is_active,
        ]);
    }

    /**
     * Update sort order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:carousels,id',
        ]);

        foreach ($request->order as $index => $id) {
            Carousel::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json([
            'status' => 'success',
            'message' => __('Carousel order updated successfully'),
        ]);
    }

    /**
     * Delete a carousel image
     */
    public function destroy($id)
    {
        // Ensure at least one carousel remains
        if (Carousel::count() <= 1) {
            return response()->json([
                'status' => 'error',
                'message' => __('At least one carousel image must exist'),
            ], 422);
        }

        $carousel = Carousel::findOrFail($id);
        $carousel->clearMediaCollection('carousel_image');
        $carousel->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('Carousel deleted successfully'),
        ]);
    }

    /**
     * Get carousel images for frontend (API)
     */
    public function getForFrontend()
    {
        $carousels = Carousel::active()
            ->ordered()
            ->get()
            ->map(function ($carousel) {
                return [
                    'id' => $carousel->id,
                    'title' => $carousel->title,
                    'description' => $carousel->description,
                    'image_url' => $carousel->image_url,
                ];
            });

        return response()->json($carousels);
    }
}
