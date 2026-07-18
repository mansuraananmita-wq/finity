@extends('layouts.app')

@section('title', __('ui.nav.wishlist'))

@section('content')
<h1 class="font-display text-3xl font-bold text-white">{{ __('ui.nav.wishlist') }}</h1>

@if($products->isEmpty())
    <div class="mt-16 text-center">
        <svg class="mx-auto h-16 w-16 text-ocean-light" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        <p class="mt-4 text-slate-400">{{ __('ui.labels.wishlist_empty') }}</p>
        <a href="{{ route('products.index') }}" class="btn-coral mt-6">{{ __('ui.hero.cta') }}</a>
    </div>
@else
    <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
        @foreach($products as $product)
            <x-product-card :product="$product" :in-wishlist="true" />
        @endforeach
    </div>
@endif
@endsection
