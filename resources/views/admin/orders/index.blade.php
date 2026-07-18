@extends('layouts.admin')
@section('title', 'Orders')

@section('content')
<h2 class="mb-4 text-xl font-semibold">Orders</h2>

<form method="GET" class="mb-4 flex flex-wrap gap-2 text-sm">
    <select name="status" class="rounded-lg border-slate-300">
        <option value="">All statuses</option>
        @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
    <select name="payment_status" class="rounded-lg border-slate-300">
        <option value="">All payment</option>
        @foreach (['unpaid', 'paid', 'failed', 'refunded'] as $ps)
            <option value="{{ $ps }}" @selected(request('payment_status') === $ps)>{{ ucfirst($ps) }}</option>
        @endforeach
    </select>
    <input type="date" name="from" value="{{ request('from') }}" class="rounded-lg border-slate-300">
    <input type="date" name="to" value="{{ request('to') }}" class="rounded-lg border-slate-300">
    <button class="rounded-lg bg-slate-700 px-4 py-2 text-white">Filter</button>
</form>

<div class="overflow-x-auto rounded-xl bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="border-b bg-slate-50 text-slate-500">
            <tr>
                <th class="p-3">#</th>
                <th class="p-3">Customer</th>
                <th class="p-3">Total</th>
                <th class="p-3">Status</th>
                <th class="p-3">Payment</th>
                <th class="p-3">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr class="border-b">
                    <td class="p-3"><a href="{{ route('admin.orders.show', $order) }}" class="text-ocean-light hover:underline">#{{ $order->id }}</a></td>
                    <td class="p-3">{{ $order->user?->name }}</td>
                    <td class="p-3">৳{{ number_format($order->total_amount, 2) }}</td>
                    <td class="p-3 capitalize">{{ $order->status }}</td>
                    <td class="p-3 capitalize">{{ $order->payment_status }}</td>
                    <td class="p-3">{{ $order->created_at->format('M j, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $orders->links() }}</div>
@endsection
