<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TaskFlow') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: { 50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e40af',900:'#1e3a8a' },
                        surface: '#f8fafc',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { transform: 'translateY(10px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' } },
                    }
                }
            }
        }
    </script>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; }
        .glass { background: rgba(255,255,255,0.85); backdrop-filter: blur(12px); }
        .ticket-card { cursor: grab; position: relative; }
        .ticket-card:active { cursor: grabbing; }
        .ticket-card[data-status="closed"] { cursor: not-allowed; opacity: 0.85; }
        .ticket-card[data-status="closed"]::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(16,185,129,0.05) 0%, rgba(5,150,105,0.08) 100%);
            pointer-events: none;
            border-radius: 12px;
        }
        /* Card dropdowns need higher z-index than other cards */
        .ticket-card:has(.card-dropdown:not(.hidden)) { z-index: 40; }
        .card-dropdown { z-index: 50; }
        .dropdown-menu-tw { display: none; }
        .dropdown-menu-tw.open { display: block; }
        /* Sortable drag states */
        .drag-ghost  { opacity: 0.35; background: #dbeafe; border: 2px dashed #3b82f6 !important; border-radius: 12px; }
        .drag-chosen { box-shadow: 0 8px 24px rgba(59,130,246,0.25) !important; transform: rotate(1.5deg); }
        .drag-dragging { opacity: 0.9; }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-100 via-blue-50 to-indigo-50">
<div id="app" class="flex flex-col min-h-screen">

    <!-- NAVBAR -->
    <nav class="glass sticky top-0 z-50 border-b border-white/60 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <!-- Brand -->
                <a href="{{ url('/') }}" class="flex items-center gap-2 text-primary-600 font-bold text-xl hover:text-primary-700 transition-colors">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                        <i class="bi bi-kanban-fill text-white text-sm"></i>
                    </div>
                    {{ config('app.name', 'TaskFlow') }}
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-1">
                    @auth
                        <a href="{{ url('home') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-primary-600 hover:bg-primary-50 transition-all">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    @endauth
                </div>

                <!-- Right Side -->
                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-primary-600 px-3 py-2 rounded-lg hover:bg-primary-50 transition-all">Login</a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-all shadow-sm">Register</a>
                    @else
                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button onclick="toggleUserMenu()" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-100 transition-all text-sm font-medium text-slate-700">
                                <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden sm:block">{{ Auth::user()->name }}</span>
                                <i class="bi bi-chevron-down text-xs text-slate-400"></i>
                            </button>

                            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50 animate-fade-in">
                                <div class="px-4 py-3 border-b border-slate-100">
                                    <p class="text-xs text-slate-500">Signed in as</p>
                                    <p class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ url('home') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                                    <i class="bi bi-speedometer2 text-primary-500"></i> Dashboard
                                </a>
                                <a href="{{ url('profile') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                                    <i class="bi bi-person text-primary-500"></i> Profile
                                </a>
                                <div class="border-t border-slate-100 mt-1">
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                       class="flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                    @endguest
                </div>

                <!-- Mobile menu button -->
                <button onclick="toggleMobileMenu()" class="md:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition-colors">
                    <i class="bi bi-list text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden border-t border-slate-100 bg-white px-4 py-3 space-y-1">
            @auth
                <a href="{{ url('home') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-600">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
                <a href="{{ url('profile') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-slate-700 hover:bg-primary-50 hover:text-primary-600">
                    <i class="bi bi-person"></i> Profile
                </a>
            @endauth
        </div>
    </nav>

    <!-- FLASH ALERTS -->
    <div id="alert-container" class="max-w-7xl mx-auto w-full px-4 pt-4 space-y-2"></div>

    <!-- MAIN CONTENT -->
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="border-t border-slate-200 bg-white/60 backdrop-blur-sm mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-4 text-center text-sm text-slate-400">
            © {{ date('Y') }} <span class="font-semibold text-slate-500">{{ config('app.name') }}</span> — All rights reserved
        </div>
    </footer>

</div>

<script>
function toggleUserMenu() {
    document.getElementById('userDropdown').classList.toggle('hidden');
}
function toggleMobileMenu() {
    document.getElementById('mobileMenu').classList.toggle('hidden');
}
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown && !dropdown.closest('.relative')?.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});

