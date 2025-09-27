<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Clinic;

class JobController extends Controller
{
	public function index(Request $request)
	{
		$jobs = Job::with('clinic')
			->active()
			->approved()
			->orderBy('created_at', 'desc')
			->paginate(12);

		// Get filter options for the form
		$jobTypes = ['full-time', 'part-time', 'contract', 'temporary', 'internship'];
		$specializations = ['nursing', 'physician', 'technician', 'therapist', 'administrative', 'pharmacy'];
		$experienceLevels = ['entry', 'mid', 'senior'];
		$locations = Job::distinct()->pluck('location')->filter()->values();
		$salaryRanges = ['30-50', '50-80', '80+'];

		return view('frontend.pages.jobs', compact(
			'jobs',
			'jobTypes',
			'specializations',
			'experienceLevels',
			'locations',
			'salaryRanges'
		));
	}

	public function filter(Request $request)
	{
		try {
			$query = Job::with('clinic')
				->active()
				->approved();

			// Search filter
			if ($request->filled('search')) {
				$searchTerm = trim($request->search);
				$query->where(function ($q) use ($searchTerm) {
					$q->where('title', 'like', "%{$searchTerm}%")
						->orWhere('description', 'like', "%{$searchTerm}%")
						->orWhereHas('clinic', function ($clinicQuery) use ($searchTerm) {
							$clinicQuery->where('name', 'like', "%{$searchTerm}%");
						});
				});
			}

			// Job type filter
			if ($request->filled('job_type')) {
				$query->where('job_type', $request->get('job_type'));
			}

			// Specialization filter
			if ($request->filled('specialization')) {
				$query->where('specialization', $request->get('specialization'));
			}

			// Experience level filter
			if ($request->filled('experience')) {
				$query->where('experience_level', $request->get('experience'));
			}

			// Location filter
			if ($request->filled('location')) {
				$query->where('location', 'like', "%{$request->get('location')}%");
			}

			// Salary range filter
			if ($request->filled('salary')) {
				$salaryRange = $request->get('salary');
				if ($salaryRange === '30-50') {
					$query->whereBetween('salary', [30000, 50000]);
				} elseif ($salaryRange === '50-80') {
					$query->whereBetween('salary', [50000, 80000]);
				} elseif ($salaryRange === '80+') {
					$query->where('salary', '>=', 80000);
				}
			}

			// Sort by
			$sortBy = $request->get('sort', 'newest');
			switch ($sortBy) {
				case 'salary':
					$query->orderBy('salary', 'desc');
					break;
				case 'title':
					$query->orderBy('title', 'asc');
					break;
				case 'company':
					$query->orderBy('clinic_id', 'asc');
					break;
				case 'newest':
				default:
					$query->orderBy('created_at', 'desc');
					break;
			}

			// Pagination
			$jobs = $query->paginate(12);

			// Add additional data for response
			$additionalData = [
				'filters_applied' => $this->getAppliedFilters($request),
				'total_jobs' => Job::active()->approved()->count(),
				'filtered_count' => $jobs->total()
			];

			if ($request->ajax()) {
				return response()->json([
					'success' => true,
					'html' => view('frontend.partials.jobs-grid', compact('jobs'))->render(),
					'pagination' => $jobs->links()->toHtml(),
					'count' => $jobs->total(),
					'data' => $additionalData
				]);
			}

			return view('frontend.pages.jobs', compact('jobs', 'additionalData'));
		} catch (\Exception $e) {
			if ($request->ajax()) {
				return response()->json([
					'success' => false,
					'message' => 'Error filtering jobs: ' . $e->getMessage()
				], 500);
			}

			return redirect()->back()->with('error', 'Error filtering jobs. Please try again.');
		}
	}

	/**
	 * Get the list of applied filters for display
	 */
	private function getAppliedFilters(Request $request)
	{
		$filters = [];

		if ($request->filled('search')) {
			$filters['search'] = $request->search;
		}

		if ($request->filled('job_type')) {
			$filters['job_type'] = ucfirst(str_replace('-', ' ', $request->job_type));
		}

		if ($request->filled('specialization')) {
			$filters['specialization'] = ucfirst($request->specialization);
		}

		if ($request->filled('experience')) {
			$filters['experience'] = ucfirst($request->experience) . ' Level';
		}

		if ($request->filled('location')) {
			$filters['location'] = $request->location;
		}

		if ($request->filled('salary')) {
			$filters['salary'] = '$' . $request->salary . 'k' . ($request->salary === '80+' ? '+' : '');
		}

		if ($request->filled('sort')) {
			$sortLabels = [
				'newest' => 'Newest First',
				'salary' => 'Highest Salary',
				'title' => 'Job Title A-Z',
				'company' => 'Company A-Z'
			];
			$filters['sort'] = $sortLabels[$request->sort] ?? $request->sort;
		}

		return $filters;
	}
}
