@extends('layouts.app')

@section('title', __('ui.nav.home'))

@section('hero')
    <x-hero />
@endsection

@section('content')
<section id="featured" class="scroll-mt-20">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="font-display text-2xl font-bold text-white sm:text-3xl">{{ __('ui.hero.featured') }}</h2>
            <p class="mt-1 text-slate-400">{{ __('ui.footer.tagline') }}</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn-ocean">{{ __('ui.hero.view_all') }}</a>
    </div>
    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
        @foreach($featuredProducts as $product)
            <x-product-card :product="$product" :in-wishlist="in_array($product->id, $wishlistIds)" />
        @endforeach
    </div>
</section>

<section class="mt-16 rounded-2xl ocean-gradient p-8 text-center sm:p-12">
    <h2 class="font-display text-2xl font-bold text-white">{{ __('ui.footer.delivery') }}</h2>
    <p class="mx-auto mt-3 max-w-xl text-slate-300">{{ __('ui.hero.shipping_note') }}</p>
    <a href="{{ route('products.index') }}" class="btn-coral mt-6">{{ __('ui.hero.cta') }}</a>
</section>
@endsection
