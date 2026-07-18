<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category');

        if ($categorySlug = $request->get('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        $search = trim((string) $request->get('search', ''));
        if ($search !== '') {
            $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $search).'%';
            $query->where(function ($q) use ($like) {
                $q->where('name_en', 'like', $like)
                    ->orWhere('name_bn', 'like', $like)
                    ->orWhere('scientific_name', 'like', $like)
                    ->orWhere('slug', 'like', $like)
                    ->orWhereHas('category', function ($cq) use ($like) {
                        $cq->where('name_en', 'like', $like)
                            ->orWhere('name_bn', 'like', $like);
                    });
            });
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'rating' => $query->orderByDesc('avg_rating'),
            'name' => $query->orderBy('name_en'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name_en')->get();

        $wishlistIds = auth()->check()
            ? auth()->user()->wishlist()->pluck('products.id')->all()
            : [];

        return view('products.index', compact('products', 'categories', 'sort', 'wishlistIds', 'search'));
    }

    public function show(Product $product): View
    {
        $product->load('category');

        $approvedReviews = $product->reviews()
            ->where('is_approved', 1)
            ->with('user')
            ->latest()
            ->get();

        // Keep card/header counts in sync if an approve missed a refresh
        if ((int) $product->review_count !== $approvedReviews->count()) {
            Product::updateRatingStats($product);
            $product->refresh();
        }

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $canReview = false;
        $hasReviewed = false;
        if (auth()->check()) {
            $hasReviewed = Review::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->exists();

            // Verified purchase: any non-cancelled order containing this product
            $canReview = ! $hasReviewed && Order::where('user_id', auth()->id())
                ->where('status', '!=', 'cancelled')
                ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
                ->exists();
        }

        $wishlistIds = auth()->check()
            ? auth()->user()->wishlist()->pluck('products.id')->all()
            : [];

        return view('products.show', compact(
            'product',
            'relatedProducts',
            'canReview',
            'hasReviewed',
            'wishlistIds',
            'approvedReviews'
        ));
    }
}
