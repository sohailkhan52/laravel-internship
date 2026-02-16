<!-- when the user starts working on a ticket  -->
 public function startTicket(Ticket $ticket)
{
    $ticket->update([
        'started_at' => now(),
        'status' => 'in_progress',
    ]);

    return back();
}

<!-- when user completes a ticket  -->

use Carbon\Carbon;

public function completeTicket(Ticket $ticket)
{
    $now = now();
    $ticket->completed_at = $now;

    // Total worked hours
    $workedHours = $ticket->started_at->diffInHours($now);

    // Deadline check
    if ($now->lessThanOrEqualTo($ticket->deadline)) {
        // Before deadline → reward
        $points = $workedHours * 10;
    } else {
        // After deadline → penalty
        $lateHours = $ticket->deadline->diffInHours($now);
        $points = ($workedHours * 10) - ($lateHours * 15);
    }

    // Update ticket points
    $ticket->points = $points;
    $ticket->status = 'completed';
    $ticket->save();

    // Update user points
    $user = User::find($ticket->assigned_to);
    $user->points += $points;
    $user->save();

    return back();
}
<!-- Every hour Using a scheduled job -->
 php artisan make:command CalculateTicketPoints

 $schedule->command('tickets:calculate-points')->hourly();

 <!-- display in blade  -->

 <p>Ticket Points: {{ $ticket->points }}</p>
<p>User Points: {{ auth()->user()->points }}</p>










use Carbon\Carbon;

public function close(Ticket $ticket)
{
    if ($ticket->status !== "in_progress") {
        return back();
    }

    $completedAt = now();

    // Base points: start → deadline
    $basePoints = $this->calculateWorkingHours(
        $ticket->started_at,
        $ticket->deadline
    );

    // Penalty: deadline → completed_at (only if late)
    $penaltyPoints = 0;

    if ($completedAt->gt($ticket->deadline)) {
        $penaltyPoints = $this->calculateWorkingHours(
            $ticket->deadline,
            $completedAt
        );
    }

    // Final points (can be negative)
    $points = $basePoints - $penaltyPoints;

    $ticket->update([
        'status'       => "closed",
        'completed_at' => $completedAt,
        'points'       => $points,
    ]);

    User::where('id', $ticket->assigned_to)
        ->increment('points', $points);

    return back();
}






//function for adjusting the time according to office time
use Carbon\Carbon;

function calculateWorkingHours(Carbon $start, Carbon $end)
{
    $officeStartHour = 11;
    $officeEndHour   = 20;
    $totalPoints    = 0;

    while ($start->lt($end)) {
        // Start and end of office hours for the current day
        $dayStart = $start->copy()->setHour($officeStartHour)->setMinute(0)->setSecond(0);
        $dayEnd   = $start->copy()->setHour($officeEndHour)->setMinute(0)->setSecond(0);

        // Adjust first day
        if ($start->gt($dayStart)) {
            $dayStart = $start;
        }

        // Adjust last day
        if ($end->lt($dayEnd)) {
            $dayEnd = $end;
        }

        // Calculate hours worked for this day
        if ($dayStart->lt($dayEnd)) {
            $hours = $dayStart->diffInHours($dayEnd);

            // Apply daily point adjustment
            if ($hours <= 4) {
                $points = $hours;
            } else {
                $points = $hours - 1; // subtract 1 point if > 4
            }

            $totalPoints += $points;
        }

        $start->addDay()->startOfDay();
    }

    return $totalPoints;
}




use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    public function __construct(public $task) {}

    public function via($notifiable)
    {
        return ['database']; // in-app
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Task Assigned',
            'message' => 'You were assigned: ' . $this->task->title,
            'task_id' => $this->task->id,
        ];
    }
}

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketDeadlineReminder;
use Carbon\Carbon;

public function handle()
{
    $tickets = Ticket::whereNull('completed_at')->get();

    foreach ($tickets as $ticket) {

        if (!$ticket->deadline) {
            continue;
        }

        $hoursLeft = now()->diffInHours(
            Carbon::parse($ticket->deadline),
            false
        );

        // notify only if 1–4 hours left
        if ($hoursLeft > 0 && $hoursLeft <= 4) {

            $user = User::find($ticket->assigned_to);

            if ($user) {
                $user->notify(
                    new TicketDeadlineReminder($ticket)
                );
            }
        }
    }
}


public function updateStatus(Request $request, Ticket $ticket)
{
    $request->validate([
        'status' => 'required|in:open,in_progress,closed',
    ]);

    $ticket->update([
        'status' => $request->status
    ]);

    return response()->json(['success' => true]);
}



<?php


use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}




class TimeLogController extends Controller
{
    // Start working
    public function start(Request $request)
    {
        $log = TimeLog::create([
            'user_id' => auth()->id(),
            'ticket_id' => $request->ticket_id,
            'start_time' => now(),
        ]);

        return response()->json($log);
    }

    // Stop working
    public function stop(Request $request)
    {
        $log = TimeLog::find($request->log_id);
        $log->end_time = now();
        $log->work_detail = $request->work_detail; // optional notes
        $log->save();

        return response()->json($log);
    }
}

 Route::post('/time-log/start', [TimeLogController::class, 'start']);
    Route::post('/time-log/stop', [TimeLogController::class, 'stop']);




    
        $pdf = Pdf::loadView('pdf.timelog_pdf',compact('results','time_logs'))->setPaper('a4','landscape');

    return $pdf->download('timelog_report.pdf');