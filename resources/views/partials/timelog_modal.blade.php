<div class="modal fade" id="open-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5>User Time</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                            <form action="{{ url('/user_duration') }}" method="POST" >
                @csrf
                    <div class="mb-3">
                        <label class="form-label">Select User</label>
                        <select name="user" id="user" class="form-select" >
                            <option disabled selected>Select User</option>
                            @if(!empty($users))
                            @foreach($total_users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                                                <div class="mb-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date"
                            class="form-control"max="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date"
                            class="form-control"max="{{ now()->format('Y-m-d') }}">
                    </div>
                        </div>
                        <div class="col-md-6">                    <div class="mb-3">
                        <label class="form-label">From time</label>
                        <input type="time" name="from_time"
                            class="form-control"max="{{ now()->format('H:i') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">To time</label>
                        <input type="time" name="to_time"
                            class="form-control"max="{{ now()->format('H:i') }}">
                    </div></div>
                    </div>

                </div>

                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Submit</button>
                </div>
                </div>
                </div>