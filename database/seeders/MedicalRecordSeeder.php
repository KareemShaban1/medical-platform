<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicalRecord;
use App\Models\Appointment;
use App\Enums\VisitType;

class MedicalRecordSeeder extends Seeder
{
    private $complaints = [
        'Chest pain and shortness of breath',
        'Persistent headache for 3 days',
        'Lower back pain after exercise',
        'Fever and cough',
        'Abdominal pain and nausea',
        'Joint pain in knees',
        'Skin rash on arms',
        'Difficulty sleeping',
        'Dizziness and fatigue',
        'Sore throat and runny nose',
    ];

    private $diagnoses = [
        'Hypertension - Stage 1',
        'Acute Bronchitis',
        'Muscle Strain - Lumbar Region',
        'Viral Upper Respiratory Infection',
        'Gastritis',
        'Osteoarthritis - Bilateral Knees',
        'Contact Dermatitis',
        'Insomnia - Primary',
        'Iron Deficiency Anemia',
        'Acute Pharyngitis',
    ];

    private $treatments = [
        'Started on antihypertensive medication, lifestyle modifications recommended',
        'Prescribed bronchodilators and cough suppressant, rest advised',
        'Physical therapy recommended, prescribed NSAIDs for pain management',
        'Symptomatic treatment with rest, fluids, and OTC medications',
        'Prescribed proton pump inhibitors, dietary modifications advised',
        'Prescribed NSAIDs, recommended weight management and low-impact exercises',
        'Prescribed topical corticosteroids, avoid irritants',
        'Sleep hygiene education, prescribed short-term sleep aid',
        'Prescribed iron supplements, dietary counseling',
        'Prescribed antibiotics, throat lozenges, rest and fluids',
    ];

    public function run(): void
    {
        $this->command->info('Creating medical records for completed appointments...');

        // Get completed appointments
        $completedAppointments = Appointment::where('status', 'completed')
            ->with(['doctorProfile', 'patient'])
            ->get();

        if ($completedAppointments->isEmpty()) {
            $this->command->warn('No completed appointments found!');
            return;
        }

        $recordCount = 0;

        // Create medical records for 80% of completed appointments
        $appointmentsWithRecords = $completedAppointments->random(
            (int)ceil($completedAppointments->count() * 0.8)
        );

        foreach ($appointmentsWithRecords as $appointment) {
            try {
                // Check if record already exists
                if (MedicalRecord::where('appointment_id', $appointment->id)->exists()) {
                    continue;
                }

                $index = $recordCount % count($this->complaints);

                MedicalRecord::create([
                    'clinic_id' => $appointment->doctorProfile->clinicUser->clinic_id,
                    'appointment_id' => $appointment->id,
                    'doctor_profile_id' => $appointment->doctor_profile_id,
                    'patient_id' => $appointment->patient_id,
                    'visit_type' => $appointment->visit_type,
                    'chief_complaint' => $this->complaints[$index],
                    'diagnosis' => $this->diagnoses[$index],
                    'treatment' => $this->treatments[$index],
                    'notes' => $this->generateNotes(),
                    'is_shared_with_patient' => rand(0, 100) > 30, // 70% shared
                    'created_by' => $appointment->doctorProfile->clinicUser->id,
                ]);

                $recordCount++;
            } catch (\Exception $e) {
                $this->command->warn("Error creating medical record: " . $e->getMessage());
            }
        }

        $this->command->info("✓ Successfully created {$recordCount} medical records");
    }

    private function generateNotes()
    {
        $notes = [
            'Patient is cooperative and compliant with treatment plan.',
            'Follow-up appointment scheduled in 2 weeks.',
            'Patient educated about condition and warning signs.',
            'Vital signs stable during examination.',
            'No adverse reactions noted.',
            'Patient questions addressed and treatment explained.',
            'Referral to specialist may be needed if symptoms persist.',
            'Patient advised to return if condition worsens.',
        ];

        return $notes[array_rand($notes)];
    }
}
