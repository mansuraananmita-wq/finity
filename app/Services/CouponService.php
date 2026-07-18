<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Facades\Session;

class CouponService
{
    public const SESSION_KEY = 'applied_coupon_code';

    public function apply(string $code, float $subtotal): array
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (! $coupon) {
            throw new \RuntimeException(__('messages.coupon_invalid'));
        }

        if (! $coupon->isValidForSubtotal($subtotal)) {
            throw new \RuntimeException(__('messages.coupon_not_applicable'));
        }

        $discount = $coupon->calculateDiscount($subtotal);
        Session::put(self::SESSION_KEY, $coupon->code);

        return [
            'code' => $coupon->code,
            'discount_amount' => $discount,
            'type' => $coupon->type,
            'value' => (float) $coupon->value,
        ];
    }

    public function remove(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function getAppliedCoupon(): ?Coupon
    {
        $code = Session::get(self::SESSION_KEY);
        if (! $code) {
            return null;
        }

        return Coupon::where('code', $code)->first();
    }

    public function getDiscountAmount(float $subtotal): float
    {
        $coupon = $this->getAppliedCoupon();
        if (! $coupon || ! $coupon->isValidForSubtotal($subtotal)) {
            Session::forget(self::SESSION_KEY);

            return 0;
        }

        return $coupon->calculateDiscount($subtotal);
    }
}
