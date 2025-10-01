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

	

		$levels = [
			'beginner' => 'Beginner',
			'intermediate' => 'Intermediate',
			'advanced' => 'Advanced',
			'expert' => 'Expert'
		];

	
		return view('frontend.pages.courses.index', compact(
			'courses',
			'levels',
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

		// Level filter
		if ($request->filled('level')) {
			$query->where('level', $request->level);
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
				'html' => view('frontend.pages.courses.partials.courses-grid', ['courses' => $courses])->render(),
				'pagination' => view('frontend.pages.courses.partials.pagination', ['courses' => $courses])->render(),
				'count' => $courses->total(),
				'applied_filters' => $this->getAppliedFilters($request)
			]);
		}

		return view('frontend.pages.courses.index', compact('courses'));
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

	
		
		return $filters;
	}

	/**
	 * Show course details
	 */
	public function show($id)
	{
		$course = Course::active()
			->where('status', true)
			->findOrFail($id);

		// Get related courses from the same category
		$relatedCourses = Course::active()
			->where('status', true)
			->where('id', '!=', $id)
			->limit(4)
			->get();

		// Get courses with similar level
		$similarCourses = Course::active()
			->where('status', true)
			->where('id', '!=', $id)
			->where('level', $course->level)
			->limit(4)
			->get();

		return view('frontend.pages.courses.show', compact('course', 'relatedCourses', 'similarCourses'));
	}
}