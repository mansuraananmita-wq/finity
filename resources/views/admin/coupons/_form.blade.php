@props(['coupon' => null])

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium">Code</label>
        <input type="text" name="code" value="{{ old('code', $coupon?->code) }}" required class="mt-1 w-full rounded-lg border-slate-300 uppercase">
    </div>
    <div>
        <label class="block text-sm font-medium">Type</label>
        <select name="type" required class="mt-1 w-full rounded-lg border-slate-300">
            <option value="percentage" @selected(old('type', $coupon?->type) === 'percentage')>Percentage</option>
            <option value="fixed" @selected(old('type', $coupon?->type) === 'fixed')>Fixed amount</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium">Value</label>
        <input type="number" step="0.01" name="value" value="{{ old('value', $coupon?->value) }}" required class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Min order amount</label>
        <input type="number" step="0.01" name="min_order_amount" value="{{ old('min_order_amount', $coupon?->min_order_amount) }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Max uses</label>
        <input type="number" name="max_uses" value="{{ old('max_uses', $coupon?->max_uses) }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Expires at</label>
        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon?->expires_at?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
</div>
<div class="mt-4">
    <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon?->is_active ?? true)) class="rounded">
        <span class="text-sm">Active</span>
    </label>
</div>
