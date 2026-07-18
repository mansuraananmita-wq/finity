<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\OrderService;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private ShippingService $shippingService,
        private CouponService $couponService,
        private OrderService $orderService,
    ) {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $cart = $this->cartService->getCartWithItems();
        $subtotal = $this->cartService->calculateSubtotal($cart);
        $weight = $this->cartService->calculateTotalWeightGrams($cart);
        $discount = $this->couponService->getDiscountAmount($subtotal);
        $zones = $this->shippingService->getActiveZones();

        return view('checkout.index', [
            'cartData' => $this->cartService->toArray($cart),
            'subtotal' => $subtotal,
            'weight' => $weight,
            'discount' => $discount,
            'zones' => $zones,
            'appliedCoupon' => $this->couponService->getAppliedCoupon(),
        ]);
    }

    public function calculateShipping(Request $request): JsonResponse
    {
        $request->validate(['district' => ['required', 'string', 'max:100']]);

        $cart = $this->cartService->getCartWithItems();
        $zone = $this->shippingService->findZoneByDistrict($request->district);

        if (! $zone) {
            return response()->json(['message' => __('messages.shipping_zone_not_found')], 422);
        }

        $weight = $this->cartService->calculateTotalWeightGrams($cart);
        $subtotal = $this->cartService->calculateSubtotal($cart);
        $shippingCost = $this->shippingService->calculateCost($zone, $weight);
        $discount = $this->couponService->getDiscountAmount($subtotal);

        return response()->json([
            'zone' => [
                'id' => $zone->id,
                'name' => $zone->zone_name,
                'estimated_days' => $zone->estimated_days,
            ],
            'shipping_cost' => $shippingCost,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => round($subtotal + $shippingCost - $discount, 2),
            'total_weight_kg' => ceil($weight / 1000),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        try {
            $order = $this->orderService->createOrder(
                $request->validated(),
                auth()->id()
            );

            if ($order->payment_method === 'cod') {
                return redirect()->route('orders.show', $order)
                    ->with('success', __('messages.order_placed'));
            }

            if ($order->payment_method === 'bkash') {
                return redirect()->route('payment.bkash.initiate', $order);
            }

            return redirect()->route('payment.nagad.initiate', $order);
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['checkout' => $e->getMessage()]);
        }
    }
}
