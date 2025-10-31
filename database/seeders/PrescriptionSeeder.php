<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Appointment;

class PrescriptionSeeder extends Seeder
{
    private $medications = [
        [
            'name' => 'Amoxicillin',
            'dose' => '500mg',
            'frequency' => '3 times daily',
            'duration' => '7 days',
            'notes' => 'Take with food',
        ],
        [
            'name' => 'Ibuprofen',
            'dose' => '400mg',
            'frequency' => 'Every 6 hours as needed',
            'duration' => '5 days',
            'notes' => 'Take after meals',
        ],
        [
            'name' => 'Omeprazole',
            'dose' => '20mg',
            'frequency' => 'Once daily before breakfast',
            'duration' => '14 days',
            'notes' => 'Take on empty stomach',
        ],
        [
            'name' => 'Metformin',
            'dose' => '500mg',
            'frequency' => 'Twice daily with meals',
            'duration' => '30 days',
            'notes' => 'Monitor blood sugar levels',
        ],
        [
            'name' => 'Lisinopril',
            'dose' => '10mg',
            'frequency' => 'Once daily',
            'duration' => '30 days',
            'notes' => 'Monitor blood pressure',
        ],
        [
            'name' => 'Paracetamol',
            'dose' => '500mg',
            'frequency' => 'Every 4-6 hours as needed',
            'duration' => '3 days',
            'notes' => 'Do not exceed 4g per day',
        ],
        [
            'name' => 'Cetirizine',
            'dose' => '10mg',
            'frequency' => 'Once daily at bedtime',
            'duration' => '7 days',
            'notes' => 'May cause drowsiness',
        ],
        [
            'name' => 'Vitamin D3',
            'dose' => '1000 IU',
            'frequency' => 'Once daily',
            'duration' => '90 days',
            'notes' => 'Take with fatty meal',
        ],
    ];

    public function run(): void
    {
        $this->command->info('Creating prescriptions for completed appointments...');

        // Get completed appointments without prescriptions
        $completedAppointments = Appointment::where('status', 'completed')
            ->doesntHave('prescription')
            ->with(['doctorProfile.clinicUser', 'patient'])
            ->get();

        if ($completedAppointments->isEmpty()) {
            $this->command->warn('No completed appointments without prescriptions found!');
            return;
        }

        $prescriptionCount = 0;

        // Create prescriptions for 70% of completed appointments
        $appointmentsWithPrescriptions = $completedAppointments->random(
            min((int)ceil($completedAppointments->count() * 0.7), $completedAppointments->count())
        );

        foreach ($appointmentsWithPrescriptions as $appointment) {
            try {
                $prescription = Prescription::create([
                    'appointment_id' => $appointment->id,
                    'clinic_id' => $appointment->doctorProfile->clinicUser->clinic_id,
                    'patient_id' => $appointment->patient_id,
                    'doctor_profile_id' => $appointment->doctor_profile_id,
                    'notes' => $this->generatePrescriptionNotes(),
                ]);

                // Add 1-4 prescription items
                $itemCount = rand(1, 4);
                $selectedMeds = collect($this->medications)->random($itemCount);

                foreach ($selectedMeds as $med) {
                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'drug_name' => $med['name'],
                        'dose' => $med['dose'],
                        'frequency' => $med['frequency'],
                        'duration' => $med['duration'],
                        'notes' => $med['notes'],
                    ]);
                }

                $prescriptionCount++;
            } catch (\Exception $e) {
                $this->command->warn("Error creating prescription: " . $e->getMessage());
            }
        }

        $this->command->info("✓ Successfully created {$prescriptionCount} prescriptions");
    }

    private function generatePrescriptionNotes()
    {
        $notes = [
            'Complete the full course of medication',
            'Follow up if symptoms persist or worsen',
            'Take medications as prescribed',
            'Avoid alcohol while on medication',
            'Report any side effects immediately',
            'Keep medications in a cool, dry place',
            null, // Some prescriptions may not have notes
        ];

        return $notes[array_rand($notes)];
    }
}
