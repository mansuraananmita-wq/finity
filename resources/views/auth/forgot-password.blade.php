@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded border">
    <form method="POST" action="{{ url('/forgot-password') }}" class="space-y-4">@csrf
        <input type="email" name="email" required class="w-full rounded border-slate-300" placeholder="Email">
        <button class="w-full bg-aqua-600 text-white py-2 rounded">Send Reset Link</button>
    </form>
</div>
@endsection
