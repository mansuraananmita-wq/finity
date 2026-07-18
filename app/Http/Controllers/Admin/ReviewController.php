<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->string('tab')->toString() ?: 'pending';

        $pendingReviews = Review::with(['product', 'user'])
            ->where('is_approved', 0)
            ->latest()
            ->paginate(20, ['*'], 'pending_page');

        $approvedReviews = Review::with(['product', 'user'])
            ->where('is_approved', 1)
            ->latest()
            ->paginate(20, ['*'], 'approved_page');

        return view('admin.reviews.index', compact('pendingReviews', 'approvedReviews', 'tab'));
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->forceFill(['is_approved' => true])->save();
        $review->refresh();
        Product::updateRatingStats($review->product()->first());

        return back()->with('success', __('messages.review_approved'));
    }

    public function reject(Review $review): RedirectResponse
    {
        $product = $review->product()->first();
        $review->delete();
        Product::updateRatingStats($product);

        return back()->with('success', __('messages.review_rejected'));
    }

    public function hide(Review $review): RedirectResponse
    {
        $product = $review->product()->first();
        $review->forceFill(['is_approved' => false])->save();
        Product::updateRatingStats($product);

        return back()->with('success', 'Review hidden.');
    }
}
