<div>
    <div class="shop-layout">

        {{-- ── FILTERS SIDEBAR ── --}}
        <aside class="filters-sidebar">
            <div class="filter-header">
                <h3>Filters</h3>
                <button class="filter-clear" wire:click="clearFilters">Clear All</button>
            </div>

            {{-- Search --}}
            <div class="filter-group">
                <h4>Search</h4>
                <input type="text" wire:model.debounce.400ms="search" placeholder="Search products…" class="coupon-input"
                    style="width:100%;padding:.65rem .85rem" />
            </div>

            {{-- Category --}}
            <div class="filter-group">
                <h4>Category</h4>
                <label class="filter-option">
                    <input type="radio" wire:model="category" value=""> All
                </label>
                @foreach ($categories as $cat)
                    <label class="filter-option">
                        <input type="radio" wire:model="category" value="{{ $cat->slug }}">
                        {{ $cat->name }}
                    </label>
                @endforeach
            </div>

            {{-- Price Range --}}
            <div class="filter-group">
                <h4>Price Range</h4>
                <div class="price-range">
                    <span>$0 — ${{ $maxPrice }}</span>
                    <input type="range" min="0" max="3000" step="50" wire:model.lazy="maxPrice" />
                </div>
            </div>

            {{-- Author --}}
            <div class="filter-group">
                <h4>Author</h4>
                <select class="filter-select" wire:model="author" style="width:100%">
                    <option value="">All Authors</option>
                    @foreach ($authors as $a)
                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Publisher --}}
            <div class="filter-group">
                <h4>Publisher</h4>
                <select class="filter-select" wire:model="publisher" style="width:100%">
                    <option value="">All Publishers</option>
                    @foreach ($publishers as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sort --}}
            <div class="filter-group">
                <h4>Sort By</h4>
                <select class="filter-select" wire:model="sort">
                    <option value="featured">Featured</option>
                    <option value="newest">Newest First</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                    <option value="rating">Best Rated</option>
                </select>
            </div>
        </aside>

        {{-- ── PRODUCTS AREA ── --}}
        <div class="shop-main">
            <div class="shop-toolbar">
                <p class="product-count">
                    Showing {{ $products->total() }} product{{ $products->total() !== 1 ? 's' : '' }}
                </p>
            </div>

            {{-- Loading state --}}
            <div wire:loading.flex wire:target="category,maxPrice,author,publisher,sort,search,clearFilters"
                style="position:absolute;inset:0;background:rgba(var(--bg-rgb),.7);z-index:5;
                        align-items:center;justify-content:center">
                <div class="spinner"></div>
            </div>

            @if ($products->isEmpty())
                <div style="text-align:center;padding:5rem 2rem;color:var(--text-muted)">
                    <p style="font-size:1.1rem;margin-bottom:1rem">No products found matching your filters.</p>
                    <button class="btn-ghost" wire:click="clearFilters">Clear Filters</button>
                </div>
            @else
                <div class="products-grid">
                    @foreach ($products as $product)
                        @livewire('product-card', ['product' => $product], key($product->id))
                    @endforeach
                </div>

                <div class="pagination" style="margin-top:3rem">
                    {{ $products->links('livewire.partials.pagination') }}
                </div>
            @endif
        </div>
    </div>
</div>
