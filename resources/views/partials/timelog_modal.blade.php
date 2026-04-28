<!-- TIME LOG MODAL -->
<div id="open-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('open-modal')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md animate-slide-up">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i class="bi bi-clock-history text-emerald-600 text-sm"></i>
                </div>
                <h3 class="font-bold text-slate-800">User Time Report</h3>
            </div>
            <button onclick="closeModal('open-modal')" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <!-- Form -->
        <form action="{{ url('/user_duration') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">

                <!-- User Select -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Select User</label>
                    <select name="user" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <option disabled selected>Select User</option>
                        @if(!empty($users))
                            @foreach($total_users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">From Date</label>
                        <input type="date" name="from_date" max="{{ now()->format('Y-m-d') }}"
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">To Date</label>
                        <input type="date" name="to_date" max="{{ now()->format('Y-m-d') }}"
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                </div>

                <!-- Time Range -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">From Time</label>
                        <input type="time" name="from_time"
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">To Time</label>
                        <input type="time" name="to_time"
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                </div>

            </div>

            <div class="flex gap-3 px-6 py-4 border-t border-slate-100">
                <button type="button" onclick="closeModal('open-modal')" class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 transition-all">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-all shadow-sm">Generate Report</button>
            </div>
        </form>
    </div>
</div>
