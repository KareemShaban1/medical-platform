<?php

namespace App\Services\Appointment;

use App\Models\Appointment;
use App\Models\DailyPeriod;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AppointmentService
{
    /**
     * Book an appointment
     */
    public function bookAppointment(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            // Get or create patient
            $patient = $this->getOrCreatePatient($data);

            // Check if patient already has a booking with this doctor
            $existingAppointment = Appointment::where('doctor_profile_id', $data['doctor_profile_id'])
                ->where('patient_id', $patient->id)
                ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])
                ->first();


            if ($existingAppointment) {
                throw new \Exception(__('You already have a booking with this doctor. Please wait for confirmation or cancel your existing appointment.'));
            }

            // Get period and check availability
            $period = DailyPeriod::findOrFail($data['period_id']);

            if (!$period->is_open) {
                throw new \Exception(__('This time slot is closed'));
            }


            // Check if the period is in the past
            $periodDateTime = Carbon::parse($period->date)->setTimeFromTimeString($period->start_time);
            if ($periodDateTime->isPast()) {
                throw new \Exception(__('Cannot book appointments in the past'));
            }

            // Calculate slot number based on total appointments (confirmed + pending)
            $totalAppointments = Appointment::where('period_id', $period->id)
                ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])
                ->count();

            $slotNumber = $totalAppointments + 1;

            // Create appointment
            $appointment = Appointment::create([
                'doctor_profile_id' => $data['doctor_profile_id'],
                'patient_id' => $patient->id,
                'period_id' => $data['period_id'],
                'slot_number' => $slotNumber,
                'status' => Appointment::STATUS_PENDING,
                'patient_notes' => $data['patient_notes'] ?? null,
            ]);

            // Generate confirmation code
            $appointment->generateConfirmationCode();

            // Don't increment booked_count here - only increment when confirmed

            // Send confirmation email
            // $this->sendConfirmationEmail($appointment);

            return $appointment;
        });
    }

    /**
     * Confirm an appointment using confirmation code
     */
    public function confirmAppointment(string $confirmationCode): Appointment
    {
        $appointment = Appointment::where('confirmation_code', $confirmationCode)
            ->where('status', Appointment::STATUS_PENDING)
            ->first();

        if (!$appointment) {
            throw new \Exception(__('Invalid confirmation code'));
        }

        if (!$appointment->isConfirmationCodeValid()) {
            throw new \Exception(__('Confirmation code has expired'));
        }

        $appointment->confirm();

        return $appointment;
    }

    /**
     * Cancel an appointment
     */
    public function cancelAppointment(int $appointmentId, string $reason = null, int $cancelledBy = null): Appointment
    {
        return DB::transaction(function () use ($appointmentId, $reason, $cancelledBy) {
            $appointment = Appointment::findOrFail($appointmentId);

            if ($appointment->isCancelled()) {
                throw new \Exception(__('Appointment is already cancelled'));
            }

            $appointment->cancel($reason, $cancelledBy);

            return $appointment;
        });
    }

    /**
     * Expire pending appointments
     */
    public function expirePendingAppointments(): int
    {
        $expiredCount = 0;

        $appointments = Appointment::where('status', Appointment::STATUS_PENDING)
            ->where('confirmation_code_expires_at', '<', now())
            ->get();

        foreach ($appointments as $appointment) {
            $appointment->expire();
            $expiredCount++;
        }

        return $expiredCount;
    }

    /**
     * Get or create patient from request data
     */
    private function getOrCreatePatient(array $data): Patient
    {
        // If user is authenticated
        if (auth()->check()) {
            $user = auth()->user();

            // Find or create patient for this user
            $patient = Patient::firstOrCreate(
                ['user_id' => $user->id],
                ['phone' => $user->phone ?? $data['phone'] ?? null]
            );

            return $patient;
        }

        // If guest booking - create user and patient
        if (isset($data['email'])) {
            // Check if user exists by email
            $user = User::where('email', $data['email'])->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'password' => bcrypt("password"), // Random password
                ]);
            }

            // Find or create patient
            $patient = Patient::firstOrCreate(
                ['user_id' => $user->id],
                ['phone' => $data['phone'] ?? null]
            );

            return $patient;
        }

        throw new \Exception(__('Unable to create patient record'));
    }

    /**
     * Send confirmation email
     */
    private function sendConfirmationEmail(Appointment $appointment): void
    {
        // TODO: Implement email sending
        // This would send an email with the confirmation code
    }
}

