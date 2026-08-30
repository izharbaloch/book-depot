<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class Customers extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $expandedId = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function toggleExpand(int $id): void
    {
        $this->expandedId = $this->expandedId === $id ? null : $id;
    }

    public function render()
    {
        $customers = Customer::withCount(['orders', 'sales'])
            ->withSum('orders', 'total')
            ->withSum('sales', 'total')
            ->search($this->search)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $expanded = $this->expandedId
            ? Customer::with(['orders' => fn($q) => $q->latest()->limit(5), 'sales' => fn($q) => $q->latest()->limit(5)])
                ->find($this->expandedId)
            : null;

        return view('livewire.admin.customers', [
            'customers' => $customers,
            'expanded'  => $expanded,
        ])->extends('layouts.admin');
    }
}
