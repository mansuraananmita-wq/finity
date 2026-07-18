<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }

        $sessionId = Session::getId();

        Session::put('guest_cart_session_id', $sessionId);

        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function getCartWithItems(): Cart
    {
        return $this->getOrCreateCart()->load(['items.product.category']);
    }

    public function addItem(int $productId, int $quantity): CartItem
    {
        $product = Product::findOrFail($productId);

        if ($product->stock_qty < $quantity) {
            throw new \RuntimeException(__('messages.insufficient_stock'));
        }

        $cart = $this->getOrCreateCart();
        $item = $cart->items()->where('product_id', $productId)->first();

        if ($item) {
            $newQty = $item->quantity + $quantity;
            if ($product->stock_qty < $newQty) {
                throw new \RuntimeException(__('messages.insufficient_stock'));
            }
            $item->update(['quantity' => $newQty]);
        } else {
            $item = $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return $item->load('product');
    }

    public function updateItem(CartItem $item, int $quantity): CartItem
    {
        if ($item->product->stock_qty < $quantity) {
            throw new \RuntimeException(__('messages.insufficient_stock'));
        }

        $item->update(['quantity' => $quantity]);

        return $item->load('product');
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    public function calculateSubtotal(Cart $cart): float
    {
        return round($cart->items->sum(function (CartItem $item) {
            return (float) $item->product->price * $item->quantity;
        }), 2);
    }

    public function calculateTotalWeightGrams(Cart $cart): int
    {
        return (int) $cart->items->sum(function (CartItem $item) {
            return $item->product->weight_grams * $item->quantity;
        });
    }

    public function getItemCount(Cart $cart): int
    {
        return (int) $cart->items->sum('quantity');
    }

    public function mergeGuestCartIntoUser(int $userId, ?string $sessionId): void
    {
        if (! $sessionId) {
            return;
        }

        $guestCart = Cart::where('session_id', $sessionId)->with('items.product')->first();
        if (! $guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $userId]);

        foreach ($guestCart->items as $guestItem) {
            $existing = $userCart->items()->where('product_id', $guestItem->product_id)->first();
            if ($existing) {
                $existing->update([
                    'quantity' => min(
                        $existing->quantity + $guestItem->quantity,
                        $guestItem->product->stock_qty
                    ),
                ]);
            } else {
                $userCart->items()->create([
                    'product_id' => $guestItem->product_id,
                    'quantity' => min($guestItem->quantity, $guestItem->product->stock_qty),
                ]);
            }
        }

        $guestCart->items()->delete();
        $guestCart->delete();
    }

    public function toArray(Cart $cart): array
    {
        $cart->loadMissing('items.product');

        return [
            'items' => $cart->items->map(fn (CartItem $item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'price' => (float) $item->product->price,
                'quantity' => $item->quantity,
                'line_total' => round((float) $item->product->price * $item->quantity, 2),
                'image_path' => $item->product->image_path,
                'stock_qty' => $item->product->stock_qty,
            ])->values(),
            'subtotal' => $this->calculateSubtotal($cart),
            'item_count' => $this->getItemCount($cart),
            'total_weight_grams' => $this->calculateTotalWeightGrams($cart),
        ];
    }
}
