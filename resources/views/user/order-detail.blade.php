@extends('layouts.app')
@section('title', 'Order ' . $order->order_number . ' — Book Depot')

@section('content')
    <div class="page-hero-mini">
        <div class="container">
            <p class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> /
                <a href="{{ route('orders') }}">My Orders</a> /
                {{ $order->order_number }}
            </p>
            <h1>Order Details</h1>
        </div>
    </div>

    <section style="padding:3rem 0 6rem">
        <div class="container">
            <div style="display:grid;grid-template-columns:1fr 340px;gap:2.5rem;align-items:start">

                {{-- ── Items ── --}}
                <div>
                    {{-- Status Timeline --}}
                    <div style="padding:1.75rem;border:1px solid var(--border);background:var(--card-bg);margin-bottom:2rem">
                        <h3 style="font-family:var(--font-serif);font-size:1.2rem;margin-bottom:1.5rem">Order Status</h3>
                        <div style="display:flex;align-items:center;gap:0">
                            @foreach (['pending', 'processing', 'shipped', 'delivered'] as $step)
                                @php $active = in_array($step, ['pending','processing','shipped','delivered']) && array_search($step, ['pending','processing','shipped','delivered']) <= array_search($order->status, ['pending','processing','shipped','delivered','cancelled']); @endphp
                                <div style="flex:1;text-align:center">
                                    <div
                                        style="width:36px;height:36px;border-radius:50%;margin:0 auto .5rem;display:flex;align-items:center;justify-content:center;font-size:.8rem;
                                background:{{ $active ? 'var(--text)' : 'var(--bg-alt)' }};
                                color:{{ $active ? 'var(--bg)' : 'var(--text-muted)' }};
                                border:1px solid {{ $active ? 'var(--text)' : 'var(--border)' }}">
                                        {{ ['pending' => '1', 'processing' => '2', 'shipped' => '3', 'delivered' => '4'][$step] }}
                                    </div>
                                    <div
                                        style="font-size:.65rem;letter-spacing:.1em;text-transform:uppercase;
                                color:{{ $active ? 'var(--text)' : 'var(--text-muted)' }}">
                                        {{ ucfirst($step) }}
                                    </div>
                                </div>
                                @if (!$loop->last)
                                    <div
                                        style="flex:1;height:1px;background:{{ $active ? 'var(--text)' : 'var(--border)' }};margin-bottom:1.5rem">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @if ($order->status === 'cancelled')
                            <p style="text-align:center;color:#8b1a1a;font-size:.85rem;margin-top:1rem">This order was
                                cancelled.</p>
                        @endif
                    </div>

                    {{-- Items list --}}
                    <h3 style="font-family:var(--font-serif);font-size:1.3rem;margin-bottom:1.25rem">Items Ordered</h3>
                    <div style="border:1px solid var(--border)">
                        @foreach ($order->items as $item)
                            <div
                                style="display:flex;gap:1.25rem;padding:1.25rem;border-bottom:1px solid var(--border);align-items:flex-start">
                                <div style="width:80px;height:100px;flex-shrink:0;background:var(--bg-alt);overflow:hidden">
                                    @if ($item->product_image)
                                        <img src="{{ $item->image_url }}" alt="{{ $item->product_name }}"
                                            style="width:100%;height:100%;object-fit:cover" />
                                    @endif
                                </div>
                                <div style="flex:1">
                                    <div style="font-family:var(--font-serif);font-size:1rem">{{ $item->product_name }}
                                    </div>
                                    <div style="font-size:.82rem;color:var(--text-muted);margin-top:.35rem">Qty:
                                        {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</div>
                                </div>
                                <div style="font-weight:500;font-size:.95rem">${{ number_format($item->subtotal, 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ── Summary ── --}}
                <div style="position:sticky;top:90px">
                    <div
                        style="border:1px solid var(--border);padding:1.75rem;background:var(--card-bg);margin-bottom:1.25rem">
                        <h3
                            style="font-family:var(--font-serif);font-size:1.3rem;margin-bottom:1.25rem;padding-bottom:1rem;border-bottom:1px solid var(--border)">
                            Order Summary</h3>
                        <div class="summary-line"><span>Order Number</span><span
                                style="font-weight:500">{{ $order->order_number }}</span></div>
                        <div class="summary-line"><span>Date</span><span>{{ $order->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="summary-line"><span>Payment</span><span
                                style="text-transform:uppercase">{{ $order->payment_method }}</span></div>
                        <div class="summary-line">
                            <span>Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span></div>
                        <div class="summary-line">
                            <span>Shipping</span><span>{{ $order->shipping > 0 ? '$' . number_format($order->shipping, 2) : 'Free' }}</span>
                        </div>
                        <div class="summary-line"><span>Tax</span><span>${{ number_format($order->tax, 2) }}</span></div>
                        <div class="summary-total"><span>Total</span><span>${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>

                    <div style="border:1px solid var(--border);padding:1.75rem;background:var(--card-bg)">
                        <h3 style="font-family:var(--font-serif);font-size:1.1rem;margin-bottom:1rem">Shipping Address</h3>
                        <p style="font-size:.88rem;color:var(--text-muted);line-height:1.8">
                            {{ $order->full_name }}<br />
                            {{ $order->address }}<br />
                            {{ $order->city }}, {{ $order->state }} {{ $order->zip_code }}<br />
                            {{ $order->country }}<br />
                            @if ($order->phone)
                                {{ $order->phone }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div style="margin-top:2rem">
                <a href="{{ route('orders') }}" class="btn-ghost">← Back to Orders</a>
            </div>
        </div>
    </section>
@endsection
