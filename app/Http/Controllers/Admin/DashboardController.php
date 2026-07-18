<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        $stats = [
            'orders_today' => Order::where('created_at', '>=', $today)->count(),
            'orders_month' => Order::where('created_at', '>=', $monthStart)->count(),
            'revenue_today' => Order::where('created_at', '>=', $today)->where('payment_status', 'paid')->sum('total_amount'),
            'revenue_month' => Order::where('created_at', '>=', $monthStart)->where('payment_status', 'paid')->sum('total_amount'),
            'low_stock' => Product::where('stock_qty', '<', 5)->count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->limit(10)
            ->get();

        $lowStockProducts = Product::where('stock_qty', '<', 5)
            ->orderBy('stock_qty')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStockProducts'));
    }
}
