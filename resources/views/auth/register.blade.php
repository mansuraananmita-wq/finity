@extends('layouts.app')

@section('title', __('ui.nav.register'))

@section('main-class', 'mx-auto flex w-full max-w-md flex-1 items-center px-4 py-12')

@section('content')
<div class="card-ocean w-full p-8">
    <h1 class="font-display text-2xl font-bold text-white">{{ __('ui.nav.register') }}</h1>
    <p class="mt-1 text-sm text-slate-400">Create an account to save your cart, wishlist, and orders.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-xl border border-red-500/30 bg-red-950/40 px-4 py-3 text-sm text-red-200">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ url('/register') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="mb-1 block text-sm text-slate-400">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="input-ocean">
        </div>
        <div>
            <label class="mb-1 block text-sm text-slate-400">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="input-ocean">
        </div>
        <div>
            <label class="mb-1 block text-sm text-slate-400">{{ __('ui.labels.phone') }}</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="input-ocean" placeholder="01XXXXXXXXX">
        </div>
        <div>
            <label class="mb-1 block text-sm text-slate-400">Password</label>
            <input type="password" name="password" required class="input-ocean">
            <p class="mt-1 text-xs text-slate-500">At least 8 characters.</p>
        </div>
        <div>
            <label class="mb-1 block text-sm text-slate-400">Confirm Password</label>
            <input type="password" name="password_confirmation" required class="input-ocean">
        </div>
        <input type="hidden" name="preferred_language" value="en">
        <button class="btn-coral w-full">{{ __('ui.nav.register') }}</button>
    </form>
    <p class="mt-4 text-center text-sm text-slate-400">
        Already have an account?
        <a href="{{ route('login') }}" class="text-coral-accent hover:underline">{{ __('ui.nav.login') }}</a>
    </p>
</div>
@endsection
