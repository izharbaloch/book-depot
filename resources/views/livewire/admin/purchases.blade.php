@section('title', 'Purchases')
@section('page_title', 'Stock Receiving / Purchases')

<div>
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">All Purchases ({{ $purchases->total() }})</h3>
            @unless($showForm)
                <button type="button" wire:click="openCreate" class="btn-primary" style="padding:.6rem 1.4rem;font-size:.72rem">+ New Purchase</button>
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

        @if (session('success'))
            <div style="background:#1a5c2c22;border:1px solid #1a5c2c;padding:.85rem 1.25rem;margin-bottom:1.5rem;font-size:.85rem;color:#1a5c2c">{{ session('success') }}</div>
        @endif

        @if ($showForm)
            <div class="admin-card" style="border-color:var(--text);margin-bottom:1.5rem">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">New Purchase</h3>
                </div>
                <form wire:submit.prevent="save">
                    <div class="form-grid" style="margin-bottom:1.5rem">
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

                    <div style="border-top:1px solid var(--border);padding-top:1.5rem;margin-bottom:1.5rem">
                        <div style="display:flex;gap:.75rem;align-items:flex-start;margin-bottom:.75rem">
                            <div style="position:relative;flex:1;min-width:220px">
                                <input type="text" wire:model.live.debounce.300ms="itemSearch"
                                    placeholder="Search product by name, SKU, ISBN, or barcode…"
                                    style="width:100%;padding:.65rem .9rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.85rem" />

                                @if ($itemSearch !== '')
                                    <div style="position:absolute;top:100%;left:0;right:0;z-index:20;background:var(--bg);border:1px solid var(--border);border-top:none;max-height:280px;overflow-y:auto">
                                        @forelse ($this->itemResults as $result)
                                            <div wire:click="addProduct({{ $result->id }})"
                                                class="purchase-item-result"
                                                style="padding:.6rem .9rem;cursor:pointer;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;gap:.75rem"
                                                wire:key="result-{{ $result->id }}">
                                                <span>
                                                    <strong style="font-size:.85rem">{{ $result->name }}</strong>
                                                    <span style="font-size:.72rem;color:var(--text-muted)">({{ $result->sku }})</span>
                                                </span>
                                                <span style="font-size:.75rem;color:var(--text-muted);white-space:nowrap">Stock: {{ $result->stock }} · Cost: ${{ number_format($result->cost_price, 2) }}</span>
                                            </div>
                                        @empty
                                            <div style="padding:.75rem .9rem;font-size:.8rem;color:var(--text-muted)">
                                                No matching products. Use "+ New Product" to create one.
                                            </div>
                                        @endforelse
                                    </div>
                                @endif
                            </div>
                            <button type="button" wire:click="openNewProduct" class="btn-ghost" style="padding:.65rem 1.1rem;font-size:.72rem;white-space:nowrap">+ New Product</button>
                        </div>

                        @if ($showNewProduct)
                            <div class="admin-card" style="background:var(--bg-alt);margin-bottom:1rem">
                                <div class="admin-form-section" style="border-bottom:none;margin-bottom:.75rem;padding-bottom:0">
                                    <h4>New Product</h4>
                                </div>
                                <div class="form-grid" style="margin-bottom:1rem">
                                    <div class="form-group full">
                                        <label>Product / Book Name *</label>
                                        <input type="text" wire:model="np_name" placeholder="e.g. Blue Pen" />
                                        @error('np_name') <span style="color:#8b1a1a;font-size:.75rem">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Category *</label>
                                        <select wire:model="np_category_id" class="filter-select">
                                            <option value="">Select category…</option>
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('np_category_id') <span style="color:#8b1a1a;font-size:.75rem">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Author</label>
                                        <select wire:model="np_author_id" class="filter-select">
                                            <option value="">None</option>
                                            @foreach ($authors as $author)
                                                <option value="{{ $author->id }}">{{ $author->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Publisher</label>
                                        <select wire:model="np_publisher_id" class="filter-select">
                                            <option value="">None</option>
                                            @foreach ($publishers as $publisher)
                                                <option value="{{ $publisher->id }}">{{ $publisher->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Sale Price ($) *</label>
                                        <input type="number" wire:model="np_price" step="0.01" min="0" placeholder="0.00" />
                                        @error('np_price') <span style="color:#8b1a1a;font-size:.75rem">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>SKU <span style="font-size:.72rem;color:var(--text-muted)">(auto if blank)</span></label>
                                        <input type="text" wire:model="np_sku" placeholder="BKD-XXXXXXXX" />
                                        @error('np_sku') <span style="color:#8b1a1a;font-size:.75rem">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>ISBN</label>
                                        <input type="text" wire:model="np_isbn" placeholder="978-XXXXXXXXXX" />
                                        @error('np_isbn') <span style="color:#8b1a1a;font-size:.75rem">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Barcode</label>
                                        <input type="text" wire:model="np_barcode" placeholder="Scannable barcode" />
                                        @error('np_barcode') <span style="color:#8b1a1a;font-size:.75rem">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div style="display:flex;gap:.75rem;max-width:360px">
                                    <button type="button" wire:click="saveNewProduct" class="btn-primary" style="flex:1;padding:.55rem 1rem;font-size:.72rem">Create &amp; Add to Purchase</button>
                                    <button type="button" wire:click="cancelNewProduct" class="btn-ghost" style="flex:1;padding:.55rem 1rem;font-size:.72rem">Cancel</button>
                                </div>
                            </div>
                        @endif

                        @error('items') <div style="color:#8b1a1a;font-size:.8rem;margin-bottom:.75rem">{{ $message }}</div> @enderror

                        @if (count($items))
                            <table class="admin-table" style="margin-bottom:1rem">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th style="width:110px">Qty</th>
                                        <th style="width:130px">Rate</th>
                                        <th style="width:130px">Total</th>
                                        <th style="width:50px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $i => $item)
                                        <tr wire:key="purchase-item-{{ $i }}">
                                            <td style="font-weight:500">{{ $item['product_name'] }}</td>
                                            <td>
                                                <input type="number" wire:model="items.{{ $i }}.quantity" min="1"
                                                    style="width:100%;padding:.45rem .6rem" />
                                                @error("items.$i.quantity") <span style="color:#8b1a1a;font-size:.7rem">{{ $message }}</span> @enderror
                                            </td>
                                            <td>
                                                <input type="number" wire:model="items.{{ $i }}.purchase_price" step="0.01" min="0"
                                                    style="width:100%;padding:.45rem .6rem" />
                                                @error("items.$i.purchase_price") <span style="color:#8b1a1a;font-size:.7rem">{{ $message }}</span> @enderror
                                            </td>
                                            <td>${{ number_format($this->lineTotals[$i] ?? 0, 2) }}</td>
                                            <td>
                                                <button type="button" wire:click="removeItem({{ $i }})"
                                                    style="padding:.4rem .65rem;border:1px solid #8b1a1a;color:#8b1a1a;background:none;cursor:pointer;font-family:inherit;font-size:.75rem">✕</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p style="color:var(--text-muted);font-size:.85rem;margin-bottom:1rem">No products added yet — search above to add one.</p>
                        @endif

                        <div style="display:flex;justify-content:flex-end">
                            <div style="min-width:260px">
                                <div style="display:flex;justify-content:space-between;padding:.4rem 0;font-size:.85rem">
                                    <span style="color:var(--text-muted)">Subtotal</span>
                                    <span>${{ number_format($this->grandTotal, 2) }}</span>
                                </div>
                                <div style="display:flex;justify-content:space-between;padding:.4rem 0;font-size:1rem;font-weight:600;border-top:1px solid var(--border)">
                                    <span>Grand Total</span>
                                    <span>${{ number_format($this->grandTotal, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display:flex;gap:.75rem;max-width:400px">
                        <button type="submit" class="btn-primary" style="flex:1" wire:loading.attr="disabled" wire:target="save">Complete Purchase</button>
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
