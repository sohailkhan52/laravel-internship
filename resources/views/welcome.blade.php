<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TaskFlow') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'float': 'float 3s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { transform: 'translateY(30px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' } },
                        float: { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-10px)' } },
                    }
                }
            }
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255,255,255,0.8); backdrop-filter: blur(16px); }
        .gradient-text { background: linear-gradient(135deg, #2563eb, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .hero-bg { background: linear-gradient(135deg, #eff6ff 0%, #eef2ff 50%, #faf5ff 100%); }
        .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
    </style>
</head>

<body class="hero-bg min-h-screen">

<!-- NAVBAR -->
<nav class="glass sticky top-0 z-50 border-b border-white/60 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold text-xl text-blue-600">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="bi bi-kanban-fill text-white text-sm"></i>
                </div>
                {{ config('app.name', 'TaskFlow') }}
            </a>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/home') }}" class="flex items-center gap-1.5 text-sm font-semibold bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all shadow-sm">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-blue-600 px-3 py-2 rounded-lg hover:bg-blue-50 transition-all">Login</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-sm font-semibold bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all shadow-sm">
                            Get Started
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16">
    <div class="grid lg:grid-cols-2 gap-16 items-center">

        <!-- Left: Text -->
        <div class="animate-slide-up">
            <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1.5 rounded-full mb-6">
                <i class="bi bi-stars"></i> Ticket Management System
            </div>
            <h1 class="text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                Manage Tasks
                <span class="gradient-text block">Effortlessly</span>
            </h1>
            <p class="text-lg text-slate-500 mb-8 leading-relaxed">
                Assign tickets, track progress, collaborate with your team, and get notified — all from one beautiful dashboard.
            </p>
            <div class="flex flex-wrap gap-3">
                @auth
                    <a href="{{ url('/home') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-blue-700 transition-all shadow-md hover:shadow-lg">
                        <i class="bi bi-arrow-right-circle"></i> Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-blue-700 transition-all shadow-md hover:shadow-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white text-slate-700 font-semibold px-6 py-3 rounded-xl border border-slate-200 hover:border-blue-300 hover:text-blue-600 transition-all shadow-sm">
                        <i class="bi bi-person-plus"></i> Create Account
                    </a>
                @endauth
            </div>

            <!-- Stats -->
            <div class="flex gap-8 mt-10 pt-8 border-t border-slate-200">
                <div>
                    <p class="text-2xl font-bold text-slate-800">100%</p>
                    <p class="text-sm text-slate-500">Role-based access</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">Real-time</p>
                    <p class="text-sm text-slate-500">Notifications</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">Kanban</p>
                    <p class="text-sm text-slate-500">Drag & Drop</p>
                </div>
            </div>
        </div>

        <!-- Right: Feature Cards -->
        <div class="grid grid-cols-2 gap-4 animate-fade-in">
            <div class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-slate-100 col-span-2">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="bi bi-kanban text-blue-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-slate-800 mb-1">Kanban Board</h3>
                <p class="text-sm text-slate-500">Drag and drop tickets across Open, In Progress, and Closed columns.</p>
            </div>
            <div class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="bi bi-bell text-violet-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-slate-800 mb-1">Notifications</h3>
                <p class="text-sm text-slate-500">Stay updated with real-time alerts.</p>
            </div>
            <div class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="bi bi-clock-history text-emerald-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-slate-800 mb-1">Time Logs</h3>
                <p class="text-sm text-slate-500">Track work hours and export reports.</p>
            </div>
            <div class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="bi bi-shield-check text-amber-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-slate-800 mb-1">Role Access</h3>
                <p class="text-sm text-slate-500">Admin and member roles with permissions.</p>
            </div>
            <div class="card-hover bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="bi bi-check2-square text-rose-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-slate-800 mb-1">Checklists</h3>
                <p class="text-sm text-slate-500">Break tickets into trackable sub-tasks.</p>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="border-t border-slate-200 bg-white/60 backdrop-blur-sm">
    <div class="max-w-7xl mx-auto px-4 py-5 text-center text-sm text-slate-400">
        © {{ date('Y') }} <span class="font-semibold text-slate-500">{{ config('app.name') }}</span> — All rights reserved
    </div>
</footer>

</body>
</html>
