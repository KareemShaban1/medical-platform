<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
	public function index(Request $request)
	{
		// Get initial clinics with pagination
		$clinics = Clinic::approved()
			->where('status', true)
			->with(['approvement'])
			->paginate(12);

		return view('frontend.pages.clinics.index', compact('clinics'));
	}

	public function filter(Request $request)
	{
		$query = Clinic::approved()
			->where('status', true)
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

		$clinics = $query->paginate(12);

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
		$clinic = Clinic::approved()
			->where('status', true)
			->with(['approvement'])
			->findOrFail($id);

		// Get related clinics with same specialization
		$relatedClinics = Clinic::approved()
			->where('status', true)
			->where('id', '!=', $id)
			->limit(4)
			->get();

		// Get nearby clinics
		$nearbyClinics = Clinic::approved()
			->where('status', true)
			->where('id', '!=', $id)
			->limit(4)
			->get();

		return view('frontend.pages.clinics.show', compact('clinic', 'relatedClinics', 'nearbyClinics'));
	}
}
