<div>
    {{-- Overlay --}}
    <div class="cart-overlay {{ $isOpen ? 'open' : '' }}" wire:click="close"></div>

    {{-- Drawer --}}
    <aside class="cart-drawer {{ $isOpen ? 'open' : '' }}" aria-label="Shopping cart">
        <div class="cart-header">
            <h2 class="cart-title">
                Your Cart
                <span class="cart-badge">{{ $itemCount }}</span>
            </h2>
            <button class="cart-close" wire:click="close" aria-label="Close cart">✕</button>
        </div>

        <div class="cart-items-list">
            @if ($cart && $cart->items->count())
                @foreach ($cart->items as $item)
                    <div class="cart-item" wire:key="cart-item-{{ $item->id }}">
                        <div class="cart-item-img">
                            @if ($item->product->image)
                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}"
                                    loading="lazy" style="width:100%;height:100%;object-fit:cover" />
                            @else
                                <div
                                    style="width:100%;height:100%;background:var(--bg-alt);display:flex;align-items:center;justify-content:center;opacity:.6">
                                    <svg width="32" height="40" viewBox="0 0 70 105" fill="var(--text-muted)">
                                        <path d="M20 0 C20 18 50 18 50 0 L65 14 L70 105 L0 105 L5 14Z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="cart-item-details">
                            <div class="cart-item-name">{{ $item->product->name }}</div>
                            <div class="cart-item-meta">{{ $item->product->sku }}</div>
                            <div class="cart-item-price">${{ number_format($item->price, 2) }}</div>
                            <div class="cart-item-qty">
                                <button class="qty-btn"
                                    wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})">−</button>
                                <span class="qty-val">{{ $item->quantity }}</span>
                                <button class="qty-btn"
                                    wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})">+</button>
                            </div>
                            <button class="cart-item-remove" wire:click="removeItem({{ $item->id }})"
                                wire:confirm="Remove this item?">Remove</button>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="cart-empty">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1"
                        viewBox="0 0 24 24" style="margin:0 auto 1rem;opacity:.3">
                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                        <line x1="3" y1="6" x2="21" y2="6" />
                        <path d="M16 10a4 4 0 01-8 0" />
                    </svg>
                    <p>Your cart is empty</p>
                    <a href="{{ route('shop') }}" class="btn-primary" wire:click="close">Start Shopping</a>
                </div>
            @endif
        </div>

        @if ($cart && $cart->items->count())
            <div class="cart-footer">
                <div class="cart-total-breakdown">
                    <div class="cart-line"><span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
                    <div class="cart-line">
                        <span>Shipping</span><span>{{ $shipping > 0 ? '$' . number_format($shipping, 2) : 'Free' }}</span>
                    </div>
                    <div class="cart-line"><span>Tax (8%)</span><span>${{ number_format($tax, 2) }}</span></div>
                </div>
                <div class="cart-total">
                    <span>Total</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
                <a href="{{ route('checkout') }}" class="btn-primary btn-full">
                    Proceed to Checkout
                </a>
                <button class="btn-ghost btn-full" wire:click="close">Continue Shopping</button>
            </div>
        @endif
    </aside>

    {{-- Loading indicator --}}
    <div wire:loading.flex wire:target="addItem,updateQuantity,removeItem"
        style="position:fixed;bottom:5rem;left:50%;transform:translateX(-50%);
                background:var(--text);color:var(--bg);padding:.5rem 1.25rem;
                font-size:.75rem;letter-spacing:.1em;z-index:999;align-items:center;gap:.5rem">
        <div class="spinner-sm"></div> Updating cart…
    </div>
</div>
