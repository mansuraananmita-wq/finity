@extends('layouts.admin')
@section('title', 'Order #'.$order->id)

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
        <div class="rounded-xl bg-white p-5 shadow-sm">
            <h2 class="mb-4 font-semibold">Order items</h2>
            <table class="w-full text-sm">
                <thead class="border-b text-slate-500"><tr><th class="py-2 text-left">Product</th><th>Qty</th><th>Price</th><th>Line total</th></tr></thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr class="border-b">
                            <td class="py-2">{{ $item->product?->name_en }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-center">৳{{ number_format($item->price_at_purchase, 2) }}</td>
                            <td class="text-right">৳{{ number_format($item->price_at_purchase * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <dl class="mt-4 space-y-1 text-sm">
                <div class="flex justify-between"><dt>Subtotal</dt><dd>৳{{ number_format($order->subtotal, 2) }}</dd></div>
                <div class="flex justify-between"><dt>Shipping</dt><dd>৳{{ number_format($order->shipping_cost, 2) }}</dd></div>
                <div class="flex justify-between"><dt>Discount</dt><dd>-৳{{ number_format($order->discount_amount, 2) }}</dd></div>
                <div class="flex justify-between font-semibold"><dt>Total</dt><dd>৳{{ number_format($order->total_amount, 2) }}</dd></div>
            </dl>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm">
            <h2 class="mb-4 font-semibold">Payment transactions</h2>
            @if ($order->paymentTransactions->isEmpty())
                <p class="text-sm text-slate-500">No gateway transactions recorded.</p>
            @else
                <table class="w-full text-sm">
                    <thead class="border-b text-slate-500">
                        <tr><th class="py-2 text-left">Gateway</th><th>Trx ID</th><th>Amount</th><th>Status</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($order->paymentTransactions as $trx)
                            <tr class="border-b">
                                <td class="py-2 capitalize">{{ $trx->gateway }}</td>
                                <td class="text-xs">{{ $trx->gateway_trx_id ?? '—' }}</td>
                                <td>৳{{ number_format($trx->amount, 2) }}</td>
                                <td>{{ $trx->status }}</td>
                                <td>{{ $trx->created_at->format('M j, H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-xl bg-white p-5 shadow-sm text-sm">
            <h2 class="mb-3 font-semibold">Customer & shipping</h2>
            <p><strong>{{ $order->user?->name }}</strong></p>
            <p>{{ $order->phone }}</p>
            <p class="mt-2">{{ $order->shipping_address }}</p>
            <p>{{ $order->shipping_district }}</p>
            @if ($order->notes)<p class="mt-2 text-slate-600">Note: {{ $order->notes }}</p>@endif
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm">
            <h2 class="mb-3 font-semibold">Update status</h2>
            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="space-y-3">
                @csrf @method('PATCH')
                <select name="status" class="w-full rounded-lg border-slate-300 text-sm">
                    @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="w-full rounded-lg bg-ocean-mid py-2 text-sm text-white">Update status</button>
            </form>
            <p class="mt-2 text-xs text-slate-500">Shipped/delivered updates email the customer.</p>
        </div>

        @if ($order->payment_method === 'cod' && $order->payment_status !== 'paid')
            <div class="rounded-xl bg-white p-5 shadow-sm">
                <h2 class="mb-3 font-semibold">COD payment</h2>
                <form method="POST" action="{{ route('admin.orders.mark-paid', $order) }}">
                    @csrf
                    <button class="w-full rounded-lg bg-green-700 py-2 text-sm text-white">Mark as paid</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
