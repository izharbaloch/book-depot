@section('title', 'Sales')
@section('page_title', 'Sales')

<div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1.25rem;margin-bottom:1.5rem">
        <div class="stat-card">
            <div class="stat-card-icon">🧾</div>
            <div class="stat-card-value">{{ $totalCount }}</div>
            <div class="stat-card-label">Transactions</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon">💰</div>
            <div class="stat-card-value">${{ number_format($totalValue, 2) }}</div>
            <div class="stat-card-label">Total Value</div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">All Sales</h3>
        </div>

        <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search order # / sale #…"
                style="padding:.6rem .85rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.85rem;flex:1;min-width:200px" />
            <select wire:model.live="channel" class="filter-select">
                <option value="">All Channels</option>
                <option value="online">Online</option>
                <option value="pos">POS</option>
            </select>
            <select wire:model.live="paymentMethod" class="filter-select">
                <option value="">All Payment Methods</option>
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="cod">Cash on Delivery</option>
                <option value="stripe">Stripe</option>
                <option value="paypal">PayPal</option>
            </select>
            <input type="date" wire:model.live="dateFrom" style="padding:.6rem .85rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.85rem" />
            <input type="date" wire:model.live="dateTo" style="padding:.6rem .85rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.85rem" />
            <button type="button" wire:click="clearFilters" class="btn-ghost" style="padding:.6rem 1.25rem;font-size:.72rem">Clear</button>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Channel</th>
                    <th>Customer</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th style="text-align:right">Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td style="font-weight:500">{{ $sale->number }}</td>
                        <td>
                            <span class="badge {{ $sale->channel === 'POS' ? 'badge-processing' : 'badge-shipped' }}">{{ $sale->channel }}</span>
                        </td>
                        <td style="color:var(--text-muted)">{{ $sale->customer }}</td>
                        <td style="color:var(--text-muted);text-transform:capitalize">{{ str_replace('_', ' ', $sale->payment_method) }}</td>
                        <td style="color:var(--text-muted)">{{ $sale->status }}</td>
                        <td style="color:var(--text-muted)">{{ $sale->date->format('M j, Y g:i A') }}</td>
                        <td style="text-align:right;font-weight:500">${{ number_format($sale->total, 2) }}</td>
                        <td><a href="{{ $sale->url }}" target="_blank" class="btn-ghost" style="padding:.3rem .75rem;font-size:.7rem">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;padding:3rem;color:var(--text-muted)">No sales found.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:1.5rem">{{ $sales->links() }}</div>
    </div>
</div>
