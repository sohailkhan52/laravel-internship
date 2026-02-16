<?php

namespace App\Http\Controllers\Api;
use App\Notifications\TicketCompletionNotification;
use App\Notifications\TicketDeadlineReminder;
use App\Notifications\TicketAssignedNotification;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\TicketCreated;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Priority;
use App\Models\Ticket;
use App\Models\Checklist;

class TicketController extends Controller
{
    //------------------------------------------------------------
    // FUNCTION TO DISPLAY TICKET ON THE BASES OF MEMBER AND ADMIN
    //------------------------------------------------------------
     public function index(){

     $user = auth()->user();
     $userId = $user->id;

        $unread = $user->unreadNotifications;
        $read = $user->readNotifications;
        $user->unreadNotifications->markAsRead();
        $allNotifications = $unread->merge($read)->sortByDesc('created_at');        

        //getting priority
        $priorities=Priority::all();
        $topUsers="";
        $admin = $user->hasRole('admin'); // true if admin, false if member
        if ($admin) {
            
             $topUsers=User::where("role","member")->orderBy('points', 'desc')->limit(4)->get();
    $tickets = Ticket::latest()->get(); // all tickets for admin
         } else {
    $tickets = Ticket::where('created_by', $userId)
                     ->orWhere('assigned_to', $userId)
                     ->latest()->get(); // only user's tickets
       }

    //    TO  GAIN CHECKLISTS ON THE BASES OF TICKETS
    $checklists=[];
    foreach ($tickets as $checklistTicket) {

    $checklists[$checklistTicket->id]=Checklist::where("ticket_id",$checklistTicket->id)->get();
    
    }



       $open_tickets=$tickets->where("status","open")->values();
       $in_progress_tickets=$tickets->where("status","in_progress")->values();
       $closed_tickets=$tickets->where("status","closed")->values();
                     
       //SHOWS RESPONSE ON THE BASES OF MEMBERS AND ADMIN ROLE
            if($user->hasRole('admin')){
                return response()->json([
            'success' => true,
            'unreadNotifications' => $unread,
            'notifications' => $allNotifications,
            'All tickets' => $tickets->count(),
            'open' => $open_tickets,
            'in_progress' => $in_progress_tickets,
            'closed' => $closed_tickets,
            'Checklists' => $checklists,
            'Top Users' => $topUsers,
            ]);
             }
             
                return response()->json([
            'success' => true,
            'unreadNotifications' => $unread,
            'notifications' => $allNotifications,
            'All tickets' => $tickets->count(),
            'open' => $open_tickets,
            'in_progress' => $in_progress_tickets,
            'closed' => $closed_tickets,
            'Checklists' => $checklists,
            ]);

    }

