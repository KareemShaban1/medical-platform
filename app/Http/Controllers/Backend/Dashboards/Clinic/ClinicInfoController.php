<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ClinicInfoController extends Controller
{
    public function index()
    {
        $clinic = auth('clinic')->user()->clinic;
        return view('backend.dashboards.clinic.pages.clinic-info.index', compact('clinic'));
    }

    public function update(Request $request)
    {
        $clinic = auth('clinic')->user()->clinic;

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string',
            'governorate_id' => 'required|exists:governorates,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'remove_media_ids' => 'array',
            'remove_media_ids.*' => 'integer',
        ]);

        $clinic->update($request->only(['name', 'phone', 'address', 'governorate_id', 'city_id', 'area_id']));

        // Handle image deletions
        $removeIds = collect($request->input('remove_media_ids', []))->filter();
        if ($removeIds->isNotEmpty()) {
            $mediaItems = Media::whereIn('id', $removeIds)->where('model_id', $clinic->id)->where('model_type', get_class($clinic))->get();
            foreach ($mediaItems as $media) {
                $media->delete();
            }
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($image) {
                    $clinic->addMedia($image)->toMediaCollection('clinic_images');
                }
            }
        }

        // Ensure at least one image exists
        if ($clinic->getMedia('clinic_images')->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => __('At least one image is required.'),
                'errors' => ['images' => [__('At least one image is required.')]],
            ], 422);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Clinic info updated successfully')]);
        }

        return redirect()->back()->with('success', __('Clinic info updated successfully'));
    }
}
