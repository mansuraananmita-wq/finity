<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        $recentOrders = $user->orders()
            ->withCount('items')
            ->latest()
            ->limit(5)
            ->get();

        $wishlistItems = $user->wishlist()
            ->orderByDesc('wishlists.created_at')
            ->limit(4)
            ->get();

        $stats = [
            'orders' => $user->orders()->count(),
            'pending_orders' => $user->orders()->whereIn('status', ['pending', 'processing', 'shipped'])->count(),
            'wishlist' => $user->wishlist()->count(),
            'reviews' => $user->reviews()->count(),
        ];

        return view('profile.show', compact('user', 'recentOrders', 'wishlistItems', 'stats'));
    }

    public function update(Request $request, UpdateUserProfileInformation $updater): RedirectResponse
    {
        $updater->update($request->user(), $request->all());

        return back()->with('success', __('messages.profile_updated'));
    }

    public function updatePassword(Request $request, UpdateUserPassword $updater): RedirectResponse
    {
        $updater->update($request->user(), $request->all());

        return back()->with('success', __('messages.password_updated'));
    }
}
