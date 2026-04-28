@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-8">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="bi bi-envelope-check-fill text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Verify your email</h1>
            <p class="text-slate-500 text-sm mt-1">One more step to get started</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">

            @if (session('resent'))
                <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 flex items-center gap-2">
                    <i class="bi bi-check-circle-fill text-emerald-500"></i>
                    A fresh verification link has been sent to your email address.
                </div>
            @endif

            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-envelope-open text-blue-500 text-2xl"></i>
                </div>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Before proceeding, please check your email for a verification link.
                    If you did not receive the email, click the button below to request another.
                </p>
            </div>

            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2.5 rounded-xl hover:bg-blue-700 transition-all shadow-sm text-sm">
                    <i class="bi bi-send me-1"></i> Resend Verification Email
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-slate-500 mt-6">
            Wrong account?
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-verify-form').submit();"
               class="text-blue-600 font-semibold hover:text-blue-700">Sign out</a>
        </p>
        <form id="logout-verify-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
    </div>
</div>
@endsection
