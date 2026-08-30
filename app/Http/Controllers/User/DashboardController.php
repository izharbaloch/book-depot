<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $user   = auth()->user();
        $orders = Order::where('user_id', $user->id)->recent(5)->get();
        return view('user.dashboard', compact('user', 'orders'));
    }

    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('user.orders', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        return view('user.order-detail', ['order' => $order->load('items')]);
    }
}
