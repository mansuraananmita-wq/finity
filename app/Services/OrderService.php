<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    public function __construct(
        private CartService $cartService,
        private ShippingService $shippingService,
        private CouponService $couponService,
    ) {}

    public function createOrder(array $data, int $userId): Order
    {
        return DB::transaction(function () use ($data, $userId) {
            $cart = $this->cartService->getCartWithItems();

            if ($cart->items->isEmpty()) {
                throw new RuntimeException(__('messages.cart_empty'));
            }

            $zone = $this->shippingService->findZoneByDistrict($data['shipping_district']);
            if (! $zone) {
                throw new RuntimeException(__('messages.shipping_zone_not_found'));
            }

            foreach ($cart->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if (! $product || $product->stock_qty < $item->quantity) {
                    throw new RuntimeException(__('messages.insufficient_stock_for', [
                        'product' => $item->product->name,
                    ]));
                }
            }

            $subtotal = $this->cartService->calculateSubtotal($cart);
            $weight = $this->cartService->calculateTotalWeightGrams($cart);
            $shippingCost = $this->shippingService->calculateCost($zone, $weight);

            $coupon = null;
            $discount = 0;
            $appliedCoupon = $this->couponService->getAppliedCoupon();
            if ($appliedCoupon) {
                $coupon = Coupon::lockForUpdate()->find($appliedCoupon->id);
                if (! $coupon || ! $coupon->isValidForSubtotal($subtotal)) {
                    throw new RuntimeException(__('messages.coupon_not_applicable'));
                }
                $discount = $coupon->calculateDiscount($subtotal);
            }

            $total = round($subtotal + $shippingCost - $discount, 2);

            $order = Order::create([
                'user_id' => $userId,
                'shipping_zone_id' => $zone->id,
                'coupon_id' => $coupon?->id,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => $data['payment_method'] === 'cod' ? 'unpaid' : 'unpaid',
                'shipping_address' => $data['shipping_address'],
                'shipping_district' => $data['shipping_district'],
                'phone' => $data['phone'],
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                $product->decrement('stock_qty', $item->quantity);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price_at_purchase' => $product->price,
                ]);
            }

            if ($coupon) {
                $coupon->increment('used_count');
                $this->couponService->remove();
            }

            $cart->items()->delete();

            if ($data['payment_method'] === 'cod') {
                $order->update(['status' => 'processing']);
                $order->user->notify(new OrderPlacedNotification($order));
            }

            return $order->load(['items.product', 'shippingZone', 'coupon']);
        });
    }
}
