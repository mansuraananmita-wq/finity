@extends('layouts.admin')
@section('title', 'Coupons')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <h2 class="text-xl font-semibold">Coupons</h2>
    <a href="{{ route('admin.coupons.create') }}" class="rounded-lg bg-ocean-mid px-4 py-2 text-sm text-white">Add coupon</a>
</div>

<div class="overflow-x-auto rounded-xl bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="border-b bg-slate-50 text-slate-500">
            <tr>
                <th class="p-3">Code</th>
                <th class="p-3">Type</th>
                <th class="p-3">Value</th>
                <th class="p-3">Uses</th>
                <th class="p-3">Expires</th>
                <th class="p-3">Active</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($coupons as $coupon)
                <tr class="border-b">
                    <td class="p-3 font-mono font-medium">{{ $coupon->code }}</td>
                    <td class="p-3">{{ $coupon->type }}</td>
                    <td class="p-3">{{ $coupon->type === 'percentage' ? $coupon->value.'%' : '৳'.$coupon->value }}</td>
                    <td class="p-3">{{ $coupon->used_count }} / {{ $coupon->max_uses ?? '∞' }}</td>
                    <td class="p-3">{{ $coupon->expires_at?->format('M j, Y') ?? '—' }}</td>
                    <td class="p-3">
                        <form method="POST" action="{{ route('admin.coupons.toggle', $coupon) }}">
                            @csrf
                            <button class="{{ $coupon->is_active ? 'text-green-700' : 'text-slate-400' }}">
                                {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="p-3">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-ocean-light">Edit</a>
                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="ml-2 text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $coupons->links() }}</div>
@endsection
