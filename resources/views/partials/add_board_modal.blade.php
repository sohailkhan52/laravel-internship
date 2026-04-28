<!-- ADD BOARD MODAL -->
<div id="addBoard" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('addBoard')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md animate-slide-up">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-kanban-fill text-blue-600 text-sm"></i>
                </div>
                <h3 class="font-bold text-slate-800">Create New Board</h3>
            </div>
            <button onclick="closeModal('addBoard')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <!-- Form -->
        <form action="{{ url('/createboard') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Board Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                        placeholder="e.g. Sprint 1, Marketing Tasks...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" required
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all resize-none"
                        placeholder="What is this board for?"></textarea>
                </div>

            </div>

            <div class="flex gap-3 px-6 py-4 border-t border-slate-100">
                <button type="button" onclick="closeModal('addBoard')" class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 transition-all">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-sm">Create Board</button>
            </div>
        </form>
    </div>
</div>
