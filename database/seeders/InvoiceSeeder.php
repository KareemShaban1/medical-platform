<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\DoctorProfile;
use App\Models\Clinic;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding invoices from completed appointments...');

        // Fetch completed appointments with relations to determine clinic
        $appointments = Appointment::with(['doctorProfile.clinicUser', 'patient'])
            ->where('status', Appointment::STATUS_COMPLETED)
            ->get();

        if ($appointments->isEmpty()) {
            $this->command->warn('No completed appointments found. Skipping invoice seeding.');
            return;
        }

        $created = 0; $items = 0;

        foreach ($appointments as $appt) {
            $clinicId = optional($appt->doctorProfile?->clinicUser)->clinic_id;
            if (!$clinicId) { continue; }

            $invoice = Invoice::firstOrCreate(
                ['appointment_id' => $appt->id],
                [
                    'clinic_id' => $clinicId,
                    'patient_id' => $appt->patient_id,
                    'doctor_profile_id' => $appt->doctor_profile_id,
                    'subtotal' => 0,
                    'discount' => 0,
                    'tax' => 0,
                    'total' => 0,
                    'status' => ($appt->payment_status === 'paid') ? 'paid' : 'unpaid',
                    'payment_method' => ($appt->payment_status === 'paid') ? 'cash' : null,
                    'paid_at' => ($appt->payment_status === 'paid') ? now() : null,
                ]
            );

            if ($invoice->wasRecentlyCreated) { $created++; }

            // Ensure default consultation item exists
            if ($invoice->items()->count() === 0) {
                $invoice->items()->create([
                    'description' => 'Clinic Consultation',
                    'quantity' => 1,
                    'unit_price' => $appt->cost_amount ?? 200,
                ]);
                $items++;

                // Optionally add 0-2 extra items
                $extra = rand(0, 2);
                $catalog = [
                    ['Lab Test', 150, 400],
                    ['Medication', 50, 250],
                    ['Injection', 80, 180],
                ];
                for ($i=0; $i < $extra; $i++) {
                    $pick = $catalog[array_rand($catalog)];
                    $invoice->items()->create([
                        'description' => $pick[0],
                        'quantity' => 1,
                        'unit_price' => rand($pick[1], $pick[2]),
                    ]);
                    $items++;
                }
                $invoice->refresh();
                $invoice->recalcTotals();
            }
        }

        $this->command->info("Invoices created/ensured: {$created}, Items added: {$items}");
    }
}

