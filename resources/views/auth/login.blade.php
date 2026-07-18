@extends('layouts.app')

@section('title', __('ui.nav.login'))

@section('main-class', 'mx-auto flex w-full max-w-md flex-1 items-center px-4 py-12')

@section('content')
<div class="card-ocean w-full p-8">
    <h1 class="font-display text-2xl font-bold text-white">{{ __('ui.nav.login') }}</h1>
    <p class="mt-1 text-sm text-slate-400">Welcome back — sign in to checkout and track orders.</p>
    <p class="mt-2 rounded-lg border border-ocean-light/30 bg-ocean-mid/40 px-3 py-2 text-xs text-slate-300">
        Admin panel: use <span class="font-mono text-coral-accent">admin@finity.test</span> / <span class="font-mono text-coral-accent">password</span>, then open <a href="{{ url('/admin') }}" class="text-coral-accent underline">/admin</a>.
    </p>

    @if (session('status'))
        <div class="mt-4 rounded-xl border border-emerald-500/30 bg-emerald-950/40 px-4 py-3 text-sm text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-4 rounded-xl border border-red-500/30 bg-red-950/40 px-4 py-3 text-sm text-red-200">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ url('/login') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="mb-1 block text-sm text-slate-400">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="input-ocean">
        </div>
        <div>
            <label class="mb-1 block text-sm text-slate-400">Password</label>
            <input type="password" name="password" required autocomplete="current-password" class="input-ocean">
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-300">
            <input type="checkbox" name="remember" class="rounded border-ocean-light/40 bg-ocean-dark text-coral-accent">
            Remember me
        </label>
        <button class="btn-coral w-full">{{ __('ui.nav.login') }}</button>
    </form>
    <p class="mt-4 text-center text-sm text-slate-400">
        <a href="{{ url('/forgot-password') }}" class="hover:text-coral-accent">Forgot password?</a>
    </p>
    <p class="mt-2 text-center text-sm text-slate-400">
        New here?
        <a href="{{ route('register') }}" class="text-coral-accent hover:underline">{{ __('ui.nav.register') }}</a>
    </p>
</div>
@endsection
