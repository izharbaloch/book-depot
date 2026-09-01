<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Collection;
use Livewire\Component;

class Reports extends Component
{
    public string $tab = 'sales';
    public string $dateFrom;
    public string $dateTo;

    public function mount(): void
    {
        $this->dateFrom = today()->startOfMonth()->toDateString();
        $this->dateTo   = today()->toDateString();
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function setPreset(string $preset): void
    {
        [$this->dateFrom, $this->dateTo] = match ($preset) {
            'today'     => [today()->toDateString(), today()->toDateString()],
            'yesterday' => [today()->subDay()->toDateString(), today()->subDay()->toDateString()],
            'week'      => [today()->startOfWeek()->toDateString(), today()->toDateString()],
            'month'     => [today()->startOfMonth()->toDateString(), today()->toDateString()],
            default     => [$this->dateFrom, $this->dateTo],
        };
    }

    private function ordersInRange(): Collection
    {
        return Order::whereDate('created_at', '>=', $this->dateFrom)
            ->whereDate('created_at', '<=', $this->dateTo)
            ->where('status', '!=', 'cancelled')
            ->get();
    }

    private function salesInRange(): Collection
    {
        return Sale::whereDate('created_at', '>=', $this->dateFrom)
            ->whereDate('created_at', '<=', $this->dateTo)
            ->get();
    }

    public function render()
    {
        $orders = $this->ordersInRange();
        $sales  = $this->salesInRange();

        $byPaymentMethod = [];
        foreach ($orders->groupBy('payment_method') as $method => $group) {
            $byPaymentMethod[$method] = ($byPaymentMethod[$method] ?? 0) + $group->sum('total');
        }
        foreach ($sales->groupBy('payment_method') as $method => $group) {
            $byPaymentMethod[$method] = ($byPaymentMethod[$method] ?? 0) + $group->sum('total');
        }

        $revenue = (float) $orders->sum('total') + (float) $sales->sum('total');
        $cost = (float) OrderItem::whereIn('order_id', $orders->pluck('id'))->selectRaw('COALESCE(SUM(quantity * cost_price), 0) as cost')->value('cost')
            + (float) SaleItem::whereIn('sale_id', $sales->pluck('id'))->selectRaw('COALESCE(SUM(quantity * cost_price), 0) as cost')->value('cost');

        $salesReport = [
            'onlineTotal'     => (float) $orders->sum('total'),
            'onlineCount'     => $orders->count(),
            'posTotal'        => (float) $sales->sum('total'),
            'posCount'        => $sales->count(),
            'grandTotal'      => $revenue,
            'revenue'         => $revenue,
            'cost'            => $cost,
            'grossProfit'     => $revenue - $cost,
            'byPaymentMethod' => $byPaymentMethod,
        ];

        $productSales = collect();
        if ($this->tab === 'products') {
            $orderItemStats = OrderItem::whereIn('order_id', $orders->pluck('id'))
                ->selectRaw('product_id, product_name, SUM(quantity) as qty, SUM(subtotal) as revenue, SUM(quantity * cost_price) as cost')
                ->groupBy('product_id', 'product_name')
                ->get();

            $saleItemStats = SaleItem::whereIn('sale_id', $sales->pluck('id'))
                ->selectRaw('product_id, product_name, SUM(quantity) as qty, SUM(subtotal) as revenue, SUM(quantity * cost_price) as cost')
                ->groupBy('product_id', 'product_name')
                ->get();

            $productSales = $orderItemStats->concat($saleItemStats)
                ->groupBy('product_name')
                ->map(function ($rows) {
                    $revenue = $rows->sum('revenue');
                    $cost    = $rows->sum('cost');
                    return (object) [
                        'product_name' => $rows->first()->product_name,
                        'qty'          => $rows->sum('qty'),
                        'revenue'      => $revenue,
                        'cost'         => $cost,
                        'grossProfit'  => $revenue - $cost,
                    ];
                })
                ->sortByDesc('qty')
                ->values();
        }

        $purchases = collect();
        if ($this->tab === 'purchases') {
            $purchases = Purchase::with(['supplier', 'items'])
                ->whereDate('purchase_date', '>=', $this->dateFrom)
                ->whereDate('purchase_date', '<=', $this->dateTo)
                ->orderBy('purchase_date', 'desc')
                ->get();
        }

        return view('livewire.admin.reports', [
            'salesReport'  => $salesReport,
            'productSales' => $productSales,
            'purchases'    => $purchases,
            'stockProducts' => $this->tab === 'stock' ? Product::orderBy('stock')->get() : collect(),
            'lowStockCount'  => Product::lowStock()->count(),
            'outOfStockCount' => Product::outOfStock()->count(),
        ])->extends('layouts.admin');
    }
}
