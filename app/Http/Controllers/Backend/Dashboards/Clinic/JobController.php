<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Models\Job;
use App\Http\Requests\Clinic\Job\StoreJobRequest;
use App\Http\Requests\Clinic\Job\UpdateJobRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\Clinic\JobRepositoryInterface;

class JobController extends Controller
{
    protected $jobRepo;

    public function __construct(JobRepositoryInterface $jobRepo)
    {
        $this->jobRepo = $jobRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view jobs'), 403, __('You are not authorized to view jobs'));

        return view('backend.dashboards.clinic.pages.jobs.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view jobs'), 403, __('You are not authorized to view jobs'));

        return $this->jobRepo->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // apply permissions
        abort_if(!hasPermission('create job'), 403, __('You are not authorized to create job'));

        return view('backend.dashboards.clinic.pages.jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobRequest $request)
    {
        // apply permissions
        abort_if(!hasPermission('create job'), 403, __('You are not authorized to create job'));

        return $this->jobRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view job'), 403, __('You are not authorized to view job'));

        $job = $this->jobRepo->show($id);

        return request()->ajax()
            ? response()->json($job)
            : view('backend.dashboards.clinic.pages.jobs.show', compact('job'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // apply permissions
        abort_if(!hasPermission('update job'), 403, __('You are not authorized to update job'));

        $job = $this->jobRepo->show($id);

        return view('backend.dashboards.clinic.pages.jobs.edit', compact('job'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update job'), 403, __('You are not authorized to update job'));

        return $this->jobRepo->update($request, $id);
    }

    public function updateStatus(Request $request)
    {
        return $this->jobRepo->updateStatus($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete job'), 403, __('You are not authorized to delete job'));

        return $this->jobRepo->destroy($id);
    }

    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash jobs'), 403, __('You are not authorized to view trash jobs'));

        return view('backend.dashboards.clinic.pages.jobs.trash');
    }

    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash jobs'), 403, __('You are not authorized to view trash jobs'));

        return $this->jobRepo->trashData();
    }

    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore job'), 403, __('You are not authorized to restore job'));

        return $this->jobRepo->restore($id);
    }

    public function forceDelete($id)
    {
           // apply permissions
           abort_if(!hasPermission('force delete job'), 403, __('You are not authorized to force delete job'));

        return $this->jobRepo->forceDelete($id);
    }

	/**
	 * Display job applicants for a specific job.
	 */
	public function applicants($id)
	{
        // apply permissions
        abort_if(!hasPermission('view job applicants'), 403, __('You are not authorized to view job applicants'));

		$job = $this->jobRepo->show($id);
		$applicants = $this->jobRepo->getApplicants($id);

		return view('backend.dashboards.clinic.pages.jobs.applicants', compact('job', 'applicants'));
	}

	/**
	 * Update job application status.
	 */
	public function updateApplicationStatus(Request $request)
	{
        // apply permissions
        abort_if(!hasPermission('update job application status'), 403, __('You are not authorized to update job application status'));

		return $this->jobRepo->updateApplicationStatus($request);
	}

	/**
	 * Get application details for modal.
	 */
	public function getApplicationDetails($applicationId)
	{
               // apply permissions
               abort_if(!hasPermission('view job application details'), 403, __('You are not authorized to view job application details'));


		$application = $this->jobRepo->getApplicationDetails($applicationId);

		if (!$application) {
			return response()->json([
				'status' => 'error',
				'message' => __('Application not found'),
			], 404);
		}

		$html = view('backend.dashboards.clinic.pages.jobs.partials.application-details', compact('application'))->render();

		return response()->json([
			'status' => 'success',
			'html' => $html,
		]);
	}

	/**
	 * Update application notes and data.
	 */
	public function updateApplicationData(Request $request, $applicationId)
	{
               // apply permissions
               abort_if(!hasPermission('update job application data'), 403, __('You are not authorized to update job application data'));

		return $this->jobRepo->updateApplicationData($request, $applicationId);
	}
}