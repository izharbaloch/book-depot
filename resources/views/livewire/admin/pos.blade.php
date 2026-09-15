@section('title', 'POS')
@section('page_title', 'Point of Sale')

<div>
    @if ($completedSaleId)
        <div class="admin-card" style="max-width:480px;margin:2rem auto;text-align:center">
            <div style="font-size:2.5rem;margin-bottom:.5rem">✅</div>
            <h3 class="admin-card-title" style="justify-content:center;margin-bottom:.5rem">Sale Complete</h3>
            <p style="color:var(--text-muted);margin-bottom:1.5rem">The sale has been recorded and stock updated.</p>
            <div style="display:flex;gap:.75rem">
                <a href="{{ route('admin.pos.receipt', $completedSaleId) }}" target="_blank" class="btn-primary" style="flex:1;text-align:center">Print Receipt</a>
                <button type="button" wire:click="startNewSale" class="btn-ghost" style="flex:1">New Sale</button>
            </div>
        </div>
    @else
        <div class="split-layout">
            {{-- ── Left: search + product grid ── --}}
            <div class="admin-card">
                <h3 class="admin-card-title" style="margin-bottom:1rem">Search / Scan Barcode</h3>
                <input type="text" wire:model.live.debounce.200ms="search" wire:keydown.enter.prevent="scanEnter"
                    placeholder="Search by name, SKU, ISBN, barcode, author, publisher…" autofocus
                    class="form-input" style="font-size:1rem;padding:.85rem 1rem;margin-bottom:1.25rem" />

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($results as $product)
                            <tr wire:key="pos-result-{{ $product->id }}">
                                <td>
                                    <div style="display:flex;align-items:center;gap:.75rem">
                                        <div class="admin-thumb">
                                            @if ($product->image)
                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" />
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-weight:500;font-size:.88rem">{{ $product->name }}</div>
                                            <div style="font-size:.72rem;color:var(--text-muted)">{{ $product->sku }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>${{ number_format($product->current_price, 2) }}</td>
                                <td style="color:{{ $product->stock > 0 ? 'var(--text)' : 'var(--danger)' }}">{{ $product->stock }}</td>
                                <td>
                                    <button type="button" wire:click="addToCart({{ $product->id }})" class="btn-ghost"
                                        style="padding:.35rem .9rem;font-size:.72rem" @disabled($product->stock <= 0)>Add</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center;padding:2rem;color:var(--text-muted)">
                                    {{ trim($search) === '' ? 'Start typing to search products…' : 'No products found.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── Right: cart + customer + payment ── --}}
            <div>
                <div class="admin-card">
                    <h3 class="admin-card-title" style="margin-bottom:1rem">Cart</h3>

                    @if ($cartError)
                        <div class="alert alert-error" style="margin-top:0;padding:.65rem .9rem">{{ $cartError }}</div>
                    @endif

                    @forelse ($cart as $item)
                        <div style="display:flex;align-items:center;gap:.6rem;padding:.6rem 0;border-bottom:1px solid var(--border)" wire:key="cart-item-{{ $item['product_id'] }}">
                            <div style="flex:1">
                                <div style="font-size:.85rem;font-weight:500">{{ $item['name'] }}</div>
                                <div style="font-size:.72rem;color:var(--text-muted)">${{ number_format($item['price'], 2) }} each</div>
                            </div>
                            <button type="button" wire:click="decrementQty({{ $item['product_id'] }})" class="btn-ghost" style="padding:.2rem .55rem;font-size:.8rem">−</button>
                            <span style="min-width:1.5rem;text-align:center">{{ $item['quantity'] }}</span>
                            <button type="button" wire:click="incrementQty({{ $item['product_id'] }})" class="btn-ghost" style="padding:.2rem .55rem;font-size:.8rem">+</button>
                            <div style="width:70px;text-align:right;font-size:.85rem">${{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                            <button type="button" wire:click="removeFromCart({{ $item['product_id'] }})" class="btn-icon-danger">✕</button>
                        </div>
                    @empty
                        <p style="color:var(--text-muted);font-size:.85rem;padding:1rem 0;text-align:center">Cart is empty.</p>
                    @endforelse

                    @if (count($cart))
                        <button type="button" wire:click="clearCart" class="btn-ghost" style="margin-top:.75rem;padding:.4rem .9rem;font-size:.72rem">Clear Cart</button>
                    @endif
                </div>

                <div class="admin-card">
                    <h3 class="admin-card-title" style="margin-bottom:1rem">Customer</h3>
                    @if ($customerId)
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <span>{{ $customerName }}</span>
                            <button type="button" wire:click="clearCustomer" class="btn-ghost" style="padding:.3rem .75rem;font-size:.7rem">Change</button>
                        </div>
                    @elseif ($showCustomerForm)
                        <div class="form-group" style="margin-bottom:.5rem">
                            <label>Name *</label>
                            <input type="text" wire:model="newCustomerName" />
                            @error('newCustomerName') <span style="color:var(--danger);font-size:.72rem">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group" style="margin-bottom:.75rem">
                            <label>Phone</label>
                            <input type="text" wire:model="newCustomerPhone" />
                        </div>
                        <div style="display:flex;gap:.5rem">
                            <button type="button" wire:click="saveNewCustomer" class="btn-primary" style="flex:1;padding:.5rem;font-size:.75rem">Save</button>
                            <button type="button" wire:click="cancelCustomerForm" class="btn-ghost" style="flex:1;padding:.5rem;font-size:.75rem">Cancel</button>
                        </div>
                    @else
                        <input type="text" wire:model.live.debounce.300ms="customerSearch" placeholder="Search customer, or leave blank for walk-in…"
                            class="form-input" style="margin-bottom:.5rem" />
                        @if ($customerSearch !== '')
                            <div style="max-height:150px;overflow-y:auto;margin-bottom:.5rem">
                                @foreach ($customerResults as $c)
                                    <div wire:click="selectCustomer({{ $c->id }})" style="padding:.5rem;cursor:pointer;font-size:.85rem;border-bottom:1px solid var(--border)">
                                        {{ $c->name }} @if($c->phone) &middot; {{ $c->phone }} @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <button type="button" wire:click="openCustomerForm" class="btn-ghost" style="padding:.4rem .9rem;font-size:.72rem">+ New Customer</button>
                        <p style="color:var(--text-muted);font-size:.75rem;margin-top:.5rem">No customer selected — will be recorded as Walk-in Customer.</p>
                    @endif
                </div>

                <div class="admin-card">
                    <h3 class="admin-card-title" style="margin-bottom:1rem">Payment</h3>

                    <div class="form-group" style="margin-bottom:.75rem">
                        <label>Discount ($)</label>
                        <input type="number" wire:model.live="discount" min="0" step="0.01" />
                    </div>

                    <div class="form-group" style="margin-bottom:.75rem">
                        <label>Payment Method</label>
                        <select wire:model.live="paymentMethod" class="filter-select" style="width:100%">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>

                    @if ($paymentMethod === 'cash')
                        <div class="form-group" style="margin-bottom:.75rem">
                            <label>Amount Paid ($)</label>
                            <input type="number" wire:model.live="amountPaid" min="0" step="0.01" />
                        </div>
                    @endif

                    <table style="width:100%;font-size:.9rem;margin-bottom:1rem">
                        <tr>
                            <td style="color:var(--text-muted)">Subtotal</td>
                            <td style="text-align:right">${{ number_format($this->subtotal, 2) }}</td>
                        </tr>
                        <tr style="font-weight:600;font-size:1.1rem">
                            <td>Total</td>
                            <td style="text-align:right">${{ number_format($this->total, 2) }}</td>
                        </tr>
                        @if ($paymentMethod === 'cash')
                            <tr>
                                <td style="color:var(--text-muted)">Change</td>
                                <td style="text-align:right">${{ number_format($this->change, 2) }}</td>
                            </tr>
                        @endif
                    </table>

                    <button type="button" wire:click="completeSale" class="btn-primary" style="width:100%;padding:.9rem"
                        wire:loading.attr="disabled" wire:target="completeSale" @disabled(count($cart) === 0)>
                        Complete Sale
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
