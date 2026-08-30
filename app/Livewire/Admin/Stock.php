<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;

class Stock extends Component
{
    use WithPagination;

    public string $search = '';
    public string $stockFilter = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedStockFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = Product::search($this->search)
            ->when($this->stockFilter === 'low', fn($q) => $q->lowStock())
            ->when($this->stockFilter === 'out', fn($q) => $q->outOfStock())
            ->orderBy('stock')
            ->paginate(15);

        return view('livewire.admin.stock', [
            'products'       => $products,
            'lowStockCount'  => Product::lowStock()->count(),
            'outOfStockCount' => Product::outOfStock()->count(),
            'recentMovements' => StockMovement::with('product')->latest()->limit(20)->get(),
        ])->extends('layouts.admin');
    }
}
