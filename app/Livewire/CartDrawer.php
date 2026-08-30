<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;

class CartDrawer extends Component
{
    public bool $isOpen = false;
    public $cart        = null;

    public function mount(): void
    {
        $this->loadCart();
    }

    #[On('refreshCart')]
    public function loadCart(): void
    {
        $this->cart = Cart::getForUser()->load('items.product');
    }

    #[On('openCartDrawer')]
    public function open(): void
    {
        $this->loadCart();
        $this->isOpen = true;
    }

    #[On('closeCartDrawer')]
    public function close(): void
    {
        $this->isOpen = false;
    }

    #[On('addToCart')]
    public function addItem(int $productId, int $quantity = 1): void
    {
        $product = Product::findOrFail($productId);
        $cart    = Cart::getForUser();
        $price   = (float)($product->sale_price ?? $product->price);

        $existing = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        $existingQty = $existing?->quantity ?? 0;
        $newQty = min($existingQty + $quantity, $product->stock, 10);

        if ($newQty <= $existingQty) {
            $this->dispatch('showToast', message: "Only {$product->stock} available for \"{$product->name}\"", type: 'default');
            $this->loadCart();
            return;
        }

        if ($existing) {
            $existing->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $productId,
                'quantity'   => $newQty,
                'price'      => $price,
            ]);
        }

        $this->loadCart();
        $this->isOpen = true;

        $count = $this->cart->item_count;
        $this->dispatch('cartUpdated', count: $count);
        $this->dispatch('showToast', 
            message: "✓ {$product->name} added to cart",
            type: 'success',
        );
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        $item = CartItem::findOrFail($itemId);
        $this->authorizeItem($item);

        if ($quantity <= 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => min($quantity, $item->product->stock, 10)]);
        }

        $this->loadCart();
        $this->dispatch('cartUpdated', count: $this->cart->item_count);
    }

    public function removeItem(int $itemId): void
    {
        $item = CartItem::findOrFail($itemId);
        $this->authorizeItem($item);
        $item->delete();

        $this->loadCart();
        $this->dispatch('cartUpdated', count: $this->cart->item_count);
        $this->dispatch('showToast', message: 'Item removed', type: 'default');
    }

    public function clearCart(): void
    {
        Cart::getForUser()->items()->delete();
        $this->loadCart();
        $this->dispatch('cartUpdated', count: 0);
    }

    private function authorizeItem(CartItem $item): void
    {
        $cart = Cart::getForUser();
        abort_unless($item->cart_id === $cart->id, 403);
    }

    public function getSubtotalProperty(): float
    {
        return $this->cart?->subtotal ?? 0;
    }
    public function getTaxProperty(): float
    {
        return $this->cart?->tax ?? 0;
    }
    public function getShippingProperty(): float
    {
        return $this->cart?->shipping ?? 0;
    }
    public function getTotalProperty(): float
    {
        return $this->cart?->total ?? 0;
    }
    public function getItemCountProperty(): int
    {
        return $this->cart?->item_count ?? 0;
    }

    public function render()
    {
        return view('livewire.cart-drawer', [
            'subtotal'  => $this->getSubtotalProperty(),
            'tax'       => $this->getTaxProperty(),
            'shipping'  => $this->getShippingProperty(),
            'total'     => $this->getTotalProperty(),
            'itemCount' => $this->getItemCountProperty(),
        ]);
    }
}
