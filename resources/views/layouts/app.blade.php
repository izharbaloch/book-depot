<!DOCTYPE html>
<html lang="en" data-theme="light" x-data="{ theme: localStorage.getItem('bookDepotTheme') || 'light' }" x-init="$el.setAttribute('data-theme', theme)" :data-theme="theme">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="@yield('meta_desc', 'Book Depot — books, textbooks, and stationery in store and online.')" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Book Depot')</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet" />

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire Styles --}}
    @livewireStyles

    @stack('styles')
</head>

<body>

    {{-- ===== TOAST NOTIFICATION ===== --}}
    <div id="toast" class="toast" aria-live="polite"></div>

    {{-- ===== LIVEWIRE CART DRAWER ===== --}}
    @livewire('cart-drawer')

    {{-- ===== QUICK VIEW MODAL ===== --}}
    <div id="quickViewModal" class="modal-overlay" onclick="closeQuickView(event)">
        <div class="modal-box" id="quickViewBox">
            <button class="modal-close" onclick="closeQuickView()" aria-label="Close">✕</button>
            <div id="quickViewContent"></div>
        </div>
    </div>

    {{-- ===== TOP UTILITY BAR ===== --}}
    <div class="top-bar">
        <div class="top-bar-inner">
            <div class="top-bar-links">
                <span>📞 Customer Support: (042) 111-222-333</span>
                <span>🚚 Free Delivery on Orders Over $150</span>
            </div>
            <div class="top-bar-links">
                @guest
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                @else
                    <a href="{{ route('orders') }}">Track Order</a>
                @endguest
            </div>
        </div>
    </div>

    {{-- ===== NAVBAR ===== --}}
    <header id="navbar" class="navbar" x-data="{ menuOpen: false }">
        <div class="nav-inner">
            <button class="nav-hamburger" :class="{ open: menuOpen }" @click="menuOpen = !menuOpen" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>

            <a href="{{ route('home') }}" class="nav-logo">
                <svg class="nav-logo-icon" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.75"
                    viewBox="0 0 24 24">
                    <path d="M4 19.5A2.5 2.5 0 016.5 17H20" />
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z" />
                </svg>
                Book Depot
            </a>

            <form class="nav-search" method="GET" action="{{ route('shop') }}">
                <div class="nav-search-input-wrap">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search Books, Stationery & School Supplies..." aria-label="Search products" />
                    <button type="submit" class="nav-search-btn" aria-label="Search">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="7" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </button>
                </div>
            </form>

            <nav class="nav-links" :class="{ open: menuOpen }">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                    @click="menuOpen = false">Home</a>
                <a href="{{ route('shop') }}" class="nav-link {{ request()->routeIs('shop*') ? 'active' : '' }}"
                    @click="menuOpen = false">Shop</a>

                <div class="nav-dropdown">
                    <a href="#" class="nav-link">Categories ▾</a>
                    <div class="dropdown-menu">
                        <div class="dropdown-inner">
                            @foreach (\App\Models\Category::active()->get() as $cat)
                                <a href="{{ route('shop', ['category' => $cat->slug]) }}">{{ $cat->name }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <a href="{{ route('shop', ['sort' => 'newest']) }}" class="nav-link" @click="menuOpen = false">New
                    Arrivals</a>
                <a href="{{ route('shop', ['bestseller' => 1]) }}" class="nav-link" @click="menuOpen = false">Best
                    Sellers</a>
                <a href="{{ route('shop', ['sale' => 1]) }}" class="nav-link" @click="menuOpen = false">Offers</a>
            </nav>

            <div class="nav-actions">
                {{-- Theme Toggle --}}
                <button class="nav-icon"
                    @click="theme = theme === 'dark' ? 'light' : 'dark'; localStorage.setItem('bookDepotTheme', theme)"
                    aria-label="Toggle theme">
                    <svg x-show="theme === 'light'" width="20" height="20" fill="none" stroke="currentColor"
                        stroke-width="1.5" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="5" />
                        <line x1="12" y1="1" x2="12" y2="3" />
                        <line x1="12" y1="21" x2="12" y2="23" />
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                        <line x1="1" y1="12" x2="3" y2="12" />
                        <line x1="21" y1="12" x2="23" y2="12" />
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
                    </svg>
                    <svg x-show="theme === 'dark'" width="20" height="20" fill="none"
                        stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                    </svg>
                </button>

                {{-- Wishlist --}}
                @auth
                    <a href="{{ route('wishlist') }}" class="nav-icon" aria-label="Wishlist">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path
                                d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                        </svg>
                    </a>
                @endauth

                {{-- Cart --}}
                <button class="nav-icon" onclick="openCart()" aria-label="Cart">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5"
                        viewBox="0 0 24 24">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <path d="M16 10a4 4 0 01-8 0" />
                    </svg>
                    <span id="navCartBadge" class="nav-badge"
                        style="{{ session('cart_count', 0) > 0 ? '' : 'display:none' }}">
                        {{ session('cart_count', 0) }}
                    </span>
                </button>

                {{-- Auth --}}
                @guest
                    <a href="{{ route('login') }}" class="btn-ghost"
                        style="padding:.5rem 1rem;font-size:.72rem">Login</a>
                @else
                    <div class="nav-dropdown">
                        <button class="nav-icon" style="width:auto;padding:0 .5rem;gap:.4rem;font-size:.78rem">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                            {{ Auth::user()->name }}
                        </button>
                        <div class="dropdown-menu" style="right:0;left:auto">
                            <div class="dropdown-inner">
                                @if (Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}">Admin Panel</a>
                                @elseif (Auth::user()->hasRole('cashier'))
                                    <a href="{{ route('admin.pos.index') }}">POS</a>
                                @elseif (Auth::user()->hasRole('inventory_staff'))
                                    <a href="{{ route('admin.products.index') }}">Inventory Panel</a>
                                @elseif (Auth::user()->hasRole('order_staff'))
                                    <a href="{{ route('admin.orders.index') }}">Orders Panel</a>
                                @endif
                                <a href="{{ route('dashboard') }}">My Account</a>
                                <a href="{{ route('orders') }}">My Orders</a>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                            </div>
                        </div>
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf
                    </form>
                @endguest
            </div>
        </div>
    </header>

    {{-- ===== PAGE CONTENT ===== --}}
    @yield('content')

    {{-- ===== FOOTER ===== --}}
    @unless (request()->routeIs('admin.*'))
        <footer class="footer" id="about">
            <div class="footer-top">
                <div class="footer-brand">
                    <a href="{{ route('home') }}" class="footer-logo">Book Depot</a>
                    <p>Your neighborhood source for books,<br />textbooks, and stationery.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Instagram" class="social-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <rect x="2" y="2" width="20" height="20" rx="5" />
                                <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z" />
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                            </svg>
                        </a>
                        <a href="#" aria-label="Twitter" class="social-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path
                                    d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z" />
                            </svg>
                        </a>
                        <a href="#" aria-label="TikTok" class="social-icon">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="footer-links">
                    <div class="footer-col">
                        <h4>Shop</h4>
                        @foreach (\App\Models\Category::active()->get() as $cat)
                            <a href="{{ route('shop', ['category' => $cat->slug]) }}">{{ $cat->name }}</a>
                        @endforeach
                        <a href="{{ route('shop', ['sale' => 1]) }}">Sale</a>
                    </div>
                    <div class="footer-col">
                        <h4>Company</h4>
                        <a href="#">About Us</a>
                        <a href="#">Our Story</a>
                        <a href="#">Sustainability</a>
                        <a href="#">Press</a>
                        <a href="#">Careers</a>
                    </div>
                    <div class="footer-col">
                        <h4>Support</h4>
                        <a href="#">Contact Us</a>
                        <a href="#">Shipping Policy</a>
                        <a href="#">Returns & Exchanges</a>
                        <a href="#">Bulk / Institutional Orders</a>
                        <a href="#">FAQ</a>
                    </div>
                    <div class="footer-col">
                        <h4>Legal</h4>
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                        <a href="#">Cookie Policy</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© {{ date('Y') }} Book Depot. All rights reserved.</p>
                <div class="payment-icons">
                    <span class="payment-icon">VISA</span>
                    <span class="payment-icon">MC</span>
                    <span class="payment-icon">AMEX</span>
                    <span class="payment-icon">PayPal</span>
                    <span class="payment-icon">Stripe</span>
                </div>
            </div>
        </footer>
    @endunless

    {{-- ===== LIVEWIRE SCRIPTS ===== --}}
    @livewireScripts



    {{-- ===== APP SCRIPTS ===== --}}
    <script>
        // Cart functions (bridges Livewire)
        function openCart() {
            Livewire.dispatch('openCartDrawer');
        }

        function closeCart() {
            Livewire.dispatch('closeCartDrawer');
        }

        // Toast system
        function showToast(msg, type = 'default') {
            const t = document.getElementById('toast');
            if (!t) return;
            t.textContent = msg;
            t.className = 'toast show' + (type !== 'default' ? ' ' + type : '');
            clearTimeout(t._timer);
            t._timer = setTimeout(() => {
                t.className = 'toast';
            }, 3200);
        }

        // Quick view
        function openQuickView(productId) {
            fetch(`/product/${productId}/quick-view`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('quickViewContent').innerHTML = data.html;
                    document.getElementById('quickViewModal').classList.add('open');
                });
        }

        function closeQuickView(e) {
            if (e && e.target !== document.getElementById('quickViewModal')) return;
            document.getElementById('quickViewModal').classList.remove('open');
        }

        let _qvQtys = {};
        function qvQtyChange(delta, pid) {
            _qvQtys[pid] = Math.max(1, Math.min(10, (_qvQtys[pid] || 1) + delta));
            const el = document.getElementById('qvQty-' + pid);
            if (el) el.textContent = _qvQtys[pid];
        }
        function qvAddToCart(pid) {
            const qty = _qvQtys[pid] || 1;
            Livewire.dispatch('addToCart', { productId: pid, quantity: qty });
            document.getElementById('quickViewModal').classList.remove('open');
        }

        // Sticky navbar
        window.addEventListener('scroll', () => {
            document.getElementById('navbar')?.classList.toggle('scrolled', window.scrollY > 20);
        });

        // Livewire events
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('cartUpdated', (data) => {
                let count = Array.isArray(data) ? (data[0]?.count ?? data[0]) : (data?.count ?? data);
                const badge = document.getElementById('navCartBadge');
                if (badge) {
                    badge.textContent = count;
                    badge.style.display = count > 0 ? 'flex' : 'none';
                }
            });
            Livewire.on('showToast', (data) => {
                let payload = Array.isArray(data) ? data[0] : data;
                showToast(payload?.message || '', payload?.type || 'default');
            });
        });

        // ESC key
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeCart();
                document.getElementById('quickViewModal')?.classList.remove('open');
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
