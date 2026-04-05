<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Quote;
use Carbon\Carbon;

class UpdateDailyQuote extends Command
{
    protected $signature = 'quote:daily-update';
    protected $description = 'Select a random quote for today';

    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');

        // Check if we already have a quote for today
        if (Quote::where('is_active_for_date', $today)->exists()) {
            $this->info('Quote for today already exists.');
            return;
        }

        // Reset previous active quotes (optional, strictly speaking we just look for today's date)
        // Quote::whereNotNull('is_active_for_date')->update(['is_active_for_date' => null]);

        // Pick a random quote
        $quote = Quote::inRandomOrder()->first();

        if ($quote) {
            $quote->update(['is_active_for_date' => $today]);
            $this->info("Today's quote updated: " . $quote->content);

            // Send push notifications to all users with active subscriptions
            $users = \App\Models\User::whereHas('pushSubscriptions')->get();
            foreach ($users as $user) {
                $user->notify(new \App\Notifications\DailyQuoteNotification($quote));
            }
            $this->info("Notifications sent to " . $users->count() . " users.");
        } else {
            $this->error('No quotes found in database.');
        }
    }
}
