@extends('layouts.admin')
@section('title', 'Edit Coupon')

@section('content')
<form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="max-w-2xl rounded-xl bg-white p-6 shadow-sm">
    @csrf @method('PUT')
    @include('admin.coupons._form', ['coupon' => $coupon])
    <p class="mt-4 text-sm text-slate-500">Used: {{ $coupon->used_count }} times</p>
    <div class="mt-6 flex gap-3">
        <button class="rounded-lg bg-ocean-mid px-5 py-2 text-white">Save</button>
        <a href="{{ route('admin.coupons.index') }}" class="rounded-lg border px-5 py-2">Cancel</a>
    </div>
</form>
@endsection
