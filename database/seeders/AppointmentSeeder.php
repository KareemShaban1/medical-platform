<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\DailyPeriod;
use App\Models\DoctorProfile;
use App\Models\Patient;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating appointments...');

        // Get all available periods
        $periods = DailyPeriod::where('date', '>=', Carbon::today()->subDays(30))
            ->where('date', '<=', Carbon::today()->addDays(30))
            ->where('is_open', true)
            ->with(['doctorProfile.clinicUser'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        if ($periods->isEmpty()) {
            $this->command->error('No daily periods found! Please run WorkingHourSeeder first.');
            return;
        }

        $appointmentCount = 0;
        $today = Carbon::today();

        foreach ($periods as $period) {
            $clinicId = $period->doctorProfile->clinicUser->clinic_id;

            // Get patients assigned to this doctor in this clinic
            $patients = Patient::whereHas('doctors', function($q) use ($period, $clinicId) {
                $q->where('doctor_profiles.id', $period->doctor_profile_id)
                  ->where('doctor_patient.clinic_id', $clinicId);
            })->get();

            if ($patients->isEmpty()) {
                continue;
            }

            // Fill 60-90% of capacity
            $appointmentsToCreate = rand(
                (int)ceil($period->capacity * 0.6),
                min((int)ceil($period->capacity * 0.9), $period->capacity)
            );

            $periodDate = Carbon::parse($period->date);

            for ($i = 0; $i < $appointmentsToCreate; $i++) {
                $patient = $patients->random();

                // Determine status based on date
                $status = $this->determineStatus($periodDate, $today);

                // Determine visit type (0=Initial, 1=Follow-up, 2=Consultation)
                $visitType = $this->weightedRandom([0, 1, 2], [50, 30, 20]);

                $appointment = Appointment::create([
                    'doctor_profile_id' => $period->doctor_profile_id,
                    'patient_id' => $patient->id,
                    'period_id' => $period->id,
                    'slot_number' => $i + 1,
                    'status' => $status,
                    'visit_type' => $visitType,
                    'cost_amount' => $this->determineCost($visitType),
                    'payment_status' => ($status === 'completed') ? 'paid' : 'pending',
                    'patient_notes' => $this->generatePatientNotes($visitType),
                    'doctor_notes' => ($status === 'completed') ? $this->generateDoctorNotes() : null,
                    'booked_at' => $periodDate->copy()->subDays(rand(1, 14)),
                ]);

                if (in_array($status, ['confirmed', 'completed'])) {
                    $period->increment('booked_count');
                }

                $appointmentCount++;
            }
        }

        $this->command->info("✓ Successfully created {$appointmentCount} appointments");
        $this->displaySummary();
    }

    private function determineStatus($appointmentDate, $today)
    {
        if ($appointmentDate->lt($today)) {
            return rand(1, 100) <= 85 ? 'completed' : 'cancelled';
        }

        if ($appointmentDate->eq($today)) {
            $rand = rand(1, 100);
            if ($rand <= 40) return 'waiting';
            if ($rand <= 80) return 'confirmed';
            return 'completed';
        }

        return rand(1, 100) <= 80 ? 'confirmed' : 'pending';
    }

    private function weightedRandom($values, $weights)
    {
        $rand = rand(1, array_sum($weights));
        $cumulative = 0;

        foreach ($values as $index => $value) {
            $cumulative += $weights[$index];
            if ($rand <= $cumulative) {
                return $value;
            }
        }

        return $values[0];
    }

    private function determineCost($visitType)
    {
        return match($visitType) {
            0 => rand(200, 400), // Initial
            1 => rand(150, 300), // Follow-up
            2 => rand(100, 250), // Consultation
            default => 200,
        };
    }

    private function generatePatientNotes($visitType)
    {
        $notes = [
            0 => ['Chest pain', 'First visit', 'Routine checkup', 'Headaches', 'Annual screening'],
            1 => ['Follow-up visit', 'Medication review', 'Treatment progress check'],
            2 => ['Second opinion', 'Treatment consultation', 'Test results discussion'],
        ];

        return $notes[$visitType][array_rand($notes[$visitType])];
    }

    private function generateDoctorNotes()
    {
        $notes = [
            'Patient responding well to treatment',
            'Prescribed medication and follow-up in 2 weeks',
            'All vital signs normal',
            'Recommended lifestyle changes',
            'Ordered additional tests',
        ];

        return $notes[array_rand($notes)];
    }

    private function displaySummary()
    {
        $this->command->info("\n" . str_repeat('=', 50));
        $this->command->info("APPOINTMENT SUMMARY");
        $this->command->info(str_repeat('=', 50));

        $statusCounts = Appointment::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        foreach ($statusCounts as $stat) {
            $this->command->info("  {$stat->status}: {$stat->count}");
        }

        $total = Appointment::sum('cost_amount');
        $this->command->info("\nTotal Revenue: " . number_format($total, 2) . " EGP");
        $this->command->info(str_repeat('=', 50));
    }
}