// Alert system
function showAlert(type, message) {
    const container = document.getElementById('alert-container');
    if (!container) return;
    const isSuccess = type === 'success';
    const alertDiv = document.createElement('div');
    alertDiv.className = `flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium shadow-sm animate-slide-up ${
        isSuccess
            ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
            : 'bg-red-50 border-red-200 text-red-800'
    }`;
    alertDiv.innerHTML = `
        <i class="bi ${isSuccess ? 'bi-check-circle-fill text-emerald-500' : 'bi-exclamation-circle-fill text-red-500'} text-base flex-shrink-0"></i>
        <span class="flex-1">${message}</span>
        <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 transition-colors ml-2">
            <i class="bi bi-x-lg text-xs"></i>
        </button>
    `;
    container.appendChild(alertDiv);
    window.scrollTo({ top: 0, behavior: 'smooth' });
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        alertDiv.style.transition = 'opacity 0.3s';
        setTimeout(() => alertDiv.remove(), 300);
    }, 4000);
}

document.addEventListener('DOMContentLoaded', function () {
    // Drag and Drop
    function initSortable() {
        document.querySelectorAll('.ticket-cards-container').forEach(container => {
            // Avoid double-initialising
            if (container._sortable) return;
            container._sortable = new Sortable(container, {
                group: 'tickets',
                animation: 150,
                draggable: '.ticket-card',
                // Use plain CSS classes — no Tailwind custom colours here
                ghostClass: 'drag-ghost',
                chosenClass: 'drag-chosen',
                dragClass: 'drag-dragging',
                // Prevent dropdown clicks from starting a drag
                filter: '.no-drag, input, button, a, select, textarea',
                preventOnFilter: false,
                onMove: function (evt) {
                    const fromStatus = evt.from.closest('.ticket-column')?.dataset.status;
                    if (fromStatus === 'closed') return false;
                },
                onEnd: function (evt) {
                    const ticketId = evt.item.dataset.ticketId;
                    const newStatus = evt.to.closest('.ticket-column')?.dataset.status;
                    const oldStatus = evt.from.closest('.ticket-column')?.dataset.status;

                    // No actual column change — nothing to do
                    if (newStatus === oldStatus) return;

                    if (oldStatus === 'closed') {
                        showAlert('error', 'Cannot move tickets from Closed column');
                        evt.from.insertBefore(evt.item, evt.from.children[evt.oldIndex] || null);
                        return;
                    }

                    let endpoint = '';
                    switch (newStatus) {
                        case 'in_progress': endpoint = `/ticket/${ticketId}/start`; break;
                        case 'open':        endpoint = `/ticket/${ticketId}/open`; break;
                        case 'closed':      endpoint = `/ticket/${ticketId}/closeTicket`; break;
                        default:
                            evt.from.appendChild(evt.item);
                            return;
                    }

                    evt.item.style.opacity = '0.5';
                    evt.item.style.pointerEvents = 'none';

                    fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        evt.item.style.opacity = '1';
                        evt.item.style.pointerEvents = '';
                        if (data.success) {
                            showAlert('success', data.message || `Ticket moved to ${newStatus.replace('_', ' ')}`);
                            // Update the status badge on the card without a full reload
                            const statusBadge = evt.item.querySelector('[data-status-badge]');
                            if (statusBadge) {
                                const labels = { open: 'Open', in_progress: 'In Progress', closed: 'Closed' };
                                statusBadge.textContent = labels[newStatus] || newStatus;
                            }
                        } else {
                            showAlert('error', data.message || 'Something went wrong');
                            // Revert the card back to its original column
                            evt.from.appendChild(evt.item);
                        }
                    })
                    .catch(() => {
                        evt.item.style.opacity = '1';
                        evt.item.style.pointerEvents = '';
                        showAlert('error', 'Server error occurred');
                        evt.from.appendChild(evt.item);
                    });
                }
            });
        });
    }

    initSortable();

    // ── Card dropdown toggles (checklist & status) ──────────────────────────
    // Single delegated listener — no per-card script duplication
    // Uses fixed positioning so dropdowns are never clipped by overflow:hidden parents

    let activeDropdown = null; // { panel, cleanup }

    function closeActiveDropdown() {
        if (activeDropdown) {
            activeDropdown.panel.classList.add('hidden');
            activeDropdown.panel.style.cssText = '';
            activeDropdown = null;
        }
    }

    function positionDropdown(btn, panel) {
        const rect = btn.getBoundingClientRect();
        panel.style.position   = 'fixed';
        panel.style.top        = (rect.bottom + 4) + 'px';
        panel.style.left       = rect.left + 'px';
        panel.style.width      = rect.width + 'px';
        panel.style.zIndex     = '9999';
        panel.style.maxHeight  = '320px';
        panel.style.overflowY  = 'auto';
    }

    document.addEventListener('click', function (e) {
        const toggleBtn = e.target.closest('.checklist-toggle, .status-toggle');

        if (toggleBtn) {
            e.stopPropagation();
            const targetId = toggleBtn.dataset.target;
            const panel    = document.getElementById(targetId);
            if (!panel) return;

            const isAlreadyOpen = activeDropdown && activeDropdown.panel === panel;

            // Close whatever is open
            closeActiveDropdown();

            if (!isAlreadyOpen) {
                panel.classList.remove('hidden');
                positionDropdown(toggleBtn, panel);
                activeDropdown = { panel };
            }
            return;
        }

        // Click inside an open dropdown — keep it open
        if (activeDropdown && activeDropdown.panel.contains(e.target)) return;

        // Anything else — close
        closeActiveDropdown();
    });

    // Reposition on scroll/resize so the dropdown follows its button
    window.addEventListener('scroll', closeActiveDropdown, true);
    window.addEventListener('resize', closeActiveDropdown);

    // ── "Start Progress" button (open → in_progress via DragController JSON) ─
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.ticket-action-btn');
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();

        const endpoint = btn.dataset.endpoint;
        const cardId   = btn.dataset.cardId;

        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split animate-spin"></i> <span>Working…</span>';

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message || 'Status updated');
                // Reload the page so the card re-renders in the correct column
                setTimeout(() => location.reload(), 800);
            } else {
                showAlert('error', data.message || 'Something went wrong');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-play-circle-fill text-amber-500"></i> <span>Start Progress</span>';
            }
        })
        .catch(() => {
            showAlert('error', 'Server error occurred');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-play-circle-fill text-amber-500"></i> <span>Start Progress</span>';
        });
    });

    // ── "Close Ticket" button (in_progress → closed via DragController JSON) ─
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.close-ticket-btn');
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();

        const endpoint = btn.dataset.endpoint;
        const detailId = btn.dataset.detailId;
        const errorId  = btn.dataset.errorId;
        const input    = document.getElementById(detailId);
        const errorEl  = document.getElementById(errorId);

        // Validate work detail
        if (!input || !input.value.trim()) {
            if (errorEl) errorEl.classList.remove('hidden');
            if (input)   input.focus();
            return;
        }
        if (errorEl) errorEl.classList.add('hidden');

        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> <span>Closing…</span>';

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ work_detail: input.value.trim() })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const pts = data.points_awarded !== undefined ? ` (+${data.points_awarded} pts)` : '';
                showAlert('success', (data.message || 'Ticket closed') + pts);
                setTimeout(() => location.reload(), 900);
            } else {
                showAlert('error', data.message || 'Could not close ticket');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> <span>Close Ticket</span>';
            }
        })
        .catch(() => {
            showAlert('error', 'Server error occurred');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> <span>Close Ticket</span>';
        });
    });

    // ── Work-detail validation for "Back to Open" form ───────────────────────
    window.validateWorkDetail = function (form) {
        const input   = form.querySelector('.work-detail-input');
        const errorEl = form.querySelector('.work-detail-error');
        if (!input.value.trim()) {
            if (errorEl) errorEl.classList.remove('hidden');
            input.focus();
            return false;
        }
        if (errorEl) errorEl.classList.add('hidden');
        return true;
    };

    const notificationBtn = document.getElementById('notificationBtn');
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function () {
            fetch('{{ route("notifications.mark-read") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
            }).then(() => {
                const badge = document.querySelector('#notificationBtn .notification-badge');
                if (badge) badge.remove();
            });
        });
    }
});
</script>
</body>
</html>
