@props(['product', 'inWishlist' => false])

@php
    $outOfStock = $product->stock_qty < 1;
@endphp

<article {{ $attributes->merge(['class' => 'card-ocean group flex flex-col overflow-hidden transition hover:border-coral-accent/40 hover:shadow-glow']) }}>
    <div class="relative aspect-[4/3] overflow-hidden bg-ocean-dark">
        <a href="{{ route('products.show', $product) }}">
            <img src="{{ $product->image_path }}" alt="{{ $product->name }}"
                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                 loading="lazy"
                 onerror="this.onerror=null;this.src='/images/products/placeholder.jpg'">
        </a>
        @if($product->is_featured)
            <span class="absolute left-2 top-2 rounded-full bg-coral-accent/90 px-2 py-0.5 text-xs font-semibold text-ocean-dark">
                {{ __('ui.labels.featured') }}
            </span>
        @endif
        @if($outOfStock)
            <span class="absolute right-2 top-2 rounded-full bg-red-600/90 px-2 py-0.5 text-xs font-semibold text-white">
                {{ __('ui.labels.out_of_stock') }}
            </span>
        @endif
        @auth
            <button type="button"
                    data-wishlist-toggle="{{ $product->id }}"
                    class="wishlist-btn absolute bottom-2 right-2 rounded-full bg-ocean-dark/70 p-2 backdrop-blur transition hover:bg-ocean-light/80"
                    aria-label="{{ __('ui.nav.wishlist') }}">
                <svg class="h-5 w-5 {{ $inWishlist ? 'fill-coral-accent text-coral-accent' : 'text-white' }}" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
        @endauth
    </div>
    <div class="flex flex-1 flex-col p-4">
        <p class="text-xs text-slate-400">{{ $product->category->name }}</p>
        <h3 class="mt-1 font-display text-base font-semibold leading-snug">
            <a href="{{ route('products.show', $product) }}" class="hover:text-coral-accent">{{ $product->name }}</a>
        </h3>
        <div class="mt-2 flex items-center gap-2">
            <x-rating-stars :rating="$product->avg_rating" />
            @if($product->review_count > 0)
                <span class="text-xs text-slate-400">({{ $product->review_count }})</span>
            @endif
        </div>
        <p class="mt-3 text-lg font-bold text-coral-accent">৳{{ number_format($product->price, 0) }}</p>
        <div class="mt-auto pt-4">
            @if($outOfStock)
                <button disabled class="w-full cursor-not-allowed rounded-full bg-slate-700/50 px-4 py-2.5 text-sm text-slate-400">
                    {{ __('ui.labels.out_of_stock') }}
                </button>
            @else
                <button type="button" data-add-cart="{{ $product->id }}"
                        class="add-cart-btn w-full rounded-full bg-ocean-light px-4 py-2.5 text-sm font-medium text-white transition hover:bg-coral-accent hover:text-ocean-dark">
                    {{ __('ui.actions.add_to_cart') }}
                </button>
            @endif
        </div>
    </div>
</article>
