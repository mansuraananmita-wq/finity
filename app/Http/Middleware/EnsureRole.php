<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! auth()->check()) {
            return redirect()->guest(route('login'));
        }

        if (auth()->user()->role !== $role) {
            return redirect()
                ->route('home')
                ->with('error', 'Admin access only. Please log out and sign in with the admin account.');
        }

        return $next($request);
    }
}
