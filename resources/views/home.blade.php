@extends('layouts.app')

@section('content')

<!-- Flash Messages -->
@if(session('success'))
    <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-800 font-medium">
        <i class="bi bi-check-circle-fill text-emerald-500"></i>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800 font-medium">
        <i class="bi bi-exclamation-circle-fill text-red-500"></i>
        {{ session('error') }}
    </div>
@endif

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
        <p class="text-sm text-slate-500 mt-0.5">Welcome back, <span class="font-semibold text-blue-600">{{ Auth::user()->name }}</span></p>
    </div>

    <!-- Notification Bell -->
    <button id="notificationBtn" onclick="openNotifications()" class="relative inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
        <i class="bi bi-bell text-slate-500"></i>
        Notifications
        @if($allNotifications->count() > 0)
            <span class="notification-badge absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                {{ $unread->count() > 9 ? '9+' : $unread->count() }}
            </span>
        @endif
    </button>
</div>

<!-- Action Buttons (Admin) -->
@role('admin')
<div class="flex flex-wrap gap-3 mb-6">
    <button onclick="openModal('addBoard')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-sm hover:shadow-md">
        <i class="bi bi-plus-lg"></i> Add Board
    </button>
    <button onclick="openModal('addTicket')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 text-white text-sm font-semibold rounded-xl hover:bg-violet-700 transition-all shadow-sm hover:shadow-md">
        <i class="bi bi-ticket-perforated"></i> Add Ticket
    </button>
    <button onclick="openModal('open-modal')" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-all shadow-sm hover:shadow-md">
        <i class="bi bi-clock-history"></i> Time Log
    </button>
</div>
@endrole

<!-- Board Selector -->
<div class="mb-6 relative inline-block" id="boardSelectorWrapper">
    <button onclick="toggleBoardDropdown()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-sm font-medium text-slate-700 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
        <i class="bi bi-layout-three-columns text-blue-500"></i>
        Select Board
        <i class="bi bi-chevron-down text-xs text-slate-400"></i>
    </button>
    <div id="boardDropdownMenu" class="hidden absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-40">
        @foreach($boards as $board)
            <a href="/board/{{ $board->id }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                <i class="bi bi-kanban text-slate-400"></i>
                {{ $board->name }}
            </a>
        @endforeach
    </div>
</div>

<!-- Top Points Table (Admin) -->
@role('admin')
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm mb-8 overflow-hidden">
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
                        @if($index === 0) 🥇
                        @elseif($index === 1) 🥈
                        @elseif($index === 2) 🥉
                        @else {{ $index + 1 }}
                        @endif
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
                            <i class="bi bi-star-fill text-amber-400 text-xs"></i>
                            {{ $user->points }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endrole

<!-- Boards Grid -->
<div>
    <h2 class="text-lg font-bold text-slate-800 mb-4">Your Boards</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($boards as $board)
        <a href="board/{{ $board->id }}" class="group block bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all overflow-hidden">
            <!-- Card Header -->
            <div class="h-2 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
            <div class="p-5">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                        <i class="bi bi-kanban text-blue-600"></i>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-full">
                        <i class="bi bi-ticket-perforated text-xs"></i>
                        {{ $board->ticket->count() }} tickets
                    </span>
                </div>
                <h3 class="font-bold text-slate-800 mb-2 group-hover:text-blue-600 transition-colors">{{ $board->name }}</h3>
                <div class="space-y-1">
                    @foreach($board->ticket->take(3) as $ticket)
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <div class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></div>
                            <span class="truncate">{{ $ticket->title }}</span>
                        </div>
                    @endforeach
                    @if($board->ticket->count() > 3)
                        <p class="text-xs text-slate-400 pl-3.5">+{{ $board->ticket->count() - 3 }} more tickets</p>
                    @endif
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-400">View board</span>
                    <i class="bi bi-arrow-right text-blue-500 group-hover:translate-x-1 transition-transform"></i>
                </div>
            </div>
        </a>
        @endforeach
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
                                <i class="bi bi-clock"></i>
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@include('partials.add_board_modal')
@include('partials.add-ticket-modal')
@include('partials.timelog_modal')

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}
function openNotifications() {
    openModal('notificationsModal');
}
function toggleBoardDropdown() {
    document.getElementById('boardDropdownMenu').classList.toggle('hidden');
}
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('boardSelectorWrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('boardDropdownMenu').classList.add('hidden');
    }
});
</script>

@endsection
