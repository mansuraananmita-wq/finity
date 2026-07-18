@props(['category' => null])

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium">Name (English)</label>
        <input type="text" name="name_en" value="{{ old('name_en', $category?->name_en) }}" required class="mt-1 w-full rounded-lg border-slate-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Name (Bengali)</label>
        <input type="text" name="name_bn" value="{{ old('name_bn', $category?->name_bn) }}" required class="mt-1 w-full rounded-lg border-slate-300">
    </div>
</div>
<div class="mt-4">
    <label class="block text-sm font-medium">Slug</label>
    <input type="text" name="slug" value="{{ old('slug', $category?->slug) }}" required class="mt-1 w-full rounded-lg border-slate-300">
</div>
<div class="mt-4 grid gap-4 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium">Description (English)</label>
        <textarea name="description_en" rows="3" class="mt-1 w-full rounded-lg border-slate-300">{{ old('description_en', $category?->description_en) }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium">Description (Bengali)</label>
        <textarea name="description_bn" rows="3" class="mt-1 w-full rounded-lg border-slate-300">{{ old('description_bn', $category?->description_bn) }}</textarea>
    </div>
</div>
<div class="mt-4">
    <label class="block text-sm font-medium">Image</label>
    <input type="file" name="image" accept="image/*" class="mt-1 w-full text-sm">
    @if ($category?->image_path)
        <img src="{{ $category->image_path }}" alt="" class="mt-2 h-16 w-16 rounded object-cover">
    @endif
</div>
