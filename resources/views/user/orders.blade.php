@extends('layouts.app')
@section('title', 'My Orders — Book Depot')

@section('content')
    <div class="page-hero-mini">
        <div class="container">
            <p class="breadcrumb"><a href="{{ route('home') }}">Home</a> / <a href="{{ route('dashboard') }}">Account</a>
                / Orders</p>
            <h1>My Orders</h1>
        </div>
    </div>

    <section style="padding:3rem 0 6rem">
        <div class="container">
            @if ($orders->isEmpty())
                <div style="text-align:center;padding:5rem 2rem;color:var(--text-muted)">
                    <p style="font-size:1.1rem;margin-bottom:1.5rem">You haven't placed any orders yet.</p>
                    <a href="{{ route('shop') }}" class="btn-primary">Browse Products</a>
                </div>
            @else
                <div style="border:1px solid var(--border)">
                    <table style="width:100%;border-collapse:collapse;font-size:.85rem">
                        <thead>
                            <tr style="background:var(--bg-alt);border-bottom:1px solid var(--border)">
                                <th
                                    style="padding:1rem 1.25rem;text-align:left;font-size:.68rem;letter-spacing:.15em;text-transform:uppercase">
                                    Order #</th>
                                <th
                                    style="padding:1rem 1.25rem;text-align:left;font-size:.68rem;letter-spacing:.15em;text-transform:uppercase">
                                    Date</th>
                                <th
                                    style="padding:1rem 1.25rem;text-align:left;font-size:.68rem;letter-spacing:.15em;text-transform:uppercase">
                                    Items</th>
                                <th
                                    style="padding:1rem 1.25rem;text-align:left;font-size:.68rem;letter-spacing:.15em;text-transform:uppercase">
                                    Status</th>
                                <th
                                    style="padding:1rem 1.25rem;text-align:left;font-size:.68rem;letter-spacing:.15em;text-transform:uppercase">
                                    Payment</th>
                                <th
                                    style="padding:1rem 1.25rem;text-align:right;font-size:.68rem;letter-spacing:.15em;text-transform:uppercase">
                                    Total</th>
                                <th style="padding:1rem 1.25rem"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr style="border-bottom:1px solid var(--border);transition:background .15s"
                                    onmouseover="this.style.background='var(--bg-alt)'"
                                    onmouseout="this.style.background=''">
                                    <td style="padding:1rem 1.25rem;font-weight:500">{{ $order->order_number }}</td>
                                    <td style="padding:1rem 1.25rem;color:var(--text-muted)">
                                        {{ $order->created_at->format('M d, Y') }}</td>
                                    <td style="padding:1rem 1.25rem;color:var(--text-muted)">{{ $order->items->count() }}
                                        item(s)</td>
                                    <td style="padding:1rem 1.25rem">
                                        <span
                                            style="font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;padding:.25rem .6rem;border:1px solid;
                                    border-color:{{ ['pending' => 'var(--warning)', 'processing' => '#2563eb', 'shipped' => '#7c3aed', 'delivered' => 'var(--success)', 'cancelled' => 'var(--danger)'][$order->status] ?? 'var(--border)' }};
                                    color:{{ ['pending' => 'var(--warning)', 'processing' => '#2563eb', 'shipped' => '#7c3aed', 'delivered' => 'var(--success)', 'cancelled' => 'var(--danger)'][$order->status] ?? 'var(--text-muted)' }}">
                                            {{ $order->status_icon }} {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td
                                        style="padding:1rem 1.25rem;color:var(--text-muted);text-transform:uppercase;font-size:.75rem">
                                        {{ $order->payment_method }}</td>
                                    <td style="padding:1rem 1.25rem;text-align:right;font-weight:500">
                                        ${{ number_format($order->total, 2) }}</td>
                                    <td style="padding:1rem 1.25rem;text-align:right">
                                        <a href="{{ route('orders.detail', $order) }}" class="btn-ghost"
                                            style="padding:.4rem .9rem;font-size:.7rem">Details</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="margin-top:2rem">{{ $orders->links() }}</div>
            @endif
        </div>
    </section>
@endsection
