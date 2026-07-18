<?php

namespace App\Providers;

use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            $cartCount = 0;
            $wishlistCount = 0;

            try {
                $cartCount = app(CartService::class)->getItemCount(
                    app(CartService::class)->getOrCreateCart()
                );
            } catch (\Throwable) {
                // Ignore during console/early boot
            }

            if (Auth::check()) {
                $wishlistCount = Auth::user()->wishlist()->count();
            }

            $view->with(compact('cartCount', 'wishlistCount'));
        });
    }
}
