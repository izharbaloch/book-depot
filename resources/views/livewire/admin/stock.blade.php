@section('title', 'Stock')
@section('page_title', 'Inventory / Stock')

<div>
    <div class="stat-grid" style="margin-bottom:1.5rem">
        <div class="stat-card">
            <div class="stat-card-icon">⚠️</div>
            <div class="stat-card-value">{{ $lowStockCount }}</div>
            <div class="stat-card-label">Low Stock</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">🚫</div>
            <div class="stat-card-value">{{ $outOfStockCount }}</div>
            <div class="stat-card-label">Out of Stock</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">💰</div>
            <div class="stat-card-value">${{ number_format($stockValue, 2) }}</div>
            <div class="stat-card-label">Stock Value</div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Current Stock ({{ $products->total() }})</h3>
        </div>

        <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search name, SKU, ISBN, barcode…"
                class="form-input" style="flex:1;min-width:200px" />
            <select wire:model.live="stockFilter" class="filter-select">
                <option value="">All Stock</option>
                <option value="low">Low Stock</option>
                <option value="out">Out of Stock</option>
            </select>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Stock</th>
                    <th>Avg Cost</th>
                    <th>Sale Price</th>
                    <th>Stock Value</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr wire:key="stock-{{ $product->id }}">
                        <td style="font-weight:500">{{ $product->name }}</td>
                        <td style="color:var(--text-muted)">{{ $product->sku }}</td>
                        <td>{{ $product->stock }}</td>
                        <td style="color:var(--text-muted)">${{ number_format($product->cost_price, 2) }}</td>
                        <td style="color:var(--text-muted)">${{ number_format($product->current_price, 2) }}</td>
                        <td>${{ number_format($product->stock * $product->cost_price, 2) }}</td>
                        <td>
                            @if ($product->stock <= 0)
                                <span class="badge badge-hidden">Out of Stock</span>
                            @elseif ($product->stock <= $product->min_stock_level)
                                <span class="badge badge-pending">Low Stock</span>
                            @else
                                <span class="badge badge-active">In Stock</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" wire:click="toggleExpand({{ $product->id }})" class="btn-ghost" style="padding:.35rem .75rem;font-size:.7rem">
                                {{ $expandedId === $product->id ? 'Hide History' : 'Purchase History' }}
                            </button>
                        </td>
                    </tr>
                    @if ($expandedId === $product->id)
                        <tr>
                            <td colspan="8" style="background:var(--bg-alt);padding:1rem 1.5rem">
                                <table style="width:100%;font-size:.82rem">
                                    <thead>
                                        <tr style="color:var(--text-muted)">
                                            <th style="text-align:left;padding:.35rem 0">Purchase #</th>
                                            <th style="text-align:left;padding:.35rem 0">Supplier</th>
                                            <th style="text-align:left;padding:.35rem 0">Date</th>
                                            <th style="text-align:left;padding:.35rem 0">Qty</th>
                                            <th style="text-align:left;padding:.35rem 0">Rate</th>
                                            <th style="text-align:left;padding:.35rem 0">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($purchaseHistory as $line)
                                            <tr>
                                                <td style="padding:.35rem 0">{{ $line->purchase->purchase_number }}</td>
                                                <td style="padding:.35rem 0">{{ $line->purchase->supplier->name }}</td>
                                                <td style="padding:.35rem 0">{{ $line->purchase->purchase_date->format('M j, Y') }}</td>
                                                <td style="padding:.35rem 0">{{ $line->quantity }}</td>
                                                <td style="padding:.35rem 0">${{ number_format($line->purchase_price, 2) }}</td>
                                                <td style="padding:.35rem 0">${{ number_format($line->subtotal, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" style="padding:.75rem 0;color:var(--text-muted)">No purchase history for this product.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="8" style="text-align:center;padding:3rem;color:var(--text-muted)">No products found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:1.5rem">{{ $products->links() }}</div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Recent Stock Movements</h3>
        </div>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Qty</th>
                    <th>Balance After</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentMovements as $movement)
                    <tr wire:key="movement-{{ $movement->id }}">
                        <td>{{ $movement->product->name ?? 'Deleted product' }}</td>
                        <td style="color:var(--text-muted);text-transform:capitalize">{{ str_replace('_', ' ', $movement->type) }}</td>
                        <td style="color:{{ $movement->quantity >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                            {{ $movement->quantity >= 0 ? '+' : '' }}{{ $movement->quantity }}
                        </td>
                        <td>{{ $movement->balance_after }}</td>
                        <td style="color:var(--text-muted)">{{ $movement->created_at->format('M j, Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;padding:3rem;color:var(--text-muted)">No stock movements yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
