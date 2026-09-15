@section('title', 'Products')
@section('page_title', 'Product Management')

<div>
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">All Products ({{ $products->total() }})</h3>
            @unless($showForm)
                <button type="button" wire:click="openCreate" class="btn-primary" style="padding:.6rem 1.4rem;font-size:.72rem">+ Add Product</button>
            @endunless
        </div>

        {{-- ── Filters ── --}}
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem">
            <input type="text" wire:model.live.debounce.400ms="search"
                placeholder="Search name, SKU, ISBN, barcode, author, publisher…"
                class="form-input" style="flex:1;min-width:220px" />

            <select wire:model.live="categoryFilter" class="filter-select">
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="authorFilter" class="filter-select">
                <option value="">All Authors</option>
                @foreach ($authors as $author)
                    <option value="{{ $author->id }}">{{ $author->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="publisherFilter" class="filter-select">
                <option value="">All Publishers</option>
                @foreach ($publishers as $publisher)
                    <option value="{{ $publisher->id }}">{{ $publisher->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="stockFilter" class="filter-select">
                <option value="">All Stock</option>
                <option value="low">Low Stock</option>
                <option value="out">Out of Stock</option>
            </select>

            <select wire:model.live="statusFilter" class="filter-select">
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Hidden</option>
            </select>

            <button type="button" wire:click="clearFilters" class="btn-ghost" style="padding:.6rem 1.25rem;font-size:.72rem">Clear</button>
        </div>

        @if (session('error'))
            <div class="alert alert-error" style="margin-top:0;margin-bottom:1.5rem">{{ session('error') }}</div>
        @endif

        {{-- ── Same-page form: Add / Edit ── --}}
        @if ($showForm)
            <div class="admin-card" style="border-color:var(--text);margin-bottom:1.5rem">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">{{ $editingId ? 'Edit Product' : 'Add New Product' }}</h3>
                </div>

                <form wire:submit.prevent="save">
                    <div class="split-layout">
                        {{-- Left column --}}
                        <div>
                            <div class="admin-form-section">
                                <h4>Basic Information</h4>
                                <div class="form-grid" style="margin-bottom:1rem">
                                    <div class="form-group full">
                                        <label>Product / Book Name *</label>
                                        <input type="text" wire:model="name" placeholder="e.g. Pride and Prejudice" autofocus />
                                        @error('name') <span style="color:var(--danger);font-size:.75rem">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Category *</label>
                                        <select wire:model="category_id" class="filter-select">
                                            <option value="">Select category…</option>
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id') <span style="color:var(--danger);font-size:.75rem">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Author</label>
                                        <select wire:model="author_id" class="filter-select">
                                            <option value="">None</option>
                                            @foreach ($authors as $author)
                                                <option value="{{ $author->id }}">{{ $author->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Publisher</label>
                                        <select wire:model="publisher_id" class="filter-select">
                                            <option value="">None</option>
                                            @foreach ($publishers as $publisher)
                                                <option value="{{ $publisher->id }}">{{ $publisher->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>SKU <span style="font-size:.72rem;color:var(--text-muted)">(auto if blank)</span></label>
                                        <input type="text" wire:model="sku" placeholder="BKD-XXXXXXXX" />
                                        @error('sku') <span style="color:var(--danger);font-size:.75rem">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>ISBN</label>
                                        <input type="text" wire:model="isbn" placeholder="978-XXXXXXXXXX" />
                                        @error('isbn') <span style="color:var(--danger);font-size:.75rem">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Barcode</label>
                                        <input type="text" wire:model="barcode" placeholder="Scannable barcode" />
                                        @error('barcode') <span style="color:var(--danger);font-size:.75rem">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom:1rem">
                                    <label>Short Description</label>
                                    <textarea wire:model="short_description" rows="2"
                                        style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.9rem;resize:vertical"
                                        placeholder="Brief one-line summary…"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Full Description</label>
                                    <textarea wire:model="description" rows="5"
                                        style="width:100%;padding:.75rem 1rem;border:1px solid var(--border);background:var(--bg);color:var(--text);font-family:inherit;font-size:.9rem;resize:vertical"
                                        placeholder="Detailed description…"></textarea>
                                </div>
                            </div>

                            <div class="admin-form-section">
                                <h4>Pricing & Inventory</h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Sale Price ($) *</label>
                                        <input type="number" wire:model="price" step="0.01" min="0" placeholder="0.00" />
                                        @error('price') <span style="color:var(--danger);font-size:.75rem">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Discount Price ($) <span style="font-size:.72rem;color:var(--text-muted)">(optional)</span></label>
                                        <input type="number" wire:model="sale_price" step="0.01" min="0" placeholder="Leave blank if none" />
                                        @error('sale_price') <span style="color:var(--danger);font-size:.75rem">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Purchase / Cost Price ($)</label>
                                        <input type="number" wire:model="cost_price" step="0.01" min="0" placeholder="0.00" />
                                    </div>
                                    <div class="form-group">
                                        <label>Stock Quantity *</label>
                                        <input type="number" wire:model="stock" min="0" />
                                        @error('stock') <span style="color:var(--danger);font-size:.75rem">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Minimum Stock Level *</label>
                                        <input type="number" wire:model="min_stock_level" min="0" />
                                        @error('min_stock_level') <span style="color:var(--danger);font-size:.75rem">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="form-group">
                                        <label>Weight (kg) <span style="font-size:.72rem;color:var(--text-muted)">(optional)</span></label>
                                        <input type="number" wire:model="weight" step="0.01" min="0" placeholder="0.00" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right column --}}
                        <div>
                            <div class="admin-card">
                                <div class="admin-form-section">
                                    <h4>Main Product Image</h4>
                                    @if ($image)
                                        <div style="margin-bottom:1rem">
                                            <img src="{{ $image->temporaryUrl() }}" alt="Preview" style="width:100%;aspect-ratio:3/4;object-fit:cover;border:1px solid var(--border)" />
                                        </div>
                                    @elseif ($currentImage)
                                        <div style="margin-bottom:1rem">
                                            <img src="{{ asset('storage/' . $currentImage) }}" alt="Current image" style="width:100%;aspect-ratio:3/4;object-fit:cover;border:1px solid var(--border)" />
                                        </div>
                                    @endif
                                    <input type="file" wire:model="image" accept="image/*"
                                        style="width:100%;padding:.65rem;border:1px solid var(--border);background:var(--bg);font-family:inherit;font-size:.82rem" />
                                    <p style="font-size:.72rem;color:var(--text-muted);margin-top:.5rem">JPG, PNG, WEBP. Max 3MB.</p>
                                    @error('image') <span style="color:var(--danger);font-size:.75rem">{{ $message }}</span> @enderror
                                </div>

                                <div class="admin-form-section">
                                    <h4>Gallery Images <span style="font-size:.72rem;color:var(--text-muted)">(optional, replaces existing)</span></h4>
                                    <input type="file" wire:model="gallery" accept="image/*" multiple
                                        style="width:100%;padding:.65rem;border:1px solid var(--border);background:var(--bg);font-family:inherit;font-size:.82rem" />
                                </div>
                            </div>

                            <div class="admin-card">
                                <div class="admin-form-section" style="border-bottom:none;margin-bottom:0;padding-bottom:0">
                                    <h4>Product Flags</h4>
                                    <label style="display:flex;align-items:center;gap:.75rem;font-size:.85rem;cursor:pointer;padding:.5rem 0;border-bottom:1px solid var(--border)">
                                        <input type="checkbox" wire:model="is_active" style="accent-color:var(--text);width:16px;height:16px" />
                                        Active (visible in shop)
                                    </label>
                                    <label style="display:flex;align-items:center;gap:.75rem;font-size:.85rem;cursor:pointer;padding:.5rem 0;border-bottom:1px solid var(--border)">
                                        <input type="checkbox" wire:model="is_featured" style="accent-color:var(--text);width:16px;height:16px" />
                                        Featured on Homepage
                                    </label>
                                    <label style="display:flex;align-items:center;gap:.75rem;font-size:.85rem;cursor:pointer;padding:.5rem 0;border-bottom:1px solid var(--border)">
                                        <input type="checkbox" wire:model="is_trending" style="accent-color:var(--text);width:16px;height:16px" />
                                        Show in Trending
                                    </label>
                                    <label style="display:flex;align-items:center;gap:.75rem;font-size:.85rem;cursor:pointer;padding:.5rem 0;border-bottom:1px solid var(--border)">
                                        <input type="checkbox" wire:model="is_bestseller" style="accent-color:var(--text);width:16px;height:16px" />
                                        Mark as Best Seller
                                    </label>
                                    <label style="display:flex;align-items:center;gap:.75rem;font-size:.85rem;cursor:pointer;padding:.5rem 0">
                                        <input type="checkbox" wire:model="is_new" style="accent-color:var(--text);width:16px;height:16px" />
                                        Mark as New Arrival
                                    </label>
                                </div>
                            </div>

                            <div style="display:flex;gap:.75rem">
                                <button type="submit" class="btn-primary" style="flex:1" wire:loading.attr="disabled" wire:target="save">
                                    {{ $editingId ? 'Update Product' : 'Create Product' }}
                                </button>
                                <button type="button" wire:click="cancel" class="btn-ghost" style="flex:1">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        {{-- ── Listing ── --}}
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Author / Publisher</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr wire:key="product-{{ $product->id }}">
                        <td>
                            <div style="display:flex;align-items:center;gap:.85rem">
                                <div class="admin-thumb">
                                    @if ($product->image)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" />
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight:500;font-size:.88rem">{{ $product->name }}</div>
                                    <div style="font-size:.72rem;color:var(--text-muted)">{{ $product->sku }}@if($product->isbn) &middot; ISBN {{ $product->isbn }}@endif</div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--text-muted);font-size:.82rem">
                            {{ $product->author?->name ?? '—' }}<br>
                            <span style="font-size:.75rem">{{ $product->publisher?->name ?? '—' }}</span>
                        </td>
                        <td style="color:var(--text-muted)">{{ $product->category->name }}</td>
                        <td>
                            @if ($product->is_on_sale)
                                <span style="color:var(--accent-dark);font-weight:500">${{ number_format($product->sale_price, 2) }}</span>
                                <span style="text-decoration:line-through;color:var(--text-muted);font-size:.78rem;margin-left:.25rem">${{ number_format($product->price, 2) }}</span>
                            @else
                                ${{ number_format($product->price, 2) }}
                            @endif
                        </td>
                        <td>
                            <span style="color:{{ $product->stock > $product->min_stock_level ? 'var(--text)' : ($product->stock > 0 ? 'var(--warning)' : 'var(--danger)') }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $product->is_active ? 'badge-active' : 'badge-hidden' }}">
                                {{ $product->is_active ? 'Active' : 'Hidden' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:.5rem">
                                <button type="button" wire:click="openEdit({{ $product->id }})" class="btn-ghost" style="padding:.35rem .75rem;font-size:.7rem">Edit</button>
                                <button type="button" wire:click="delete({{ $product->id }})" wire:confirm="Delete this product?" class="btn-danger">Delete</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:3rem;color:var(--text-muted)">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:1.5rem">{{ $products->links() }}</div>
    </div>
</div>
