<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Models\ClinicUserSalary;
use App\Http\Requests\Clinic\UserSalary\StoreClinicUserSalaryRequest;
use App\Http\Requests\Clinic\UserSalary\UpdateClinicUserSalaryRequest;
use App\Http\Controllers\Controller;
use App\Interfaces\Clinic\ClinicUserSalaryRepositoryInterface;
use App\Models\ClinicUser;


class ClinicUserSalaryController extends Controller
{
    protected $clinicUserSalaryRepo;

    public function __construct(ClinicUserSalaryRepositoryInterface $clinicUserSalaryRepo)
    {
        $this->clinicUserSalaryRepo = $clinicUserSalaryRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.dashboards.clinic.pages.clinic-user-salaries.index');
    }

    public function data()
    {
        return $this->clinicUserSalaryRepo->data();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clinicUsers = ClinicUser::all();
        return view('backend.dashboards.clinic.pages.clinic-user-salaries.create', compact('clinicUsers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClinicUserSalaryRequest $request)
    {
        return $this->clinicUserSalaryRepo->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $clinicUserSalary = $this->clinicUserSalaryRepo->show($id);

        return request()->ajax()
            ? response()->json($clinicUserSalary)
            : view('backend.dashboards.clinic.pages.clinic-user-salaries.show', compact('clinicUserSalary'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $clinicUserSalary = $this->clinicUserSalaryRepo->show($id);
        $clinicUsers = ClinicUser::all();
        return view('backend.dashboards.clinic.pages.clinic-user-salaries.edit', compact('clinicUserSalary', 'clinicUsers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClinicUserSalaryRequest $request, $id)
    {
        return $this->clinicUserSalaryRepo->update($request, $id);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->clinicUserSalaryRepo->destroy($id);
    }

    public function trash()
    {
        return view('backend.dashboards.clinic.pages.clinic-user-salaries.trash');
    }

    public function trashData()
    {
        return $this->clinicUserSalaryRepo->trashData();
    }

    public function restore($id)
    {
        return $this->clinicUserSalaryRepo->restore($id);
    }

    public function forceDelete($id)
    {
        return $this->clinicUserSalaryRepo->forceDelete($id);
    }

    /**
     * Get clinic user salary data for AJAX requests
     */
    public function getUserSalaryData($userId)
    {
        $clinicUser = ClinicUser::find($userId);

        if (!$clinicUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'salary_frequency' => $clinicUser->salary_frequency,
            'amount_per_salary_frequency' => $clinicUser->amount_per_salary_frequency
        ]);
    }
}