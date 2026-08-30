<div>
    <div>
        {{-- Quantity + Add to Cart --}}
        <div class="pdp-atc">
            <div class="pdp-qty">
                <button class="qty-btn" wire:click="decrement" {{ $quantity <= 1 ? 'disabled' : '' }}>−</button>
                <div class="qty-val"
                    style="width:48px;height:48px;display:flex;align-items:center;
                                        justify-content:center;border-left:1px solid var(--border);
                                        border-right:1px solid var(--border)">
                    {{ $quantity }}
                </div>
                <button class="qty-btn" wire:click="increment" {{ $quantity >= min($product->stock, 10) ? 'disabled' : '' }}>+</button>
            </div>
            <button class="btn-primary pdp-add-btn" wire:click="addToCart" wire:loading.attr="disabled"
                wire:loading.class="opacity-60" @disabled($product->stock < 1)>
                <span wire:loading.remove wire:target="addToCart">{{ $product->stock < 1 ? 'Out of Stock' : 'Add to Cart' }}</span>
                <span wire:loading wire:target="addToCart">Adding…</span>
            </button>
        </div>
    </div>
</div>
