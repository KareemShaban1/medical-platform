<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\DoctorProfile;
use App\Models\Clinic;

class LabOrderSeeder extends Seeder
{
    private $labTests = [
        'Complete Blood Count (CBC)',
        'Comprehensive Metabolic Panel',
        'Lipid Panel',
        'Thyroid Function Test (TSH, T3, T4)',
        'HbA1c (Diabetes Test)',
        'Liver Function Test',
        'Kidney Function Test',
        'Urinalysis',
        'Vitamin D Level',
        'Iron Studies',
        'COVID-19 PCR Test',
        'Chest X-Ray',
        'ECG (Electrocardiogram)',
        'Ultrasound - Abdomen',
        'Blood Glucose Fasting',
    ];

    private $labNames = [
        'Al-Borg Medical Laboratories',
        'Cairo Lab',
        'El-Mokhtabar Medical Lab',
        'Alpha Lab',
        'Bio Lab',
        'Medical Lab Center',
    ];

    public function run(): void
    {
        $this->command->info('Creating lab orders...');

        $clinics = Clinic::with('clinicUsers.doctorProfile')->get();

        if ($clinics->isEmpty()) {
            $this->command->error('No clinics found!');
            return;
        }

        $totalOrders = 0;

        foreach ($clinics as $clinic) {
            $doctors = $clinic->clinicUsers()
                ->whereHas('doctorProfile')
                ->with('doctorProfile')
                ->get();

            if ($doctors->isEmpty()) {
                continue;
            }

            // Get patients assigned to this clinic
            $patients = Patient::whereHas('doctors', function($q) use ($clinic) {
                $q->where('doctor_patient.clinic_id', $clinic->id);
            })->get();

            if ($patients->isEmpty()) {
                continue;
            }

            // Create 5-15 lab orders per clinic
            $ordersCount = rand(5, 15);

            for ($i = 0; $i < $ordersCount; $i++) {
                $patient = $patients->random();
                $doctor = $doctors->random();

                try {
                    LabOrder::create([
                        'clinic_id' => $clinic->id,
                        'patient_id' => $patient->id,
                        'clinic_user_id' => $doctor->id,
                        'doctor_profile_id' => $doctor->doctorProfile->id ?? null,
                        'test_name' => $this->labTests[array_rand($this->labTests)],
                        'lab_name' => $this->labNames[array_rand($this->labNames)],
                        'status' => $this->getRandomStatus(),
                        'cost_amount' => rand(100, 500),
                        'notes' => $this->generateNotes(),
                        'result_comment' => rand(0, 100) > 60 ? $this->generateResultComment() : null,
                        'sent_at' => now()->subDays(rand(1, 30)),
                        'received_at' => rand(0, 100) > 40 ? now()->subDays(rand(0, 15)) : null,
                    ]);

                    $totalOrders++;
                } catch (\Exception $e) {
                    $this->command->warn("Error creating lab order: " . $e->getMessage());
                }
            }
        }

        $this->command->info("✓ Successfully created {$totalOrders} lab orders");
        $this->displaySummary();
    }

    private function getRandomStatus()
    {
        $statuses = ['pending', 'received', 'completed'];
        $weights = [20, 30, 50]; // 20% pending, 30% received, 50% completed

        $rand = rand(1, array_sum($weights));
        $cumulative = 0;

        foreach ($statuses as $index => $status) {
            $cumulative += $weights[$index];
            if ($rand <= $cumulative) {
                return $status;
            }
        }

        return 'pending';
    }

    private function generateNotes()
    {
        $notes = [
            'Fasting required before test',
            'Urgent - priority processing requested',
            'Follow-up from previous abnormal results',
            'Routine health screening',
            'Pre-operative clearance',
            null,
        ];

        return $notes[array_rand($notes)];
    }

    private function generateResultComment()
    {
        $comments = [
            'All values within normal range',
            'Slightly elevated levels, follow-up recommended',
            'Results show improvement from previous test',
            'Some abnormal values detected, further investigation needed',
            'Results consistent with diagnosis',
        ];

        return $comments[array_rand($comments)];
    }

    private function displaySummary()
    {
        $this->command->info("\n" . str_repeat('=', 50));
        $this->command->info("LAB ORDERS SUMMARY");
        $this->command->info(str_repeat('=', 50));

        $statusCounts = LabOrder::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        foreach ($statusCounts as $stat) {
            $this->command->info("  {$stat->status}: {$stat->count}");
        }

        $total = LabOrder::sum('cost_amount');
        $this->command->info("\nTotal Lab Revenue: " . number_format($total, 2) . " EGP");
        $this->command->info(str_repeat('=', 50));
    }
}
