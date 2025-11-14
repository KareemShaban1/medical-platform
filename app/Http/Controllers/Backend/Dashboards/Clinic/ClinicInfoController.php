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
            'clinic_email' => 'nullable|email|max:255|unique:clinics,clinic_email,' . $clinic->id,
            'clinic_website' => 'nullable|url|max:255',
            'about' => 'nullable|string',
            'services_offered' => 'nullable|array',
            'services_offered.*.name' => 'required|string|max:255',
            'services_offered.*.description' => 'nullable|string',
            'working_hours' => 'nullable|array',
            'working_hours.*.day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'working_hours.*.is_open' => 'nullable|boolean',
            'working_hours.*.open_time' => 'nullable|date_format:H:i',
            'working_hours.*.close_time' => 'nullable|date_format:H:i',
            'has_emergency' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'remove_media_ids' => 'array',
            'remove_media_ids.*' => 'integer',
        ]);

        $updateData = $request->only([
            'name', 'phone', 'address', 'governorate_id', 'city_id', 'area_id',
            'clinic_email', 'clinic_website', 'about', 'services_offered', 'working_hours'
        ]);

        $updateData['has_emergency'] = $request->has('has_emergency') ? (bool) $request->has_emergency : false;

        $clinic->update($updateData);

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

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Clinic info updated successfully')]);
        }

        return redirect()->back()->with('success', __('Clinic info updated successfully'));
    }
}
