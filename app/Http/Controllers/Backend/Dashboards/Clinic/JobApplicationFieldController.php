<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Models\JobApplicationFields;
use App\Http\Requests\Clinic\Job\StoreJobApplicationFieldRequest;
use App\Http\Requests\Clinic\Job\UpdateJobApplicationFieldRequest;
use App\Http\Controllers\Controller;
use App\Interfaces\Clinic\JobApplicationFieldRepositoryInterface;
use App\Models\Job;
use Illuminate\Http\Request;
use App\Models\JobApplicationField;

class JobApplicationFieldController extends Controller
{
    protected $jobApplicationFieldRepo;

    public function __construct(JobApplicationFieldRepositoryInterface $jobApplicationFieldRepo)
    {
        $this->jobApplicationFieldRepo = $jobApplicationFieldRepo;
    }

    public function show($id)
    {
        $jobApplicationField = $this->jobApplicationFieldRepo->show($id);
        return request()->ajax()
            ? response()->json($jobApplicationField)
            : view('backend.dashboards.clinic.pages.job-application-fields.show', compact('jobApplicationField'));
    }

    public function create($id)
    {
        $job = Job::find($id);

        if (!$job) {
            return redirect()->route('clinic.jobs.index')
                ->with('error', 'Job not found.');
        }

        return view('backend.dashboards.clinic.pages.job-application-fields.create', compact('job'));
    }

    public function store(StoreJobApplicationFieldRequest $request)
    {
        try {
            \Log::info('Job Application Field Controller Store:', [
                'request_data' => $request->all(),
                'is_ajax' => $request->ajax()
            ]);

            $result = $this->jobApplicationFieldRepo->store($request);

            if ($request->ajax()) {
                return $result;
            }

            // Check if the result indicates an error
            if (isset($result->getData()->success) && !$result->getData()->success) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result->getData()->message);
            }

            \Log::info('Redirecting to jobs index after successful creation');
            return redirect()->route('clinic.jobs.index')
                ->with('success', 'Job application fields created successfully.');
        } catch (\Exception $e) {
            \Log::error('Job Application Field Controller Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the field.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the field: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $job = Job::with('jobApplicationFields')->findOrFail($id);
        return view('backend.dashboards.clinic.pages.job-application-fields.edit', compact('job'));
    }

    public function update(UpdateJobApplicationFieldRequest $request, $id)
    {
        try {
            \Log::info('Job Application Field Controller Update:', [
                'request_data' => $request->all(),
                'job_id' => $id,
                'is_ajax' => $request->ajax()
            ]);

            $result = $this->jobApplicationFieldRepo->update($request, $id);

            if ($request->ajax()) {
                return $result;
            }

            // Check if the result indicates an error
            if (isset($result->getData()->success) && !$result->getData()->success) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result->getData()->message);
            }

            \Log::info('Redirecting to jobs index after successful update');
            return redirect()->route('clinic.jobs.index')
                ->with('success', 'Job application fields updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Job Application Field Controller Update Error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the fields.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the fields: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request)
    {
        return $this->jobApplicationFieldRepo->updateStatus($request);
    }
}
