<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class AddToCart extends Component
{
    public Product $product;
    public int $quantity = 1;

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function increment(): void
    {
        $this->quantity = min($this->product->stock, 10, $this->quantity + 1);
    }
    public function decrement(): void
    {
        $this->quantity = max(1, $this->quantity - 1);
    }

    public function addToCart(): void
    {
        if ($this->product->stock < 1) {
            return;
        }

        $this->dispatch('addToCart', productId: $this->product->id, quantity: $this->quantity);
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}
