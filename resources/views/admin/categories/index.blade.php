@extends('layouts.admin')
@section('title', 'Categories')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <h2 class="text-xl font-semibold">Categories ({{ $categories->total() }})</h2>
    <a href="{{ route('admin.categories.create') }}" class="rounded-lg bg-ocean-mid px-4 py-2 text-sm text-white">Add category</a>
</div>

<div class="overflow-x-auto rounded-xl bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="border-b bg-slate-50 text-slate-500">
            <tr>
                <th class="p-3">Name (EN)</th>
                <th class="p-3">Name (BN)</th>
                <th class="p-3">Slug</th>
                <th class="p-3">Products</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr class="border-b">
                    <td class="p-3">{{ $category->name_en }}</td>
                    <td class="p-3">{{ $category->name_bn }}</td>
                    <td class="p-3">{{ $category->slug }}</td>
                    <td class="p-3">{{ $category->products_count }}</td>
                    <td class="p-3">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="text-ocean-light hover:underline">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="ml-2 text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $categories->links() }}</div>
@endsection
