@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="grid gap-8 lg:grid-cols-2" x-data="{ lightbox: false }">
  <div class="card-ocean overflow-hidden">
    <button type="button" @click="lightbox = true" class="group relative block w-full cursor-zoom-in text-left" aria-label="View larger image">
      <img src="{{ $product->image_path }}" alt="{{ $product->name }}" class="aspect-[4/3] w-full object-cover transition group-hover:opacity-95"
           onerror="this.onerror=null;this.src='/images/products/placeholder.jpg'">
      <span class="pointer-events-none absolute bottom-3 right-3 rounded-full bg-ocean-dark/70 px-3 py-1 text-xs text-white backdrop-blur">Click to enlarge</span>
    </button>
  </div>

  {{-- Lightbox: modest enlarge only (use built Tailwind sizes so constraints ship in CSS) --}}
  <div x-show="lightbox" x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-8"
       @keydown.escape.window="lightbox = false"
       @click.self="lightbox = false">
    <button type="button" @click="lightbox = false" class="absolute right-4 top-4 rounded-full bg-white/10 px-3 py-1 text-white hover:bg-white/20">✕</button>
    <div class="w-full max-w-md rounded-xl border border-white/10 bg-ocean-dark/90 p-2 shadow-glow" @click.stop>
      <img src="{{ $product->image_path }}" alt="{{ $product->name }}"
           class="mx-auto max-h-80 w-full object-contain"
           onerror="this.onerror=null;this.src='/images/products/placeholder.jpg'">
    </div>
  </div>

  <div>
    <p class="text-sm text-coral-accent">{{ $product->category->name }}</p>
    <h1 class="mt-1 font-display text-3xl font-bold text-white">{{ $product->name }}</h1>
    <p class="mt-1 italic text-slate-400">{{ $product->scientific_name }}</p>

    <a href="#reviews" class="mt-4 flex flex-wrap items-center gap-3 hover:opacity-90">
      <x-rating-stars :rating="$product->avg_rating" size="md" />
      <span class="text-sm text-slate-400">({{ $approvedReviews->count() }} {{ __('ui.labels.reviews') }})</span>
    </a>

    <p class="mt-4 text-3xl font-bold text-coral-accent">৳{{ number_format($product->price, 0) }}</p>

    @php
      $careColors = ['Easy' => 'bg-emerald-500/20 text-emerald-300', 'Medium' => 'bg-amber-500/20 text-amber-300', 'Hard' => 'bg-red-500/20 text-red-300'];
    @endphp
    <span class="mt-3 inline-block rounded-full px-3 py-1 text-xs font-medium {{ $careColors[$product->care_level] ?? 'bg-slate-500/20' }}">
      {{ __('ui.labels.care_level') }}: {{ $product->care_level }}
    </span>

    <dl class="mt-6 grid grid-cols-2 gap-3 text-sm">
      <div class="card-ocean p-3"><dt class="text-slate-400">{{ __('ui.labels.tank_size') }}</dt><dd class="font-medium">{{ $product->tank_size_liters ?? '—' }} L</dd></div>
      <div class="card-ocean p-3"><dt class="text-slate-400">{{ __('ui.labels.weight') }}</dt><dd class="font-medium">{{ $product->weight_grams }}g</dd></div>
      <div class="card-ocean p-3"><dt class="text-slate-400">{{ __('ui.labels.stock') }}</dt><dd class="font-medium {{ $product->stock_qty < 1 ? 'text-red-400' : 'text-emerald-300' }}">
        {{ $product->stock_qty < 1 ? __('ui.labels.out_of_stock') : $product->stock_qty }}
      </dd></div>
    </dl>

    <p class="mt-6 leading-relaxed text-slate-300">{{ $product->description }}</p>

    @if($product->stock_qty > 0)
    <div class="mt-8 flex flex-wrap items-center gap-4" x-data="{ qty: 1 }">
      <div class="flex items-center rounded-full border border-ocean-light/40 bg-ocean-dark/60">
        <button type="button" @click="qty = Math.max(1, qty - 1)" class="px-4 py-2 text-lg">−</button>
        <span class="min-w-[2rem] text-center" x-text="qty"></span>
        <button type="button" @click="qty = Math.min({{ $product->stock_qty }}, qty + 1)" class="px-4 py-2 text-lg">+</button>
      </div>
      <button type="button"
              @click="fetch('{{ route('cart.add') }}', { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}, body: JSON.stringify({product_id:{{ $product->id }}, quantity: qty}) }).then(async r=>{ const d=await r.json(); if(d.cart) window.dispatchEvent(new CustomEvent('cart-updated',{detail:d.cart})); window.showToast?.(d.message || (r.ok?'Added':'Error'), r.ok?'success':'error'); })"
              class="btn-coral flex-1 sm:flex-none">
        {{ __('ui.actions.add_to_cart') }}
      </button>
      @auth
        <button type="button" data-wishlist-toggle="{{ $product->id }}" class="btn-ocean">
          {{ in_array($product->id, $wishlistIds) ? '♥' : '♡' }} {{ __('ui.nav.wishlist') }}
        </button>
      @endauth
    </div>
    @else
      <p class="mt-8 rounded-xl bg-red-950/40 px-4 py-3 text-red-300">{{ __('ui.labels.out_of_stock') }}</p>
    @endif
  </div>
