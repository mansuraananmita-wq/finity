<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Notifications\OrderStatusChangedNotification;
use App\Notifications\PaymentStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'shippingZone']);

        if ($status = $request->string('status')->trim()->toString()) {
            $query->where('status', $status);
        }

        if ($paymentStatus = $request->string('payment_status')->trim()->toString()) {
            $query->where('payment_status', $paymentStatus);
        }

        if ($from = $request->date('from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->date('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'shippingZone', 'coupon', 'items.product', 'paymentTransactions']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $previousStatus = $order->status;
        $newStatus = $request->validated('status');

        if ($previousStatus !== $newStatus) {
            $order->update(['status' => $newStatus]);

            if (in_array($newStatus, ['shipped', 'delivered'], true)) {
                $order->user->notify(new OrderStatusChangedNotification($order->fresh()));
            }
        }

        return back()->with('success', 'Order status updated.');
    }

    public function markPaid(Order $order): RedirectResponse
    {
        if ($order->payment_status === 'paid') {
            return back()->with('success', 'Order is already marked as paid.');
        }

        $order->update(['payment_status' => 'paid']);
        $order->user->notify(new PaymentStatusNotification($order->fresh(), true));

        return back()->with('success', 'Order marked as paid.');
    }
}
