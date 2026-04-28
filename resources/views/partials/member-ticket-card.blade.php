@php
    $priorityColors = [
        'high'   => 'bg-red-100 text-red-700',
        'medium' => 'bg-amber-100 text-amber-700',
        'low'    => 'bg-emerald-100 text-emerald-700',
    ];
    $priorityName  = strtolower($ticket->priority->name ?? 'low');
    $priorityClass = $priorityColors[$priorityName] ?? 'bg-slate-100 text-slate-600';

    $statusColors = [
        'open'        => 'bg-blue-100 text-blue-700',
        'in_progress' => 'bg-amber-100 text-amber-700',
        'closed'      => 'bg-emerald-100 text-emerald-700',
    ];
    $statusClass = $statusColors[$ticket->status] ?? 'bg-slate-100 text-slate-600';
@endphp

<div class="ticket-card bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-200 transition-all overflow-hidden"
     data-ticket-id="{{ $ticket->id }}" data-status="{{ $ticket->status }}">

    <!-- Priority colour stripe -->
    <div class="h-1 {{ $priorityName === 'high' ? 'bg-red-400' : ($priorityName === 'medium' ? 'bg-amber-400' : 'bg-emerald-400') }}"></div>

    <div class="p-4">

        <!-- Priority + Status badges -->
        <div class="flex items-center justify-between mb-3">
            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full {{ $priorityClass }}">
                {{ ucfirst($priorityName) }}
            </span>
            <span data-status-badge
                  class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full {{ $statusClass }}">
                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
            </span>
        </div>

        <!-- Title -->
        <h4 class="font-bold text-slate-800 text-sm mb-2 leading-snug">{{ $ticket->title }}</h4>

        <!-- Description -->
        <p class="text-xs text-slate-500 mb-3 leading-relaxed">{{ Str::limit($ticket->description, 80) }}</p>

        <!-- Attachment -->
        @if(!empty($ticket->attachment))
            <div class="mb-3">
                <img src="{{ asset('storage/' . $ticket->attachment) }}"
                     alt="Attachment"
                     class="w-full h-24 object-cover rounded-lg border border-slate-100">
            </div>
        @endif

        <!-- Dates -->
        <div class="space-y-1 mb-3">
            <div class="flex items-center gap-1.5 text-xs text-slate-400">
                <i class="bi bi-calendar-plus text-slate-300"></i>
                <span>Created {{ optional($ticket->created_at)->diffForHumans() ?? 'N/A' }}</span>
            </div>
            @php
                $isOverdue = optional($ticket->deadline) && $ticket->deadline->isPast() && $ticket->status !== 'closed';
            @endphp
            <div class="flex items-center gap-1.5 text-xs {{ $isOverdue ? 'text-red-500 font-semibold' : 'text-slate-400' }}">
                <i class="bi bi-alarm {{ $isOverdue ? 'text-red-400' : 'text-slate-300' }}"></i>
                <span>Due {{ optional($ticket->deadline)->diffForHumans() ?? 'No deadline' }}</span>
            </div>
        </div>

        <!-- Assignee -->
        <div class="flex items-center gap-2 mb-3 pb-3 border-b border-slate-100">
            <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                {{ strtoupper(substr($ticket->assignee->name, 0, 1)) }}
            </div>
            <span class="text-xs text-slate-600 truncate">{{ $ticket->assignee->name }}</span>
        </div>

        <!-- Checklist dropdown -->
        <div class="relative mb-2 no-drag">
            <button type="button"
                    class="checklist-toggle w-full flex items-center justify-between px-3 py-2 text-xs font-medium text-slate-600 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors"
                    data-target="checklist-{{ $ticket->id }}">
                <span class="flex items-center gap-1.5 pointer-events-none">
                    <i class="bi bi-check2-square text-blue-500"></i>
                    Checklist ({{ $ticket->checklist->count() }})
                </span>
                <i class="bi bi-chevron-down text-slate-400 text-xs pointer-events-none"></i>
            </button>
            {{-- Panel rendered outside normal flow; JS positions it fixed --}}
            <div id="checklist-{{ $ticket->id }}"
                 class="card-dropdown hidden bg-white border border-slate-200 rounded-xl shadow-xl">
                @forelse($ticket->checklist as $checklist)
                    <a href="checklist/{{ $checklist->id }}"
                       class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 transition-colors">
                        <i class="bi {{ $checklist->completed_at ? 'bi-check-circle-fill text-emerald-500' : 'bi-circle text-slate-300' }} flex-shrink-0"></i>
                        <span class="{{ $checklist->completed_at ? 'line-through text-slate-400' : '' }}">{{ $checklist->name }}</span>
                    </a>
                @empty
                    <p class="px-3 py-3 text-xs text-slate-400 text-center">No checklist items</p>
                @endforelse
            </div>
        </div>

        <!-- Status-change dropdown -->
        <div class="no-drag">
            @if($ticket->status === 'closed')
                {{-- Closed: locked, no dropdown --}}
                <div class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg">
                    <i class="bi bi-lock-fill text-emerald-500"></i>
                    Ticket Closed — Locked
                </div>
            @else
                <button type="button"
                        class="status-toggle w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors"
                        data-target="status-{{ $ticket->id }}">
                    <span class="flex items-center gap-1.5 pointer-events-none">
                        <i class="bi bi-arrow-left-right"></i>
                        Change Status
                    </span>
                    <i class="bi bi-chevron-down text-blue-400 text-xs pointer-events-none"></i>
                </button>

                <div id="status-{{ $ticket->id }}"
                     class="card-dropdown hidden bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden">

                    @if($ticket->status === 'open')
                        {{-- Open → In Progress --}}
                        <button type="button"
                                class="ticket-action-btn w-full flex items-center gap-2 px-3 py-2.5 text-xs text-amber-700 hover:bg-amber-50 transition-colors text-left"
                                data-endpoint="/ticket/{{ $ticket->id }}/start"
                                data-card-id="{{ $ticket->id }}">
                            <i class="bi bi-play-circle-fill text-amber-500 pointer-events-none"></i>
                            <span class="pointer-events-none">Start Progress</span>
                        </button>
                    @endif

                    @if($ticket->status === 'in_progress')
                        {{-- In Progress → Open --}}
                        <form method="POST" action="/tickets/{{ $ticket->id }}/open"
                              class="m-0 p-3 border-b border-slate-100"
                              onsubmit="return validateWorkDetail(this)">
                            @csrf
                            <label class="block text-xs font-medium text-slate-600 mb-1">Work detail <span class="text-red-500">*</span></label>
                            <input type="text" name="Work-detail"
                                   class="work-detail-input w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 mb-2"
                                   placeholder="Describe work done...">
                            <p class="work-detail-error hidden text-xs text-red-500 mb-1">Please enter work detail.</p>
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="bi bi-arrow-counterclockwise"></i> Back to Open
                            </button>
                        </form>

                        {{-- In Progress → Closed --}}
                        <div class="p-3">
                            <label class="block text-xs font-medium text-slate-600 mb-1">Work detail <span class="text-red-500">*</span></label>
                            <input type="text" id="close-detail-{{ $ticket->id }}"
                                   class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 mb-1"
                                   placeholder="Describe work done...">
                            <p id="close-detail-error-{{ $ticket->id }}" class="hidden text-xs text-red-500 mb-1">Please enter work detail.</p>
                            <button type="button"
                                    class="close-ticket-btn w-full flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors"
                                    data-endpoint="/ticket/{{ $ticket->id }}/closeTicket"
                                    data-detail-id="close-detail-{{ $ticket->id }}"
                                    data-error-id="close-detail-error-{{ $ticket->id }}"
                                    data-card-id="{{ $ticket->id }}">
                                <i class="bi bi-check-circle-fill pointer-events-none"></i>
                                <span class="pointer-events-none">Close Ticket</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>

    </div>
</div>
