@extends('layouts.admin')
@section('title', 'Add Coupon')

@section('content')
<form method="POST" action="{{ route('admin.coupons.store') }}" class="max-w-2xl rounded-xl bg-white p-6 shadow-sm">
    @csrf
    @include('admin.coupons._form')
    <div class="mt-6 flex gap-3">
        <button class="rounded-lg bg-ocean-mid px-5 py-2 text-white">Create</button>
        <a href="{{ route('admin.coupons.index') }}" class="rounded-lg border px-5 py-2">Cancel</a>
    </div>
</form>
@endsection
