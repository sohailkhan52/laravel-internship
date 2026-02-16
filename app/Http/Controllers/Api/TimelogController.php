<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\TimeLog;
use App\Models\User;

class TimelogController extends Controller
{
    public function userDurationPdf(Request $request){
        $user_id=$request->user_id;
$from_date=Carbon::parse($request->from_date)->startOfDay();
$to_date=Carbon::parse($request->to_date)->endOfDay();
$user=User::find($user_id);

$user_time=TimeLog::where('user_id',$user_id)->whereBetween("start_time",[$from_date,$to_date])
->whereNotNull('end_time')->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(end_time) - TIME_TO_SEC(start_time))) as total_time'))->value('total_time');

$time_logs = TimeLog::where('user_id',$user_id)->whereNotNull('end_time')->whereBetween('start_time',[$from_date,$to_date])->get();


$results=[
'user_time'=>$user_time,
'from_date'=>$from_date,
'to_date'=>$to_date,
'name'=>$user->name,];



    $pdf = Pdf::loadView('pdf.timelog_pdf',compact("results","time_logs"))->setPaper("a4",'landscape');
    return $pdf->download('timelog_report.pdf');
    }



    public function userDuration(Request $request){
        $user_id=$request->user_id;
$from_date=Carbon::parse($request->from_date)->startOfDay();
$to_date=Carbon::parse($request->to_date)->endOfDay();
$user=User::find($user_id);

$user_time=TimeLog::where('user_id',$user_id)->whereBetween("start_time",[$from_date,$to_date])
->whereNotNull('end_time')->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(end_time) - TIME_TO_SEC(start_time))) as total_time'))->value('total_time');
$results=[];
$results['user_time']=$user_time;
$results['from_date']=$from_date;
$results['to_date']=$to_date;
$results['name']=$user->name;

$time_logs = TimeLog::where('user_id',$user_id)->whereNotNull('end_time')->whereBetween('start_time',[$from_date,$to_date])->get();

    return response()->json(['Status'=>true,"Search Result"=>$results,"Time logs"=>$time_logs]);
    }



    public function start($ticket_id){
        $log=TimeLog::create([
            'user_id'=>auth()->id(),
            "ticket_id"=>$ticket_id,
            "start_time"=>now()
        ]);
        return response()->json(["Status"=>true,"Message"=>"Time log created successfully","Data"=>$log]);
    }
   public function stop(Request $request, $ticket_id)
{
    $work_detail = $request->query('work_detail', '');

        $log=TimeLog::where('ticket_id',$ticket_id) ->orderBy('id', 'desc')->first();
        if(!$log->user_id){
        return response()->json(["error"=>'No active time log found.']);
        }
        if($log->user_id !== auth()->id()&& auth()->user()->role !== 'admin'){
            return response()->json(['status' => false,'message' => 'Unauthorized access'], 403);
        }
        if ($log->end_time!==null) {
         return response()->json(["error"=>'TimeLog must have started time and null end time']);}
        $log->update([
        'end_time' => now(),
        'work_detail' => $work_detail,
         ]);
        return response()->json(["status"=>true,"end_time"=>now(),"work_detail"=>$work_detail]);
    }
}
