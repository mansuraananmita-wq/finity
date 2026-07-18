<?php

namespace App\Listeners;

use App\Services\CartService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;

class MergeGuestCartOnLogin
{
    public function __construct(private CartService $cartService) {}

    public function handle(Login $event): void
    {
        $sessionId = Session::get('guest_cart_session_id', Session::getId());
        $this->cartService->mergeGuestCartIntoUser($event->user->id, $sessionId);
        Session::forget('guest_cart_session_id');
    }
}
