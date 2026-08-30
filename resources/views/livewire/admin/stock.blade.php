@section('title', 'Stock')
@section('page_title', 'Inventory / Stock')

<div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1.25rem;margin-bottom:1.5rem">
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
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">Current Stock ({{ $products->total() }})</h3>
        </div>

        <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search name, SKU, ISBN, barcode…"
                style="padding:.6rem .85rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.85rem;flex:1;min-width:200px" />
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
                    <th>Min Level</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr wire:key="stock-{{ $product->id }}">
                        <td style="font-weight:500">{{ $product->name }}</td>
                        <td style="color:var(--text-muted)">{{ $product->sku }}</td>
                        <td>{{ $product->stock }}</td>
                        <td style="color:var(--text-muted)">{{ $product->min_stock_level }}</td>
                        <td>
                            @if ($product->stock <= 0)
                                <span style="font-size:.72rem;padding:.2rem .55rem;border:1px solid #8b1a1a;color:#8b1a1a">Out of Stock</span>
                            @elseif ($product->stock <= $product->min_stock_level)
                                <span style="font-size:.72rem;padding:.2rem .55rem;border:1px solid #d4a840;color:#d4a840">Low Stock</span>
                            @else
                                <span style="font-size:.72rem;padding:.2rem .55rem;border:1px solid #1a5c2c;color:#1a5c2c">In Stock</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;padding:3rem;color:var(--text-muted)">No products found.</td></tr>
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
                        <td style="color:{{ $movement->quantity >= 0 ? '#1a5c2c' : '#8b1a1a' }}">
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
