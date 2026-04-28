@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-8">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="bi bi-key-fill text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Reset your password</h1>
            <p class="text-slate-500 text-sm mt-1">Enter your email and we'll send you a reset link</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">

            @if (session('status'))
                <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 flex items-center gap-2">
                    <i class="bi bi-check-circle-fill text-emerald-500"></i>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="bi bi-envelope text-slate-400"></i>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                            class="w-full pl-10 pr-4 py-2.5 border @error('email') border-red-400 bg-red-50 @else border-slate-200 @enderror rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            placeholder="you@example.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-amber-500 text-white font-semibold py-2.5 rounded-xl hover:bg-amber-600 transition-all shadow-sm text-sm">
                    Send Reset Link
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-slate-500 mt-6">
            Remember your password?
            <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:text-blue-700">Sign in</a>
        </p>
    </div>
</div>
@endsection
