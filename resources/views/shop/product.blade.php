@extends('layouts.app')
@section('title', $product->name . ' — Book Depot')
@section('meta_desc', $product->short_description ?? $product->description)

@section('content')
    <section class="product-detail-section">
        <div class="container">
            <p class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> /
                <a href="{{ route('shop') }}">Shop</a> /
                <a href="{{ route('shop', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a> /
                <span>{{ $product->name }}</span>
            </p>

            <div class="product-detail-grid">
                {{-- ── Gallery ── --}}
                <div class="pdp-gallery">
                    <div class="pdp-thumbs" id="pdpThumbs">
                        <div class="pdp-thumb active" onclick="switchPdpImg(this, '{{ $product->image_url }}')">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy"
                                style="width:100%;height:100%;object-fit:cover" />
                        </div>
                        @foreach ($product->gallery_urls as $gImg)
                            <div class="pdp-thumb" onclick="switchPdpImg(this, '{{ $gImg }}')">
                                <img src="{{ $gImg }}" alt="{{ $product->name }}" loading="lazy"
                                    style="width:100%;height:100%;object-fit:cover" />
                            </div>
                        @endforeach
                    </div>
                    <div class="pdp-main-img" id="pdpMainImg">
                        <img id="pdpMainImgEl" src="{{ $product->image_url }}" alt="{{ $product->name }}"
                            style="width:100%;height:100%;object-fit:cover" />
                        <div class="pdp-zoom-hint">+ Click to Zoom</div>
                    </div>
                </div>

                {{-- ── Info ── --}}
                <div class="pdp-info">
                    <div class="pdp-category">{{ $product->category->name }}</div>
                    <h1 class="pdp-name">{{ $product->name }}</h1>

                    <div class="pdp-rating">
                        <span class="stars">
                            @for ($i = 1; $i <= 5; $i++)
                                {{ $i <= round($product->rating) ? '★' : '☆' }}
                            @endfor
                        </span>
                        <span>{{ $product->rating }} ({{ $product->review_count }} reviews)</span>
                    </div>

                    <div class="pdp-price">
                        @if ($product->is_on_sale)
                            <span class="price-sale">${{ number_format($product->sale_price, 2) }}</span>
                            <span class="price-original"
                                style="margin-left:.5rem">${{ number_format($product->price, 2) }}</span>
                            <span class="product-badge badge-sale"
                                style="position:static;margin-left:.75rem">-{{ $product->discount_percent }}%</span>
                        @else
                            ${{ number_format($product->price, 2) }}
                        @endif
                    </div>

                    <div class="pdp-divider"></div>
                    <p class="pdp-desc">{{ $product->description }}</p>

                    {{-- Livewire Add to Cart --}}
                    @livewire('add-to-cart', ['product' => $product])

                    {{-- Wishlist --}}
                    @auth
                        <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                            @csrf
                            <button type="submit" class="pdp-wishlist-btn">
                                <span>{{ auth()->user()->hasInWishlist($product->id) ? '♥' : '♡' }}</span>
                                {{ auth()->user()->hasInWishlist($product->id) ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="pdp-wishlist-btn">
                            <span>♡</span> Save to Wishlist
                        </a>
                    @endauth

                    {{-- Meta --}}
                    <div class="pdp-meta">
                        <div class="pdp-meta-row"><strong>SKU</strong><span>{{ $product->sku }}</span></div>
                        @if ($product->author)
                            <div class="pdp-meta-row"><strong>Author</strong><span>{{ $product->author->name }}</span></div>
                        @endif
                        @if ($product->publisher)
                            <div class="pdp-meta-row"><strong>Publisher</strong><span>{{ $product->publisher->name }}</span></div>
                        @endif
                        @if ($product->isbn)
                            <div class="pdp-meta-row"><strong>ISBN</strong><span>{{ $product->isbn }}</span></div>
                        @endif
                        <div class="pdp-meta-row"><strong>Shipping</strong><span>Free on orders over $150</span></div>
                        <div class="pdp-meta-row"><strong>Returns</strong><span>30-day free returns</span></div>
                        <div class="pdp-meta-row"><strong>Stock</strong>
                            <span style="color:{{ $product->stock > $product->min_stock_level ? '#1a5c2c' : '#8b1a1a' }}">
                                {{ $product->stock > $product->min_stock_level ? 'In Stock (' . $product->stock . ' available)' : ($product->stock > 0 ? 'Only ' . $product->stock . ' left!' : 'Out of Stock') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Reviews ── --}}
            @if ($product->reviews->count())
                <div class="related-section">
                    <div class="section-header">
                        <div>
                            <p class="section-eyebrow">Customer Feedback</p>
                            <h2 class="section-title">Reviews</h2>
                        </div>
                    </div>
                    <div class="testimonials-grid">
                        @foreach ($product->reviews->take(3) as $review)
                            <div class="testimonial-card">
                                <div class="stars">
                                    {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                                <p class="review-text">{{ $review->body }}</p>
                                <div class="reviewer">
                                    <div class="reviewer-avatar" style="background:var(--beige-mid);color:var(--grey-dark)">
                                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $review->user->name }}</strong>
                                        <span>{{ $review->created_at->format('M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── Related Products ── --}}
            @if ($relatedProducts->count())
                <div class="related-section">
                    <div class="section-header">
                        <div>
                            <p class="section-eyebrow">You May Also Like</p>
                            <h2 class="section-title">Related Products</h2>
                        </div>
                    </div>
                    <div class="products-grid">
                        @foreach ($relatedProducts as $rp)
                            @livewire('product-card', ['product' => $rp], key('rel-' . $rp->id))
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    @push('scripts')
        <script>
            function switchPdpImg(thumb, src) {
                document.querySelectorAll('.pdp-thumb').forEach(t => t.classList.remove('active'));
                thumb.classList.add('active');
                document.getElementById('pdpMainImgEl').src = src;
            }
        </script>
    @endpush
@endsection
