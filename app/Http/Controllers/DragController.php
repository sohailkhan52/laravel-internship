<?php

namespace App\Http\Controllers;
use App\Notifications\TicketCompletionNotification;
use App\Notifications\TicketDeadlineReminder;
use App\Notifications\TicketAssignedNotification;
use App\Mail\TicketCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Priority;
use App\Models\Ticket;
use App\Models\TimeLog;
use App\Models\Checklist;
class DragController extends Controller
{
    //--------------------------------------------------------
    // function for adjusting the time accoding to office time 
    //--------------------------------------------------------
    private function calculateWorkingHours(Carbon $start, Carbon $end)
    {
        $officeStartHour = 11;
        $officeEndHour   = 20;

        $totalPoints = 0;

        $start = $start->copy();
        $end   = $end->copy();

        while ($start->lt($end)) {
            $dayStart = $start->copy()->setHour($officeStartHour)->setMinute(0)->setSecond(0);
            $dayEnd   = $start->copy()->setHour($officeEndHour)->setMinute(0)->setSecond(0);

            if ($start->gt($dayStart)) {
                $dayStart = $start;
            }

            if ($end->lt($dayEnd)) {
                $dayEnd = $end;
            }

            if ($dayStart->lt($dayEnd)) {
                
                $totalHours = $dayStart->diffInHours($dayEnd);

                if($totalHours<5&& $totalHours>-5){
                    $points =$totalHours;
                }elseif($totalHours<-4){
                    $points=$totalHours +1;
                }else{
                    $points= $totalHours -1;// working points excluded brake time
                }
                $totalPoints +=$points;
            }            
            $start->addDay()->startOfDay();
            }
        return $totalPoints;
    }


    public function start(Ticket $ticket)
{
    try {
        if ($ticket->status !== 'open') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket must be open to start'
            ], 400);
        }
        $ticket->update([
            'status'     => 'in_progress',
            'started_at' => now(),
        ]);
         // THIS WILL CREATE TIMELOG IN DRAG AND DROP TICKET
            $log =TimeLog::create([
            'user_id'=>auth()->id(),
            'ticket_id'=>$ticket->id,
            'start_time'=>now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket started successfully',
            'ticket_id' => $ticket->id,
            'new_status' => 'in_progress',
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error starting ticket: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}



public function open(Ticket $ticket)
{
    try {
        if ($ticket->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket must be in progress to reopen'
            ], 400);
        }
       $log = TimeLog::where('ticket_id', $ticket->id)->get()->last();
       if ($log->end_time!==NULL&& $log->start_time===NULL) {

         return redirect()
            ->withErrors('TimeLog must have started time and null end time');            
     }       
        $ticket->update([
            'status'     => 'open',
            'started_at' => null,
        ]);
        $log->update([
        'end_time' => now(),
        'work_detail' =>'Ticket Status had been changed back to open',
    ]);

    
        return response()->json([
            'success' => true,
            'message' => 'Ticket reopened successfully',
            'ticket_id' => $ticket->id,
            'new_status' => 'open',
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error reopening ticket: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}


//--------------------
//TICKET COMPLETION OCCURES HERE
//--------------------
public function closeTicket(Ticket $ticket)
{ 
    try {
        if($ticket->status !== "in_progress" || $ticket->started_at === null){
            return response()->json([
                'success' => false,
                'message' => 'Ticket must be in progress and have a start time'
            ], 400);
        }
        
        foreach($ticket->checklist as $checklist){
            if($checklist->completed_at === null){
                return response()->json([
                    'success' => false,
                    'message' => withErrors('All checklist items must be completed')
                ], 400);
            }
        }

        $completedAt = now();
        $basePoints = $this->calculateWorkingHours($ticket->started_at, $ticket->deadline);
        $penaltyPoints = 0;

        if($completedAt->gt($ticket->deadline)){
            $penaltyPoints = $this->calculateWorkingHours($ticket->deadline, $completedAt);
        }
        
        $points = $basePoints - $penaltyPoints;
       $log = TimeLog::where('ticket_id', $ticket->id)->get()->last();
       if ($log->end_time!==null&& $log->start_time===null) {

         return redirect()
            ->withErrors('TimeLog must have started time and null end time');            
     }   
        $ticket->update([
            'status' => 'closed',
            'completed_at' => $completedAt,
            'points' => $points,
        ]);
        
        $log->update([
        'end_time' => now(),
        'work_detail' => "Ticket status has been changed to closed",
    ]);


        $creator = $ticket->creator;
        if ($creator) {
            $creator->notify(new TicketCompletionNotification($ticket));
        }

        $user = User::find($ticket->assigned_to);
        if ($user) {
            $user->increment('points', $points);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Ticket closed successfully',
            'points_awarded' => $points,
            'ticket_id' => $ticket->id,
            'new_status' => 'closed',
            'route'=> route('time-log.stop', $ticket->id) . '?work_detail=' . urlencode( "ticket have been moved to {$ticket->status}"),
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error closing ticket: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}

}

