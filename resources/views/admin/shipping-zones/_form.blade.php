@props(['zone' => null])

<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium">Zone name</label>
        <input type="text" name="zone_name" value="{{ old('zone_name', $zone?->zone_name) }}" required class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Districts (comma-separated)</label>
        <textarea name="districts" rows="3" required class="mt-1 w-full rounded-lg border-slate-300">{{ old('districts', $zone ? implode(', ', $zone->districts ?? []) : '') }}</textarea>
    </div>
    <div class="grid gap-4 sm:grid-cols-3">
        <div>
            <label class="block text-sm font-medium">Base cost (৳)</label>
            <input type="number" step="0.01" name="base_cost" value="{{ old('base_cost', $zone?->base_cost) }}" required class="mt-1 w-full rounded-lg border-slate-300">
        </div>
        <div>
            <label class="block text-sm font-medium">Per kg cost (৳)</label>
            <input type="number" step="0.01" name="per_kg_cost" value="{{ old('per_kg_cost', $zone?->per_kg_cost) }}" required class="mt-1 w-full rounded-lg border-slate-300">
        </div>
        <div>
            <label class="block text-sm font-medium">Estimated delivery</label>
            <input type="text" name="estimated_days" value="{{ old('estimated_days', $zone?->estimated_days) }}" required placeholder="2-4 days" class="mt-1 w-full rounded-lg border-slate-300">
        </div>
    </div>
    <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $zone?->is_active ?? true)) class="rounded">
        <span class="text-sm">Active</span>
    </label>
</div>
