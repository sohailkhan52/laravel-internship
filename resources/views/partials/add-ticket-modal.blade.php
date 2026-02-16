<!-- ADD TICKET MODAL -->
<div class="modal fade" id="addTicket" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5>Add New Ticket</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ url('/createTicket') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Attachment</label>
                        <input type="file" name="attachment" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assign Member</label>
                        <select name="assigned_to[]" id="assigned_to" class="form-select" required multiple>
                            <option disabled selected>Select member</option>
                            @if(!empty($member))
                            @foreach($member as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select name="priority_id" class="form-select" required>
                            <option disabled selected>Select priority</option>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">board</label>
                        <select name="board_id" class="form-select" required>
                            <option disabled selected>Select board</option>
                            @foreach($boards as $board)
                                <option value="{{ $board->id }}">{{ $board->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Checklist</label>
                        <div id="checklist-container">
                            <div class="input-group mb-2 checklist-item">
                                <input type="text" name="checklist[]" class="form-control" placeholder="Checklist item" required>
                                <button type="button" class="btn btn-danger remove-checklist">X</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary mt-2" id="add-checklist">Add</button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deadline</label>
                        <input type="date" name="deadline"
                            class="form-control"
                            min="{{ now()->format('Y-m-d') }}" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Create Ticket</button>
                </div>

            </form>
        </div>
    </div>
</div>