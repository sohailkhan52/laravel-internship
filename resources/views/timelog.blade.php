@extends('layouts.app')

@section('content')

<div class="col-md-6 mx-auto">
<div class="card shadow">
    <div class="card-header bg-success text-light">User Time </div>
    <div class="card-body">
        <table class=table>
            <thead>
                <th class="bg-info text-light">Name</th>
                <th class="bg-info text-light">From date</th>
                <th class="bg-info text-light">To Date</th>
                <th class="bg-info text-light">User Time</th>
            </thead>
            <tbody>
                
                <tr>
                    <td>{{$results['name']}}</td>
                    <td>{{$results['from_date']}}</td>
                    <td>{{$results['to_date']}}</td>
                    <td>{{$results['user_time']}}</td>
                </tr>
              
            </tbody>
        </table>
    </div>
    </div>
    
</div>
<hr>
<div class="col-md-8 mt-5 mx-auto">
    <div class="card shadow">
    <div class="card-header bg-success text-light"> Time logs </div>
    <div class="card-body">
        <table class=table>
            <thead>
                <th class="bg-info text-light">ID</th>
                <th class="bg-info text-light">Ticket Title</th>
                <th class="bg-info text-light">Start Time</th>
                <th class="bg-info text-light">End Time</th>
                <th class="bg-info text-light">Work Detail</th>
                <th class="bg-info text-light">Created At</th>
                <th class="bg-info text-light">Updated At</th>
            </thead>
            <tbody>
                @foreach($time_logs as $time_log)
                <tr>
                    <td>{{$time_log->id}}</td>
                    <td>{{$time_log->ticket->title}}</td>
                    <td>{{$time_log->start_time}}</td>
                    <td>{{$time_log->end_time}}</td>
                    <td>{{$time_log->work_detail}}</td>
                    <td>{{$time_log->created_at}}</td>
                    <td>{{$time_log->updated_at}}</td>
                </tr>
              @endforeach
            </tbody>
        </table>
    </div>
    </div>
</div>
<a href="{{ route('timelog.export', [
    'user' => request()->user,
    'from_date' => request()->from_date,
    'to_date' => request()->to_date
]) }}" class="btn btn-outline-primary">
    Export To PDF
</a>

@endsection