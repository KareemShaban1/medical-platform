<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Interfaces\User\DoctorProfileRepositoryInterface;
use Carbon\Carbon;

class DoctorProfileController extends Controller
{
    protected $repo;

    public function __construct(DoctorProfileRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function show($id)
    {
        $doctor = $this->repo->find($id);

        // Get available days for the next 30 days
        $startDate = Carbon::today()->toDateString();
        $endDate = Carbon::today()->addDays(30)->toDateString();
        $availableDays = $this->repo->getAvailableDays($id, $startDate, $endDate);

        // Prefill patient data if authenticated as patient
        $patient = auth('patient')->check() ? auth('patient')->user() : null;

        return view('frontend.pages.doctors.show', compact('doctor', 'availableDays', 'patient'));
    }

    public function getAvailableDays($id)
    {
        try {
            $startDate = request('start_date', Carbon::today()->toDateString());
            $endDate = request('end_date', Carbon::today()->addDays(30)->toDateString());

            $availableDays = $this->repo->getAvailableDays($id, $startDate, $endDate);

            return response()->json([
                'status' => 'success',
                'days' => $availableDays
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getAvailablePeriods($id)
    {
        try {
            $date = request('date', Carbon::today()->toDateString());

            $periods = $this->repo->getAvailablePeriods($id, $date);

            return response()->json([
                'status' => 'success',
                'periods' => $periods
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
