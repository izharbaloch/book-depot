@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard Overview')

@section('content')

    {{-- ── STAT CARDS ── --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-card-icon">💰</div>
            <div class="stat-card-value">${{ number_format($todaySales, 0) }}</div>
            <div class="stat-card-label">Today's Sales</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">🧾</div>
            <div class="stat-card-value">{{ number_format($todayOrdersCount) }}</div>
            <div class="stat-card-label">Today's Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">🌐</div>
            <div class="stat-card-value">{{ number_format($todayOnlineOrders) }}</div>
            <div class="stat-card-label">Online Orders Today</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">🛒</div>
            <div class="stat-card-value">{{ number_format($todayPosSales) }}</div>
            <div class="stat-card-label">POS Sales Today</div>
        </div>
    </div>
    <div class="stat-grid" style="margin-bottom:2rem">
        <div class="stat-card">
            <div class="stat-card-icon">📦</div>
            <div class="stat-card-value">{{ number_format($totalProducts) }}</div>
            <div class="stat-card-label">Total Products</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">⚠️</div>
            <div class="stat-card-value">{{ number_format($lowStockCount) }}</div>
            <div class="stat-card-label">Low Stock</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">🚫</div>
            <div class="stat-card-value">{{ number_format($outOfStockCount) }}</div>
            <div class="stat-card-label">Out of Stock</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">👥</div>
            <div class="stat-card-value">{{ number_format($totalCustomers) }}</div>
            <div class="stat-card-label">Total Customers</div>
        </div>
    </div>

    <div class="split-layout">

        {{-- ── Recent Orders ── --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}"
                    class="text-link">View
                    All →</a>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th style="text-align:right">Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentOrders as $order)
                        <tr>
                            <td style="font-weight:500">{{ $order->order_number }}</td>
                            <td>
                                <div style="font-size:.85rem">{{ $order->full_name }}</div>
                                <div style="font-size:.75rem;color:var(--text-muted)">{{ $order->email }}</div>
                            </td>
                            <td style="color:var(--text-muted)">{{ $order->created_at->format('M d, Y') }}</td>
                            <td><span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                            <td style="text-align:right;font-weight:500">${{ number_format($order->total, 2) }}</td>
                            <td><a href="{{ route('admin.orders.show', $order) }}"
                                    class="text-link">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ── Right Column ── --}}
        <div>
            {{-- Orders by status --}}
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Orders by Status</h3>
                </div>
                @foreach (\App\Models\Order::STATUSES as $status)
                    @php
                        $count = $ordersByStatus[$status] ?? 0;
                        $max = $ordersByStatus->max() ?: 1;
                    @endphp
                    <div style="margin-bottom:1rem">
                        <div style="display:flex;justify-content:space-between;font-size:.78rem;margin-bottom:.4rem">
                            <span style="text-transform:capitalize">{{ $status }}</span>
                            <strong>{{ $count }}</strong>
                        </div>
                        <div style="height:4px;background:var(--border);border-radius:2px">
                            <div
                                style="height:100%;background:var(--text);border-radius:2px;width:{{ $max > 0 ? round(($count / $max) * 100) : 0 }}%;transition:width .5s">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Top Products --}}
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Top Products</h3>
                </div>
                @foreach ($topProducts as $i => $product)
                    <div
                        style="display:flex;align-items:center;gap:.75rem;padding:.6rem 0;{{ !$loop->last ? 'border-bottom:1px solid var(--border)' : '' }}">
                        <div
                            style="font-family:var(--font-serif);font-size:1.1rem;color:var(--text-muted);width:24px;flex-shrink:0">
                            {{ $i + 1 }}</div>
                        <div style="flex:1;min-width:0">
                            <div style="font-size:.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ $product->name }}</div>
                            <div style="font-size:.72rem;color:var(--text-muted)">{{ $product->order_items_count }} orders
                            </div>
                        </div>
                        <div style="font-size:.85rem;font-weight:500">${{ number_format($product->price, 0) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="split-layout" style="margin-top:1.5rem">
        {{-- ── Recent POS Sales ── --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Recent POS Sales</h3>
                <a href="{{ route('admin.sales.index') }}"
                    class="text-link">View
                    All →</a>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Sale #</th>
                        <th>Customer</th>
                        <th>Cashier</th>
                        <th>Date</th>
                        <th style="text-align:right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentSales as $sale)
                        <tr>
                            <td style="font-weight:500">{{ $sale->sale_number }}</td>
                            <td style="color:var(--text-muted)">{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td style="color:var(--text-muted)">{{ $sale->cashier->name ?? '—' }}</td>
                            <td style="color:var(--text-muted)">{{ $sale->created_at->format('M d, Y') }}</td>
                            <td style="text-align:right;font-weight:500">${{ number_format($sale->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text-muted)">No POS sales yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── Low / Out of Stock ── --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <h3 class="admin-card-title">Low Stock Products</h3>
                <a href="{{ route('admin.stock.index') }}"
                    class="text-link">View
                    All →</a>
            </div>
            @forelse ($lowStockProducts as $product)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:.6rem 0;{{ !$loop->last ? 'border-bottom:1px solid var(--border)' : '' }}">
                    <span style="font-size:.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $product->name }}</span>
                    <span style="font-size:.8rem;font-weight:500;color:{{ $product->stock <= 0 ? 'var(--danger)' : 'var(--warning)' }}">{{ $product->stock }} left</span>
                </div>
            @empty
                <p style="color:var(--text-muted);font-size:.85rem;padding:1rem 0">All products are well stocked.</p>
            @endforelse
        </div>
    </div>
@endsection
