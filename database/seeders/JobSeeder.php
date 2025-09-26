<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Job::factory()->count(100)->create();

        foreach (Job::all() as $job) {
            $job->approvement()->create([
                'module_type' => Job::class,
                'module_id' => $job->id,
                'action_by' => 1,
                'action' => 'approved',
                'notes' => 'Approved by admin',
            ]);
        }
    }
}
