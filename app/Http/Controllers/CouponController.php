<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyCouponRequest;
use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private CouponService $couponService,
    ) {}

    public function apply(ApplyCouponRequest $request): JsonResponse
    {
        $cart = $this->cartService->getCartWithItems();
        $subtotal = $this->cartService->calculateSubtotal($cart);

        try {
            $result = $this->couponService->apply($request->validated('code'), $subtotal);

            return response()->json([
                'message' => __('messages.coupon_applied'),
                'coupon' => $result,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function remove(): JsonResponse
    {
        $this->couponService->remove();

        return response()->json([
            'message' => __('messages.coupon_removed'),
        ]);
    }
}
