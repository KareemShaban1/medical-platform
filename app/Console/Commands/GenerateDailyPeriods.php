<?php

namespace App\Console\Commands;

use App\Services\Appointment\PeriodGeneratorService;
use App\Services\Appointment\AppointmentService;
use Illuminate\Console\Command;

class GenerateDailyPeriods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:generate-periods
                            {--days=30 : Number of days ahead to generate periods for}
                            {--doctor= : Specific doctor profile ID to generate for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily periods for doctors based on their working hours and availability overrides';

    protected $generatorService;
    protected $appointmentService;

    /**
     * Create a new command instance.
     */
    public function __construct(
        PeriodGeneratorService $generatorService,
        AppointmentService $appointmentService
    ) {
        parent::__construct();
        $this->generatorService = $generatorService;
        $this->appointmentService = $appointmentService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting daily period generation...');

        $daysAhead = (int) $this->option('days');
        $doctorId = $this->option('doctor');

        try {
            if ($doctorId) {
                // Generate for specific doctor
                $this->info("Generating periods for doctor ID: {$doctorId}");
                $count = $this->generatorService->generatePeriodsForDoctor($doctorId, $daysAhead);
                $this->info("✓ Generated {$count} periods for doctor {$doctorId}");
            } else {
                // Generate for all doctors
                $this->info("Generating periods for all approved doctors...");
                $results = $this->generatorService->generatePeriodsForAllDoctors($daysAhead);

                $this->info("✓ Successfully processed {$results['success']} doctors");

                if ($results['skipped'] > 0) {
                    $this->warn("⚠ Skipped {$results['skipped']} doctors");
                }

                if ($results['errors'] > 0) {
                    $this->error("✗ Failed to process {$results['errors']} doctors");
                }
            }

            // Expire pending appointments with expired confirmation codes
            $this->info('Expiring pending appointments...');
            $expiredCount = $this->appointmentService->expirePendingAppointments();
            $this->info("✓ Expired {$expiredCount} pending appointments");

            $this->info('✓ Daily period generation completed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('✗ Error: ' . $e->getMessage());
            return 1;
        }
    }
}

