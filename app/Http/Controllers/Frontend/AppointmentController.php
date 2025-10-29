<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Appointment\BookAppointmentRequest;
use App\Http\Requests\User\Appointment\ConfirmAppointmentRequest;
use App\Services\Appointment\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function book(BookAppointmentRequest $request)
    {
        try {
            $appointment = $this->appointmentService->bookAppointment($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => __('Appointment booked successfully! Check your email for confirmation code.'),
                'appointment' => $appointment,
                'confirmation_code' => $appointment->confirmation_code
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function confirm(ConfirmAppointmentRequest $request)
    {
        try {
            $appointment = $this->appointmentService->confirmAppointment($request->confirmation_code);

            return response()->json([
                'status' => 'success',
                'message' => __('Appointment confirmed successfully!'),
                'appointment' => $appointment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function myAppointments()
    {
        if (!auth('patient')->check()) {
            return redirect()->route('login')->with('error', __('Please login to view your appointments'));
        }

        $patient = auth('patient')->user();

        $appointments = $patient->appointments()
            ->with(['doctorProfile.clinicUser.clinic', 'doctorProfile.speciality', 'period'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.pages.appointments.my-appointments', compact('appointments', 'patient'));
    }

    public function cancel(Request $request, $id)
    {
        try {
            $patient = auth('patient')->user();
            $appointment = $patient->appointments()->findOrFail($id);

            $this->appointmentService->cancelAppointment(
                $appointment->id,
                $request->reason,
                $patient->id
            );

            return response()->json([
                'status' => 'success',
                'message' => __('Appointment cancelled successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
