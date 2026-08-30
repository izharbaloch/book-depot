<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Wishlist;
use Livewire\Component;

class ProductCard extends Component
{
    public Product $product;
    public bool    $inWishlist = false;

    public function mount(Product $product): void
    {
        $this->product    = $product;
        $this->inWishlist = auth()->check()
            ? auth()->user()->hasInWishlist($product->id)
            : false;
    }

    public function toggleWishlist(): void
    {
        if (!auth()->check()) {
            $this->dispatch('showToast', message: 'Please login to save items', type: 'default');
            return;
        }

        $wl = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $this->product->id)
            ->first();

        if ($wl) {
            $wl->delete();
            $this->inWishlist = false;
            $this->dispatch('showToast', message: 'Removed from wishlist', type: 'default');
        } else {
            Wishlist::create(['user_id' => auth()->id(), 'product_id' => $this->product->id]);
            $this->inWishlist = true;
            $this->dispatch('showToast', message: '♡ Added to wishlist', type: 'success');
        }
    }

    public function quickAdd(): void
    {
        if ($this->product->stock < 1) {
            return;
        }
        $this->dispatch('addToCart', productId: $this->product->id, quantity: 1);
    }

    public function render()
    {
        return view('livewire.product-card');
    }
}
