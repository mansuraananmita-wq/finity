@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded border">
    <form method="POST" action="{{ url('/reset-password') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <input type="email" name="email" value="{{ old('email', $request->email) }}" required class="w-full rounded border-slate-300">
        <input type="password" name="password" required class="w-full rounded border-slate-300" placeholder="New password">
        <input type="password" name="password_confirmation" required class="w-full rounded border-slate-300" placeholder="Confirm password">
        <button class="w-full bg-aqua-600 text-white py-2 rounded">Reset Password</button>
    </form>
</div>
@endsection
