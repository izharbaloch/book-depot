@extends('layouts.admin')
@section('title', 'Order ' . $order->order_number)
@section('page_title', 'Order: ' . $order->order_number)

@section('content')
    <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">
        <div>
            {{-- Items --}}
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Order Items</h3>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Qty</th>
                            <th style="text-align:right">Price</th>
                            <th style="text-align:right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:.75rem">
                                        <div
                                            style="width:44px;height:55px;background:var(--bg-alt);flex-shrink:0;overflow:hidden">
                                            @if ($item->product_image)
                                                <img src="{{ $item->image_url }}" alt="{{ $item->product_name }}"
                                                    style="width:100%;height:100%;object-fit:cover" />
                                            @endif
                                        </div>
                                        <div>{{ $item->product_name }}</div>
                                    </div>
                                </td>
                                <td>{{ $item->product->sku ?? '—' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td style="text-align:right">${{ number_format($item->price, 2) }}</td>
                                <td style="text-align:right;font-weight:500">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align:right;padding-top:1rem;color:var(--text-muted)">Subtotal
                            </td>
                            <td style="text-align:right;padding-top:1rem">${{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align:right;color:var(--text-muted)">Shipping</td>
                            <td style="text-align:right">
                                {{ $order->shipping > 0 ? '$' . number_format($order->shipping, 2) : 'Free' }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align:right;color:var(--text-muted)">Tax</td>
                            <td style="text-align:right">${{ number_format($order->tax, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4"
                                style="text-align:right;font-weight:600;padding-top:.5rem;border-top:1px solid var(--border)">
                                Total</td>
                            <td
                                style="text-align:right;font-weight:600;padding-top:.5rem;border-top:1px solid var(--border)">
                                ${{ number_format($order->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Shipping Address --}}
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Shipping Address</h3>
                </div>
                <p style="font-size:.88rem;line-height:1.9;color:var(--text-muted)">
                    <strong style="color:var(--text)">{{ $order->full_name }}</strong><br />
                    {{ $order->address }}<br />
                    {{ $order->city }}, {{ $order->state }} {{ $order->zip_code }}<br />
                    {{ $order->country }}<br />
                    @if ($order->phone)
                        📞 {{ $order->phone }}
                    @endif
                    <br />
                    ✉️ {{ $order->email }}
                </p>
                @if ($order->notes)
                    <div
                        style="margin-top:1rem;padding:1rem;background:var(--bg-alt);font-size:.85rem;color:var(--text-muted)">
                        <strong>Notes:</strong> {{ $order->notes }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Summary + Status Update --}}
        <div>
            {{-- Status Update --}}
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Update Status</h3>
                </div>
                <div style="margin-bottom:1rem">
                    <span class="badge badge-{{ $order->status }}" style="font-size:.82rem;padding:.4rem .9rem">
                        Current: {{ ucfirst($order->status) }}
                    </span>
                </div>
                <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                    @csrf @method('PATCH')
                    <div class="form-group" style="margin-bottom:1rem">
                        <select name="status" class="filter-select">
                            @foreach (\App\Models\Order::STATUSES as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-primary btn-full">Update Status</button>
                </form>
            </div>

            {{-- Order Info --}}
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Order Info</h3>
                </div>
                <div class="summary-line"><span>Order #</span><strong>{{ $order->order_number }}</strong></div>
                <div class="summary-line"><span>Date</span><span>{{ $order->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="summary-line"><span>Payment</span><span
                        style="text-transform:uppercase">{{ $order->payment_method }}</span></div>
                <div class="summary-line"><span>Pay Status</span>
                    <span
                        style="color:{{ $order->payment_status === 'paid' ? 'var(--success)' : 'var(--warning)' }}">{{ ucfirst($order->payment_status) }}</span>
                </div>
                @if ($order->user)
                    <div class="summary-line"><span>Customer</span><a href="{{ route('admin.users.show', $order->user) }}"
                            style="color:var(--text);border-bottom:1px solid var(--border)">{{ $order->user->name }}</a>
                    </div>
                @endif
            </div>

            <a href="{{ route('admin.orders.index') }}" class="btn-ghost btn-full" style="text-align:center">← Back to
                Orders</a>
        </div>
    </div>
@endsection
