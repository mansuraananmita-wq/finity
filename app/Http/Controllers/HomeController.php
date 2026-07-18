<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::with('category')
            ->where('is_featured', true)
            ->inRandomOrder()
            ->limit(8)
            ->get();

        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::with('category')->latest()->limit(8)->get();
        }

        $wishlistIds = auth()->check()
            ? auth()->user()->wishlist()->pluck('products.id')->all()
            : [];

        return view('home', compact('featuredProducts', 'wishlistIds'));
    }
}
