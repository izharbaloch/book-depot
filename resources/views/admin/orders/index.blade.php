@extends('layouts.admin')
@section('title', 'Online Orders')
@section('page_title', 'Online Order Management')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">All Orders ({{ $orders->total() }})</h3>
        </div>

        {{-- Filters --}}
        <form method="GET" style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Order # or email…"
                style="padding:.6rem .85rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.85rem;flex:1;min-width:200px" />
            <select name="status"
                style="padding:.6rem .85rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.85rem">
                <option value="">All Statuses</option>
                @foreach ($statuses as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-ghost" style="padding:.6rem 1.25rem;font-size:.72rem">Filter</button>
            <a href="{{ route('admin.orders.index') }}" class="btn-ghost"
                style="padding:.6rem 1.25rem;font-size:.72rem">Clear</a>
        </form>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th style="text-align:right">Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td style="font-weight:500">{{ $order->order_number }}</td>
                        <td>
                            <div>{{ $order->full_name }}</div>
                            <div style="font-size:.75rem;color:var(--text-muted)">{{ $order->email }}</div>
                        </td>
                        <td style="color:var(--text-muted)">{{ $order->created_at->format('M d, Y H:i') }}</td>
                        <td style="color:var(--text-muted)">{{ $order->items->count() }}</td>
                        <td><span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                        <td style="text-transform:uppercase;font-size:.75rem;color:var(--text-muted)">
                            {{ $order->payment_method }}</td>
                        <td style="text-align:right;font-weight:500">${{ number_format($order->total, 2) }}</td>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="btn-ghost"
                                style="padding:.35rem .75rem;font-size:.7rem">View</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:3rem;color:var(--text-muted)">No orders found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:1.5rem">{{ $orders->links() }}</div>
    </div>
@endsection
