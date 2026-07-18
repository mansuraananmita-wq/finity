@extends('layouts.admin')
@section('title', 'Reviews')

@section('content')
<div class="mb-4 flex gap-2 border-b">
    <a href="{{ route('admin.reviews.index', ['tab' => 'pending']) }}" class="px-4 py-2 text-sm {{ $tab === 'pending' ? 'border-b-2 border-ocean-light font-semibold' : 'text-slate-500' }}">
        Pending ({{ $pendingReviews->total() }})
    </a>
    <a href="{{ route('admin.reviews.index', ['tab' => 'approved']) }}" class="px-4 py-2 text-sm {{ $tab === 'approved' ? 'border-b-2 border-ocean-light font-semibold' : 'text-slate-500' }}">
        Approved ({{ $approvedReviews->total() }})
    </a>
</div>

@if ($tab === 'approved')
    <div class="space-y-4">
        @forelse ($approvedReviews as $review)
            <div class="rounded-xl bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <p class="font-medium">{{ $review->product?->name_en }} — {{ $review->rating }}/5</p>
                        <p class="text-sm text-slate-600">{{ $review->user?->name }} · {{ $review->created_at->format('M j, Y') }}</p>
                        <p class="mt-2 text-sm">{{ $review->comment }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.reviews.hide', $review) }}">
                        @csrf @method('PATCH')
                        <button class="rounded-lg border border-amber-300 px-3 py-1 text-sm text-amber-800">Hide</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-slate-500">No approved reviews.</p>
        @endforelse
        {{ $approvedReviews->links() }}
    </div>
@else
    <div class="space-y-4">
        @forelse ($pendingReviews as $review)
            <div class="rounded-xl bg-white p-4 shadow-sm">
                <p class="font-medium">{{ $review->product?->name_en }} — {{ $review->rating }}/5</p>
                <p class="text-sm text-slate-600">{{ $review->user?->name }}</p>
                <p class="mt-2 text-sm">{{ $review->comment }}</p>
                <div class="mt-3 flex gap-2">
                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                        @csrf
                        <button class="rounded-lg bg-green-700 px-3 py-1 text-sm text-white">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                        @csrf @method('DELETE')
                        <button class="rounded-lg bg-red-600 px-3 py-1 text-sm text-white">Reject</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-slate-500">No pending reviews.</p>
        @endforelse
        {{ $pendingReviews->links() }}
    </div>
@endif
@endsection
