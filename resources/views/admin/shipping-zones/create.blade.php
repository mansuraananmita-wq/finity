@extends('layouts.admin')
@section('title', 'Add Shipping Zone')

@section('content')
<form method="POST" action="{{ route('admin.shipping-zones.store') }}" class="max-w-2xl rounded-xl bg-white p-6 shadow-sm">
    @csrf
    @include('admin.shipping-zones._form')
    <div class="mt-6 flex gap-3">
        <button class="rounded-lg bg-ocean-mid px-5 py-2 text-white">Create</button>
        <a href="{{ route('admin.shipping-zones.index') }}" class="rounded-lg border px-5 py-2">Cancel</a>
    </div>
</form>
@endsection
