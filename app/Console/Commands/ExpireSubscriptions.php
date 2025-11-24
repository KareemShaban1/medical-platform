<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ExpireSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire subscriptions whose end_date has passed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();
        $updated = 0;

        Subscription::query()
            ->where('status', 'active')
            ->whereNotNull('end_date')
            ->where('end_date', '<', $today)
            ->chunkById(500, function ($subscriptions) use (&$updated) {
                foreach ($subscriptions as $subscription) {
                    $subscription->status = 'expired';
                    $subscription->save();
                    $updated++;
                }
            });

        if ($updated > 0) {
            $message = sprintf('Expired %d subscription(s) dated before %s', $updated, Carbon::today()->toDateString());
            Log::info($message);
            $this->info($message);
        } else {
            $this->info('No subscriptions needed to be expired today.');
        }

        return Command::SUCCESS;
    }
}
