<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}@hasSection('title') — @yield('title')@endif</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="flex min-h-screen flex-col">
    <x-nav />
    <x-flash-message />

    @hasSection('hero')
        @yield('hero')
    @endif

    <main class="@yield('main-class', 'mx-auto w-full max-w-7xl flex-1 px-4 py-8 sm:py-10')">
        @yield('content')
    </main>

    <x-footer />

    <script>
    window.showToast = function (message, type = 'success') {
        const root = document.getElementById('toast-root');
        if (!root || !message) return;
        const el = document.createElement('div');
        const ok = type === 'success';
        el.className = 'pointer-events-auto animate-fade-in rounded-xl border px-4 py-3 text-sm shadow-glow backdrop-blur '
            + (ok
                ? 'border-emerald-500/40 bg-emerald-950/90 text-emerald-100'
                : 'border-red-500/40 bg-red-950/90 text-red-100');
        el.textContent = message;
        root.appendChild(el);
        setTimeout(() => el.remove(), 3500);
    };

    document.addEventListener('click', async (e) => {
        const addBtn = e.target.closest('[data-add-cart]');
        if (addBtn) {
            e.preventDefault();
            if (addBtn.disabled) return;
            addBtn.disabled = true;
            try {
                const id = addBtn.dataset.addCart;
                const res = await fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: JSON.stringify({ product_id: id, quantity: 1 }),
                });
                const data = await res.json();
                if (res.ok) {
                    window.dispatchEvent(new CustomEvent('cart-updated', { detail: data.cart }));
                    window.showToast(data.message || 'Added to cart');
                } else {
                    window.showToast(data.message || 'Could not add to cart', 'error');
                }
            } catch {
                window.showToast('Could not add to cart', 'error');
            } finally {
                addBtn.disabled = false;
            }
        }
        const wishBtn = e.target.closest('[data-wishlist-toggle]');
        if (wishBtn) {
            e.preventDefault();
            const id = wishBtn.dataset.wishlistToggle;
            const res = await fetch(`/wishlist/${id}/toggle`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }});
            const data = await res.json();
            if (res.ok) location.reload();
            else window.showToast(data.message || 'Wishlist error', 'error');
        }
    });
    </script>
    @stack('scripts')
</body>
</html>
