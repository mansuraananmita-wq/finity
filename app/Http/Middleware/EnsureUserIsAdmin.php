<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->guest(route('login'));
        }

        if (! auth()->user()->isAdmin()) {
            return redirect()
                ->route('home')
                ->with('error', 'Admin access only. Please log out and sign in with the admin account.');
        }

        return $next($request);
    }
}
