<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::getForUser()->load('items.product');
        if ($cart->items->isEmpty()) {
            return redirect()->route('shop')->with('info', 'Your cart is empty.');
        }
        return view('shop.checkout', compact('cart'));
    }

    public function placeOrder(Request $request, InventoryService $inventory)
    {
        $data = $request->validate([
            'first_name'     => 'required|string|max:60',
            'last_name'      => 'required|string|max:60',
            'email'          => 'required|email',
            'phone'          => 'nullable|string|max:20',
            'address'        => 'required|string|max:200',
            'city'           => 'required|string|max:80',
            'state'          => 'nullable|string|max:60',
            'zip_code'       => 'required|string|max:20',
            'country'        => 'required|string|max:60',
            'payment_method' => 'required|in:stripe,paypal,cod',
            'notes'          => 'nullable|string|max:500',
        ]);

        $cart = Cart::getForUser()->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('shop')->with('error', 'Your cart is empty.');
        }

        foreach ($cart->items as $item) {
            if ($item->quantity > $item->product->stock) {
                return redirect()->route('checkout')
                    ->with('error', "Only {$item->product->stock} of \"{$item->product->name}\" available. Please update your cart.");
            }
        }

        try {
            $order = DB::transaction(function () use ($data, $cart, $inventory) {
                $subtotal = $cart->subtotal;
                $shipping = $cart->shipping;
                $tax      = $cart->tax;

                $customer = Customer::findOrCreateByEmail(
                    "{$data['first_name']} {$data['last_name']}",
                    $data['email'],
                    $data['phone'] ?? null,
                    $data['address'] ?? null,
                );

                $order = Order::create([
                    ...$data,
                    'user_id'        => auth()->id(),
                    'customer_id'    => $customer->id,
                    'order_number'   => Order::generateOrderNumber(),
                    'status'         => 'pending',
                    'payment_status' => $data['payment_method'] === 'cod' ? 'unpaid' : 'unpaid',
                    'subtotal'       => $subtotal,
                    'shipping'       => $shipping,
                    'tax'            => $tax,
                    'total'          => $subtotal + $shipping + $tax,
                ]);

                foreach ($cart->items as $item) {
                    OrderItem::create([
                        'order_id'      => $order->id,
                        'product_id'    => $item->product_id,
                        'product_name'  => $item->product->name,
                        'product_image' => $item->product->image,
                        'quantity'      => $item->quantity,
                        'price'         => $item->price,
                        'cost_price'    => $item->product->cost_price,
                        'subtotal'      => $item->subtotal,
                    ]);

                    $inventory->adjust($item->product, -$item->quantity, 'online_sale', $order);
                }

                // Clear cart
                $cart->items()->delete();

                return $order;
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('checkout')->with('error', $e->getMessage());
        }

        return redirect()->route('order.success', $order)->with('success', 'Order placed!');
    }

    public function success(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        return view('shop.order-success', compact('order'));
    }
}
