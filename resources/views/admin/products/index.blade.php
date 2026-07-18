@extends('layouts.admin')
@section('title', 'Products')

@section('content')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <h2 class="text-xl font-semibold">Products ({{ $products->total() }})</h2>
    <a href="{{ route('admin.products.create') }}" class="rounded-lg bg-ocean-mid px-4 py-2 text-sm text-white hover:bg-ocean-light">Add product</a>
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search..." class="rounded-lg border-slate-300 text-sm">
    <select name="category_id" class="rounded-lg border-slate-300 text-sm">
        <option value="">All categories</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name_en }}</option>
        @endforeach
    </select>
    <label class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
        <input type="checkbox" name="low_stock" value="1" @checked(request('low_stock'))> Low stock only
    </label>
    <button class="rounded-lg bg-slate-700 px-4 py-2 text-sm text-white">Filter</button>
</form>

<div class="overflow-x-auto rounded-xl bg-white shadow-sm">
    <table class="w-full text-left text-sm">
        <thead class="border-b bg-slate-50 text-slate-500">
            <tr>
                <th class="p-3">Product</th>
                <th class="p-3">Category</th>
                <th class="p-3">Price</th>
                <th class="p-3">Stock</th>
                <th class="p-3">Weight</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr class="border-b border-slate-100">
                    <td class="p-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $product->image_path }}" alt="" class="h-10 w-10 rounded object-cover">
                            <div>
                                <p class="font-medium">{{ $product->name_en }}</p>
                                <p class="text-xs text-slate-500">{{ $product->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-3">{{ $product->category?->name_en }}</td>
                    <td class="p-3">৳{{ number_format($product->price, 2) }}</td>
                    <td class="p-3">
                        @if ($product->stock_qty < 5)
                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">{{ $product->stock_qty }} low</span>
                        @else
                            {{ $product->stock_qty }}
                        @endif
                    </td>
                    <td class="p-3">{{ $product->weight_grams }}g</td>
                    <td class="p-3">
                        <a href="{{ route('admin.products.edit', $product) }}" class="text-ocean-light hover:underline">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="ml-2 text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>
@endsection
