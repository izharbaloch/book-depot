<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Admin') — Book Depot</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        /* Admin-specific layout */
        .admin-layout {
            display: flex;
            min-height: 100vh;
            background: var(--bg-alt);
        }

        .admin-sidebar {
            width: 260px;
            flex-shrink: 0;
            background: var(--brand);
            border-right: 1px solid var(--brand-dark);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .admin-sidebar-logo {
            padding: 1.75rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            font-family: var(--font-serif);
            font-size: 1.3rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.01em;
        }

        .admin-sidebar-logo span {
            display: block;
            font-family: var(--font-sans);
            font-size: .68rem;
            font-weight: 500;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.55);
            margin-top: .3rem;
        }

        .admin-nav {
            padding: 1rem 0;
            flex: 1;
        }

        .admin-nav-section {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.4);
            padding: .9rem 1.5rem .35rem;
        }

        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .65rem 1.5rem;
            font-size: .85rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.75);
            transition: all .15s;
            border-left: 3px solid transparent;
        }

        .admin-nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.06);
        }

        .admin-nav-link.active {
            color: #fff;
            font-weight: 600;
            border-left-color: var(--accent);
            background: rgba(255, 255, 255, 0.1);
        }

        .admin-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .admin-topbar {
            height: 60px;
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .admin-content {
            padding: 2rem;
            flex: 1;
        }

        .admin-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1.75rem;
            margin-bottom: 1.5rem;
        }

        .admin-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .admin-card-title {
            font-family: var(--font-serif);
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--brand);
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: .85rem;
        }

        .admin-table th {
            padding: .75rem 1rem;
            text-align: left;
            font-size: .7rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--brand);
            border-bottom: 2px solid var(--border);
            background: var(--bg-alt);
            font-weight: 700;
        }

        .admin-table td {
            padding: .9rem 1rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .admin-table tr:hover td {
            background: var(--bg-alt);
        }

        .admin-table tr:last-child td {
            border-bottom: none;
        }

        .purchase-item-result:hover {
            background: var(--bg-alt);
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .stat-card-value {
            font-family: var(--font-serif);
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--brand);
            line-height: 1;
        }

        .stat-card-label {
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .stat-card-icon {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            border-radius: var(--radius);
            background: var(--brand-light);
            margin-bottom: .5rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: .3rem .7rem;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .03em;
            border-radius: 999px;
        }

        .badge-pending {
            color: #92670e;
            background: #92670e1a;
        }

        .badge-processing {
            color: #2563eb;
            background: #2563eb1a;
        }

        .badge-shipped {
            color: #7c3aed;
            background: #7c3aed1a;
        }

        .badge-delivered {
            color: #1a7a3d;
            background: #1a7a3d1a;
        }

        .badge-cancelled {
            color: #b3382c;
            background: #b3382c1a;
        }

        .admin-form-section {
            margin-bottom: 2rem;
        }

        .admin-form-section h4 {
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .04em;
            color: var(--brand);
            margin-bottom: 1rem;
            padding-bottom: .75rem;
            border-bottom: 1px solid var(--border);
        }
    </style>
</head>

<body>
    <div class="admin-layout">

        {{-- ── SIDEBAR ── --}}
        <aside class="admin-sidebar">
            <div class="admin-sidebar-logo">
                Book Depot <span>Management System</span>
            </div>
            <nav class="admin-nav">
                @can('manage reports')
                    <a href="{{ route('admin.dashboard') }}"
                        class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" />
                            <rect x="14" y="3" width="7" height="7" />
                            <rect x="14" y="14" width="7" height="7" />
                            <rect x="3" y="14" width="7" height="7" />
                        </svg>
                        Dashboard
                    </a>
                @endcan

                @canany(['manage products', 'manage suppliers'])
                    <div class="admin-nav-section">Store</div>
                    @can('manage products')
                        <a href="{{ route('admin.products.index') }}"
                            class="admin-nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z" />
                                <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" />
                            </svg>
                            Products
                        </a>
                        <a href="{{ route('admin.categories.index') }}"
                            class="admin-nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z" />
                            </svg>
                            Categories
                        </a>
                        <a href="{{ route('admin.authors.index') }}"
                            class="admin-nav-link {{ request()->routeIs('admin.authors*') ? 'active' : '' }}">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <circle cx="12" cy="8" r="4" />
                                <path d="M4 21v-1a8 8 0 0116 0v1" />
                            </svg>
                            Authors
                        </a>
                        <a href="{{ route('admin.publishers.index') }}"
                            class="admin-nav-link {{ request()->routeIs('admin.publishers*') ? 'active' : '' }}">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path d="M4 19.5A2.5 2.5 0 016.5 17H20" />
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z" />
                            </svg>
                            Publishers
                        </a>
                    @endcan
                    @can('manage suppliers')
                        <a href="{{ route('admin.suppliers.index') }}"
                            class="admin-nav-link {{ request()->routeIs('admin.suppliers*') ? 'active' : '' }}">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <rect x="1" y="3" width="15" height="13" />
                                <path d="M16 8h4l3 3v5h-7V8z" />
                            </svg>
                            Suppliers
                        </a>
                    @endcan
                @endcanany

                @canany(['access pos', 'manage sales', 'manage orders', 'manage customers'])
                    <div class="admin-nav-section">Sales</div>
                    @can('access pos')
                        <a href="{{ route('admin.pos.index') }}"
                            class="admin-nav-link {{ request()->routeIs('admin.pos*') ? 'active' : '' }}">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <rect x="2" y="7" width="20" height="14" rx="2" />
                                <path d="M16 3H8a2 2 0 00-2 2v2h12V5a2 2 0 00-2-2z" />
                                <circle cx="12" cy="14" r="2" />
                            </svg>
                            POS
                        </a>
                    @endcan
                    @can('manage sales')
                        <a href="{{ route('admin.sales.index') }}"
                            class="admin-nav-link {{ request()->routeIs('admin.sales*') ? 'active' : '' }}">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                            </svg>
                            Sales
                        </a>
                    @endcan
                    @can('manage orders')
                        <a href="{{ route('admin.orders.index') }}"
                            class="admin-nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path d="M9 11l3 3L22 4" />
                                <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
                            </svg>
                            Online Orders
                        </a>
                    @endcan
                    @can('manage customers')
                        <a href="{{ route('admin.customers.index') }}"
                            class="admin-nav-link {{ request()->routeIs('admin.customers*') ? 'active' : '' }}">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                            </svg>
                            Customers
                        </a>
                    @endcan
                @endcanany

                @canany(['manage stock', 'manage purchases'])
                    <div class="admin-nav-section">Inventory</div>
                    @can('manage stock')
                        <a href="{{ route('admin.stock.index') }}"
                            class="admin-nav-link {{ request()->routeIs('admin.stock*') ? 'active' : '' }}">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path d="M20.5 7.3L12 12l-8.5-4.7M12 22V12" />
                                <path d="M20.5 7.3L12 2 3.5 7.3l8.5 4.7 8.5-4.7z" />
                            </svg>
                            Stock
                        </a>
                    @endcan
                    @can('manage purchases')
                        <a href="{{ route('admin.purchases.index') }}"
                            class="admin-nav-link {{ request()->routeIs('admin.purchases*') ? 'active' : '' }}">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path d="M3 3h2l2.4 12.4A2 2 0 009.36 17H19a2 2 0 002-1.6L23 6H6" />
                            </svg>
                            Purchases
                        </a>
                    @endcan
                @endcanany

                @can('manage reports')
                    <div class="admin-nav-section">Reports</div>
                    <a href="{{ route('admin.reports.index') }}"
                        class="admin-nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path d="M18 20V10M12 20V4M6 20v-6" />
                        </svg>
                        Reports
                    </a>
                @endcan

                @can('manage users')
                    <div class="admin-nav-section">Admin</div>
                    <a href="{{ route('admin.users.index') }}"
                        class="admin-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="4" />
                            <path d="M4 21v-1a8 8 0 0116 0v1" />
                        </svg>
                        Staff Users
                    </a>
                @endcan
            </nav>
            <div style="padding:1.25rem 1.5rem;border-top:1px solid rgba(255,255,255,0.12)">
                <a href="{{ route('home') }}"
                    style="font-size:.75rem;color:rgba(255,255,255,0.6);display:flex;align-items:center;gap:.5rem">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        <polyline points="9 22 9 12 15 12 15 22" />
                    </svg>
                    ← Back to Store
                </a>
            </div>
        </aside>

        {{-- ── MAIN ── --}}
        <main class="admin-main">
            {{-- Topbar --}}
            <div class="admin-topbar">
                <h2 style="font-family:var(--font-serif);font-size:1.1rem;font-weight:700;color:var(--brand)">@yield('page_title', 'Dashboard')</h2>
                <div style="display:flex;align-items:center;gap:1.25rem">
                    <span style="font-size:.82rem;color:var(--text-muted)">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            style="font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;color:var(--text-muted);transition:color .2s"
                            onmouseover="this.style.color='var(--text)'"
                            onmouseout="this.style.color='var(--text-muted)'">Logout</button>
                    </form>
                </div>
            </div>

            {{-- Flash messages --}}
            <div style="padding:0 2rem">
                @if (session('success'))
                    <div
                        style="background:#1a7a3d14;border:1px solid #1a7a3d;border-radius:8px;padding:.85rem 1.25rem;margin-top:1.25rem;font-size:.85rem;font-weight:500;color:#1a7a3d;display:flex;align-items:center;justify-content:space-between">
                        {{ session('success') }}
                        <button onclick="this.parentElement.remove()" style="font-size:1rem;color:#1a7a3d">✕</button>
                    </div>
                @endif
                @if (session('error'))
                    <div
                        style="background:#b3382c14;border:1px solid #b3382c;border-radius:8px;padding:.85rem 1.25rem;margin-top:1.25rem;font-size:.85rem;font-weight:500;color:#b3382c;display:flex;align-items:center;justify-content:space-between">
                        {{ session('error') }}
                        <button onclick="this.parentElement.remove()" style="font-size:1rem;color:#b3382c">✕</button>
                    </div>
                @endif
            </div>

            <div class="admin-content">
                @yield('content')
            </div>
        </main>
    </div>
    @livewireScripts
    @stack('scripts')
</body>

</html>
