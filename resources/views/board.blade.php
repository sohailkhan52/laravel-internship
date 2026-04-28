@extends('layouts.app')

@section('content')

<!-- Flash Messages -->
@if(session('success'))
    <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-800 font-medium">
        <i class="bi bi-check-circle-fill text-emerald-500"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800 font-medium">
        <i class="bi bi-exclamation-circle-fill text-red-500"></i> {{ session('error') }}
    </div>
@endif

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        @role('admin')
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-violet-100 text-violet-700 text-xs font-semibold rounded-full mb-2">
                <i class="bi bi-shield-fill-check"></i> Admin View
            </span>
        @endrole
        <h1 class="text-2xl font-bold text-slate-800">Kanban Board</h1>
        <p class="text-sm text-slate-500 mt-0.5">Drag and drop tickets to update their status</p>
    </div>

    <!-- Notification Bell -->
    <button id="notificationBtn" onclick="openModal('notificationsModal')" class="relative inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-all shadow-sm self-start sm:self-auto">
        <i class="bi bi-bell text-slate-500"></i>
        Notifications
        @if($allNotifications->count() > 0)
            <span class="notification-badge absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                {{ $unread->count() > 9 ? '9+' : $unread->count() }}
            </span>
        @endif
    </button>
</div>

<!-- Admin: Top Points Table -->
@role('admin')
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm mb-6 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2">
        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
            <i class="bi bi-trophy-fill text-amber-500 text-sm"></i>
        </div>
        <h2 class="font-bold text-slate-800">Top Points Leaderboard</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-left">
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Member</th>
                    <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Points</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($users as $index => $user)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-3.5 text-slate-400 font-medium">
                        @if($index === 0) 🥇 @elseif($index === 1) 🥈 @elseif($index === 2) 🥉 @else {{ $index + 1 }} @endif
                    </td>
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="font-medium text-slate-800">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3.5">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-semibold rounded-full">
                            <i class="bi bi-star-fill text-amber-400 text-xs"></i> {{ $user->points }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endrole

<!-- Kanban Columns -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5">

    <!-- OPEN -->
    <div class="ticket-column" data-status="open">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden h-full">
            <div class="px-4 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                    <h3 class="font-bold text-slate-800 text-sm">Open</h3>
                </div>
                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                    {{ $tickets->where('status','open')->count() }}
                </span>
            </div>
            <div class="ticket-cards-container p-3 space-y-3 min-h-[200px]">
                @foreach($tickets->where('status','open') as $ticket)
                    @include('partials.member-ticket-card', ['ticket' => $ticket])
                @endforeach
            </div>
        </div>
    </div>

    <!-- IN PROGRESS -->
    <div class="ticket-column" data-status="in_progress">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden h-full">
            <div class="px-4 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                    <h3 class="font-bold text-slate-800 text-sm">In Progress</h3>
                </div>
                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">
                    {{ $tickets->where('status','in_progress')->count() }}
                </span>
            </div>
            <div class="ticket-cards-container p-3 space-y-3 min-h-[200px]">
                @foreach($tickets->where('status','in_progress') as $ticket)
                    @include('partials.member-ticket-card', ['ticket' => $ticket])
                @endforeach
            </div>
        </div>
    </div>

    <!-- CLOSED -->
    <div class="ticket-column" data-status="closed">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden h-full">
            <div class="px-4 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                    <h3 class="font-bold text-slate-800 text-sm">Closed</h3>
                </div>
                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">
                    {{ $tickets->where('status','closed')->count() }}
                </span>
            </div>
            <div class="ticket-cards-container p-3 space-y-3 min-h-[200px]">
                @foreach($tickets->where('status','closed') as $ticket)
                    @include('partials.member-ticket-card', ['ticket' => $ticket])
                @endforeach
            </div>
        </div>
    </div>

</div>

<!-- ===== MODALS ===== -->

<!-- Notifications Modal -->
<div id="notificationsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('notificationsModal')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[80vh] flex flex-col animate-slide-up">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <i class="bi bi-bell-fill text-blue-500"></i>
                <h3 class="font-bold text-slate-800">Notifications</h3>
            </div>
            <button onclick="closeModal('notificationsModal')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>
        <div class="overflow-y-auto flex-1 p-4">
            @if($allNotifications->isEmpty())
                <div class="text-center py-10">
                    <i class="bi bi-bell-slash text-4xl text-slate-300"></i>
                    <p class="text-slate-500 text-sm mt-3">No notifications yet</p>
                </div>
            @else
                <div class="space-y-2">
                    @foreach($allNotifications as $notification)
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="font-semibold text-slate-800 text-sm">{{ $notification->data['title'] ?? 'Notification' }}</p>
                            <p class="text-slate-600 text-xs mt-0.5">{{ $notification->data['message'] ?? '' }}</p>
                            <p class="text-slate-400 text-xs mt-2 flex items-center gap-1">
                                <i class="bi bi-clock"></i> {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Time Log Modal (Admin) -->
@role('admin')
<div id="open-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('open-modal')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md animate-slide-up">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <i class="bi bi-clock-history text-emerald-500"></i>
                <h3 class="font-bold text-slate-800">User Time Report</h3>
            </div>
            <button onclick="closeModal('open-modal')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>
        <form action="{{ url('/user_duration') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Select User</label>
                <select name="user" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                    <option disabled selected>Select User</option>
                    @if(!empty($users))
                        @foreach($total_users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">From Date</label>
                    <input type="date" name="from_date" max="{{ now()->format('Y-m-d') }}" required
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">To Date</label>
                    <input type="date" name="to_date" max="{{ now()->format('Y-m-d') }}" required
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('open-modal')" class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 transition-all">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-all">Submit</button>
            </div>
        </form>
    </div>
</div>
@endrole

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
</script>

@endsection
