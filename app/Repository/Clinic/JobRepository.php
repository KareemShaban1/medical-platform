<?php

namespace App\Repository\Clinic;

use App\Interfaces\Clinic\JobRepositoryInterface;
use App\Models\Job;
use App\Models\JobApplication;
use App\Traits\HandlesMediaUploads;

class JobRepository implements JobRepositoryInterface
{
    use HandlesMediaUploads;
    /** ---------------------- PUBLIC METHODS ---------------------- */

    public function index()
    {
        return [];
    }

    public function data()
    {
        $jobs = Job::query();

        return datatables()->of($jobs)
            ->editColumn('main_image', function ($item) {
                return '<img src="' . $item->main_image . '" alt="" class="img-fluid" style="width: 50px; height: 50px;">';
            })
            ->editColumn('status', fn($item) => $this->jobStatus($item))
            ->editColumn('job_applications', fn($item) => $this->jobApplicationActions($item))
            ->addColumn('action', fn($item) => $this->jobActions($item))
            ->rawColumns(['status', 'action', 'main_image', 'job_applications'])
            ->make(true);
    }

    public function store($request)
    {
        return $this->saveJob(new Job(), $request, 'created');
    }

    public function show($id)
    {
        return Job::findOrFail($id);
    }


    public function update($request, $id)
    {
        $job = Job::findOrFail($id);
        return $this->saveJob($job, $request, 'updated');
    }

    public function updateStatus($request)
    {
        $job = Job::findOrFail($request->id);

        // fallback to "status" if field is not sent
        $field = $request->field ?? 'status';
        $value = (bool)$request->value;

        $job->{$field} = $value;
        $job->save();

        return response()->json([
            'status' => 'success',
            'message' => __('Job status updated successfully'),
        ]);
    }

    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        $job->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('Job deleted successfully'),
        ]);
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $jobs = Job::onlyTrashed()->get();

        return datatables()->of($jobs)
            ->editColumn('status', fn($item) => $this->jobStatus($item))
            ->addColumn('trash_action', fn($item) => $this->jobTrashActions($item))
            ->rawColumns(['status', 'trash_action'])
            ->make(true);
    }

    public function restore($id)
    {
        $job = Job::onlyTrashed()->findOrFail($id);
        $job->restore();

        return $this->jsonResponse('success', __('Job restored successfully'));
    }

    public function forceDelete($id)
    {
        $job = Job::onlyTrashed()->findOrFail($id);
        $job->forceDelete();

        return $this->jsonResponse('success', __('Job deleted successfully'));
    }


    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function saveJob($job, $request, string $action)
    {
        try {
            $job->fill($request->validated())->save();

            // Media
            if ($request->hasFile('main_image')) {
                $this->processMedia($job, $request, [
                    ['field' => 'main_image', 'collection' => 'main_image', 'multiple' => false],
                ], $action);
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => __('Job ' . $action . ' successfully'),
                ]);
            }

            return redirect()->route('clinic.jobs.index')->with('success', __('Job ' . $action . ' successfully'));
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function jobStatus($item): string
    {
        $checked = $item->status ? 'checked' : '';
        return <<<HTML
        <div class="form-check form-switch mt-2">
            <input type="checkbox"
                   class="form-check-input toggle-boolean"
                   data-id="{$item->id}"
                   data-field="status"
                   value="1" {$checked}>
        </div>
        HTML;
    }

    private function jobActions($item): string
    {
        $editUrl = route('clinic.jobs.edit', $item->id);
        $showUrl = route('clinic.jobs.show', $item->id);

        return <<<HTML
        <div class="d-flex gap-2">
           <a href="{$showUrl}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
           <a href="{$editUrl}" class="btn btn-sm btn-warning text-white"><i class="fa fa-edit"></i></a>
           <button onclick="deleteJob({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

    private function jobApplicationActions($item): string
    {
        if ($item->jobApplicationFields->count() > 0) {
            $applicationFieldsUrl = route('clinic.job-application-fields.edit', $item->id);
            $applicationFieldsIcon = 'fa fa-edit';
            $applicationFieldsClass = 'btn-warning text-white';
        } else {
            $applicationFieldsUrl = route('clinic.job-application-fields.create', $item->id);
            $applicationFieldsIcon = 'fa fa-plus';
            $applicationFieldsClass = 'btn-success';
        }
        $applicantsUrl = route('clinic.jobs.applicants', $item->id);
        $applicantsIcon = 'fa fa-users';

        return <<<HTML
        <span>Applicants Count: {$item->jobApplications->count()}</span>
        <div class="d-flex gap-2 mt-2">
        <a href="{$applicationFieldsUrl}" class="btn btn-sm {$applicationFieldsClass}"><i class="{$applicationFieldsIcon}"></i></a>
        <a href="{$applicantsUrl}" class="btn btn-sm btn-primary"><i class="{$applicantsIcon}"></i></a>
        </div>
        HTML;
    }

    private function jobTrashActions($item): string
    {
        return <<<HTML
        <div class="d-flex gap-2">
            <button onclick="restore({$item->id})" class="btn btn-sm btn-info" title="Restore"><i class="fa fa-undo"></i></button>
            <button onclick="forceDelete({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }


    public function getApplicants($jobId)
    {
        return JobApplication::where('job_id', $jobId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function updateApplicationStatus($request)
    {
        $application = JobApplication::findOrFail($request->application_id);

        $application->status = $request->status;
        $application->save();

        return response()->json([
            'status' => 'success',
            'message' => __('Application status updated successfully'),
        ]);
    }

    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
