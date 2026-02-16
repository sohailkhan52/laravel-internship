<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Time Log Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; font-size: 12px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

<h3>User Time Summary</h3>

<table>
    <tr>
        <th>Name</th>
        <th>From Date</th>
        <th>To Date</th>
        <th>Total Time</th>
    </tr>
    <tr>
        <td>{{ $results['name'] }}</td>
        <td>{{ $results['from_date'] }}</td>
        <td>{{ $results['to_date'] }}</td>
        <td>{{ $results['user_time'] }}</td>
    </tr>
</table>

<h3>Time Logs</h3>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Ticket Title</th>
            <th>Start</th>
            <th>End</th>
            <th>Work Detail</th>
        </tr>
    </thead>
    <tbody>
        @foreach($time_logs as $log)
        <tr>
            <td>{{ $log->id }}</td>
            <td>{{ $log->ticket->title }}</td>
            <td>{{ $log->start_time }}</td>
            <td>{{ $log->end_time }}</td>
            <td>{{ $log->work_detail }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
