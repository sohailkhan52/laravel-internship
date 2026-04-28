<!-- ADD TICKET MODAL -->
<div id="addTicket" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('addTicket')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col animate-slide-up">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 flex-shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-ticket-perforated-fill text-violet-600 text-sm"></i>
                </div>
                <h3 class="font-bold text-slate-800">Create New Ticket</h3>
            </div>
            <button onclick="closeModal('addTicket')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <!-- Body -->
        <form action="{{ url('/createTicket') }}" method="POST" enctype="multipart/form-data" class="overflow-y-auto flex-1">
            @csrf
            <div class="p-6 space-y-4">

                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                        placeholder="Brief ticket title">
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" required
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all resize-none"
                        placeholder="Describe the ticket in detail..."></textarea>
                </div>

                <!-- Attachment -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Attachment</label>
                    <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center hover:border-blue-300 transition-colors">
                        <i class="bi bi-cloud-upload text-2xl text-slate-300 mb-1"></i>
                        <p class="text-xs text-slate-500 mb-2">Click to upload or drag & drop</p>
                        <input type="file" name="attachment" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

                <!-- Assign Member -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Assign Member <span class="text-red-500">*</span></label>
                    <select name="assigned_to[]" multiple required
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        @if(!empty($member))
                            @foreach($member as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <p class="text-xs text-slate-400 mt-1">Hold Ctrl/Cmd to select multiple</p>
                </div>

                <!-- Priority + Board (2 cols) -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Priority <span class="text-red-500">*</span></label>
                        <select name="priority_id" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <option disabled selected>Select</option>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Board <span class="text-red-500">*</span></label>
                        <select name="board_id" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <option disabled selected>Select</option>
                            @foreach($boards as $board)
                                <option value="{{ $board->id }}">{{ $board->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Checklist -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Checklist Items</label>
                    <div id="checklist-container" class="space-y-2">
                        <div class="flex gap-2 checklist-item">
                            <input type="text" name="checklist[]"
                                class="flex-1 px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                placeholder="Checklist item" required>
                            <button type="button" class="remove-checklist w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-colors flex-shrink-0">
                                <i class="bi bi-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" id="add-checklist"
                        class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                        <i class="bi bi-plus-circle"></i> Add Item
                    </button>
                </div>

                <!-- Deadline -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deadline <span class="text-red-500">*</span></label>
                    <input type="date" name="deadline" min="{{ now()->format('Y-m-d') }}" required
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

            </div>

            <!-- Footer -->
            <div class="flex gap-3 px-6 py-4 border-t border-slate-100 flex-shrink-0">
                <button type="button" onclick="closeModal('addTicket')" class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 transition-all">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-violet-600 text-white text-sm font-semibold rounded-xl hover:bg-violet-700 transition-all shadow-sm">Create Ticket</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('add-checklist')?.addEventListener('click', function () {
    const container = document.getElementById('checklist-container');
    const div = document.createElement('div');
    div.className = 'flex gap-2 checklist-item';
    div.innerHTML = `
        <input type="text" name="checklist[]"
            class="flex-1 px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
            placeholder="Checklist item">
        <button type="button" class="remove-checklist w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-colors flex-shrink-0">
            <i class="bi bi-trash text-sm"></i>
        </button>
    `;
    container.appendChild(div);
});

document.getElementById('checklist-container')?.addEventListener('click', function (e) {
    if (e.target.closest('.remove-checklist')) {
        const items = document.querySelectorAll('.checklist-item');
        if (items.length > 1) {
            e.target.closest('.checklist-item').remove();
        }
    }
});
</script>
