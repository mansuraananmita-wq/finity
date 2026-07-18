@extends('layouts.admin')
@section('title', 'Edit Category')

@section('content')
<form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" class="max-w-3xl rounded-xl bg-white p-6 shadow-sm">
    @csrf @method('PUT')
    @include('admin.categories._form', ['category' => $category])
    <div class="mt-6 flex gap-3">
        <button class="rounded-lg bg-ocean-mid px-5 py-2 text-white">Save</button>
        <a href="{{ route('admin.categories.index') }}" class="rounded-lg border px-5 py-2">Cancel</a>
    </div>
</form>
@endsection
