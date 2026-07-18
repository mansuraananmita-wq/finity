<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(StoreReviewRequest $request, Product $product): JsonResponse
    {
        $user = auth()->user();

        $order = Order::where('user_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->latest()
            ->first();

        if (! $order) {
            return response()->json([
                'message' => __('messages.review_not_eligible'),
            ], 403);
        }

        if (Review::where('user_id', $user->id)->where('product_id', $product->id)->exists()) {
            return response()->json([
                'message' => __('messages.review_already_submitted'),
            ], 422);
        }

        Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'rating' => $request->validated('rating'),
            'comment' => $request->validated('comment'),
            'is_approved' => false,
        ]);

        return response()->json([
            'message' => __('messages.review_submitted'),
        ]);
    }
}
