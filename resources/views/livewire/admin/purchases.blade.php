@section('title', 'Purchases')
@section('page_title', 'Stock Receiving / Purchases')

<div>
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">All Purchases ({{ $purchases->total() }})</h3>
            @unless($showForm)
                <button type="button" wire:click="openCreate" class="btn-primary" style="padding:.6rem 1.4rem;font-size:.72rem">+ Receive Stock</button>
            @endunless
        </div>

        <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem">
            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Search PO # or invoice #…"
                style="padding:.6rem .85rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.85rem;flex:1;min-width:200px" />
            <select wire:model.live="supplierFilter" class="filter-select">
                <option value="">All Suppliers</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>

        @if ($showForm)
            <div class="admin-card" style="border-color:var(--text);margin-bottom:1.5rem">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Receive Stock</h3>
                </div>
                <form wire:submit.prevent="save">
                    <div class="form-grid" style="margin-bottom:1rem">
                        <div class="form-group">
                            <label>Supplier *</label>
                            <select wire:model="supplier_id" class="filter-select">
                                <option value="">Select supplier…</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <span style="color:#8b1a1a;font-size:.75rem">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Purchase Date *</label>
                            <input type="date" wire:model="purchase_date" />
                            @error('purchase_date') <span style="color:#8b1a1a;font-size:.75rem">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label>Invoice Number</label>
                            <input type="text" wire:model="invoice_number" />
                        </div>
                        <div class="form-group full">
                            <label>Notes</label>
                            <input type="text" wire:model="notes" />
                        </div>
                    </div>

                    <h4 style="font-size:.72rem;letter-spacing:.15em;text-transform:uppercase;margin-bottom:1rem">Line Items</h4>
                    @error('items') <div style="color:#8b1a1a;font-size:.8rem;margin-bottom:.75rem">{{ $message }}</div> @enderror

                    <div style="display:flex;flex-direction:column;gap:.75rem;margin-bottom:1rem">
                        @foreach ($items as $i => $item)
                            <div style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:.75rem;align-items:start" wire:key="purchase-item-{{ $i }}">
                                <div>
                                    <select wire:model="items.{{ $i }}.product_id" class="filter-select" style="width:100%">
                                        <option value="">Select product…</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                        @endforeach
                                    </select>
                                    @error("items.$i.product_id") <span style="color:#8b1a1a;font-size:.72rem">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <input type="number" wire:model="items.{{ $i }}.quantity" min="1" placeholder="Qty" />
                                    @error("items.$i.quantity") <span style="color:#8b1a1a;font-size:.72rem">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <input type="number" wire:model="items.{{ $i }}.purchase_price" step="0.01" min="0" placeholder="Unit cost" />
                                    @error("items.$i.purchase_price") <span style="color:#8b1a1a;font-size:.72rem">{{ $message }}</span> @enderror
                                </div>
                                <button type="button" wire:click="removeItem({{ $i }})"
                                    style="padding:.6rem .85rem;border:1px solid #8b1a1a;color:#8b1a1a;background:none;cursor:pointer;font-family:inherit;font-size:.75rem">✕</button>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" wire:click="addItem" class="btn-ghost" style="padding:.5rem 1rem;font-size:.72rem;margin-bottom:1.5rem">+ Add Line</button>

                    <div style="display:flex;gap:.75rem;max-width:400px">
                        <button type="submit" class="btn-primary" style="flex:1" wire:loading.attr="disabled" wire:target="save">Receive & Update Stock</button>
                        <button type="button" wire:click="cancel" class="btn-ghost" style="flex:1">Cancel</button>
                    </div>
                </form>
            </div>
        @endif

        <table class="admin-table">
            <thead>
                <tr>
                    <th>PO #</th>
                    <th>Supplier</th>
                    <th>Date</th>
                    <th>Invoice #</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchases as $purchase)
                    <tr wire:key="purchase-{{ $purchase->id }}">
                        <td style="font-weight:500">{{ $purchase->purchase_number }}</td>
                        <td style="color:var(--text-muted)">{{ $purchase->supplier->name }}</td>
                        <td style="color:var(--text-muted)">{{ $purchase->purchase_date->format('M j, Y') }}</td>
                        <td style="color:var(--text-muted)">{{ $purchase->invoice_number ?? '—' }}</td>
                        <td>${{ number_format($purchase->total, 2) }}</td>
                        <td>
                            <button type="button" wire:click="toggleExpand({{ $purchase->id }})" class="btn-ghost" style="padding:.35rem .75rem;font-size:.7rem">
                                {{ $expandedId === $purchase->id ? 'Hide Items' : 'View Items' }}
                            </button>
                        </td>
                    </tr>
                    @if ($expandedId === $purchase->id)
                        <tr>
                            <td colspan="6" style="background:var(--bg-alt);padding:1rem 1.5rem">
                                <table style="width:100%;font-size:.82rem">
                                    <thead>
                                        <tr style="color:var(--text-muted)">
                                            <th style="text-align:left;padding:.35rem 0">Product</th>
                                            <th style="text-align:left;padding:.35rem 0">Qty</th>
                                            <th style="text-align:left;padding:.35rem 0">Unit Cost</th>
                                            <th style="text-align:left;padding:.35rem 0">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchase->items as $item)
                                            <tr>
                                                <td style="padding:.35rem 0">{{ $item->product->name ?? 'Deleted product' }}</td>
                                                <td style="padding:.35rem 0">{{ $item->quantity }}</td>
                                                <td style="padding:.35rem 0">${{ number_format($item->purchase_price, 2) }}</td>
                                                <td style="padding:.35rem 0">${{ number_format($item->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="6" style="text-align:center;padding:3rem;color:var(--text-muted)">No purchases recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:1.5rem">{{ $purchases->links() }}</div>
    </div>
</div>
