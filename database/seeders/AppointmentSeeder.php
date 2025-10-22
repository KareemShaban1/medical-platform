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
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting appointment creation...');

        // Get all patients
        $patients = Patient::all();
        if ($patients->isEmpty()) {
            $this->command->error('No patients found! Please run PatientSeeder first.');
            return;
        }

        // Get all doctor profiles
        $doctors = DoctorProfile::where('status', DoctorProfile::STATUS_APPROVED)->get();
        if ($doctors->isEmpty()) {
            $this->command->error('No approved doctors found!');
            return;
        }

        // Get daily periods for the next 30 days
        $periods = DailyPeriod::where('date', '>=', Carbon::today())
            ->where('date', '<=', Carbon::today()->addDays(30))
            ->where('is_open', true)
            ->with('doctorProfile')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        if ($periods->isEmpty()) {
            $this->command->error('No daily periods found! Please run WorkingHourSeeder first.');
            return;
        }

        $this->command->info("Found {$periods->count()} available periods");
        $this->command->info("Found {$patients->count()} patients");

        $appointmentCount = 0;
        $patientIndex = 0;

        // Visit types: 0=Initial, 1=Follow-up, 2=Consultation
        $visitTypes = [0, 1, 2];
        $visitTypesWeights = [50, 30, 20]; // 50% initial, 30% follow-up, 20% consultation

        // Statuses with realistic distribution
        $statusDistribution = [
            'confirmed' => 40,  // 40%
            'completed' => 30,  // 30%
            'pending' => 15,    // 15%
            'waiting' => 10,    // 10%
            'cancelled' => 5,   // 5%
        ];

        // Process each period
        foreach ($periods as $period) {
            // Determine how many appointments to create for this period (60-90% of capacity)
            $appointmentsToCreate = rand(
                (int)ceil($period->capacity * 0.6),
                min((int)ceil($period->capacity * 0.9), $period->capacity)
            );

            $this->command->info("Creating {$appointmentsToCreate} appointments for period {$period->id} ({$period->date} {$period->start_time})");

            // Track slot numbers for this period
            $slotNumber = 1;

            for ($i = 0; $i < $appointmentsToCreate; $i++) {
                // Get patient (cycle through patients)
                $patient = $patients[$patientIndex % $patients->count()];
                $patientIndex++;

                // Determine status based on distribution and date
                $status = $this->determineStatus($period->date, $statusDistribution);

                // Determine visit type
                $visitType = $this->weightedRandom($visitTypes, $visitTypesWeights);

                // Determine cost based on visit type and doctor
                $costAmount = $this->determineCost($visitType);

                // Determine payment status
                $paymentStatus = ($status === 'completed') ? 'paid' : (rand(0, 100) > 30 ? 'pending' : 'paid');

                // Create appointment
                $appointmentData = [
                    'doctor_profile_id' => $period->doctor_profile_id,
                    'patient_id' => $patient->id,
                    'period_id' => $period->id,
                    'slot_number' => $slotNumber,
                    'status' => $status,
                    'visit_type' => $visitType,
                    'cost_amount' => $costAmount,
                    'payment_status' => $paymentStatus,
                    'patient_notes' => $this->generatePatientNotes($visitType),
                    'doctor_notes' => ($status === 'completed') ? $this->generateDoctorNotes() : null,
                    'booked_at' => $this->determineBookedAt($period->date, $status),
                    'cancelled_at' => ($status === 'cancelled') ? Carbon::parse($period->date)->subDays(rand(1, 5)) : null,
                    'cancellation_reason' => ($status === 'cancelled') ? $this->getCancellationReason() : null,
                ];

                try {
                    $appointment = Appointment::create($appointmentData);

                    // Increment booked count if confirmed or completed
                    if (in_array($status, ['confirmed', 'completed'])) {
                        $period->increment('booked_count');
                    }

                    $appointmentCount++;
                    $slotNumber++;
                } catch (\Exception $e) {
                    $this->command->error("Error creating appointment: " . $e->getMessage());
                }
            }
        }

        $this->command->info("Successfully created {$appointmentCount} appointments!");
        $this->displaySummary($appointmentCount);
    }

    /**
     * Determine appointment status based on date and distribution
     */
    private function determineStatus($date, $distribution)
    {
        $appointmentDate = Carbon::parse($date);
        $today = Carbon::today();

        // Past appointments should be mostly completed
        if ($appointmentDate->lt($today)) {
            $rand = rand(1, 100);
            if ($rand <= 80) return 'completed';
            if ($rand <= 95) return 'cancelled';
            return 'confirmed'; // Some might still be pending
        }

        // Today's appointments
        if ($appointmentDate->eq($today)) {
            $rand = rand(1, 100);
            if ($rand <= 40) return 'waiting';
            if ($rand <= 70) return 'confirmed';
            if ($rand <= 90) return 'completed';
            return 'pending';
        }

        // Future appointments
        $rand = rand(1, 100);
        $cumulative = 0;
        foreach ($distribution as $status => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $status;
            }
        }

        return 'confirmed';
    }

    /**
     * Weighted random selection
     */
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

    /**
     * Determine cost based on visit type
     */
    private function determineCost($visitType)
    {
        switch ($visitType) {
            case 0: // Initial visit
                return rand(200, 400);
            case 1: // Follow-up
                return rand(150, 300);
            case 2: // Consultation
                return rand(100, 250);
            default:
                return 200;
        }
    }

    /**
     * Generate patient notes based on visit type
     */
    private function generatePatientNotes($visitType)
    {
        $initialNotes = [
            'Experiencing chest pain for the past week',
            'First time visiting, recommended by friend',
            'Routine checkup, no specific complaints',
            'Having persistent headaches',
            'Follow-up from previous diagnosis',
            'Knee pain after sports injury',
            'Annual health screening',
            'Vaccination appointment',
            'Medical certificate needed for work',
            'Consultation about test results',
        ];

        $followUpNotes = [
            'Follow-up visit to check progress',
            'Medication side effects discussion',
            'Reviewing treatment plan',
            'Check healing progress',
            'Post-surgery follow-up',
        ];

        $consultationNotes = [
            'Second opinion requested',
            'Discuss treatment options',
            'Review test results',
            'Treatment plan consultation',
            'Specialist referral consultation',
        ];

        if ($visitType === 0) {
            return $initialNotes[array_rand($initialNotes)];
        } elseif ($visitType === 1) {
            return $followUpNotes[array_rand($followUpNotes)];
        } else {
            return $consultationNotes[array_rand($consultationNotes)];
        }
    }

    /**
     * Generate doctor notes
     */
    private function generateDoctorNotes()
    {
        $notes = [
            'Patient responded well to treatment. Continue current medication.',
            'Recommended lifestyle changes and follow-up in 2 weeks.',
            'Prescribed medication and ordered lab tests.',
            'All vital signs normal. Patient in good health.',
            'Recommended physical therapy sessions.',
            'Scheduled for surgery next month.',
            'Advised rest and pain medication as needed.',
            'Referred to specialist for further evaluation.',
            'Treatment plan discussed and agreed upon.',
            'Patient educated about condition and prevention.',
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * Determine booked_at timestamp
     */
    private function determineBookedAt($date, $status)
    {
        if (in_array($status, ['confirmed', 'completed', 'waiting'])) {
            $appointmentDate = Carbon::parse($date);
            // Booked 1-14 days before appointment
            return $appointmentDate->copy()->subDays(rand(1, 14))->subHours(rand(0, 23));
        }

        if ($status === 'pending') {
            // Recently booked
            return Carbon::now()->subHours(rand(1, 48));
        }

        return null;
    }

    /**
     * Get cancellation reason
     */
    private function getCancellationReason()
    {
        $reasons = [
            'Patient requested cancellation - personal reasons',
            'Doctor unavailable - rescheduled',
            'Emergency situation',
            'Patient feeling better - no longer needed',
            'Conflict with another appointment',
            'Travel plans changed',
            'Financial reasons',
            'Found closer clinic',
        ];

        return $reasons[array_rand($reasons)];
    }

    /**
     * Display summary statistics
     */
    private function displaySummary($totalCount)
    {
        $this->command->info("\n" . str_repeat('=', 50));
        $this->command->info("APPOINTMENT SEEDING SUMMARY");
        $this->command->info(str_repeat('=', 50));

        $statusCounts = Appointment::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $this->command->info("\nStatus Distribution:");
        foreach ($statusCounts as $status => $count) {
            $percentage = round(($count / $totalCount) * 100, 1);
            $this->command->info("  {$status}: {$count} ({$percentage}%)");
        }

        $visitTypeCounts = Appointment::selectRaw('visit_type, COUNT(*) as count')
            ->groupBy('visit_type')
            ->pluck('count', 'visit_type');

        $visitTypeLabels = [0 => 'Initial', 1 => 'Follow-up', 2 => 'Consultation'];
        $this->command->info("\nVisit Type Distribution:");
        foreach ($visitTypeCounts as $type => $count) {
            $percentage = round(($count / $totalCount) * 100, 1);
            $label = $visitTypeLabels[$type] ?? 'Unknown';
            $this->command->info("  {$label}: {$count} ({$percentage}%)");
        }

        $paymentStats = Appointment::selectRaw('payment_status, SUM(cost_amount) as total')
            ->groupBy('payment_status')
            ->get();

        $this->command->info("\nRevenue Statistics:");
        foreach ($paymentStats as $stat) {
            $this->command->info("  {$stat->payment_status}: " . number_format($stat->total, 2) . " EGP");
        }

        $doctorStats = Appointment::selectRaw('doctor_profile_id, COUNT(*) as count')
            ->groupBy('doctor_profile_id')
            ->with('doctorProfile')
            ->get();

        $this->command->info("\nAppointments per Doctor:");
        foreach ($doctorStats as $stat) {
            $doctorName = $stat->doctorProfile->name ?? 'Unknown';
            $this->command->info("  {$doctorName}: {$stat->count} appointments");
        }

        $this->command->info("\n" . str_repeat('=', 50));
        $this->command->info("Total Appointments Created: {$totalCount}");
        $this->command->info(str_repeat('=', 50) . "\n");
    }
}

