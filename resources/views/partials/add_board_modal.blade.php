<!-- ADD TICKET MODAL -->
<div class="modal fade" id="addBoard" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5>Add New Board</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ url('/createboard') }}" method="POST" >
                @csrf

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Board Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Create Board</button>
                </div>

            </form>
        </div>
    </div>
</div>