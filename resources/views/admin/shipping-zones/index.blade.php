@extends('layouts.admin')
@section('title', 'Shipping Zones')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <h2 class="text-xl font-semibold">Shipping zones</h2>
    <a href="{{ route('admin.shipping-zones.create') }}" class="rounded-lg bg-ocean-mid px-4 py-2 text-sm text-white">Add zone</a>
</div>

<div class="overflow-x-auto rounded-xl bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="border-b bg-slate-50 text-slate-500">
            <tr>
                <th class="p-3">Zone</th>
                <th class="p-3">Base cost</th>
                <th class="p-3">Per kg</th>
                <th class="p-3">ETA</th>
                <th class="p-3">Active</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($zones as $zone)
                <tr class="border-b">
                    <td class="p-3">
                        <p class="font-medium">{{ $zone->zone_name }}</p>
                        <p class="text-xs text-slate-500">{{ implode(', ', $zone->districts ?? []) }}</p>
                    </td>
                    <td class="p-3">৳{{ $zone->base_cost }}</td>
                    <td class="p-3">৳{{ $zone->per_kg_cost }}</td>
                    <td class="p-3">{{ $zone->estimated_days }}</td>
                    <td class="p-3">{{ $zone->is_active ? 'Yes' : 'No' }}</td>
                    <td class="p-3">
                        <a href="{{ route('admin.shipping-zones.edit', $zone) }}" class="text-ocean-light">Edit</a>
                        <form action="{{ route('admin.shipping-zones.destroy', $zone) }}" method="POST" class="inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="ml-2 text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $zones->links() }}</div>
@endsection
