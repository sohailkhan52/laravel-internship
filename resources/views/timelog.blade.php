@extends('layouts.app')

@section('content')

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Time Log Report</h1>
        <p class="text-sm text-slate-500 mt-0.5">Work hours summary for <span class="font-semibold text-blue-600">{{ $results['name'] }}</span></p>
    </div>
    <a href="{{ route('timelog.export', [
        'user' => request()->user,
        'from_date' => request()->from_date,
        'to_date' => request()->to_date
    ]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 text-white text-sm font-semibold rounded-xl hover:bg-rose-700 transition-all shadow-sm hover:shadow-md self-start sm:self-auto">
        <i class="bi bi-file-earmark-pdf"></i> Export PDF
    </a>
</div>

<!-- Summary Card -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="bi bi-person-fill text-blue-600"></i>
            </div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Member</span>
        </div>
        <p class="text-lg font-bold text-slate-800">{{ $results['name'] }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 bg-emerald-100 rounded-xl flex items-center justify-center">
                <i class="bi bi-calendar-range text-emerald-600"></i>
            </div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Period</span>
        </div>
        <p class="text-sm font-bold text-slate-800">{{ $results['from_date'] }} → {{ $results['to_date'] }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 bg-violet-100 rounded-xl flex items-center justify-center">
                <i class="bi bi-clock-fill text-violet-600"></i>
            </div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Time</span>
        </div>
        <p class="text-lg font-bold text-slate-800">{{ $results['user_time'] }}</p>
    </div>
</div>

<!-- Time Logs Table -->
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
            <i class="bi bi-list-check text-emerald-600 text-sm"></i>
        </div>
        <h2 class="font-bold text-slate-800">Detailed Time Logs</h2>
        <span class="ml-auto px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-full">
            {{ $time_logs->count() }} entries
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-left">
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Ticket</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Start Time</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">End Time</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Work Detail</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Logged</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($time_logs as $time_log)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-5 py-3.5 text-slate-400 font-medium">{{ $time_log->id }}</td>
                    <td class="px-5 py-3.5">
                        <span class="font-medium text-slate-800">{{ $time_log->ticket->title }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-slate-600">
                        <span class="inline-flex items-center gap-1">
                            <i class="bi bi-play-circle text-emerald-500 text-xs"></i>
                            {{ $time_log->start_time }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-slate-600">
                        <span class="inline-flex items-center gap-1">
                            <i class="bi bi-stop-circle text-red-400 text-xs"></i>
                            {{ $time_log->end_time }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-slate-600 max-w-xs truncate">{{ $time_log->work_detail }}</td>
                    <td class="px-5 py-3.5 text-slate-400 text-xs">{{ \Carbon\Carbon::parse($time_log->created_at)->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <i class="bi bi-clock text-4xl text-slate-300"></i>
                        <p class="text-slate-500 text-sm mt-3">No time logs found for this period</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
