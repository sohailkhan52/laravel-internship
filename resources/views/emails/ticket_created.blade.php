<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Ticket Assigned</title>
</head>
<body>
    <h2>New Ticket Assigned to You</h2>
    <p>Hello {{ $ticket->assignee->name ?? 'User' }},</p>

    <p>A new ticket has been assigned to you by the admin. Here are the details:</p>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Title</th>
            <td>{{ $ticket->title }}</td>
        </tr>
        <tr>
            <th>Description</th>
            <td>{{ $ticket->description }}</td>
        </tr>
        <tr>
            <th>Priority</th>
            <td>{{ $ticket->priority->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Assigned By</th>
            <td>{{ $ticket->creator->name ?? 'Admin' }}</td>
        </tr>
        <tr>
            <th>Deadline</th>
            <td>{{ $ticket->deadline }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ ucfirst($ticket->status) }}</td>
        </tr>
    </table>

    <p>Thanks,<br>Support Team</p>
</body>
</html>
