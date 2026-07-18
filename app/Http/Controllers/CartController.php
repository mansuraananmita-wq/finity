<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartAddRequest;
use App\Http\Requests\CartUpdateRequest;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function view(): View|JsonResponse
    {
        $cart = $this->cartService->getCartWithItems();
        $data = $this->cartService->toArray($cart);

        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return view('cart.index', ['cartData' => $data]);
    }

    public function add(CartAddRequest $request): JsonResponse
    {
        try {
            $this->cartService->addItem(
                (int) $request->validated('product_id'),
                (int) $request->validated('quantity')
            );
            $cart = $this->cartService->getCartWithItems();

            return response()->json([
                'message' => __('messages.added_to_cart'),
                'cart' => $this->cartService->toArray($cart),
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function update(CartUpdateRequest $request, CartItem $cartItem): JsonResponse
    {
        $this->authorizeCartItem($cartItem);

        try {
            if ($request->validated('quantity') < 1) {
                $cartItem->delete();
            } else {
                $this->cartService->updateItem($cartItem, (int) $request->validated('quantity'));
            }

            $cart = $this->cartService->getCartWithItems();

            return response()->json([
                'message' => __('messages.cart_updated'),
                'cart' => $this->cartService->toArray($cart),
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function remove(CartItem $cartItem): JsonResponse
    {
        $this->authorizeCartItem($cartItem);
        $this->cartService->removeItem($cartItem);
        $cart = $this->cartService->getCartWithItems();

        return response()->json([
            'message' => __('messages.removed_from_cart'),
            'cart' => $this->cartService->toArray($cart),
        ]);
    }

    private function authorizeCartItem(CartItem $cartItem): void
    {
        $cart = $this->cartService->getOrCreateCart();
        if ($cartItem->cart_id !== $cart->id) {
            abort(403);
        }
    }
}
