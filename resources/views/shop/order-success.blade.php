@extends('layouts.app')
@section('title', 'Order Confirmed — Book Depot')

@section('content')
    <section style="padding:8rem 0 5rem">
        <div class="container">
            <div class="order-success">
                <div class="success-icon">✓</div>
                <h2>Order Confirmed!</h2>
                <p>Thank you for shopping with Book Depot.</p>
                <p>Your order <strong>{{ $order->order_number }}</strong> has been placed successfully.</p>
                <p style="opacity:.6;font-size:.9rem;margin-top:.5rem">
                    A confirmation has been recorded. Your order total was
                    <strong>${{ number_format($order->total, 2) }}</strong>.
                </p>
                <div class="pdp-meta" style="max-width:500px;margin:2rem auto">
                    <div class="pdp-meta-row"><strong>Order #</strong><span>{{ $order->order_number }}</span></div>
                    <div class="pdp-meta-row"><strong>Status</strong><span>{{ ucfirst($order->status) }}</span></div>
                    <div class="pdp-meta-row"><strong>Payment</strong><span>{{ strtoupper($order->payment_method) }}</span>
                    </div>
                    <div class="pdp-meta-row"><strong>Ship to</strong><span>{{ $order->address }}, {{ $order->city }},
                            {{ $order->country }}</span></div>
                </div>
                <div style="display:flex;gap:1rem;margin-top:2rem;justify-content:center;flex-wrap:wrap">
                    <a href="{{ route('orders.detail', $order) }}" class="btn-primary">View Order</a>
                    <a href="{{ route('shop') }}" class="btn-ghost">Continue Shopping</a>
                </div>
            </div>
        </div>
    </section>
@endsection
