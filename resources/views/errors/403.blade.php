@extends('layouts.app')
@section('main-class', 'flex flex-1 flex-col items-center justify-center px-4 py-20 text-center')
@section('title', '403')
@section('content')
<div class="max-w-md animate-fade-in">
    <p class="font-display text-8xl font-bold text-ocean-light/50">403</p>
    <h1 class="mt-4 font-display text-2xl text-white">Access denied</h1>
    <p class="mt-2 text-slate-400">You don’t have permission to view this page. Admin login is required for the admin panel.</p>
    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
        <a href="{{ route('home') }}" class="btn-ocean">Back to store</a>
        <a href="{{ route('login') }}" class="btn-coral">Admin login</a>
    </div>
</div>
@endsection
