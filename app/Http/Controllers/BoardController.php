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
class BoardController extends Controller
{
    public function index(Request $request){
     $board_id=$request->board_id;
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
    $tickets = Ticket::where("board_id",$board_id)->latest()->get(); // all tickets for admin
         } else {
    $tickets = Ticket::where("board_id",$board_id)
                     ->where('assigned_to', $userId)
                     ->latest()->get(); // only user's tickets
       }
       

        // to see all users in time log dropdown 
          $total_users=User::all();
           $members = $admin ? User::role('member')->get():null; 
        // admin sees members, member sees null



        
             $member=User::where("role","member")->get();
        

             $users=User::where("role","member")->orderBy('points', 'desc')->limit(4)->get();
             
        return view('board', compact('user_time','boards','member','members','total_time',
        'total_users','users', 'tickets', 'priorities', 'unread', 'allNotifications'
        ));
    }

}
