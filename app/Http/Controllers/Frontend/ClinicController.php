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

		// Get filter options
		$specializations = [
			'general' => 'General Practice',
			'cardiology' => 'Cardiology',
			'dermatology' => 'Dermatology',
			'orthopedics' => 'Orthopedics',
			'pediatrics' => 'Pediatrics',
			'neurology' => 'Neurology',
			'oncology' => 'Oncology',
			'gynecology' => 'Gynecology',
			'psychiatry' => 'Psychiatry',
			'ophthalmology' => 'Ophthalmology'
		];

		$locations = [
			'downtown' => 'Downtown',
			'suburbs' => 'Suburbs',
			'medical-district' => 'Medical District',
			'university-area' => 'University Area',
			'business-district' => 'Business District'
		];

		$ratings = [
			'5' => '5 Stars',
			'4' => '4+ Stars',
			'3' => '3+ Stars',
			'2' => '2+ Stars',
			'1' => '1+ Stars'
		];

		return view('frontend.pages.clinics', compact(
			'clinics',
			'specializations',
			'locations',
			'ratings'
		));
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

		// Specialization filter
		if ($request->filled('specialization')) {
			$query->where('specialization', $request->specialization);
		}

		// Location filter
		if ($request->filled('location')) {
			$query->where('location', $request->location);
		}

		// Rating filter
		if ($request->filled('rating')) {
			$minRating = (float) $request->rating;
			$query->where('rating', '>=', $minRating);
		}

		// Status filter
		if ($request->filled('status')) {
			$query->where('status', $request->status === 'open' ? true : false);
		}

		// Sort options
		switch ($request->get('sort', 'name')) {
			case 'name':
				$query->orderBy('name', 'asc');
				break;
			case 'rating':
				$query->orderBy('rating', 'desc');
				break;
			case 'newest':
				$query->orderBy('created_at', 'desc');
				break;
			case 'oldest':
				$query->orderBy('created_at', 'asc');
				break;
			case 'location':
				$query->orderBy('address', 'asc');
				break;
			default:
				$query->orderBy('name', 'asc');
		}

		$clinics = $query->paginate(12);

		if ($request->ajax()) {
			return response()->json([
				'success' => true,
				'html' => view('frontend.partials.clinics-grid', ['clinics' => $clinics])->render(),
				'pagination' => $clinics->links()->toHtml(),
				'count' => $clinics->total(),
				'applied_filters' => $this->getAppliedFilters($request)
			]);
		}

		return view('frontend.pages.clinics', compact('clinics'));
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

		if ($request->filled('specialization')) {
			$specializations = [
				'general' => 'General Practice',
				'cardiology' => 'Cardiology',
				'dermatology' => 'Dermatology',
				'orthopedics' => 'Orthopedics',
				'pediatrics' => 'Pediatrics',
				'neurology' => 'Neurology',
				'oncology' => 'Oncology',
				'gynecology' => 'Gynecology',
				'psychiatry' => 'Psychiatry',
				'ophthalmology' => 'Ophthalmology'
			];
			$filters[] = [
				'label' => 'Specialization',
				'value' => $specializations[$request->specialization] ?? $request->specialization,
				'type' => 'specialization'
			];
		}

		if ($request->filled('location')) {
			$locations = [
				'downtown' => 'Downtown',
				'suburbs' => 'Suburbs',
				'medical-district' => 'Medical District',
				'university-area' => 'University Area',
				'business-district' => 'Business District'
			];
			$filters[] = [
				'label' => 'Location',
				'value' => $locations[$request->location] ?? $request->location,
				'type' => 'location'
			];
		}

		if ($request->filled('rating')) {
			$filters[] = [
				'label' => 'Minimum Rating',
				'value' => $request->rating . '+ Stars',
				'type' => 'rating'
			];
		}

		if ($request->filled('status')) {
			$filters[] = [
				'label' => 'Status',
				'value' => $request->status === 'open' ? 'Open' : 'Closed',
				'type' => 'status'
			];
		}

		return $filters;
	}
}
