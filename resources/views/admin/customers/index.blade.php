@extends('layouts.admin')
@section('title', 'Customers')

@section('content')
<h2 class="mb-4 text-xl font-semibold">Customers (read-only)</h2>

<div class="overflow-x-auto rounded-xl bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="border-b bg-slate-50 text-slate-500">
            <tr>
                <th class="p-3">Name</th>
                <th class="p-3">Email</th>
                <th class="p-3">Phone</th>
                <th class="p-3">Orders</th>
                <th class="p-3">Total spent</th>
                <th class="p-3">Joined</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $customer)
                <tr class="border-b">
                    <td class="p-3">{{ $customer->name }}</td>
                    <td class="p-3">{{ $customer->email }}</td>
                    <td class="p-3">{{ $customer->phone ?? '—' }}</td>
                    <td class="p-3">{{ $customer->orders_count }}</td>
                    <td class="p-3">৳{{ number_format($customer->total_spent ?? 0, 2) }}</td>
                    <td class="p-3">{{ $customer->created_at->format('M j, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $customers->links() }}</div>
@endsection
