<div class="card shadow-sm mb-3 ticket-card" data-ticket-id="{{ $ticket->id }}" data-status="{{ $ticket->status }}">

    <div class="card-body d-flex flex-column">

        <small class="text-muted mb-2">
<div><strong>Created:</strong> {{ optional($ticket->created_at)->diffForHumans() ?? 'Not created yet' }}</div>
<div><strong>Deadline:</strong> {{ optional($ticket->deadline)->diffForHumans() ?? 'No deadline' }}</div>

        </small>

        <h6 class="fw-bold mb-2">{{ $ticket->title }}</h6>

        @if(!empty($ticket->attachment))
        Attachment
    <img src="{{ asset('storage/' . $ticket->attachment) }}"
         alt="Ticket attachment"
         class="img-fluid rounded mb-2"
         style="max-height:100px; object-fit: contain;">
@endif


        <p class="text-muted small flex-grow-1">{{ Str::limit($ticket->description, 80) }}</p>

        <div class="small mb-2 text-truncate">
            👤 Assigned to: <strong>{{ $ticket->assignee->name }}</strong>
        </div>

         
         <!-- Checklist Dropdown -->
        <div class="dropdown mt-auto">
            <button class="btn btn-sm btn-outline-primary w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Checklist
            </button>

            <ul class="dropdown-menu w-100 p-2" style="max-height: 200px; overflow-y: auto;">
                @forelse($ticket->checklist as $checklist)
                    <li class="mb-1">
                        <input type="checkbox" id="checklist-{{ $checklist->id }}" 
                               @if($checklist->completed_at) checked @endif 
                               disabled>
                        <a href="checklist/{{ $checklist->id }}" class="btn btn ms-1">
                            {{ $checklist->name }}
                        </a>
                    </li>
                @empty
                    <li class="text-muted">No checklist items</li>
                @endforelse
            </ul>
        </div>
                
        <!-- Status Dropdown -->
        <div class="dropdown mt-auto">
            <button class="btn btn-sm btn-outline-primary w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
            </button>

            <ul class="dropdown-menu w-100">
                @if($ticket->status === 'open')
                <li>
                    <form method="POST" action="/tickets/{{ $ticket->id }}/start" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item">Start Progress</button>
                    </form>
                </li>
                @endif

                @if($ticket->status === 'in_progress')
                <li>
                    <form method="POST" action="/tickets/{{ $ticket->id }}/open" class="m-0">
                        @csrf

                      <label class="form-label">Work detail</label>
                        <input type="text" name="Work-detail" class="form-control" required>
                        <button type="submit" class="dropdown-item text-danger">Back To Open</button>
                    </form>
                </li>
                <li>
                    <form method="POST" action="/tickets/{{ $ticket->id }}/close" class="m-0">
                        @csrf
  
                      <label class="form-label">Work detail</label>
                        <input type="text" name="Work-detail" class="form-control" required>
                        <button type="submit" class="dropdown-item text-danger">Close Ticket</button>
                    </form>
                </li>
                @endif

                @if($ticket->status === 'closed')
                <li>
                    <button class="dropdown-item disabled">Ticket Closed</button>
                </li>
                @endif
            </ul>
        </div>

    </div>
</div>

