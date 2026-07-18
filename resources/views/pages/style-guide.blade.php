@extends('layouts.app')

@section('title', 'Style Guide')

@section('content')
<h1 class="font-display text-3xl font-bold text-white">Style Guide</h1>
<p class="mt-2 text-slate-400">Ocean theme kitchen sink — verify before wiring pages.</p>

<section class="mt-10">
    <h2 class="font-display text-xl text-coral-accent">Colors</h2>
    <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="h-20 rounded-xl bg-ocean-dark border border-ocean-light/30 flex items-end p-2 text-xs">ocean-dark</div>
        <div class="h-20 rounded-xl bg-ocean-mid flex items-end p-2 text-xs">ocean-mid</div>
        <div class="h-20 rounded-xl bg-ocean-light flex items-end p-2 text-xs">ocean-light</div>
        <div class="h-20 rounded-xl bg-coral-accent text-ocean-dark flex items-end p-2 text-xs font-medium">coral-accent</div>
    </div>
</section>

<section class="mt-10">
    <h2 class="font-display text-xl text-coral-accent">Typography</h2>
    <p class="mt-4 font-display text-2xl">Sora Display — Finity Fish Store</p>
    <p class="mt-2 font-sans">Inter body — Premium ornamental fish for your aquarium.</p>
    <p class="mt-2" lang="bn">নোটো সান্স বাংলা — আপনার অ্যাকোয়ারিয়ামের জন্য প্রিমিয়াম শোভাময় মাছ</p>
</section>

<section class="mt-10 flex flex-wrap gap-4">
    <button class="btn-coral">Coral CTA</button>
    <button class="btn-ocean">Ocean Button</button>
</section>

<section class="mt-10">
    <x-rating-stars :rating="4.5" size="lg" />
</section>

<section class="mt-10 max-w-sm">
    <x-product-card :product="\App\Models\Product::first()" />
</section>
@endsection
