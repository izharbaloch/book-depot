<div>
    <div class="product-card" wire:key="product-{{ $product->id }}">
        <div class="product-img-wrap">
            {{-- Badge --}}
            @if ($product->badge)
                <span class="product-badge badge-{{ $product->badge }}">
                    {{ $product->badge === 'new' ? 'New' : ($product->badge === 'sale' ? 'Sale' : 'Limited') }}
                </span>
            @endif

            {{-- Images --}}
            <a href="{{ route('product.show', $product) }}" class="product-img-link">
                <div class="product-img-main">
                    @if ($product->image)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy"
                            style="width:100%;height:100%;object-fit:cover" />
                    @else
                        <div class="prod-visual-placeholder"
                            style="width:100%;height:100%;background:var(--bg-alt);display:flex;align-items:center;justify-content:center;opacity:.3">
                            <svg width="70" height="105" viewBox="0 0 70 105" fill="var(--text)">
                                <path d="M20 0 C20 18 50 18 50 0 L65 14 L70 105 L0 105 L5 14Z" />
                            </svg>
                        </div>
                    @endif
                </div>
                @if (!empty($product->gallery_urls))
                    <div class="product-img-hover">
                        <img src="{{ $product->gallery_urls[0] }}" alt="{{ $product->name }}" loading="lazy"
                            style="width:100%;height:100%;object-fit:cover" />
                    </div>
                @endif
            </a>

            {{-- Actions --}}
            <div class="product-actions">
                <button class="action-btn wl-btn {{ $inWishlist ? 'wishlisted' : '' }}" wire:click="toggleWishlist"
                    title="{{ $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}">
                    {{ $inWishlist ? '♥' : '♡' }}
                </button>
                <button class="action-btn" title="Quick view" onclick="openQuickView('{{ $product->slug }}')">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </button>
            </div>

            {{-- Quick view btn --}}
            <button class="quick-view-btn" onclick="openQuickView('{{ $product->slug }}')">Quick View</button>
        </div>

        <div class="product-info">
            <div class="product-category">{{ $product->category->name ?? '' }}</div>
            <a href="{{ route('product.show', $product) }}" class="product-name" title="{{ $product->name }}">
                {{ $product->name }}
            </a>
            <div class="product-price-row">
                @if ($product->is_on_sale)
                    <span class="price-current price-sale">${{ number_format($product->sale_price, 2) }}</span>
                    <span class="price-original">${{ number_format($product->price, 2) }}</span>
                @else
                    <span class="price-current">${{ number_format($product->price, 2) }}</span>
                @endif
            </div>
            <button class="add-to-cart-btn" wire:click="quickAdd" @disabled($product->stock < 1)>
                {{ $product->stock < 1 ? 'Out of Stock' : 'Add to Cart' }}
            </button>
        </div>
    </div>
</div>
