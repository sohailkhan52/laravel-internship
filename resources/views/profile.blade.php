@extends('layouts.app')

@section('content')

<!-- Flash Messages -->
@if(session('success'))
    <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-800 font-medium">
        <i class="bi bi-check-circle-fill text-emerald-500"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800 font-medium">
        <i class="bi bi-exclamation-circle-fill text-red-500"></i> {{ session('error') }}
    </div>
@endif

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">My Profile</h1>
    <p class="text-sm text-slate-500 mt-0.5">Manage your account information and security</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Profile Info Card -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <!-- Avatar Header -->
            <div class="h-24 bg-gradient-to-br from-blue-500 to-indigo-600"></div>
            <div class="px-6 pb-6">
                <div class="-mt-10 mb-4">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white text-3xl font-bold border-4 border-white shadow-md">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>
                <h2 class="text-xl font-bold text-slate-800">{{ $user->name }}</h2>
                <p class="text-sm text-slate-500 mb-4">{{ $user->email }}</p>

                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
                        <span class="text-sm text-slate-500 flex items-center gap-2">
                            <i class="bi bi-shield-check text-blue-400"></i> Role
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full capitalize">
                            {{ $user->role }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2.5 border-b border-slate-100">
                        <span class="text-sm text-slate-500 flex items-center gap-2">
                            <i class="bi bi-star-fill text-amber-400"></i> Points
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-semibold rounded-full">
                            {{ $user->points }} pts
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm text-slate-500 flex items-center gap-2">
                            <i class="bi bi-calendar3 text-slate-400"></i> Joined
                        </span>
                        <span class="text-sm text-slate-700 font-medium">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Card -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="bi bi-lock-fill text-blue-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Change Password</h3>
                    <p class="text-xs text-slate-500">Update your account password</p>
                </div>
            </div>

            <form method="POST" action="/change_password" class="p-6 space-y-5">
                @csrf

                <!-- Old Password -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Current Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-lock text-slate-400"></i>
                        </div>
                        <input type="password" name="old_password"
                            class="w-full pl-10 pr-4 py-2.5 border @error('old_password') border-red-400 bg-red-50 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                            placeholder="Enter current password">
                    </div>
                    @error('old_password')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-lock-fill text-slate-400"></i>
                        </div>
                        <input type="password" name="new_password"
                            class="w-full pl-10 pr-4 py-2.5 border @error('new_password') border-red-400 bg-red-50 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                            placeholder="Min. 8 characters">
                    </div>
                    @error('new_password')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm New Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-lock-fill text-slate-400"></i>
                        </div>
                        <input type="password" name="new_password_confirmation"
                            class="w-full pl-10 pr-4 py-2.5 border @error('new_password_confirmation') border-red-400 bg-red-50 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                            placeholder="Repeat new password">
                    </div>
                    @error('new_password_confirmation')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-sm hover:shadow-md">
                        <i class="bi bi-check-lg"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection
