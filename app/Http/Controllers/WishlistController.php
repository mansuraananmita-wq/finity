<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View|JsonResponse
    {
        $products = auth()->user()->wishlist()->with('category')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'products' => $products->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => (float) $p->price,
                    'slug' => $p->slug,
                    'image_path' => $p->image_path,
                ]),
            ]);
        }

        return view('wishlist.index', compact('products'));
    }

    public function add(Product $product): JsonResponse
    {
        auth()->user()->wishlist()->syncWithoutDetaching([$product->id]);

        return response()->json([
            'message' => __('messages.added_to_wishlist'),
            'in_wishlist' => true,
        ]);
    }

    public function remove(Product $product): JsonResponse
    {
        auth()->user()->wishlist()->detach($product->id);

        return response()->json([
            'message' => __('messages.removed_from_wishlist'),
            'in_wishlist' => false,
        ]);
    }

    public function toggle(Product $product): JsonResponse
    {
        $user = auth()->user();
        if ($user->wishlist()->where('product_id', $product->id)->exists()) {
            $user->wishlist()->detach($product->id);

            return response()->json([
                'message' => __('messages.removed_from_wishlist'),
                'in_wishlist' => false,
            ]);
        }

        $user->wishlist()->attach($product->id);

        return response()->json([
            'message' => __('messages.added_to_wishlist'),
            'in_wishlist' => true,
        ]);
    }
}
