@extends('layouts.app')

@section('title', __('ui.footer.contact'))

@section('content')
<h1 class="font-display text-3xl font-bold text-white">{{ __('ui.footer.contact') }}</h1>
<div class="mt-8 grid gap-6 sm:grid-cols-2">
    <div class="card-ocean p-6">
        <h2 class="font-semibold text-coral-accent">{{ __('ui.labels.phone') }}</h2>
        <p class="mt-2 text-slate-300">+880 1700-000000</p>
    </div>
    <div class="card-ocean p-6">
        <h2 class="font-semibold text-coral-accent">Email</h2>
        <p class="mt-2 text-slate-300">hello@finity.test</p>
    </div>
    <div class="card-ocean p-6 sm:col-span-2">
        <h2 class="font-semibold text-coral-accent">{{ __('ui.labels.address') }}</h2>
        <p class="mt-2 text-slate-300"><!-- TODO: replace with final policy text --> Dhaka, Bangladesh</p>
    </div>
</div>
@endsection
