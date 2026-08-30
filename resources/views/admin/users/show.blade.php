@extends('layouts.admin')
@section('title', 'User: ' . $user->name)
@section('page_title', 'User Profile')

@section('content')
    <div style="display:grid;grid-template-columns:300px 1fr;gap:1.5rem;align-items:start">

        {{-- Profile Card --}}
        <div>
            <div class="admin-card" style="text-align:center">
                <div
                    style="width:80px;height:80px;border-radius:50%;background:var(--beige-mid);color:var(--grey-dark);
                        font-size:2rem;font-family:var(--font-serif);display:flex;align-items:center;
                        justify-content:center;margin:0 auto 1.25rem">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h3 style="font-family:var(--font-serif);font-size:1.3rem;margin-bottom:.25rem">{{ $user->name }}</h3>
                <p style="font-size:.82rem;color:var(--text-muted);margin-bottom:1rem">{{ $user->email }}</p>
                <span
                    style="font-size:.72rem;padding:.3rem .75rem;border:1px solid;text-transform:uppercase;
                border-color:{{ $user->hasRole('admin') ? 'var(--text)' : 'var(--border)' }};
                color:{{ $user->hasRole('admin') ? 'var(--text)' : 'var(--text-muted)' }}">
                    {{ $user->roles->first()?->name ?? 'user' }}
                </span>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Details</h3>
                </div>
                <div class="summary-line"><span>Member Since</span><span>{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                <div class="summary-line"><span>Phone</span><span>{{ $user->phone ?? '—' }}</span></div>
                <div class="summary-line"><span>Total Orders</span><span>{{ $user->orders()->count() }}</span></div>
                <div class="summary-line"><span>Total
                        Spent</span><span>${{ number_format($user->orders()->where('status', 'delivered')->sum('total'), 2) }}</span>
                </div>
            </div>

            @if ($user->id !== auth()->id())
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Change Role</h3>
                    </div>
                    <form method="POST" action="{{ route('admin.users.role', $user) }}">
                        @csrf @method('PATCH')
                        <div class="form-group" style="margin-bottom:1rem">
                            <select name="role" class="filter-select">
                                <option value="user" {{ $user->hasRole('user') ? 'selected' : '' }}>User</option>
                                <option value="cashier" {{ $user->hasRole('cashier') ? 'selected' : '' }}>Cashier</option>
                                <option value="inventory_staff" {{ $user->hasRole('inventory_staff') ? 'selected' : '' }}>Inventory Staff</option>
                                <option value="order_staff" {{ $user->hasRole('order_staff') ? 'selected' : '' }}>Order Staff</option>
                                <option value="admin" {{ $user->hasRole('admin') ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary btn-full">Update Role</button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Orders --}}
        <div>
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Order History</h3>
                </div>
                @if ($orders->isEmpty())
                    <p style="text-align:center;padding:2rem;color:var(--text-muted)">No orders yet.</p>
                @else
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th style="text-align:right">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td style="font-weight:500">{{ $order->order_number }}</td>
                                    <td style="color:var(--text-muted)">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td style="color:var(--text-muted)">{{ $order->items->count() }}</td>
                                    <td><span
                                            class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td style="text-align:right;font-weight:500">${{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            style="font-size:.72rem;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);border-bottom:1px solid var(--border)">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div style="margin-top:1rem">
        <a href="{{ route('admin.users.index') }}" class="btn-ghost">← Back to Users</a>
    </div>
@endsection
