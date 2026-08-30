<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('user')
            ->when($request->status,  fn($q, $s) => $q->where('status', $s))
            ->when($request->search,  fn($q, $s) =>
            $q->where('order_number', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%"))
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders'   => $orders,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', [
            'order' => $order->load('items.product', 'user'),
        ]);
    }

    public function updateStatus(Request $request, Order $order, InventoryService $inventory)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', Order::STATUSES),
        ]);

        $wasCancelled = $order->status === 'cancelled';
        $isNowCancelled = $request->status === 'cancelled';

        if ($isNowCancelled && !$wasCancelled) {
            foreach ($order->load('items')->items as $item) {
                if ($item->product) {
                    $inventory->adjust($item->product, $item->quantity, 'order_cancelled', $order);
                }
            }
        }

        $order->update(['status' => $request->status]);

        return back()->with('success', "Order status updated to {$request->status}.");
    }
}
