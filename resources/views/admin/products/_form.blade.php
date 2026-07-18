@props(['product' => null, 'categories'])

<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <label class="block text-sm font-medium">Name (English)</label>
        <input type="text" name="name_en" value="{{ old('name_en', $product?->name_en) }}" required class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Name (Bengali)</label>
        <input type="text" name="name_bn" value="{{ old('name_bn', $product?->name_bn) }}" required class="mt-1 w-full rounded-lg border-slate-300">
    </div>
</div>

<div class="mt-4 grid gap-6 lg:grid-cols-2">
    <div>
        <label class="block text-sm font-medium">Description (English)</label>
        <textarea name="description_en" rows="4" required class="mt-1 w-full rounded-lg border-slate-300">{{ old('description_en', $product?->description_en) }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium">Description (Bengali)</label>
        <textarea name="description_bn" rows="4" required class="mt-1 w-full rounded-lg border-slate-300">{{ old('description_bn', $product?->description_bn) }}</textarea>
    </div>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <div>
        <label class="block text-sm font-medium">Scientific name</label>
        <input type="text" name="scientific_name" value="{{ old('scientific_name', $product?->scientific_name) }}" required class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $product?->slug) }}" required class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Category</label>
        <select name="category_id" required class="mt-1 w-full rounded-lg border-slate-300">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product?->category_id) == $category->id)>{{ $category->name_en }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium">Price (৳)</label>
        <input type="number" step="0.01" name="price" value="{{ old('price', $product?->price) }}" required class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Stock quantity</label>
        <input type="number" name="stock_qty" value="{{ old('stock_qty', $product?->stock_qty ?? 0) }}" required class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="block text-sm font-medium text-red-700">Weight (grams) *</label>
        <input type="number" name="weight_grams" value="{{ old('weight_grams', $product?->weight_grams) }}" required class="mt-1 w-full rounded-lg border-red-200 ring-red-100 focus:border-red-400 focus:ring-red-200">
        <p class="mt-1 text-xs text-slate-500">Used for shipping cost calculation.</p>
    </div>
    <div>
        <label class="block text-sm font-medium">Care level</label>
        <select name="care_level" required class="mt-1 w-full rounded-lg border-slate-300">
            @foreach (['Easy', 'Medium', 'Hard'] as $level)
                <option value="{{ $level }}" @selected(old('care_level', $product?->care_level) === $level)>{{ $level }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium">Tank size (liters)</label>
        <input type="number" name="tank_size_liters" value="{{ old('tank_size_liters', $product?->tank_size_liters) }}" class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Image (max 2MB)</label>
        <input type="file" name="image" accept="image/*" class="mt-1 w-full text-sm">
        @if ($product?->image_path)
            <img src="{{ $product->image_path }}" alt="" class="mt-2 h-20 w-20 rounded object-cover">
        @endif
    </div>
</div>

<div class="mt-4">
    <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product?->is_featured)) class="rounded border-slate-300 text-ocean-light">
        <span class="text-sm">Featured product</span>
    </label>
</div>
