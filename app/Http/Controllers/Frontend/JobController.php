<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Clinic;
use App\Models\JobApplicationField;
use App\Models\JobApplication;

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

		return view('frontend.pages.jobs.index', compact(
			'jobs',
			'jobTypes',
			'specializations',
			'experienceLevels',
			'locations',
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
			if ($request->filled('type')) {
				$query->where('type', $request->get('type'));
			}

			// Specialization filter
			if ($request->filled('specialization')) {
				$query->where('specialization', $request->get('specialization'));
			}

			// Experience level filter
			if ($request->filled('experience')) {
				$query->where('experience_level', $request->get('experience'));
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
					'html' => view('frontend.pages.jobs.partials.jobs-grid', compact('jobs'))->render(),
					'pagination' => view('frontend.pages.jobs.partials.pagination', compact('jobs'))->render(),
					'count' => $jobs->total(),
					'data' => $additionalData
				]);
			}

			return view('frontend.pages.jobs.index', compact('jobs', 'additionalData'));
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

		if ($request->filled('type')) {
			$filters['type'] = ucfirst(str_replace('-', ' ', $request->type));
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

	/**
	 * Show job details
	 */
	public function show($id)
	{
		$job = Job::with(['clinic'])
			->active()
			->approved()
			->findOrFail($id);

		// Get related jobs from the same clinic
		$relatedJobs = Job::with('clinic')
			->active()
			->approved()
			->where('id', '!=', $id)
			->where('clinic_id', $job->clinic_id)
			->limit(4)
			->get();

		// Get jobs with similar specialization
		$similarJobs = Job::with('clinic')
			->active()
			->approved()
			->where('id', '!=', $id)
			// ->where('specialization', $job->specialization)
			->limit(4)
			->get();

		return view('frontend.pages.jobs.show', compact('job', 'relatedJobs', 'similarJobs'));
	}

    /**
     * Show job application form
     */
    public function application($id)
    {
        $job = Job::with(['clinic', 'jobApplicationFields'])
            ->active()
            ->approved()
            ->findOrFail($id);

        // Get application fields ordered by their order field
        $applicationFields = $job->jobApplicationFields()
            ->orderBy('order')
            ->get();

        return view('frontend.pages.jobs.job-application', compact('job', 'applicationFields'));
    }

    /**
     * Submit job application
     */
    public function submitApplication(Request $request, $id)
    {
        try {
            $job = Job::with('jobApplicationFields')
                ->active()
                ->approved()
                ->findOrFail($id);

            // Get application fields for this job
            $applicationFields = $job->jobApplicationFields()
                ->orderBy('order')
                ->get();

            // Build validation rules for dynamic fields only
            $validationRules = [];

            // Add dynamic field validation
            foreach ($applicationFields as $field) {
                $fieldName = $field->field_name;
                $rules = [];

                if ($field->required) {
                    $rules[] = 'required';
                } else {
                    $rules[] = 'nullable';
                }

                // Add type-specific validation
                switch ($field->field_type) {
                    case 'email':
                        $rules[] = 'email';
                        break;
                    case 'phone':
                        $rules[] = 'string';
                        break;
                    case 'file':
                        $rules[] = 'file';
                        $rules[] = 'mimes:pdf,doc,docx,jpg,jpeg,png';
                        $rules[] = 'max:2048';
                        break;
                    default:
                        $rules[] = 'string';
                        break;
                }

                $validationRules[$fieldName] = $rules;
            }

            // Validate dynamic fields only
            $request->validate($validationRules);

            // Prepare applicant data array with only dynamic fields
            $applicantData = [];

            // Handle dynamic fields
            foreach ($applicationFields as $field) {
                $fieldName = $field->field_name;
                $value = $request->input($fieldName);

                if ($field->field_type === 'file' && $request->hasFile($fieldName)) {
                    // Store file and get path
                    $file = $request->file($fieldName);
                    $fileName = time() . '_' . $fieldName . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('job-applications', $fileName, 'public');
                    $applicantData[$fieldName] = $filePath;
                } else {
                    $applicantData[$fieldName] = $value;
                }
            }

            // Store application data in database
            $jobApplication = JobApplication::create([
                'job_id' => $id,
                'applicant_data' => $applicantData,
                'status' => 'pending'
            ]);

            \Log::info('Job Application Submitted:', [
                'application_id' => $jobApplication->id,
                'job_id' => $id,
                'job_title' => $job->title,
                'applicant_data' => $applicantData,
                'submitted_at' => now()
            ]);

            return redirect()->route('jobs.show', $id)
                ->with('success', 'Your application has been submitted successfully! We will contact you soon.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Job Application Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while submitting your application. Please try again.')
                ->withInput();
        }
    }
}