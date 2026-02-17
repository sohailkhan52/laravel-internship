<?php

namespace App\Http\Controllers;

use App\Notifications\TicketCompletionNotification;
use App\Notifications\TicketDeadlineReminder;
use App\Notifications\TicketAssignedNotification;
use App\Mail\TicketCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Priority;
use App\Models\Ticket;
use App\Models\Board;
use App\Models\TimeLog;
use App\Models\Checklist;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){

     $user = auth()->user();
     $userId = $user->id;

        // $userId=auth()->id();

        //
       // ticket completion notification 
        $user = auth()->user();

        // I AM USING TO STORE EVERY USER TOTAL TIME IN A TOTAL TIME ARRAY
        $total_time=[];
        $all_user=User::all();
        foreach($all_user as $single_user)
            {    $user_total = TimeLog::where('user_id', $single_user->id)->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(end_time) - TIME_TO_SEC(start_time))) as total_time'))->value('total_time');

          $total_time[$single_user->id] = $user_total;
            }        

            // dd($total_time);
        // I AM CALCULATING THIS FOR MEMBER TOTAL TIME  TO SHOWV ON HOME PAGE
        //----------------------------
        $user_time = TimeLog::select(
        DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(end_time) - TIME_TO_SEC(start_time))) as total_time')
        )->where('user_id',$user)->value('total_time');
        
         
        $unread = $user->unreadNotifications;
        $read = $user->readNotifications;
        $user->unreadNotifications->markAsRead();
        $allNotifications = $unread->merge($read)->sortByDesc('created_at');        
        $boards=Board::all();

        //getting priority
        $priorities=Priority::all();

        $admin = $user->hasRole('admin'); // true if admin, false if member
        if ($admin) {
    $tickets = Ticket::latest()->get(); // all tickets for admin
    $boards = Board::with('ticket')->get();
         } else {
    $tickets = Ticket::where('assigned_to', $userId)
                     ->latest()->get(); // only user's tickets
                     $boards = Board::with(['ticket' => function($query) use ($userId) {$query->where('assigned_to', $userId);    }])->get();
       }
       

        // to see all users in time log dropdown 
          $total_users=User::all();
           $members = $admin ? User::role('member')->get():null; 
        // admin sees members, member sees null



        
             $member=User::where("role","member")->get();
        

             $users=User::where("role","member")->orderBy('points', 'desc')->limit(4)->get();
             
        return view('home', compact('user_time','boards','member','members','total_time',
        'total_users','users', 'tickets', 'priorities', 'unread', 'allNotifications'
        ));
    }


    public function createTicket(Request $request){
        
           
               $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to'   => 'required|array',
            'assigned_to.*' => 'exists:users,id',

            'priority_id' => 'required',
            'board_id' => 'required',
            'attachment' => 'required',
            'checklist' => 'required',
            'deadline' => 'required|date|after_or_equal:today',
        ]);
        $deadline=Carbon::parse($request->deadline)->endOfDay();

        // dd($request->board_id);
        // decide the file storing path
        // Check if attachment exists
         if ($request->hasFile('attachment')) {
         $file = $request->file('attachment');
         // store in storage/app/public/tickets
         $path = $file->store('tickets', 'public'); // 'tickets' folder in storage/app/public
         } else {
          $path = null;
         }
       
         //TICKET CREATION 
        foreach($request->assigned_to as $member){
         $ticket = Ticket::create([
            'title'       => $request->title,
            'board_id'       => $request->board_id,
            'description' => $request->description,
            'created_by'  => auth()->id(),
            'assigned_to' => $member,
            'priority_id' => $request->priority_id,
            'attachment' => $path,
            'deadline' => $deadline,
            'status' => "open",
        ]);


        //CREATS THE CHECKLIST OF EACH TICKET
        foreach($request->checklist as $checklist){
            $checklist=Checklist::create([
                "ticket_id"=>$ticket->id,
                "name"=>$checklist,
                "completed_at"=>null
            ]);
            
        }
        $assignee = User::findOrFail($request->assigned_to);
        $ticket->assignee->notify(new TicketAssignedNotification($ticket));

        $member = User::find($member);
        // dd($member->email);
if ($member) {
    // Send email using default mailer
 // Send email to this member
        Mail::to($member->email)->send(new TicketCreated($ticket));
}
        }

    return redirect()->route('home');

    }


    // Member can view their assigned tickets
    public function myTickets()
    {
        $tickets = Ticket::where('assigned_to', auth()->id())->latest()->get();

        return response()->json($tickets);
    }

    //--------------------------------------------------------
    // FUNCTION FOR ADJUSTING DRAG AND DROP
    //--------------------------------------------------------


     public function updateStatus(Request $request, Ticket $ticket)
    {
    // 🔒 LOCK CLOSED TICKETS FOREVER
    if ($ticket->status === 'closed') {
        return response()->json([
            'success' => false,
            'message' => 'Closed tickets cannot be moved'
        ], 403);
    }

    // ✅ Validate allowed statuses
    $request->validate([
        'status' => 'required|in:open,in_progress,closed'
    ]);

    // ✅ Update status
    
    $ticket->status = $request->status;
    $ticket->save();

    return response()->json([
        'success' => true,
        'status' => $ticket->status
    ]);
}


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

    public function checklist(Request $request){
        $checklist=Checklist::find($request->checklist);
        if($checklist->completed_at!==NULL){
        $checklist->update(["completed_at"=>NULL]);
            return back()->with('error', "$checklist->name Checklist had been changed back");}
            $checklist->update(["completed_at"=>now()]);
            return back()->with('success', "$checklist->name Checklist had been completed successfully");
    }
