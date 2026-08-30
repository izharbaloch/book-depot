<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = today();

        return view('admin.dashboard', [
            'todaySales'      => (float) Order::whereDate('created_at', $today)->where('status', '!=', 'cancelled')->sum('total')
                + (float) Sale::whereDate('created_at', $today)->sum('total'),
            'todayOrdersCount' => Order::whereDate('created_at', $today)->count() + Sale::whereDate('created_at', $today)->count(),
            'todayOnlineOrders' => Order::whereDate('created_at', $today)->count(),
            'todayPosSales'    => Sale::whereDate('created_at', $today)->count(),
            'totalProducts'    => Product::count(),
            'lowStockCount'    => Product::lowStock()->count(),
            'outOfStockCount'  => Product::outOfStock()->count(),
            'totalCustomers'   => Customer::count(),

            'recentOrders'   => Order::with('user')->recent(8)->get(),
            'recentSales'    => Sale::with(['customer', 'cashier'])->recent(8)->get(),
            'lowStockProducts' => Product::lowStock()->orWhere(fn($q) => $q->outOfStock())->orderBy('stock')->limit(8)->get(),

            'ordersByStatus' => Order::selectRaw('status, count(*) as count')
                ->groupBy('status')->pluck('count', 'status'),
            'topProducts'    => Product::withCount('orderItems')
                ->orderBy('order_items_count', 'desc')->limit(5)->get(),
        ]);
    }
}
