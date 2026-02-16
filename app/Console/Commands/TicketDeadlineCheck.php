<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketDeadlineReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TicketDeadlineCheck extends Command
{
    protected $signature = 'tickets:deadline-check';
    protected $description = 'Notify users when ticket deadline is less than 4 hours';

    public function handle()
    {
        $tickets = Ticket::whereNull('completed_at')
            ->whereNotNull('deadline')
            ->get();

        foreach ($tickets as $ticket) {

            $deadline = Carbon::parse($ticket->deadline)
                ->setTimezone(config('app.timezone'));

            $hoursLeft = now()->setTimezone(config('app.timezone'))
                ->diffInHours($deadline, false);

            $minutesLeft = now()->setTimezone(config('app.timezone'))
                ->diffInMinutes($deadline, false);

            // Only tickets less than 4 hours and still in the future
            if ($hoursLeft < 4 && $minutesLeft > 0) {

                $user = User::find($ticket->assigned_to);
                if (!$user) continue;

                $user->notify(new TicketDeadlineReminder($ticket));

                \Log::info("Deadline reminder sent for ticket {$ticket->id} | {$minutesLeft} minutes left");
            }
        }
    }
}
