@extends('layouts.admin')
@section('title', 'Edit Product')

@section('content')
<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="max-w-5xl rounded-xl bg-white p-6 shadow-sm">
    @csrf @method('PUT')
    @include('admin.products._form', ['product' => $product, 'categories' => $categories])
    <div class="mt-6 flex gap-3">
        <button type="submit" class="rounded-lg bg-ocean-mid px-5 py-2 text-white hover:bg-ocean-light">Save changes</button>
        <a href="{{ route('admin.products.index') }}" class="rounded-lg border border-slate-300 px-5 py-2">Cancel</a>
    </div>
</form>
@endsection
