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
        return view('backend.dashboards.clinic.pages.jobs.index');
    }

    public function data()
    {
        return $this->jobRepo->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.dashboards.clinic.pages.jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobRequest $request)
    {
        return $this->jobRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
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
        $job = $this->jobRepo->show($id);

        return view('backend.dashboards.clinic.pages.jobs.edit', compact('job'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobRequest $request, $id)
    {
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
        return $this->jobRepo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.clinic.pages.jobs.trash');
    }

    public function trashData()
    {
        return $this->jobRepo->trashData();
    }

    public function restore($id)
    {
        return $this->jobRepo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->jobRepo->forceDelete($id);
    }
}
