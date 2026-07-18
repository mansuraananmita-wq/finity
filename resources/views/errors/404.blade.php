@extends('layouts.app')
@section('main-class', 'flex flex-1 flex-col items-center justify-center px-4 py-20 text-center')
@section('title', '404')
@section('content')
<div class="max-w-md animate-fade-in">
    <p class="font-display text-8xl font-bold text-ocean-light/50">404</p>
    <h1 class="mt-4 font-display text-2xl text-white">{{ __('ui.errors.not_found_title') }}</h1>
    <p class="mt-2 text-slate-400">{{ __('ui.errors.not_found_message') }}</p>
    <a href="{{ route('home') }}" class="btn-coral mt-8">{{ __('ui.errors.back_home') }}</a>
</div>
@endsection
