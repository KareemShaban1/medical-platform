<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\DoctorProfile;
use App\Models\Speciality;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        // Get all approved doctors with proper ordering
        // Order: Featured first, then doctors with clinic_id, then without clinic
        $doctors = DoctorProfile::where('doctor_profiles.status', DoctorProfile::STATUS_APPROVED)
            ->with(['clinicUser.clinic', 'speciality'])
            ->leftJoin('clinic_users', 'doctor_profiles.clinic_user_id', '=', 'clinic_users.id')
            ->select('doctor_profiles.*')
            ->orderBy('doctor_profiles.is_featured', 'desc')
            ->orderByRaw('CASE WHEN clinic_users.clinic_id IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('doctor_profiles.name', 'asc')
            ->paginate(12);

        $specialities = Speciality::orderBy('name_en')->get();

        return view('frontend.pages.doctors.index', compact('doctors', 'specialities'));
    }

    public function filter(Request $request)
    {
        $query = DoctorProfile::where('doctor_profiles.status', DoctorProfile::STATUS_APPROVED)
            ->with(['clinicUser.clinic', 'speciality']);

        // Search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('doctor_profiles.name', 'like', "%{$searchTerm}%")
                    ->orWhere('doctor_profiles.email', 'like', "%{$searchTerm}%")
                    ->orWhere('doctor_profiles.bio', 'like', "%{$searchTerm}%")
                    ->orWhereHas('clinicUser', function ($qq) use ($searchTerm) {
                        $qq->where('name', 'like', "%{$searchTerm}%");
                    });
            });
        }

        // Speciality filter
        if ($request->filled('speciality_id')) {
            $query->where('speciality_id', $request->speciality_id);
        }

        // Featured filter
        if ($request->filled('featured') && $request->featured == '1') {
            $query->where('is_featured', true);
        }

        // Clinic filter (doctors with clinic or without)
        if ($request->filled('has_clinic')) {
            if ($request->has_clinic == '1') {
                $query->whereHas('clinicUser', function($q) {
                    $q->whereNotNull('clinic_id');
                });
            } else {
                $query->whereHas('clinicUser', function($q) {
                    $q->whereNull('clinic_id');
                });
            }
        }

        // Location filters
        if ($request->filled('governorate_id')) {
            $query->whereHas('clinicUser.clinic', function($q) use ($request) {
                $q->where('governorate_id', $request->governorate_id);
            });
        }
        if ($request->filled('city_id')) {
            $query->whereHas('clinicUser.clinic', function($q) use ($request) {
                $q->where('city_id', $request->city_id);
            });
        }
        if ($request->filled('area_id')) {
            $query->whereHas('clinicUser.clinic', function($q) use ($request) {
                $q->where('area_id', $request->area_id);
            });
        }

        // Join with clinic_users for ordering
        $query->leftJoin('clinic_users', 'doctor_profiles.clinic_user_id', '=', 'clinic_users.id')
            ->select('doctor_profiles.*');

        // Sort options
        switch ($request->get('sort', 'featured')) {
            case 'featured':
                // Featured first, then by clinic status, then name
                $query->orderBy('doctor_profiles.is_featured', 'desc')
                    ->orderByRaw('CASE WHEN clinic_users.clinic_id IS NOT NULL THEN 0 ELSE 1 END')
                    ->orderBy('doctor_profiles.name', 'asc');
                break;
            case 'name':
                $query->orderBy('doctor_profiles.name', 'asc');
                break;
            case 'newest':
                $query->orderBy('doctor_profiles.created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('doctor_profiles.created_at', 'asc');
                break;
            case 'experience':
                $query->orderBy('doctor_profiles.years_experience', 'desc');
                break;
            default:
                $query->orderBy('doctor_profiles.is_featured', 'desc')
                    ->orderByRaw('CASE WHEN clinic_users.clinic_id IS NOT NULL THEN 0 ELSE 1 END')
                    ->orderBy('doctor_profiles.name', 'asc');
        }

        $doctors = $query->paginate(12);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('frontend.pages.doctors.partials.doctors-grid', ['doctors' => $doctors])->render(),
                'pagination' => view('frontend.pages.doctors.partials.pagination', ['doctors' => $doctors])->render(),
                'count' => $doctors->total(),
                'applied_filters' => $this->getAppliedFilters($request)
            ]);
        }

        $specialities = Speciality::orderBy('name_en')->get();
        return view('frontend.pages.doctors.index', compact('doctors', 'specialities'));
    }

    private function getAppliedFilters(Request $request)
    {
        $filters = [];

        if ($request->filled('search')) {
            $filters[] = [
                'label' => 'Search',
                'value' => $request->search,
                'type' => 'search'
            ];
        }

        if ($request->filled('speciality_id')) {
            $speciality = \App\Models\Speciality::find($request->speciality_id);
            if ($speciality) {
                $filters[] = [
                    'label' => 'Speciality',
                    'value' => $speciality->name_en,
                    'type' => 'speciality'
                ];
            }
        }

        if ($request->filled('featured') && $request->featured == '1') {
            $filters[] = [
                'label' => 'Featured',
                'value' => 'Yes',
                'type' => 'featured'
            ];
        }

        if ($request->filled('has_clinic')) {
            $filters[] = [
                'label' => 'Type',
                'value' => $request->has_clinic == '1' ? 'With Clinic' : 'Standalone',
                'type' => 'has_clinic'
            ];
        }

        return $filters;
    }
}