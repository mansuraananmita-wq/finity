@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Orders today</p>
        <p class="mt-1 text-3xl font-semibold">{{ $stats['orders_today'] }}</p>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Orders this month</p>
        <p class="mt-1 text-3xl font-semibold">{{ $stats['orders_month'] }}</p>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Revenue today</p>
        <p class="mt-1 text-3xl font-semibold">৳{{ number_format($stats['revenue_today'], 2) }}</p>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Revenue this month</p>
        <p class="mt-1 text-3xl font-semibold">৳{{ number_format($stats['revenue_month'], 2) }}</p>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Low stock products</p>
        <p class="mt-1 text-3xl font-semibold text-red-600">{{ $stats['low_stock'] }}</p>
    </div>
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Pending reviews</p>
        <p class="mt-1 text-3xl font-semibold text-amber-600">{{ $stats['pending_reviews'] }}</p>
    </div>
</div>

@if ($lowStockProducts->isNotEmpty())
    <div class="mt-6 rounded-xl bg-white p-5 shadow-sm">
        <h2 class="mb-3 font-semibold text-red-700">Low stock alert</h2>
        <ul class="space-y-1 text-sm">
            @foreach ($lowStockProducts as $product)
                <li>{{ $product->name_en }} — <span class="font-medium text-red-600">{{ $product->stock_qty }} left</span></li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mt-6 rounded-xl bg-white p-5 shadow-sm">
    <h2 class="mb-4 font-semibold">Recent orders</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b text-slate-500">
                <tr>
                    <th class="py-2 pr-4">#</th>
                    <th class="py-2 pr-4">Customer</th>
                    <th class="py-2 pr-4">Total</th>
                    <th class="py-2 pr-4">Status</th>
                    <th class="py-2">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentOrders as $order)
                    <tr class="border-b border-slate-100">
                        <td class="py-2 pr-4"><a href="{{ route('admin.orders.show', $order) }}" class="text-ocean-light hover:underline">#{{ $order->id }}</a></td>
                        <td class="py-2 pr-4">{{ $order->user?->name ?? '—' }}</td>
                        <td class="py-2 pr-4">৳{{ number_format($order->total_amount, 2) }}</td>
                        <td class="py-2 pr-4 capitalize">{{ $order->status }}</td>
                        <td class="py-2">{{ $order->created_at->format('M j, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-4 text-slate-500">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
