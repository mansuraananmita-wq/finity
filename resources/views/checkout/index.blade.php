@extends('layouts.app')

@section('title', __('ui.nav.checkout'))

@section('content')
<h1 class="font-display text-3xl font-bold text-white">{{ __('ui.nav.checkout') }}</h1>

@if ($errors->any())
    <div class="mt-4 rounded-xl border border-red-500/30 bg-red-950/40 px-4 py-3 text-sm text-red-200">
        <p class="font-medium">Please fix the following:</p>
        <ul class="mt-2 list-inside list-disc">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (empty($cartData['items']))
    <div class="mt-12 text-center">
        <p class="text-slate-400">{{ __('messages.cart_empty') }}</p>
        <a href="{{ route('products.index') }}" class="btn-coral mt-6">{{ __('ui.hero.cta') }}</a>
    </div>
@else
@php
    $allDistricts = collect();
    foreach ($zones as $zone) {
        foreach ($zone->districts as $d) {
            $allDistricts->push($d);
        }
    }
    $allDistricts = $allDistricts->unique()->sort()->values();
@endphp

<div class="mt-8 grid gap-8 lg:grid-cols-5">
    <form method="POST" action="{{ route('checkout.store') }}" class="lg:col-span-3 space-y-5 card-ocean p-6" id="checkout-form">
        @csrf
        <div>
            <label class="mb-1 block text-sm text-slate-400">{{ __('ui.labels.full_name') }}</label>
            <input type="text" value="{{ auth()->user()->name }}" disabled class="input-ocean opacity-70">
        </div>
        <div>
            <label class="mb-1 block text-sm text-slate-400">{{ __('ui.labels.phone') }}</label>
            <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required
                   class="input-ocean" placeholder="01XXXXXXXXX" inputmode="numeric">
            <p class="mt-1 text-xs text-slate-500">Format: 01XXXXXXXXX (11 digits)</p>
        </div>
        <div>
            <label class="mb-1 block text-sm text-slate-400">{{ __('ui.labels.address') }}</label>
            <textarea name="shipping_address" rows="3" required class="input-ocean" placeholder="House, road, area">{{ old('shipping_address', auth()->user()->address) }}</textarea>
        </div>
        <div>
            <label class="mb-1 block text-sm text-slate-400">{{ __('ui.labels.district') }}</label>
            <select name="shipping_district" id="district-select" required class="input-ocean">
                <option value="">{{ __('ui.labels.select_district') }}</option>
                @foreach($allDistricts as $district)
                    <option value="{{ $district }}" @selected(old('shipping_district', 'Dhaka') === $district)>{{ $district }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-sm text-slate-400">{{ __('ui.labels.notes') }}</label>
            <textarea name="notes" rows="2" class="input-ocean">{{ old('notes') }}</textarea>
        </div>

        <div>
            <label class="mb-3 block text-sm font-medium text-white">{{ __('ui.labels.payment_method') }}</label>
            <div class="grid gap-3 sm:grid-cols-3">
                @foreach(['cod' => __('ui.labels.cod'), 'bkash' => __('ui.labels.bkash'), 'nagad' => __('ui.labels.nagad')] as $val => $label)
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-ocean-light/30 p-4 transition has-[:checked]:border-coral-accent has-[:checked]:bg-ocean-light/20">
                        <input type="radio" name="payment_method" value="{{ $val }}" class="text-coral-accent focus:ring-coral-accent" @checked(old('payment_method', 'cod') === $val) required>
                        <span class="text-sm font-medium">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <button type="submit" id="place-order-btn" class="btn-coral w-full">{{ __('ui.actions.place_order') }}</button>
    </form>

    <div class="lg:col-span-2 card-ocean h-fit p-6">
        <h2 class="font-semibold text-white">{{ __('ui.labels.order_summary') }}</h2>
        <ul class="mt-4 max-h-48 space-y-2 overflow-y-auto text-sm">
            @foreach($cartData['items'] as $item)
                <li class="flex justify-between text-slate-300">
                    <span class="truncate pr-2">{{ $item['name'] }} ×{{ $item['quantity'] }}</span>
                    <span>৳{{ number_format($item['line_total'], 0) }}</span>
                </li>
            @endforeach
        </ul>
        <dl class="mt-6 space-y-2 border-t border-ocean-light/20 pt-4 text-sm">
            <div class="flex justify-between"><dt class="text-slate-400">{{ __('ui.labels.subtotal') }}</dt><dd id="sum-subtotal">৳{{ number_format($subtotal, 0) }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">{{ __('ui.labels.discount') }}</dt><dd id="sum-discount">৳{{ number_format($discount, 0) }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-400">{{ __('ui.labels.shipping') }}</dt><dd id="sum-shipping">Calculating…</dd></div>
            <div id="zone-info" class="text-xs text-slate-500"></div>
            <div class="flex justify-between border-t border-ocean-light/20 pt-3 text-lg font-bold text-coral-accent">
                <dt>{{ __('ui.labels.total') }}</dt><dd id="sum-total">—</dd>
            </div>
        </dl>
    </div>
</div>
@endif
@endsection

@push('scripts')
@if (!empty($cartData['items']))
<script>
const districtSelect = document.getElementById('district-select');
const fmt = n => '৳' + Number(n).toLocaleString('en-BD', {maximumFractionDigits:0});

async function updateShipping() {
    const district = districtSelect.value;
    if (!district) {
        document.getElementById('sum-shipping').textContent = '—';
        document.getElementById('sum-total').textContent = '—';
        return;
    }
    try {
        const res = await fetch('{{ route('checkout.shipping') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ district }),
        });
        const data = await res.json();
        if (!res.ok) {
            document.getElementById('sum-shipping').textContent = '—';
            document.getElementById('sum-total').textContent = '—';
            document.getElementById('zone-info').textContent = data.message || 'Shipping unavailable';
            window.showToast?.(data.message || 'Shipping unavailable', 'error');
            return;
        }
        document.getElementById('sum-shipping').textContent = fmt(data.shipping_cost);
        document.getElementById('sum-discount').textContent = fmt(data.discount);
        document.getElementById('sum-subtotal').textContent = fmt(data.subtotal);
        document.getElementById('sum-total').textContent = fmt(data.total);
        document.getElementById('zone-info').textContent = data.zone.name + ' — ' + data.zone.estimated_days;
    } catch (e) {
        window.showToast?.('Could not calculate shipping', 'error');
    }
}
districtSelect.addEventListener('change', updateShipping);
updateShipping();
</script>
@endif
@endpush
