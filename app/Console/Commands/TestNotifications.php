<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Models\ClinicUser;
use Illuminate\Notifications\DatabaseNotification;

class TestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notifications {type=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test notifications for admins and clinic users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');

        if ($type === 'admin' || $type === 'all') {
            $this->createAdminNotifications();
        }

        if ($type === 'clinic' || $type === 'all') {
            $this->createClinicNotifications();
        }

        $this->info('Test notifications created successfully!');
    }

    private function createAdminNotifications()
    {
        $admins = Admin::all();

        if ($admins->isEmpty()) {
            $this->warn('No admins found. Please create an admin first.');
            return;
        }

        foreach ($admins as $admin) {
            // Create some test notifications
            DatabaseNotification::create([
                'id' => \Str::uuid(),
                'type' => 'App\Notifications\ProfileSubmittedForReview',
                'notifiable_type' => Admin::class,
                'notifiable_id' => $admin->id,
                'data' => [
                    'title' => 'New Profile Submitted for Review',
                    'message' => 'Dr. John Doe has submitted their profile for review.',
                    'action_url' => route('admin.doctor-profiles.index'),
                    'type' => 'profile_submitted',
                    'doctor_name' => 'Dr. John Doe',
                ],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DatabaseNotification::create([
                'id' => \Str::uuid(),
                'type' => 'App\Notifications\ProfileSubmittedForReview',
                'notifiable_type' => Admin::class,
                'notifiable_id' => $admin->id,
                'data' => [
                    'title' => 'Profile Review Required',
                    'message' => 'Dr. Sarah Smith has updated their profile and resubmitted for review.',
                    'action_url' => route('admin.doctor-profiles.pending'),
                    'type' => 'profile_submitted',
                    'doctor_name' => 'Dr. Sarah Smith',
                ],
                'created_at' => now()->subMinutes(30),
                'updated_at' => now()->subMinutes(30),
            ]);
        }

        $this->info('Created test notifications for ' . $admins->count() . ' admin(s)');
    }

    private function createClinicNotifications()
    {
        $clinicUsers = ClinicUser::all();

        if ($clinicUsers->isEmpty()) {
            $this->warn('No clinic users found. Please create a clinic user first.');
            return;
        }

        foreach ($clinicUsers->take(2) as $user) {
            // Create approved notification
            DatabaseNotification::create([
                'id' => \Str::uuid(),
                'type' => 'App\Notifications\ProfileApproved',
                'notifiable_type' => ClinicUser::class,
                'notifiable_id' => $user->id,
                'data' => [
                    'title' => 'Profile Approved',
                    'message' => 'Congratulations! Your doctor profile has been approved and is now publicly visible.',
                    'action_url' => route('clinic.doctor-profiles.index'),
                    'type' => 'profile_approved',
                    'reviewer_name' => 'Admin',
                ],
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ]);

            // Create rejected notification
            DatabaseNotification::create([
                'id' => \Str::uuid(),
                'type' => 'App\Notifications\ProfileRejected',
                'notifiable_type' => ClinicUser::class,
                'notifiable_id' => $user->id,
                'data' => [
                    'title' => 'Profile Rejected',
                    'message' => 'Your profile has been rejected. Please review the feedback and make necessary changes.',
                    'action_url' => route('clinic.doctor-profiles.index'),
                    'type' => 'profile_rejected',
                    'rejection_reason' => 'Please add more details to your bio section.',
                    'reviewer_name' => 'Admin',
                ],
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]);
        }

        $this->info('Created test notifications for ' . $clinicUsers->take(2)->count() . ' clinic user(s)');
    }
}
