@extends('layouts.app')

@section('title', __('ui.nav.profile'))

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-bold text-white">{{ __('ui.nav.profile') }}</h1>
            <p class="mt-1 text-sm text-slate-400">Manage your account, orders, and delivery details.</p>
        </div>
        <p class="text-xs text-slate-500">Member since {{ $user->created_at->format('M Y') }}</p>
    </div>

    @if (session('success'))
        <div class="mt-4 rounded-xl border border-emerald-500/30 bg-emerald-950/40 px-4 py-3 text-sm text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    {{-- Snapshot --}}
    <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="card-ocean p-4">
            <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('ui.nav.orders') }}</p>
            <p class="mt-1 text-2xl font-bold text-white">{{ $stats['orders'] }}</p>
            <p class="text-xs text-slate-400">{{ $stats['pending_orders'] }} active</p>
        </div>
        <div class="card-ocean p-4">
            <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('ui.nav.wishlist') }}</p>
            <p class="mt-1 text-2xl font-bold text-white">{{ $stats['wishlist'] }}</p>
            <a href="{{ route('wishlist.index') }}" class="text-xs text-coral-accent hover:underline">View wishlist</a>
        </div>
        <div class="card-ocean p-4">
            <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('ui.labels.reviews') }}</p>
            <p class="mt-1 text-2xl font-bold text-white">{{ $stats['reviews'] }}</p>
            <p class="text-xs text-slate-400">Submitted by you</p>
        </div>
        <div class="card-ocean p-4">
            <p class="text-xs uppercase tracking-wide text-slate-500">Email</p>
            <p class="mt-2 text-sm font-medium {{ $user->email_verified_at ? 'text-emerald-300' : 'text-amber-300' }}">
                {{ $user->email_verified_at ? 'Verified' : 'Not verified' }}
            </p>
            @unless($user->email_verified_at)
                <a href="{{ url('/email/verify') }}" class="text-xs text-coral-accent hover:underline">Verify now</a>
            @endunless
        </div>
    </div>

    {{-- Quick links --}}
    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('orders.index') }}" class="btn-ocean text-sm">{{ __('ui.nav.orders') }}</a>
        <a href="{{ route('wishlist.index') }}" class="btn-ocean text-sm">{{ __('ui.nav.wishlist') }}</a>
        <a href="{{ route('cart.view') }}" class="btn-ocean text-sm">{{ __('ui.nav.cart') }}</a>
        <a href="{{ route('products.index') }}" class="btn-coral text-sm !py-2.5">Shop fish</a>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        {{-- Recent orders --}}
        <section class="card-ocean p-6">
            <div class="flex items-center justify-between gap-2">
                <h2 class="font-semibold text-white">{{ __('ui.labels.recent_orders') }}</h2>
                <a href="{{ route('orders.index') }}" class="text-xs text-coral-accent hover:underline">See all</a>
            </div>
            @forelse ($recentOrders as $order)
                @php
                    $statusColors = [
                        'pending' => 'text-amber-300',
                        'processing' => 'text-blue-300',
                        'shipped' => 'text-sky-300',
                        'delivered' => 'text-emerald-300',
                        'cancelled' => 'text-red-300',
                    ];
                @endphp
                <a href="{{ route('orders.show', $order) }}" class="mt-4 flex items-center justify-between gap-3 border-t border-ocean-light/20 pt-4 first:mt-3 first:border-0 first:pt-0">
                    <div>
                        <p class="font-medium text-white">#{{ $order->id }}</p>
                        <p class="text-xs text-slate-500">{{ $order->created_at->format('d M Y') }} · {{ $order->items_count }} {{ __('ui.labels.items') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-coral-accent">৳{{ number_format($order->total_amount, 0) }}</p>
                        <p class="text-xs {{ $statusColors[$order->status] ?? 'text-slate-400' }}">{{ __("messages.status_{$order->status}") }}</p>
                    </div>
                </a>
            @empty
                <p class="mt-4 text-sm text-slate-400">{{ __('ui.labels.no_orders') }}</p>
                <a href="{{ route('products.index') }}" class="mt-3 inline-block text-sm text-coral-accent hover:underline">Browse products</a>
            @endforelse
        </section>

        {{-- Wishlist preview --}}
        <section class="card-ocean p-6">
            <div class="flex items-center justify-between gap-2">
                <h2 class="font-semibold text-white">{{ __('ui.labels.saved_fish') }}</h2>
                <a href="{{ route('wishlist.index') }}" class="text-xs text-coral-accent hover:underline">See all</a>
            </div>
            @forelse ($wishlistItems as $product)
                <a href="{{ route('products.show', $product) }}" class="mt-4 flex items-center gap-3 border-t border-ocean-light/20 pt-4 first:mt-3 first:border-0 first:pt-0">
                    <img src="{{ $product->image_path }}" alt="{{ $product->name }}" class="h-14 w-14 rounded-lg object-cover"
                         onerror="this.onerror=null;this.src='/images/products/placeholder.jpg'">
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-medium text-white">{{ $product->name }}</p>
                        <p class="text-sm text-coral-accent">৳{{ number_format($product->price, 0) }}</p>
                    </div>
                </a>
            @empty
                <p class="mt-4 text-sm text-slate-400">{{ __('ui.labels.wishlist_empty') }}</p>
                <a href="{{ route('products.index') }}" class="mt-3 inline-block text-sm text-coral-accent hover:underline">Find fish to save</a>
            @endforelse
        </section>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="card-ocean mt-8 space-y-4 p-6 sm:p-8">
        @csrf
        @method('PUT')
        <h2 class="font-semibold text-white">{{ __('ui.labels.account_info') }}</h2>
        <p class="text-xs text-slate-500">Used for checkout and delivery — keep phone & address up to date.</p>

        @if ($errors->updateProfileInformation->any())
            <div class="rounded-xl border border-red-500/30 bg-red-950/40 px-4 py-3 text-sm text-red-200">
                @foreach ($errors->updateProfileInformation->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm text-slate-400">{{ __('ui.labels.full_name') }}</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input-ocean">
            </div>
            <div>
                <label class="mb-1 block text-sm text-slate-400">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input-ocean">
            </div>
            <div>
                <label class="mb-1 block text-sm text-slate-400">{{ __('ui.labels.phone') }}</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input-ocean" placeholder="01XXXXXXXXX">
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm text-slate-400">{{ __('ui.labels.default_address') }}</label>
                <textarea name="address" rows="3" class="input-ocean" placeholder="House, road, area — used at checkout">{{ old('address', $user->address) }}</textarea>
            </div>
        </div>
        <button type="submit" class="btn-coral">{{ __('ui.actions.save_profile') }}</button>
    </form>

    <form method="POST" action="{{ route('profile.password') }}" class="card-ocean mt-6 space-y-4 p-6 sm:p-8">
        @csrf
        @method('PUT')
        <h2 class="font-semibold text-white">{{ __('ui.labels.change_password') }}</h2>

        @if ($errors->updatePassword->any())
            <div class="rounded-xl border border-red-500/30 bg-red-950/40 px-4 py-3 text-sm text-red-200">
                @foreach ($errors->updatePassword->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm text-slate-400">{{ __('ui.labels.current_password') }}</label>
                <input type="password" name="current_password" required class="input-ocean" autocomplete="current-password">
            </div>
            <div>
                <label class="mb-1 block text-sm text-slate-400">{{ __('ui.labels.new_password') }}</label>
                <input type="password" name="password" required class="input-ocean" autocomplete="new-password">
            </div>
            <div>
                <label class="mb-1 block text-sm text-slate-400">{{ __('ui.labels.confirm_password') }}</label>
                <input type="password" name="password_confirmation" required class="input-ocean" autocomplete="new-password">
            </div>
        </div>
        <button type="submit" class="btn-ocean">{{ __('ui.actions.update_password') }}</button>
    </form>
</div>
@endsection
