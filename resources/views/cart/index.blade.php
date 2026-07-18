@extends('layouts.app')

@section('title', __('ui.nav.cart'))

@section('content')
<h1 class="font-display text-3xl font-bold text-white">{{ __('ui.nav.cart') }}</h1>

@if(empty($cartData['items']))
    <div class="mt-12 text-center">
        <p class="text-slate-400">{{ __('messages.cart_empty') }}</p>
        <a href="{{ route('products.index') }}" class="btn-coral mt-6">{{ __('ui.hero.cta') }}</a>
    </div>
@else
    <div class="mt-8 grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="card-ocean overflow-hidden">
                <div class="hidden sm:grid grid-cols-12 gap-4 border-b border-ocean-light/20 px-4 py-3 text-xs font-medium uppercase text-slate-400">
                    <div class="col-span-6">{{ __('ui.nav.products') }}</div>
                    <div class="col-span-2 text-center">{{ __('ui.labels.price') }}</div>
                    <div class="col-span-2 text-center">Qty</div>
                    <div class="col-span-2 text-right">{{ __('ui.labels.total') }}</div>
                </div>
                @foreach($cartData['items'] as $item)
                    <div class="grid grid-cols-1 gap-3 border-b border-ocean-light/10 p-4 sm:grid-cols-12 sm:items-center" data-item-id="{{ $item['id'] }}">
                        <div class="col-span-6 flex gap-3">
                            <img src="{{ $item['image_path'] }}" alt="" class="h-16 w-16 rounded-lg object-cover bg-ocean-dark">
                            <div>
                                <p class="font-medium">{{ $item['name'] }}</p>
                                <button type="button" class="remove-item mt-1 text-xs text-red-400 hover:text-red-300">{{ __('ui.actions.remove') }}</button>
                            </div>
                        </div>
                        <div class="col-span-2 text-center text-slate-300">৳{{ number_format($item['price'], 0) }}</div>
                        <div class="col-span-2 flex justify-center">
                            <input type="number" min="1" max="{{ $item['stock_qty'] }}" value="{{ $item['quantity'] }}"
                                   class="cart-qty w-16 rounded-lg border-ocean-light/40 bg-ocean-dark/60 text-center text-white">
                        </div>
                        <div class="col-span-2 text-right font-medium text-coral-accent line-total">৳{{ number_format($item['line_total'], 0) }}</div>
                    </div>
                @endforeach
            </div>
            <p class="mt-4 text-sm text-slate-500">{{ __('ui.labels.shipping_note') }}</p>
        </div>

        <div class="card-ocean h-fit p-6">
            <h2 class="font-semibold text-white">{{ __('ui.labels.order_summary') }}</h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-slate-400">{{ __('ui.labels.subtotal') }}</dt><dd id="subtotal">৳{{ number_format($cartData['subtotal'], 0) }}</dd></div>
                <div class="flex justify-between text-coral-accent" id="discount-row" style="display:none"><dt>{{ __('ui.labels.discount') }}</dt><dd id="discount-amount">—</dd></div>
            </dl>

            <form id="coupon-form" class="mt-6 flex gap-2">
                <input type="text" name="code" placeholder="WELCOME10" class="input-ocean flex-1 text-sm" @guest disabled @endguest>
                <button type="submit" class="btn-ocean text-xs" @guest disabled @endguest>{{ __('ui.actions.apply_coupon') }}</button>
            </form>
            @guest<p class="mt-2 text-xs text-slate-500">{{ __('ui.nav.login') }} {{ __('ui.actions.apply_coupon') }}</p>@endguest
            <button type="button" id="remove-coupon" class="mt-2 hidden text-xs text-slate-400 hover:text-red-400">{{ __('ui.actions.remove_coupon') }}</button>

            <div class="mt-6 border-t border-ocean-light/20 pt-4">
                <div class="flex justify-between text-lg font-bold">
                    <span>{{ __('ui.labels.subtotal') }}</span>
                    <span id="subtotal-bottom">৳{{ number_format($cartData['subtotal'], 0) }}</span>
                </div>
            </div>

            @auth
                <a href="{{ route('checkout.index') }}" class="btn-coral mt-6 block w-full text-center">{{ __('ui.nav.checkout') }}</a>
            @else
                <a href="{{ route('login') }}" class="btn-coral mt-6 block w-full text-center">{{ __('ui.nav.login') }}</a>
            @endauth
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
const csrf = '{{ csrf_token() }}';
const fmt = n => '৳' + Number(n).toLocaleString('en-BD', {maximumFractionDigits:0});

function syncCartTotals(cart) {
    if (!cart) return;
    const sub = document.getElementById('subtotal');
    const subBottom = document.getElementById('subtotal-bottom');
    if (sub) sub.textContent = fmt(cart.subtotal);
    if (subBottom) subBottom.textContent = fmt(cart.subtotal);
    window.dispatchEvent(new CustomEvent('cart-updated', { detail: cart }));
    if (!cart.items || cart.items.length === 0) {
        location.reload();
    }
}

function updateLineTotal(row, cart) {
    if (!cart?.items) return;
    const item = cart.items.find(i => String(i.id) === String(row.dataset.itemId));
    if (!item) return;
    const el = row.querySelector('.line-total');
    if (el) el.textContent = fmt(item.line_total);
}

document.querySelectorAll('.cart-qty').forEach(input => {
    input.addEventListener('change', async () => {
        const row = input.closest('[data-item-id]');
        const res = await fetch(`/cart/items/${row.dataset.itemId}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ quantity: parseInt(input.value, 10) }),
        });
        const data = await res.json();
        if (!res.ok) {
            window.showToast?.(data.message, 'error') || alert(data.message);
            return;
        }
        updateLineTotal(row, data.cart);
        syncCartTotals(data.cart);
        if (parseInt(input.value, 10) < 1) row.remove();
    });
});
document.querySelectorAll('.remove-item').forEach(btn => {
    btn.addEventListener('click', async () => {
        const row = btn.closest('[data-item-id]');
        const res = await fetch(`/cart/items/${row.dataset.itemId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }});
        const data = await res.json();
        row.remove();
        syncCartTotals(data.cart);
    });
});
document.getElementById('coupon-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const res = await fetch('{{ route('coupons.apply') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: JSON.stringify({ code: e.target.code.value }),
    });
    const data = await res.json();
    if (res.ok) {
        document.getElementById('discount-row').style.display = 'flex';
        document.getElementById('discount-amount').textContent = '-' + fmt(data.coupon.discount_amount);
        document.getElementById('remove-coupon').classList.remove('hidden');
        window.showToast?.(data.message || 'Coupon applied');
    } else {
        window.showToast?.(data.message || 'Invalid coupon', 'error') || alert(data.message);
    }
});
document.getElementById('remove-coupon')?.addEventListener('click', async () => {
    await fetch('{{ route('coupons.remove') }}', { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }});
    document.getElementById('discount-row').style.display = 'none';
    document.getElementById('remove-coupon').classList.add('hidden');
});
</script>
@endpush