    //-----------------------------
    // FUNCTION FOR TICKET CREATING
    //-----------------------------
    public function createTicket(Request $request){
                
               $validator =Validator::make($request->all(),[
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to'   => 'required|array',
            'assigned_to.*' => 'exists:users,id',

            'priority_id' => 'required',
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',

            'deadline' => 'required|date|after_or_equal:today',
        ]);

        if($validator->fails()){
            return response()->json(['data'=>$validator->errors()]);
        }
 

        // decide the file storing path
        // Check if attachment exists
         if ($request->hasFile('attachment')) {
         $file = $request->file('attachment');
         // store in storage/app/public/tickets
         $path = $file->store('tickets', 'public'); // 'tickets' folder in storage/app/public
         } else {
          $path = null;
         }
        foreach($request->assigned_to as $member){
         $ticket = Ticket::create([
            'title'       => $request->title,
            'description' => $request->description,
            'created_by'  => auth()->id(),
            'assigned_to' => $member,
            'priority_id' => $request->priority_id,
            'attachment' => $path,
            'deadline' => $request->deadline,
            'status' => "open",
        ]);

        $checklists=[];
        foreach ($request->checklists as $checklist) {
            $checklists=Checklist::create(['name'=>$checklist,
            "ticket_id"=>$ticket->id,]);
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

    
            return response()->json(["status"=>true,"message"=>"Ticket Created successfully",'data'=>$ticket,"checklists"=>$checklists]);

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

//-----------------------------------------------------
//FUNCTION FOR CHANGING STATUS FROM OPEN TO IN_PROGRESS 
//-----------------------------------------------------
    public function startTicket(Ticket $ticket)
{

    $user = auth()->user();
    $ticket_id=$_POST['id'];
                if (empty($ticket_id)) {
        return response()->json(["status" => false,"message" => "Ticket id is empty"], 404);} 
    $ticket = Ticket::where('id', $ticket_id)
    ->where(function ($query) use ($user) {
        $query->whereJsonContains('assigned_to', $user->id)
              ->orWhere('created_by', $user->id);})->first();

        if (!$ticket) {
        return response()->json(["status" => false,"message" => "Ticket not found"], 404);    
        }
    if ($ticket->status !== 'open') {
    return response()->json(["status"=>false,"message"=>"The ticket is already in terminated from open status"]);
    }
    $started=now();
    $ticket->update([
        'status'     => 'in_progress',
        'started_at' => now(),
    ]);
$route= route('timee-log.start', $ticket->id) ;
    return response()->json(["status"=>true,
    "message"=>"Ticket is started at $ticket->started_at",
            'route'=> $route,]);
}
//-----------------------------------------------------
//FUNCTION FOR CHECKLIST COMPLETION AND INCOMPLETION
//-----------------------------------------------------
public function checklist(Request $request){

    $checklist=Checklist::where("id",$request->id)->get();

    if(!$checklist){     
        return response()->json(["error"=>"invalid checklist id"]);
        }
        

        foreach($checklist as $checklist){
    $ticket=Ticket::find($checklist->ticket_id);
    if($ticket->assigned_to!==auth()->id()&& auth()->user()->role!=="admin")
        {
    return response()->json(["error"=>"invalid check list"]);}
            if($checklist->completed_at!==NULL){
        $checklist->update(["completed_at"=>NULL]);
            return response()->json(['success'=> "$checklist->name Checklist has been changed back to incomplete state"]);}
            $checklist->update(["completed_at"=>now()]);
    return response()->json(["status"=>$checklist]);}
}
//-----------------------------------------------------
//FUNCTION FOR CHANGING STATUS FROM IN_PROGRESS TO OPEN
//-----------------------------------------------------
    public function openTicket(Ticket $ticket)
{
    $user = auth()->user();
    $ticket_id=$_POST['id'];
                if (empty($ticket_id)) {
        return response()->json(["status" => false,"message" => "Ticket id is empty"], 404);} 
    $ticket = Ticket::where('id', $ticket_id)
    ->where(function ($query) use ($user) {
        $query->whereJsonContains('assigned_to', $user->id)
              ->orWhere('created_by', $user->id);})->first();

        if (!$ticket) {
        return response()->json(["status" => false,"message" => "Ticket not found"], 404);    
        }
    if ($ticket->status !== 'in_progress') {
    return response()->json(["status"=>$ticket->status,"message"=>"The ticket status should be in 'in_progress' to change to  open status"]);
    }
    $started=now();
    $ticket->update([
        'status'     => 'open',
        'started_at' => null,
    ]);

    return response()->json(["status"=>"in_progress","message"=>"Ticket status had been successfully changed to open ",'route'=> route('timee-log.stop', $ticket->id) . '?work_detail=' . urlencode( "ticket have been moved to {$ticket->status}"),]);
}

//-------------------------------
//FUNCTION FOR TICKET COMPLETING 
//-------------------------------
public function closeTicket(Ticket $ticket)
{
$user = auth()->user();
$ticket_id = $_POST['id'];

$ticket = Ticket::where('id', $ticket_id)
    ->where(function ($query) use ($user) {
        $query->whereJsonContains('assigned_to', $user->id)
              ->orWhere('created_by', $user->id);})->first();

    if(!$ticket){
        return response()->json(["data"=>"Ticket Not Found"]);
    }
    if($ticket->status ==="closed"){
        return response()->json(["status"=>"Ticket is already in Completed State"]);
    }
    if($ticket->status !=="in_progress"||$ticket->started_at===null){
        return response()->json(["status"=>$ticket->status,"Message"=>"Error: 'Ticket Status' must be 'in_progress' and 'started_at' cannot be null"]);
    }

        foreach ($ticket->checklist as $checklist) {
            if($checklist->completed_at===null){
        return response()->json(['error'=> "Ticket have a checklist with name $checklist->name is  incomplete."]);
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


        return response()->json(["Status"=>$ticket->status,"Message"=>"Ticket have been completed successfully and the creator has been notified","Points of the ticket"=>"$points",
            'route'=> route('timee-log.stop', $ticket->id) . '?work_detail=' . urlencode( "ticket have been moved to {$ticket->status}"),]);

}

}

