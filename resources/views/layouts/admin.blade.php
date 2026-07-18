<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — {{ config('app.name') }}@hasSection('title') — @yield('title')@endif</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body min-h-screen bg-slate-100 text-slate-800 antialiased">
    <div class="flex min-h-screen">
        <aside class="hidden w-64 shrink-0 bg-ocean-dark text-white lg:block">
            <div class="border-b border-white/10 px-5 py-5">
                <a href="{{ route('admin.dashboard') }}" class="font-display text-lg font-semibold text-coral-accent">Finity Admin</a>
                <p class="mt-1 text-xs text-white/60">Store management</p>
            </div>
            <nav class="space-y-1 p-3 text-sm">
                @php
                    $links = [
                        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'match' => 'admin.dashboard'],
                        ['route' => 'admin.products.index', 'label' => 'Products', 'match' => 'admin.products.*'],
                        ['route' => 'admin.categories.index', 'label' => 'Categories', 'match' => 'admin.categories.*'],
                        ['route' => 'admin.orders.index', 'label' => 'Orders', 'match' => 'admin.orders.*'],
                        ['route' => 'admin.coupons.index', 'label' => 'Coupons', 'match' => 'admin.coupons.*'],
                        ['route' => 'admin.reviews.index', 'label' => 'Reviews', 'match' => 'admin.reviews.*'],
                        ['route' => 'admin.shipping-zones.index', 'label' => 'Shipping Zones', 'match' => 'admin.shipping-zones.*'],
                        ['route' => 'admin.customers.index', 'label' => 'Customers', 'match' => 'admin.customers.*'],
                    ];
                @endphp
                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}"
                       class="block rounded-lg px-3 py-2 {{ request()->routeIs($link['match']) ? 'bg-ocean-light text-white' : 'text-white/80 hover:bg-white/10' }}">
                        {{ $link['label'] }}
                    </a>
                @endforeach
                <a href="{{ route('home') }}" class="mt-4 block rounded-lg px-3 py-2 text-white/60 hover:bg-white/10">← View Store</a>
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3 sm:px-6">
                <div class="lg:hidden">
                    <select onchange="if(this.value) window.location=this.value" class="rounded border-slate-300 text-sm">
                        @foreach ($links as $link)
                            <option value="{{ route($link['route']) }}" @selected(request()->routeIs($link['match']))>{{ $link['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <h1 class="hidden font-display text-lg font-semibold text-ocean-dark lg:block">@yield('title', 'Dashboard')</h1>
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-slate-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg bg-ocean-mid px-3 py-1.5 text-white hover:bg-ocean-light">Logout</button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6">
                @if (session('success'))
                    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
                @endif
                @if (isset($errors) && $errors->any())
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
