<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Admin;
use App\Notifications\Admin\NewCourseEnrollmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\HandlesFeatureLimits;

class CourseController extends Controller
{
	use HandlesFeatureLimits;

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

		// Get current page from request
		$perPage = 12;
		$currentPage = max(1, (int) $request->get('page', 1));

		// Clone query to get total count without affecting the main query
		$totalCount = (clone $query)->count();
		$lastPage = max(1, (int) ceil($totalCount / $perPage));

		// Ensure current page doesn't exceed available pages
		// If filters were applied and page is invalid, reset to page 1
		if ($currentPage > $lastPage) {
			$currentPage = 1; // Always reset to page 1 when page is invalid after filtering
		}

		// Now paginate with validated page number
		$courses = $query->paginate($perPage, ['*'], 'page', $currentPage);

		// Set paginator path to courses index route (for proper URL generation)
		$courses->setPath(route('courses'));

		// Append filter parameters to pagination URLs
		$courses->appends($request->except('page'));

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
        $course = Course::with(['links' => function($q){ $q->where('is_active', true)->orderBy('sort_order'); }])
            ->active()
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

        $enrollment = null;
        if (Auth::guard('clinic')->check()) {
            $enrollment = CourseEnrollment::where('course_id', $course->id)
                ->where('clinic_user_id', Auth::guard('clinic')->id())
                ->first();
        }

        return view('frontend.pages.courses.show', compact('course', 'relatedCourses', 'similarCourses', 'enrollment'));
    }

    /**
     * Enroll a clinic user into a course
     */
    public function enroll(Request $request, $id)
    {
        if (!Auth::guard('clinic')->check()) {
            return response()->json([
                'status' => 'error',
                'message' => __('You must be logged in as a clinic user to enroll.')
            ], 401);
        }

        $course = Course::active()->where('status', true)->findOrFail($id);
        $clinicUserId = Auth::guard('clinic')->id();

        // If already enrolled, do not consume quota
        $existingEnrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('clinic_user_id', $clinicUserId)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'status' => 'success',
                'message' => __('You are already enrolled in this course.'),
                'enrollment' => $existingEnrollment,
            ]);
        }

        $entity = $this->getAuthenticatedEntity();

        $enrollment = $this->checkFeatureLimit(
            $entity,
            'enroll_courses',
            function() use ($course, $clinicUserId) {
                return CourseEnrollment::create([
                    'course_id' => $course->id,
                    'clinic_user_id' => $clinicUserId,
                    'status' => 'approved',
                ]);
            }
        );

        // If middleware/limit returned a response, forward it
        if ($enrollment instanceof \Symfony\Component\HttpFoundation\Response) {
            return $enrollment;
        }

        // Notify admins for new enrollment
        Admin::query()->each(function ($admin) use ($enrollment) {
            $admin->notify(new NewCourseEnrollmentNotification($enrollment));
        });

        return response()->json([
            'status' => 'success',
            'message' => __('Enrollment submitted successfully and approved.'),
            'enrollment' => $enrollment
        ]);
    }
}
