<header class="sticky top-0 z-40 border-b border-ocean-light/20 bg-ocean-dark/95 backdrop-blur-md"
        x-data="{ mobileOpen: false, cartCount: {{ $cartCount ?? 0 }} }"
        @cart-updated.window="cartCount = $event.detail.item_count">
    <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3">
        <a href="{{ route('home') }}" class="shrink-0 font-display text-lg font-bold tracking-tight text-white sm:text-xl">
            <span class="text-coral-accent">Finity</span> {{ __('ui.brand.fish_store') }}
        </a>

        <form action="{{ route('products.index') }}" method="GET" class="mx-2 hidden min-w-0 flex-1 lg:block">
            <div class="relative mx-auto max-w-sm">
                <input type="search" name="search" value="{{ request('search') }}"
                       placeholder="{{ __('ui.actions.search') }} fish..."
                       class="w-full rounded-full border border-ocean-light/40 bg-ocean-dark/70 py-2 pl-4 pr-10 text-sm text-white placeholder-slate-400 focus:border-coral-accent focus:outline-none focus:ring-1 focus:ring-coral-accent/40">
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full p-1.5 text-coral-accent hover:bg-ocean-light/30" aria-label="Search">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/></svg>
                </button>
            </div>
        </form>

        {{-- Always visible from sm up so links are not trapped behind a breakpoint --}}
        <nav class="ml-auto hidden items-center gap-4 text-sm font-semibold text-white sm:flex md:gap-5">
            <a href="{{ route('home') }}" class="hover:text-coral-accent {{ request()->routeIs('home') ? 'text-coral-accent' : '' }}">{{ __('ui.nav.home') }}</a>
            <a href="{{ route('products.index') }}" class="hover:text-coral-accent {{ request()->routeIs('products.*') ? 'text-coral-accent' : '' }}">{{ __('ui.nav.products') }}</a>
            <a href="{{ route('cart.view') }}" class="relative hover:text-coral-accent {{ request()->routeIs('cart.*') ? 'text-coral-accent' : '' }}">
                {{ __('ui.nav.cart') }}
                <span x-show="cartCount > 0" x-cloak x-text="cartCount"
                      class="absolute -right-3 -top-2 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-coral-accent px-1 text-xs font-bold text-ocean-dark"></span>
            </a>
            @auth
                <a href="{{ route('wishlist.index') }}" class="relative hover:text-coral-accent {{ request()->routeIs('wishlist.*') ? 'text-coral-accent' : '' }}">
                    {{ __('ui.nav.wishlist') }}
                    @if(($wishlistCount ?? 0) > 0)
                        <span class="absolute -right-3 -top-2 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-coral-accent px-1 text-xs font-bold text-ocean-dark">{{ $wishlistCount }}</span>
                    @endif
                </a>
                <a href="{{ route('orders.index') }}" class="hover:text-coral-accent {{ request()->routeIs('orders.*') ? 'text-coral-accent' : '' }}">{{ __('ui.nav.orders') }}</a>
            @endauth
        </nav>

        <div class="hidden shrink-0 items-center gap-2 sm:flex">
            @auth
                <div x-data="{ open: false }" class="relative">
                    <button type="button" @click="open = !open" class="btn-ocean !px-3 !py-1.5 text-sm">{{ auth()->user()->name }}</button>
                    <div x-show="open" @click.outside="open = false" x-cloak
                         class="absolute right-0 z-50 mt-2 w-48 rounded-xl border border-ocean-light/30 bg-ocean-mid py-2 text-white shadow-glow">
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm hover:bg-ocean-light/30">{{ __('ui.nav.admin') }}</a>
                        @endif
                        <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm hover:bg-ocean-light/30">{{ __('ui.nav.profile') }}</a>
                        <form method="POST" action="{{ url('/logout') }}">@csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm hover:bg-ocean-light/30">{{ __('ui.nav.logout') }}</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-white hover:text-coral-accent">{{ __('ui.nav.login') }}</a>
                <a href="{{ route('register') }}" class="btn-coral text-sm !px-4 !py-2">{{ __('ui.nav.register') }}</a>
            @endauth
        </div>

        <button type="button" @click="mobileOpen = !mobileOpen" class="ml-auto rounded-lg p-2 text-white sm:ml-0 sm:hidden" aria-label="Menu" aria-expanded="false" :aria-expanded="mobileOpen.toString()">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>

    <div x-show="mobileOpen" x-cloak class="border-t border-ocean-light/20 bg-ocean-mid sm:hidden">
        <div class="flex flex-col gap-1 px-4 py-4 text-sm font-medium text-white">
            <form action="{{ route('products.index') }}" method="GET" class="mb-2">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('ui.actions.search') }}..."
                       class="w-full rounded-xl border border-ocean-light/40 bg-ocean-dark/70 px-3 py-2 text-white">
            </form>
            <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 hover:bg-ocean-light/30">{{ __('ui.nav.home') }}</a>
            <a href="{{ route('products.index') }}" class="rounded-lg px-3 py-2 hover:bg-ocean-light/30">{{ __('ui.nav.products') }}</a>
            <a href="{{ route('cart.view') }}" class="rounded-lg px-3 py-2 hover:bg-ocean-light/30">{{ __('ui.nav.cart') }} (<span x-text="cartCount">0</span>)</a>
            @auth
                <a href="{{ route('wishlist.index') }}" class="rounded-lg px-3 py-2 hover:bg-ocean-light/30">{{ __('ui.nav.wishlist') }}</a>
                <a href="{{ route('orders.index') }}" class="rounded-lg px-3 py-2 hover:bg-ocean-light/30">{{ __('ui.nav.orders') }}</a>
                <a href="{{ route('profile.show') }}" class="rounded-lg px-3 py-2 hover:bg-ocean-light/30">{{ __('ui.nav.profile') }}</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 hover:bg-ocean-light/30">{{ __('ui.nav.admin') }}</a>
                @endif
                <form method="POST" action="{{ url('/logout') }}">@csrf<button class="w-full rounded-lg px-3 py-2 text-left hover:bg-ocean-light/30">{{ __('ui.nav.logout') }}</button></form>
            @else
                <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 hover:bg-ocean-light/30">{{ __('ui.nav.login') }}</a>
                <a href="{{ route('register') }}" class="rounded-lg px-3 py-2 hover:bg-ocean-light/30">{{ __('ui.nav.register') }}</a>
            @endauth
        </div>
    </div>
</header>
