<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Single-language storefront — no BN/EN switcher.
        App::setLocale(config('app.locale', 'en'));

        return $next($request);
    }
}
