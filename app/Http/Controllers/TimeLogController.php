<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\TimeLog;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class TimeLogController extends Controller
{
    public function ExportPdf(Request $request)
    {
        $user_id=$request->user;
        $from_date =$request->from_date;
        $from_time =$request->from_time;
        $from_date = Carbon::parse($from_date . ' ' . $from_time);
        $to_date =$request->to_date;
        $to_time =$request->to_time;
        $to_date = Carbon::parse($to_date . ' ' . $to_time);
        $user=User::find($user_id);

        $user_time = TimeLog::where('user_id',$user_id)
        ->whereBetween('start_time',[$from_date,$to_date])->whereNotNull('end_time')
        ->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(end_time)-TIME_TO_SEC(start_time))) as total_time'))
        ->value('total_time');

        $results =[
            'user_time'=>$user_time,
            'from_date'=>$from_date,
            'to_date'=>$to_date,
            'name'=>$user->name,
        ];

        $time_logs = TimeLog::where('user_id',$user_id)->whereNotNull('end_time')
        ->whereBetween('start_time',[$from_date,$to_date])->get();

        $pdf = Pdf::loadView('pdf.timelog_pdf',compact('results','time_logs'))->setPaper('a4','landscape');
        return $pdf->download('timelog_report.pdf');
    }

    public function userDuration(Request $request){

        $request->validate([
        'from_date' => 'required|date',
        'to_date' => 'required|date',
        'from_time' => 'required',
        'to_time' => 'required',
        'user'=>"required"
         ]);

        $user_id=$request->user;
        $from_date =$request->from_date;
        $from_time =$request->from_time;
        $from_date = Carbon::parse($from_date . ' ' . $from_time);
        $to_date =$request->to_date;
        $to_time =$request->to_time;
        $to_date = Carbon::parse($to_date . ' ' . $to_time);
        $user=User::find($user_id);

        
$user_time = TimeLog::where('user_id', $user_id)->whereBetween('start_time', [$from_date, $to_date])->whereNotNull('end_time')
->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(end_time) - TIME_TO_SEC(start_time))) as total_time'))->value('total_time');

$results=[];
$results['user_time']=$user_time;
$results['from_date']=$from_date;
$results['to_date']=$to_date;
$results['name']=$user->name;
// dd($results);

// all timelogs between from and to date
$time_logs=TimeLog::where('user_id', $user_id)->whereBetween('start_time', [$from_date, $to_date])->whereNotNull('end_time')->get();
         return view("timelog",compact("results",'time_logs'));
    }


    public function start(Request $request){
        dd('$request->ticket');
        $log =TimeLog::create([
            'user_id'=>auth()->id(),
            'ticket_id'=>$request->ticket,
            'start_time'=>now(),
        ]);
      return  redirect()->back();
    }



public function stop( $ticket,Request $request)
{

$work_detail = $request->query('work_detail')??"  ";
    $log = TimeLog::where('ticket_id', $ticket)->get()->last();
    // dd($log);
    if (! $log->user_id) {
            //  dd("hello4");
        return redirect()
            ->withErrors('No active time log found.');            
    }
    if ($log->end_time!==null&& $log->start_time===null) {
        // dd("hello");
        return redirect()
            ->withErrors('TimeLog must have started time and null end time');            
    }

    if ($log->user_id !== auth()->id()) {
            //  dd("hello5");
        // abort(403);
    }

    $log->update([
        'end_time' => now(),
        'work_detail' => $work_detail,
    ]);

         return  redirect()->back();
}


}