// this function help in counting points while   the ticket progress 
    public function start(Ticket $ticket)
{
   

    $started=now();
    $ticket->update([
        'status'     => 'in_progress',
        'started_at' => now(),
    ]);

     return redirect()
        ->route('time-log.start', $ticket->id)
        ->with('success', 'The ticket status has been changed to started phase.');
}
    public function open(Ticket $ticket,Request $request)
{

    if ($ticket->status !== 'in_progress') {
        return back()->with('error', 'the ticket status should be in progress to changed to open');
    }
    $ticket->update([
        'status'     => 'open',
        'started_at'     => null,
    ]);

       $work_detail=$request->input('Work-detail');
         return redirect()
        ->route('time-log.stop',['ticket' => $ticket->id,'work_detail' => $work_detail])->with('success', 'the ticket status has been changed to open.');
}

//--------------------
//TICKET COMPLETION OCCURES HERE
//--------------------
public function close(Ticket $ticket,Request $request)
{
    if($ticket->status !=="in_progress"|| $ticket->started_at ===null){
        return back()->with('error', 'Ticket must have status "inprogress" and started_at cannot be null');
    }
    

    foreach($ticket->checklist as $checklist){
        if($checklist->completed_at===null){
             return back()->with('error', 'All checklist items must be completed before closing the ticket.');
        }
    }
    $completedAt = now();
    // Base points: start → deadline
    $basePoints = $this->calculateWorkingHours(
    $ticket->started_at,
    $ticket->deadline
     );

    $penaltyPoints = 0;

    if($completedAt->gt($ticket->deadline)){
        $penaltyPoints = $this->calculateWorkingHours(
            $ticket->deadline,
            $completedAt
        );
    }
    // Final points (can be negative)
    $points = $basePoints - $penaltyPoints ;

    $ticket->update([
        'status'=>"closed",
        'completed_at' => now(),
        'points'=>$points,
    ]);
$creator = $ticket->creator;
if ($creator) {
    $creator->notify(new TicketCompletionNotification($ticket));
}
    //update user points
$user = User::find($ticket->assigned_to);
if ($user) {
    $user->increment('points', $points);
} 
               $work_detail=$request->input('Work-detail');
         return redirect()
        ->route('time-log.stop',['ticket' => $ticket->id,'work_detail' => $work_detail])->with('success', 'Ticket completed and creator notified!');

}

}

