@extends('layouts.admin')
@section('title', 'Users')
@section('page_title', 'User Management')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">All Users ({{ $users->total() }})</h3>
        </div>
        <form method="GET" style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email…"
                style="padding:.6rem .85rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.85rem;flex:1;min-width:200px" />
            <select name="role"
                style="padding:.6rem .85rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.85rem">
                <option value="">All Roles</option>
                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Users</option>
                <option value="cashier" {{ request('role') === 'cashier' ? 'selected' : '' }}>Cashiers</option>
                <option value="inventory_staff" {{ request('role') === 'inventory_staff' ? 'selected' : '' }}>Inventory Staff</option>
                <option value="order_staff" {{ request('role') === 'order_staff' ? 'selected' : '' }}>Order Staff</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admins</option>
            </select>
            <button type="submit" class="btn-ghost" style="padding:.6rem 1.25rem;font-size:.72rem">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="btn-ghost"
                style="padding:.6rem 1.25rem;font-size:.72rem">Clear</a>
        </form>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Orders</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:.75rem">
                                <div
                                    style="width:38px;height:38px;border-radius:50%;background:var(--beige-mid);color:var(--grey-dark);display:flex;align-items:center;justify-content:center;font-family:var(--font-serif);flex-shrink:0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:500">{{ $user->name }}</div>
                                    <div style="font-size:.75rem;color:var(--text-muted)">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span
                                style="font-size:.72rem;padding:.2rem .55rem;border:1px solid;
                        border-color:{{ $user->hasRole('admin') ? 'var(--text)' : 'var(--border)' }};
                        color:{{ $user->hasRole('admin') ? 'var(--text)' : 'var(--text-muted)' }};
                        text-transform:uppercase">
                                {{ $user->roles->first()?->name ?? 'user' }}
                            </span>
                        </td>
                        <td style="color:var(--text-muted)">{{ $user->orders_count }}</td>
                        <td style="color:var(--text-muted)">{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div style="display:flex;gap:.5rem">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn-ghost"
                                    style="padding:.35rem .75rem;font-size:.7rem">View</a>
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="role"
                                            value="{{ $user->hasRole('admin') ? 'user' : 'admin' }}" />
                                        <button type="submit" class="btn-ghost"
                                            style="padding:.35rem .75rem;font-size:.7rem">
                                            {{ $user->hasRole('admin') ? 'Demote' : 'Make Admin' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:3rem;color:var(--text-muted)">No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:1.5rem">{{ $users->links() }}</div>
    </div>
@endsection
