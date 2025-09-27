<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
	public function index(Request $request)
	{
		// Get initial courses with pagination
		$courses = Course::active()
			->where('status', true)
			->paginate(12);

		// Get filter options
		$categories = [
			'clinical' => 'Clinical Medicine',
			'surgery' => 'Surgery',
			'nursing' => 'Nursing',
			'pharmacy' => 'Pharmacy',
			'emergency' => 'Emergency Medicine',
			'pediatrics' => 'Pediatrics',
			'cardiology' => 'Cardiology',
			'dermatology' => 'Dermatology',
			'orthopedics' => 'Orthopedics',
			'neurology' => 'Neurology',
			'oncology' => 'Oncology',
			'radiology' => 'Radiology'
		];

		$levels = [
			'beginner' => 'Beginner',
			'intermediate' => 'Intermediate',
			'advanced' => 'Advanced',
			'expert' => 'Expert'
		];

		$durations = [
			'1' => '1-2 weeks',
			'2' => '2-4 weeks',
			'3' => '1-3 months',
			'6' => '3-6 months',
			'12' => '6+ months'
		];

		$priceRanges = [
			'0' => 'Free',
			'100' => 'Under $100',
			'500' => 'Under $500',
			'1000' => 'Under $1000',
			'2000' => 'Under $2000'
		];

		return view('frontend.pages.courses', compact(
			'courses',
			'categories',
			'levels',
			'durations',
			'priceRanges'
		));
	}

	public function filter(Request $request)
	{
		$query = Course::active()
			->where('status', true);

		// Search filter
		if ($request->filled('search')) {
			$searchTerm = $request->search;
			$query->where(function ($q) use ($searchTerm) {
				$q->where('title_en', 'like', "%{$searchTerm}%")
					->orWhere('title_ar', 'like', "%{$searchTerm}%")
					->orWhere('description_en', 'like', "%{$searchTerm}%")
					->orWhere('description_ar', 'like', "%{$searchTerm}%");
			});
		}

		// Category filter
		if ($request->filled('category')) {
			$query->where('category', $request->category);
		}

		// Level filter
		if ($request->filled('level')) {
			$query->where('level', $request->level);
		}

		// Duration filter
		if ($request->filled('duration')) {
			$duration = (int) $request->duration;
			switch ($duration) {
				case 1:
					$query->whereBetween('duration', [1, 14]);
					break;
				case 2:
					$query->whereBetween('duration', [15, 28]);
					break;
				case 3:
					$query->whereBetween('duration', [29, 90]);
					break;
				case 6:
					$query->whereBetween('duration', [91, 180]);
					break;
				case 12:
					$query->where('duration', '>', 180);
					break;
			}
		}

		// Price filter
		if ($request->filled('price')) {
			$maxPrice = (int) $request->price;
			if ($maxPrice > 0) {
				$query->where('price', '<=', $maxPrice);
			} else {
				$query->where('price', 0);
			}
		}

		// Date filter
		if ($request->filled('date')) {
			$dateFilter = $request->date;
			switch ($dateFilter) {
				case 'upcoming':
					$query->where('start_date', '>', now());
					break;
				case 'ongoing':
					$query->where('start_date', '<=', now())
						->where('end_date', '>=', now());
					break;
				case 'completed':
					$query->where('end_date', '<', now());
					break;
			}
		}

		// Language filter
		if ($request->filled('language')) {
			$query->where('language', $request->language);
		}

		// Sort options
		switch ($request->get('sort', 'newest')) {
			case 'newest':
				$query->orderBy('created_at', 'desc');
				break;
			case 'oldest':
				$query->orderBy('created_at', 'asc');
				break;
			case 'title':
				$query->orderBy('title_en', 'asc');
				break;
			case 'duration':
				$query->orderBy('duration', 'asc');
				break;
			case 'price_low':
				$query->orderBy('price', 'asc');
				break;
			case 'price_high':
				$query->orderBy('price', 'desc');
				break;
			case 'start_date':
				$query->orderBy('start_date', 'asc');
				break;
			default:
				$query->orderBy('created_at', 'desc');
		}

		$courses = $query->paginate(12);

		if ($request->ajax()) {
			return response()->json([
				'success' => true,
				'html' => view('frontend.partials.courses-grid', ['courses' => $courses])->render(),
				'pagination' => $courses->links()->toHtml(),
				'count' => $courses->total(),
				'applied_filters' => $this->getAppliedFilters($request)
			]);
		}

		return view('frontend.pages.courses', compact('courses'));
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

		if ($request->filled('category')) {
			$categories = [
				'clinical' => 'Clinical Medicine',
				'surgery' => 'Surgery',
				'nursing' => 'Nursing',
				'pharmacy' => 'Pharmacy',
				'emergency' => 'Emergency Medicine',
				'pediatrics' => 'Pediatrics',
				'cardiology' => 'Cardiology',
				'dermatology' => 'Dermatology',
				'orthopedics' => 'Orthopedics',
				'neurology' => 'Neurology',
				'oncology' => 'Oncology',
				'radiology' => 'Radiology'
			];
			$filters[] = [
				'label' => 'Category',
				'value' => $categories[$request->category] ?? $request->category,
				'type' => 'category'
			];
		}

		if ($request->filled('level')) {
			$levels = [
				'beginner' => 'Beginner',
				'intermediate' => 'Intermediate',
				'advanced' => 'Advanced',
				'expert' => 'Expert'
			];
			$filters[] = [
				'label' => 'Level',
				'value' => $levels[$request->level] ?? $request->level,
				'type' => 'level'
			];
		}

		if ($request->filled('duration')) {
			$durations = [
				'1' => '1-2 weeks',
				'2' => '2-4 weeks',
				'3' => '1-3 months',
				'6' => '3-6 months',
				'12' => '6+ months'
			];
			$filters[] = [
				'label' => 'Duration',
				'value' => $durations[$request->duration] ?? $request->duration,
				'type' => 'duration'
			];
		}

		if ($request->filled('price')) {
			$priceRanges = [
				'0' => 'Free',
				'100' => 'Under $100',
				'500' => 'Under $500',
				'1000' => 'Under $1000',
				'2000' => 'Under $2000'
			];
			$filters[] = [
				'label' => 'Price Range',
				'value' => $priceRanges[$request->price] ?? $request->price,
				'type' => 'price'
			];
		}

		if ($request->filled('date')) {
			$dateFilters = [
				'upcoming' => 'Upcoming',
				'ongoing' => 'Ongoing',
				'completed' => 'Completed'
			];
			$filters[] = [
				'label' => 'Date',
				'value' => $dateFilters[$request->date] ?? $request->date,
				'type' => 'date'
			];
		}

		if ($request->filled('language')) {
			$filters[] = [
				'label' => 'Language',
				'value' => ucfirst($request->language),
				'type' => 'language'
			];
		}

		return $filters;
	}
}
