<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
	public function index(Request $request)
	{
		// Get initial clinics with pagination (exclude rental space companies)
		$clinics = Clinic::approved()
			->where('status', true)
			->notRentalSpaceCompany()
			->with(['approvement'])
			->paginate(12);

		return view('frontend.pages.clinics.index', compact('clinics'));
	}

	public function filter(Request $request)
	{
		$query = Clinic::approved()
			->where('status', true)
			->notRentalSpaceCompany()
			->with(['approvement']);

		// Search filter
		if ($request->filled('search')) {
			$searchTerm = $request->search;
			$query->where(function ($q) use ($searchTerm) {
				$q->where('name', 'like', "%{$searchTerm}%")
					->orWhere('address', 'like', "%{$searchTerm}%");
			});
		}



		// Sort options
		switch ($request->get('sort', 'name')) {
			case 'name':
				$query->orderBy('name', 'asc');
				break;
			case 'newest':
				$query->orderBy('created_at', 'desc');
				break;
			case 'oldest':
				$query->orderBy('created_at', 'asc');
				break;
			default:
				$query->orderBy('name', 'asc');
		}
		// Location filters
		if ($request->filled('governorate_id')) {
			$query->where('governorate_id', $request->governorate_id);
		}
		if ($request->filled('city_id')) {
			$query->where('city_id', $request->city_id);
		}
		if ($request->filled('area_id')) {
			$query->where('area_id', $request->area_id);
		}

		// Get current page from request
		$currentPage = $request->get('page', 1);

		$clinics = $query->paginate(12, ['*'], 'page', $currentPage);

		// Set paginator path to clinics index route (for proper URL generation)
		$clinics->setPath(route('clinics'));

		// Append filter parameters to pagination URLs
		$clinics->appends($request->except('page'));

		if ($request->ajax()) {
			return response()->json([
				'success' => true,
				'html' => view('frontend.pages.clinics.partials.clinics-grid', ['clinics' => $clinics])->render(),
				'pagination' => view('frontend.pages.clinics.partials.pagination', ['clinics' => $clinics])->render(),
				'count' => $clinics->total(),
				'applied_filters' => $this->getAppliedFilters($request)
			]);
		}

		return view('frontend.pages.clinics.index', compact('clinics'));
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



		return $filters;
	}

	/**
	 * Show clinic details
	 */
	public function show($id)
	{
		$query = Clinic::approved()
			->where('status', true)
			->notRentalSpaceCompany()
			->with(['approvement']);

		$clinic = null;
		if (!is_numeric($id)) {
			$clinic = (clone $query)->where('slug', $id)->first();
		}
		if (!$clinic) {
			$clinic = $query->findOrFail($id);
		}

		// Get doctor profiles for this clinic
		$doctors = \App\Models\DoctorProfile::where('status', \App\Models\DoctorProfile::STATUS_APPROVED)
			->whereHas('clinicUser', function ($q) use ($clinic) {
				$q->where('clinic_id', $clinic->id);
			})
			->with(['speciality', 'clinicUser'])
			->orderBy('is_featured', 'desc')
			->orderBy('name')
			->get();

		// Get related clinics with same specialization
		$relatedClinics = Clinic::approved()
			->where('status', true)
			->notRentalSpaceCompany()
			->where('id', '!=', $clinic->id)
			->limit(4)
			->get();

		// Get nearby clinics
		$nearbyClinics = Clinic::approved()
			->where('status', true)
			->notRentalSpaceCompany()
			->where('id', '!=', $clinic->id)
			->limit(4)
			->get();

		return view('frontend.pages.clinics.show', compact('clinic', 'doctors', 'relatedClinics', 'nearbyClinics'));
	}
}
