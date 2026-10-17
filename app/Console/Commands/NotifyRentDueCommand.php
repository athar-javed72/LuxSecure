<?php

namespace App\Console\Commands;

use App\Models\RentPayment;
use App\Notifications\RentDueNotification;
use Illuminate\Console\Command;

class NotifyRentDueCommand extends Command
{
    protected $signature = 'luxsecure:notify-rent-due';

    protected $description = 'Notify tenants of rent payments due today or overdue';

    public function handle(): int
    {
        $payments = RentPayment::query()
            ->with(['tenancy.tenant'])
            ->whereIn('status', ['pending', 'overdue'])
            ->whereDate('due_date', '<=', now()->toDateString())
            ->get();

        $sent = 0;
        foreach ($payments as $payment) {
            $tenant = $payment->tenancy?->tenant;
            if (! $tenant) {
                continue;
            }

            if ($payment->due_date->isPast() && $payment->status === 'pending') {
                $payment->update(['status' => 'overdue']);
            }

            $tenant->notify(new RentDueNotification($payment));
            $sent++;
        }

        $this->info("Sent {$sent} rent due notifications.");

        return self::SUCCESS;
    }
}
