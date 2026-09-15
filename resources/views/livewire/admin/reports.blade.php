@section('title', 'Reports')
@section('page_title', 'Reports')

<div>
    <div class="admin-card">
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1.25rem;border-bottom:1px solid var(--border);padding-bottom:1.25rem">
            @foreach (['sales' => 'Sales Report', 'products' => 'Product Sales', 'stock' => 'Stock Report', 'purchases' => 'Purchase Report'] as $key => $label)
                <button type="button" wire:click="setTab('{{ $key }}')"
                    class="{{ $tab === $key ? 'btn-primary' : 'btn-ghost' }}" style="padding:.55rem 1.1rem;font-size:.75rem">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        @if ($tab !== 'stock')
            <div style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;margin-bottom:1.5rem">
                <button type="button" wire:click="setPreset('today')" class="btn-ghost" style="padding:.4rem .9rem;font-size:.7rem">Today</button>
                <button type="button" wire:click="setPreset('yesterday')" class="btn-ghost" style="padding:.4rem .9rem;font-size:.7rem">Yesterday</button>
                <button type="button" wire:click="setPreset('week')" class="btn-ghost" style="padding:.4rem .9rem;font-size:.7rem">This Week</button>
                <button type="button" wire:click="setPreset('month')" class="btn-ghost" style="padding:.4rem .9rem;font-size:.7rem">This Month</button>
                <input type="date" wire:model.live="dateFrom" style="padding:.5rem .75rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.82rem" />
                <span style="color:var(--text-muted)">to</span>
                <input type="date" wire:model.live="dateTo" style="padding:.5rem .75rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.82rem" />
            </div>
        @endif

        {{-- ── SALES REPORT ── --}}
        @if ($tab === 'sales')
            <div class="stat-grid" style="margin-bottom:1.5rem">
                <div class="stat-card">
                    <div class="stat-card-value">${{ number_format($salesReport['grandTotal'], 2) }}</div>
                    <div class="stat-card-label">Total Sales</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-value">${{ number_format($salesReport['onlineTotal'], 2) }}</div>
                    <div class="stat-card-label">Online ({{ $salesReport['onlineCount'] }})</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-value">${{ number_format($salesReport['posTotal'], 2) }}</div>
                    <div class="stat-card-label">POS ({{ $salesReport['posCount'] }})</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-value">{{ $salesReport['onlineCount'] + $salesReport['posCount'] }}</div>
                    <div class="stat-card-label">Total Transactions</div>
                </div>
            </div>

            <h4 style="font-size:.72rem;letter-spacing:.15em;text-transform:uppercase;margin-bottom:1rem">Profit</h4>
            <div class="stat-grid" style="margin-bottom:1.5rem">
                <div class="stat-card">
                    <div class="stat-card-value">${{ number_format($salesReport['revenue'], 2) }}</div>
                    <div class="stat-card-label">Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-value">${{ number_format($salesReport['cost'], 2) }}</div>
                    <div class="stat-card-label">Cost</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-value">${{ number_format($salesReport['grossProfit'], 2) }}</div>
                    <div class="stat-card-label">Gross Profit</div>
                </div>
            </div>

            <h4 style="font-size:.72rem;letter-spacing:.15em;text-transform:uppercase;margin-bottom:1rem">Totals by Payment Method</h4>
            <table class="admin-table">
                <thead><tr><th>Payment Method</th><th style="text-align:right">Total</th></tr></thead>
                <tbody>
                    @forelse ($salesReport['byPaymentMethod'] as $method => $total)
                        <tr><td style="text-transform:capitalize">{{ str_replace('_', ' ', $method) }}</td><td style="text-align:right">${{ number_format($total, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="2" style="text-align:center;padding:2rem;color:var(--text-muted)">No sales in this range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        {{-- ── PRODUCT PROFITABILITY ── --}}
        @if ($tab === 'products')
            <table class="admin-table">
                <thead><tr><th>Product</th><th>Qty Sold</th><th style="text-align:right">Revenue</th><th style="text-align:right">Cost</th><th style="text-align:right">Gross Profit</th></tr></thead>
                <tbody>
                    @forelse ($productSales as $row)
                        <tr>
                            <td>{{ $row->product_name }}</td>
                            <td>{{ $row->qty }}</td>
                            <td style="text-align:right">${{ number_format($row->revenue, 2) }}</td>
                            <td style="text-align:right">${{ number_format($row->cost, 2) }}</td>
                            <td style="text-align:right">${{ number_format($row->grossProfit, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text-muted)">No product sales in this range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        {{-- ── STOCK REPORT ── --}}
        @if ($tab === 'stock')
            <div class="stat-grid" style="margin-bottom:1.5rem">
                <div class="stat-card"><div class="stat-card-value">{{ $lowStockCount }}</div><div class="stat-card-label">Low Stock</div></div>
                <div class="stat-card"><div class="stat-card-value">{{ $outOfStockCount }}</div><div class="stat-card-label">Out of Stock</div></div>
            </div>
            <table class="admin-table">
                <thead><tr><th>Product</th><th>SKU</th><th>Stock</th><th>Min Level</th><th style="text-align:right">Avg Cost</th><th style="text-align:right">Stock Value</th></tr></thead>
                <tbody>
                    @forelse ($stockProducts as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td style="color:var(--text-muted)">{{ $product->sku }}</td>
                            <td style="color:{{ $product->stock <= 0 ? 'var(--danger)' : ($product->stock <= $product->min_stock_level ? 'var(--warning)' : 'var(--text)') }}">{{ $product->stock }}</td>
                            <td style="color:var(--text-muted)">{{ $product->min_stock_level }}</td>
                            <td style="text-align:right;color:var(--text-muted)">${{ number_format($product->cost_price, 2) }}</td>
                            <td style="text-align:right">${{ number_format($product->stock * $product->cost_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        {{-- ── PURCHASE REPORT ── --}}
        @if ($tab === 'purchases')
            <table class="admin-table">
                <thead><tr><th>PO #</th><th>Supplier</th><th>Date</th><th style="text-align:right">Quantity</th><th style="text-align:right">Total</th></tr></thead>
                <tbody>
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td style="font-weight:500">{{ $purchase->purchase_number }}</td>
                            <td style="color:var(--text-muted)">{{ $purchase->supplier->name }}</td>
                            <td style="color:var(--text-muted)">{{ $purchase->purchase_date->format('M j, Y') }}</td>
                            <td style="text-align:right">{{ $purchase->items->sum('quantity') }}</td>
                            <td style="text-align:right">${{ number_format($purchase->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text-muted)">No purchases in this range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
</div>
