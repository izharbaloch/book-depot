<div class="quick-view-grid">
    <div class="qv-img">
        @if ($product->image)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                style="width:100%;height:100%;object-fit:cover" />
        @else
            <div
                style="width:100%;height:100%;background:var(--bg-alt);display:flex;align-items:center;justify-content:center;opacity:.3">
                <svg width="80" height="120" viewBox="0 0 70 105" fill="var(--text)">
                    <path d="M20 0 C20 18 50 18 50 0 L65 14 L70 105 L0 105 L5 14Z" />
                </svg>
            </div>
        @endif
    </div>
    <div class="qv-info">
        <div class="qv-category">{{ $product->category->name ?? '' }}</div>
        <h2 class="qv-name">{{ $product->name }}</h2>
        <div class="qv-price">
            @if ($product->is_on_sale)
                <span class="price-sale">${{ number_format($product->sale_price, 2) }}</span>
                <span style="text-decoration:line-through;font-size:.85rem;opacity:.6;margin-left:.5rem">
                    ${{ number_format($product->price, 2) }}
                </span>
            @else
                ${{ number_format($product->price, 2) }}
            @endif
        </div>
        <p class="qv-desc">{{ $product->short_description ?? $product->description }}</p>

        <div class="qty-selector" style="margin-bottom:1.25rem">
            <button class="qty-btn" onclick="qvQtyChange(-1,{{ $product->id }})">−</button>
            <div class="qty-val-lg" id="qvQty-{{ $product->id }}"
                style="width:48px;text-align:center;border:1px solid var(--border);
                        height:40px;display:flex;align-items:center;justify-content:center">
                1</div>
            <button class="qty-btn" onclick="qvQtyChange(1,{{ $product->id }})">+</button>
        </div>

        <div class="qv-actions">
            <button class="btn-primary" onclick="qvAddToCart({{ $product->id }})" @disabled($product->stock < 1)>
                {{ $product->stock < 1 ? 'Out of Stock' : 'Add to Cart' }}
            </button>
            <a href="{{ route('product.show', $product) }}" class="btn-ghost" style="text-align:center">
                View Full Details
            </a>
        </div>
    </div>
</div>


