@extends('layouts.app')
@section('title', 'My Account — Book Depot')

@section('content')
    <div class="page-hero-mini">
        <div class="container">
            <p class="breadcrumb"><a href="{{ route('home') }}">Home</a> / My Account</p>
            <h1>Hello, {{ explode(' ', $user->name)[0] }}</h1>
        </div>
    </div>

    <section style="padding:3rem 0 6rem">
        <div class="container">
            <div style="display:grid;grid-template-columns:240px 1fr;gap:3rem;align-items:start">

                {{-- ── Sidebar ── --}}
                <aside style="position:sticky;top:90px">
                    <div
                        style="border:1px solid var(--border);padding:1.5rem;background:var(--card-bg);margin-bottom:1.5rem;text-align:center">
                        <div
                            style="width:72px;height:72px;border-radius:50%;background:var(--beige-mid);color:var(--grey-dark);font-size:1.5rem;font-family:var(--font-serif);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div style="font-family:var(--font-serif);font-size:1.1rem">{{ $user->name }}</div>
                        <div style="font-size:.78rem;color:var(--text-muted);margin-top:.25rem">{{ $user->email }}</div>
                    </div>
                    <nav style="display:flex;flex-direction:column;gap:.25rem">
                        @foreach ([['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => '⊞'], ['route' => 'orders', 'label' => 'My Orders', 'icon' => '📦'], ['route' => 'wishlist', 'label' => 'Wishlist', 'icon' => '♡']] as $nav)
                            <a href="{{ route($nav['route']) }}"
                                style="display:flex;align-items:center;gap:.75rem;padding:.75rem 1rem;
                              border:1px solid {{ request()->routeIs($nav['route']) ? 'var(--text)' : 'var(--border)' }};
                              background:{{ request()->routeIs($nav['route']) ? 'var(--text)' : 'var(--card-bg)' }};
                              color:{{ request()->routeIs($nav['route']) ? 'var(--bg)' : 'var(--text-muted)' }};
                              font-size:.82rem;letter-spacing:.08em;text-transform:uppercase;transition:all .2s;margin-bottom:.25rem">
                                <span>{{ $nav['icon'] }}</span> {{ $nav['label'] }}
                            </a>
                        @endforeach
                        <form method="POST" action="{{ route('logout') }}" style="margin-top:.5rem">
                            @csrf
                            <button type="submit"
                                style="width:100%;display:flex;align-items:center;gap:.75rem;padding:.75rem 1rem;border:1px solid var(--border);background:var(--card-bg);color:var(--text-muted);font-size:.82rem;letter-spacing:.08em;text-transform:uppercase;cursor:pointer;font-family:inherit">
                                <span>→</span> Logout
                            </button>
                        </form>
                    </nav>
                </aside>

                {{-- ── Main Content ── --}}
                <div>
                    {{-- Stats --}}
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;margin-bottom:2.5rem">
                        @foreach ([['label' => 'Total Orders', 'value' => $user->orders()->count(), 'icon' => '📦'], ['label' => 'Delivered', 'value' => $user->orders()->where('status', 'delivered')->count(), 'icon' => '✅'], ['label' => 'Wishlist Items', 'value' => $user->wishlists()->count(), 'icon' => '♡']] as $stat)
                            <div
                                style="padding:1.75rem;border:1px solid var(--border);background:var(--card-bg);text-align:center">
                                <div style="font-size:1.75rem;margin-bottom:.5rem">{{ $stat['icon'] }}</div>
                                <div style="font-family:var(--font-serif);font-size:2rem;line-height:1">
                                    {{ $stat['value'] }}</div>
                                <div
                                    style="font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;color:var(--text-muted);margin-top:.35rem">
                                    {{ $stat['label'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Recent Orders --}}
                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem">
                            <h3 style="font-family:var(--font-serif);font-size:1.4rem">Recent Orders</h3>
                            <a href="{{ route('orders') }}" class="section-link">View All →</a>
                        </div>

                        @if ($orders->isEmpty())
                            <div
                                style="text-align:center;padding:4rem 2rem;border:1px solid var(--border);color:var(--text-muted)">
                                <p style="margin-bottom:1.5rem">You haven't placed any orders yet.</p>
                                <a href="{{ route('shop') }}" class="btn-primary">Start Shopping</a>
                            </div>
                        @else
                            <div style="border:1px solid var(--border)">
                                <table style="width:100%;border-collapse:collapse;font-size:.85rem">
                                    <thead>
                                        <tr style="background:var(--bg-alt);border-bottom:1px solid var(--border)">
                                            <th
                                                style="padding:.85rem 1rem;text-align:left;font-size:.68rem;letter-spacing:.15em;text-transform:uppercase">
                                                Order</th>
                                            <th
                                                style="padding:.85rem 1rem;text-align:left;font-size:.68rem;letter-spacing:.15em;text-transform:uppercase">
                                                Date</th>
                                            <th
                                                style="padding:.85rem 1rem;text-align:left;font-size:.68rem;letter-spacing:.15em;text-transform:uppercase">
                                                Status</th>
                                            <th
                                                style="padding:.85rem 1rem;text-align:right;font-size:.68rem;letter-spacing:.15em;text-transform:uppercase">
                                                Total</th>
                                            <th style="padding:.85rem 1rem"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $order)
                                            <tr style="border-bottom:1px solid var(--border)">
                                                <td style="padding:.85rem 1rem;font-weight:500">{{ $order->order_number }}
                                                </td>
                                                <td style="padding:.85rem 1rem;color:var(--text-muted)">
                                                    {{ $order->created_at->format('M d, Y') }}</td>
                                                <td style="padding:.85rem 1rem">
                                                    <span
                                                        style="display:inline-flex;align-items:center;gap:.3rem;font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;padding:.25rem .6rem;border:1px solid;
                                                border-color:{{ ['pending' => '#d4a840', 'processing' => '#2563eb', 'shipped' => '#7c3aed', 'delivered' => '#1a5c2c', 'cancelled' => '#8b1a1a'][$order->status] ?? 'var(--border)' }};
                                                color:{{ ['pending' => '#d4a840', 'processing' => '#2563eb', 'shipped' => '#7c3aed', 'delivered' => '#1a5c2c', 'cancelled' => '#8b1a1a'][$order->status] ?? 'var(--text-muted)' }}">
                                                        {{ $order->status_icon }} {{ ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                                <td style="padding:.85rem 1rem;text-align:right;font-weight:500">
                                                    ${{ number_format($order->total, 2) }}</td>
                                                <td style="padding:.85rem 1rem;text-align:right">
                                                    <a href="{{ route('orders.detail', $order) }}"
                                                        style="font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;border-bottom:1px solid var(--border);color:var(--text-muted)">View
                                                        →</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
