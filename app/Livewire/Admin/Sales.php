<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Sale;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class Sales extends Component
{
    use WithPagination;

    public string $search = '';
    public string $channel = '';
    public string $paymentMethod = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    protected int $perPage = 15;

    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedChannel()
    {
        $this->resetPage();
    }
    public function updatedPaymentMethod()
    {
        $this->resetPage();
    }
    public function updatedDateFrom()
    {
        $this->resetPage();
    }
    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'channel', 'paymentMethod', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    private function onlineOrders(): Collection
    {
        if ($this->channel === 'pos') {
            return collect();
        }

        return Order::with(['customer', 'user'])
            ->when($this->search, fn($q, $s) => $q->where('order_number', 'like', "%$s%")->orWhere('email', 'like', "%$s%"))
            ->when($this->paymentMethod, fn($q, $p) => $q->where('payment_method', $p))
            ->when($this->dateFrom, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($this->dateTo, fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->get()
            ->map(fn($o) => (object) [
                'channel'        => 'Online',
                'number'         => $o->order_number,
                'customer'       => $o->customer?->name ?? $o->full_name,
                'total'          => (float) $o->total,
                'payment_method' => $o->payment_method,
                'status'         => ucfirst($o->status),
                'date'           => $o->created_at,
                'url'            => route('admin.orders.show', $o),
            ]);
    }

    private function posSales(): Collection
    {
        if ($this->channel === 'online') {
            return collect();
        }

        return Sale::with(['customer', 'cashier'])
            ->when($this->search, fn($q, $s) => $q->where('sale_number', 'like', "%$s%"))
            ->when($this->paymentMethod, fn($q, $p) => $q->where('payment_method', $p))
            ->when($this->dateFrom, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($this->dateTo, fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->get()
            ->map(fn($s) => (object) [
                'channel'        => 'POS',
                'number'         => $s->sale_number,
                'customer'       => $s->customer?->name ?? 'Walk-in Customer',
                'total'          => (float) $s->total,
                'payment_method' => $s->payment_method,
                'status'         => 'Completed',
                'date'           => $s->created_at,
                'url'            => route('admin.pos.receipt', $s),
            ]);
    }

    public function render()
    {
        $combined = $this->onlineOrders()->concat($this->posSales())->sortByDesc('date')->values();

        $page = $this->getPage();
        $paginated = new LengthAwarePaginator(
            $combined->forPage($page, $this->perPage)->values(),
            $combined->count(),
            $this->perPage,
            $page,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        return view('livewire.admin.sales', [
            'sales'      => $paginated,
            'totalCount' => $combined->count(),
            'totalValue' => $combined->sum('total'),
        ])->extends('layouts.admin');
    }
}
