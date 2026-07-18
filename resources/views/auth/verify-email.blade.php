@extends('layouts.app')

@section('title', 'Verify Email')

@section('main-class', 'mx-auto flex w-full max-w-md flex-1 items-center px-4 py-12')

@section('content')
<div class="card-ocean w-full p-8 text-center">
    <h1 class="font-display text-2xl font-bold text-white">Verify your email</h1>
    <p class="mt-3 text-sm text-slate-300">
        We sent a verification link to your email. Please verify before placing an order.
    </p>

    @if (session('status') === 'verification-link-sent')
        <p class="mt-4 rounded-xl border border-emerald-500/30 bg-emerald-950/40 px-4 py-3 text-sm text-emerald-200">
            A new verification link has been sent.
        </p>
    @endif

    <form method="POST" action="{{ url('/email/verification-notification') }}" class="mt-6">
        @csrf
        <button class="btn-coral w-full">Resend verification email</button>
    </form>

    <a href="{{ route('home') }}" class="mt-4 inline-block text-sm text-coral-accent hover:underline">Continue shopping</a>

    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button type="submit" class="text-sm text-slate-400 hover:text-white">Logout</button>
    </form>
</div>
@endsection
