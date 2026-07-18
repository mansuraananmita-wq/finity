@extends('layouts.app')

@section('title', __('ui.nav.products'))

@section('content')
<div class="mb-8">
    <h1 class="font-display text-3xl font-bold text-white">{{ __('ui.nav.products') }}</h1>
    <p class="mt-1 text-slate-400">{{ __('ui.footer.tagline') }}</p>
</div>

<div class="flex flex-col gap-8 lg:flex-row">
    <aside class="lg:w-56 shrink-0">
        <div class="card-ocean p-4">
            <h2 class="font-semibold text-white">{{ __('ui.labels.category') }}</h2>
            <ul class="mt-3 space-y-1 text-sm">
                <li>
                    <a href="{{ route('products.index') }}"
                       class="block rounded-lg px-3 py-2 {{ !request('category') ? 'bg-ocean-light/40 text-coral-accent' : 'text-slate-300 hover:bg-ocean-light/20' }}">
                        {{ __('ui.nav.all_products') }}
                    </a>
                </li>
                @foreach($categories as $category)
                    <li>
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                           class="block rounded-lg px-3 py-2 {{ request('category') === $category->slug ? 'bg-ocean-light/40 text-coral-accent' : 'text-slate-300 hover:bg-ocean-light/20' }}">
                            {{ $category->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </aside>

    <div class="min-w-0 flex-1">
        <form method="GET" action="{{ route('products.index') }}" class="card-ocean mb-6 flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <input type="search" name="search" value="{{ request('search') }}"
                   placeholder="{{ __('ui.actions.search') }} by name..."
                   class="input-ocean flex-1"
                   autofocus>
            <select name="sort" class="input-ocean sm:w-48">
                @foreach(__('ui.sort') as $key => $label)
                    <option value="{{ $key }}" @selected($sort === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-coral !py-2.5 text-sm">{{ __('ui.actions.search') }}</button>
            @if(request('search') || request('category'))
                <a href="{{ route('products.index') }}" class="btn-ocean !py-2.5 text-center text-sm">Clear</a>
            @endif
        </form>

        @if(request('search'))
            <p class="mb-4 text-sm text-slate-400">
                Results for “{{ request('search') }}” — {{ $products->total() }} found
            </p>
        @endif

        @if($products->isEmpty())
            <p class="text-center text-slate-400 py-12">{{ __('ui.labels.no_products') }}</p>
        @else
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4">
                @foreach($products as $product)
                    <x-product-card :product="$product" :in-wishlist="in_array($product->id, $wishlistIds)" />
                @endforeach
            </div>
            <div class="mt-8 [&_a]:text-coral-accent [&_span]:bg-ocean-light/40 [&_*]:border-ocean-light/30">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