</div>

<section class="mt-16 scroll-mt-24" id="reviews">
  <h2 class="font-display text-2xl font-bold text-white">{{ __('ui.labels.reviews') }}</h2>
  @if($approvedReviews->isNotEmpty())
    <div class="mt-4 flex items-center gap-3 card-ocean p-4">
      <span class="text-3xl font-bold text-coral-accent">{{ number_format($product->avg_rating, 1) }}</span>
      <x-rating-stars :rating="$product->avg_rating" size="lg" />
      <span class="text-slate-400">{{ $approvedReviews->count() }} {{ __('ui.labels.reviews') }}</span>
    </div>
  @endif

  <div class="mt-6 space-y-4">
    @forelse($approvedReviews as $review)
      <div class="card-ocean p-4">
        <div class="flex items-center justify-between gap-2">
          <span class="font-medium text-white">{{ $review->user?->name ?? 'Customer' }}</span>
          <x-rating-stars :rating="$review->rating" />
        </div>
        <p class="mt-1 text-xs text-slate-500">{{ $review->created_at->format('d M Y') }}</p>
        @if($review->comment)<p class="mt-2 text-slate-300">{{ $review->comment }}</p>@endif
      </div>
    @empty
      <p class="text-slate-400">{{ __('ui.labels.no_reviews') }}</p>
    @endforelse
  </div>

  @auth
    @if($hasReviewed)
      <p class="mt-6 text-slate-400">{{ __('messages.review_already_submitted') }}</p>
    @elseif($canReview)
      <form id="review-form" class="mt-6 card-ocean space-y-4 p-6">
        <h3 class="font-semibold text-white">{{ __('ui.actions.submit_review') }}</h3>
        <p class="text-xs text-slate-400">Reviews appear after admin approval.</p>
        <div>
          <label class="text-sm text-slate-400">{{ __('ui.labels.rating') }}</label>
          <select name="rating" class="input-ocean mt-1" required>
            @for($i=5;$i>=1;$i--) <option value="{{ $i }}">{{ $i }} ★</option> @endfor
          </select>
        </div>
        <div>
          <label class="text-sm text-slate-400">Comment</label>
          <textarea name="comment" rows="3" class="input-ocean mt-1" placeholder="How was this fish?"></textarea>
        </div>
        <button type="submit" class="btn-coral">{{ __('ui.actions.submit_review') }}</button>
      </form>
    @else
      <p class="mt-6 rounded-xl border border-ocean-light/30 bg-ocean-mid/40 px-4 py-3 text-slate-300">
        {{ __('ui.labels.review_purchase_required') }}
        <a href="{{ route('products.show', $product) }}#add" class="text-coral-accent">{{ __('ui.actions.add_to_cart') }}</a>
      </p>
    @endif
  @else
    <p class="mt-6 text-slate-400"><a href="{{ route('login') }}" class="text-coral-accent">{{ __('ui.nav.login') }}</a> {{ __('ui.labels.review_login') }}</p>
  @endauth
</section>

@if($relatedProducts->isNotEmpty())
<section class="mt-16">
  <h2 class="font-display text-xl font-bold text-white">{{ __('ui.labels.related_products') }}</h2>
  <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-4">
    @foreach($relatedProducts as $related)
      <x-product-card :product="$related" :in-wishlist="in_array($related->id, $wishlistIds)" />
    @endforeach
  </div>
</section>
@endif
@endsection

@push('scripts')
<script>
document.getElementById('review-form')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd = new FormData(e.target);
  const res = await fetch('{{ route('reviews.store', $product) }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
    body: JSON.stringify({ rating: fd.get('rating'), comment: fd.get('comment') }),
  });
  const data = await res.json();
  window.showToast?.(data.message || (res.ok ? 'Submitted' : 'Error'), res.ok ? 'success' : 'error');
  if (res.ok) location.reload();
});
</script>
@endpush
