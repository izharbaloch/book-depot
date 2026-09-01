@extends('layouts.app')
@section('title', 'Book Depot — Discover Your Next Great Read')

@section('content')

    {{-- ── HERO ── --}}
    <section class="hero" id="home">
        <div class="hero-bg">
            <div class="hero-img hero-img-1"></div>
            <div class="hero-img hero-img-2"></div>
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <p class="hero-eyebrow animate-up">Your Neighborhood Book Depot</p>
            <h1 class="hero-headline animate-up delay-1">Discover Your<br /><em>Next Great Read</em></h1>
            <p class="hero-sub animate-up delay-2">
                Textbooks, novels, Islamic literature, and stationery.<br />In store and online, all from one shelf.
            </p>
            <div class="hero-ctas animate-up delay-3">
                <a href="{{ route('shop') }}" class="btn-primary">Shop Now</a>
                <a href="#categories" class="btn-ghost-white">Explore</a>
            </div>
        </div>
        <div class="hero-scroll-indicator">
            <span>Scroll</span>
            <div class="scroll-line"></div>
        </div>
    </section>

    {{-- ── MARQUEE ── --}}
    <div class="marquee-strip" aria-hidden="true">
        <div class="marquee-track">
            @foreach (['NEW ARRIVALS', 'THOUSANDS OF TITLES', 'FREE SHIPPING OVER $150', 'SCHOOL & UNIVERSITY BOOKS'] as $t)
                <span>{{ $t }}</span><span class="dot">◆</span>
            @endforeach
            @foreach (['NEW ARRIVALS', 'THOUSANDS OF TITLES', 'FREE SHIPPING OVER $150', 'SCHOOL & UNIVERSITY BOOKS'] as $t)
                <span>{{ $t }}</span><span class="dot">◆</span>
            @endforeach
        </div>
    </div>

    {{-- ── CATEGORIES ── --}}
    <section class="section categories-section" id="categories">
        <div class="container">
            <div class="section-header">
                <div>
                    <p class="section-eyebrow">Browse by</p>
                    <h2 class="section-title">Categories</h2>
                </div>
            </div>
            <div class="categories-grid">
                @foreach ($categories as $i => $cat)
                    <a href="{{ route('shop', ['category' => $cat->slug]) }}"
                        class="category-card reveal {{ $i > 0 ? 'delay-' . $i : '' }}">
                        <div class="cat-img"
                            style="background:{{ ['linear-gradient(145deg,#1b3a5c,#2c5580)', 'linear-gradient(145deg,#e8871e,#c96f12)', 'linear-gradient(145deg,#12283f,#1b3a5c)', 'linear-gradient(145deg,#3e6690,#1b3a5c)'][$loop->index % 4] }}">
                            @if ($cat->image)
                                <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}"
                                    style="width:100%;height:100%;object-fit:cover;opacity:.7" />
                            @else
                                <div class="cat-pattern"></div>
                            @endif
                        </div>
                        <div class="cat-info">
                            <h3>{{ $cat->name }}</h3>
                            <span>{{ $cat->products()->active()->count() }} Items →</span>
                        </div>
                        <div class="cat-hover-line"></div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── TRENDING ── --}}
    <section class="section products-section">
        <div class="container">
            <div class="section-header">
                <div>
                    <p class="section-eyebrow">Hand-picked</p>
                    <h2 class="section-title">Trending Now</h2>
                </div>
                <a href="{{ route('shop') }}" class="section-link">View All →</a>
            </div>
            <div class="products-grid">
                @foreach ($trendingProducts as $product)
                    @livewire('product-card', ['product' => $product], key('trend-' . $product->id))
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── NEW ARRIVALS SLIDER ── --}}
    <section class="section arrivals-section">
        <div class="container">
            <div class="section-header">
                <div>
                    <p class="section-eyebrow">Just In</p>
                    <h2 class="section-title">New Arrivals</h2>
                </div>
                <div class="slider-controls">
                    <button class="slider-btn" id="sliderPrev" aria-label="Previous">←</button>
                    <button class="slider-btn" id="sliderNext" aria-label="Next">→</button>
                </div>
            </div>
            <div class="slider-wrapper">
                <div class="slider-track" id="arrivalSlider">
                    @foreach ($newArrivals as $product)
                        <div style="flex:0 0 calc(25% - 1.125rem);min-width:0">
                            @livewire('product-card', ['product' => $product], key('new-' . $product->id))
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ── PROMO BANNER ── --}}
    <section class="promo-banner">
        <div class="promo-bg"></div>
        <div class="promo-content">
            <p class="promo-eyebrow">Limited Time</p>
            <h2 class="promo-title">Back to School<br /><em>{{ date('Y') }} Sale</em></h2>
            <p class="promo-sub">Save on textbooks, stationery, and this season's must-reads.</p>
            <a href="{{ route('shop', ['sale' => 1]) }}" class="btn-primary">Shop the Sale</a>
        </div>
    </section>

    {{-- ── BEST SELLERS ── --}}
    <section class="section products-section">
        <div class="container">
            <div class="section-header">
                <div>
                    <p class="section-eyebrow">Fan Favourites</p>
                    <h2 class="section-title">Best Sellers</h2>
                </div>
                <a href="{{ route('shop') }}" class="section-link">View All →</a>
            </div>
            <div class="products-grid">
                @foreach ($bestSellers as $product)
                    @livewire('product-card', ['product' => $product], key('best-' . $product->id))
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── SCHOOL ESSENTIALS ── --}}
    @if ($schoolEssentials->count())
        <section class="section products-section" style="background:var(--bg-alt)">
            <div class="container">
                <div class="section-header">
                    <div>
                        <p class="section-eyebrow">Back to Basics</p>
                        <h2 class="section-title">School Essentials</h2>
                    </div>
                    <a href="{{ route('shop', ['category' => 'stationery']) }}" class="section-link">View All →</a>
                </div>
                <div class="products-grid">
                    @foreach ($schoolEssentials as $product)
                        @livewire('product-card', ['product' => $product], key('essential-' . $product->id))
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ── WHY CHOOSE US ── --}}
    <section class="section why-section">
        <div class="container">
            <div class="section-header center">
                <p class="section-eyebrow">Our Promise</p>
                <h2 class="section-title">Why Book Depot?</h2>
            </div>
            <div class="why-grid">
                @foreach ([['icon' => 'shield', 'title' => 'Genuine Titles', 'desc' => 'Every book sourced directly from publishers and verified for quality before it reaches our shelves.'], ['icon' => 'truck', 'title' => 'Fast Delivery', 'desc' => 'Reliable shipping with real-time order tracking, delivered to your door in 2–5 days.'], ['icon' => 'refresh', 'title' => 'Easy Returns', 'desc' => '7-day hassle-free returns on damaged or incorrect items — no questions asked.'], ['icon' => 'card', 'title' => 'Secure Payments', 'desc' => 'Pay online or cash on delivery, with the same secure checkout for every order.']] as $i => $w)
                    <div class="why-card reveal {{ $i > 0 ? 'delay-' . $i : '' }}">
                        <div class="why-icon">
                            @if ($w['icon'] === 'shield')
                                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.2"
                                    viewBox="0 0 24 24">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                </svg>
                            @elseif($w['icon'] === 'truck')
                                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.2"
                                    viewBox="0 0 24 24">
                                    <rect x="1" y="3" width="15" height="13" />
                                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                                    <circle cx="5.5" cy="18.5" r="2.5" />
                                    <circle cx="18.5" cy="18.5" r="2.5" />
                                </svg>
                            @elseif($w['icon'] === 'refresh')
                                <svg width="32" height="32" fill="none" stroke="currentColor"
                                    stroke-width="1.2" viewBox="0 0 24 24">
                                    <polyline points="1 4 1 10 7 10" />
                                    <path d="M3.51 15a9 9 0 1 0 .49-4.54" />
                                </svg>
                            @else
                                <svg width="32" height="32" fill="none" stroke="currentColor"
                                    stroke-width="1.2" viewBox="0 0 24 24">
                                    <rect x="1" y="4" width="22" height="16" rx="2" />
                                    <line x1="1" y1="10" x2="23" y2="10" />
                                </svg>
                            @endif
                        </div>
                        <h3>{{ $w['title'] }}</h3>
                        <p>{{ $w['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── TESTIMONIALS ── --}}
    <section class="section testimonials-section">
        <div class="container">
            <div class="section-header center">
                <p class="section-eyebrow">Real Reviews</p>
                <h2 class="section-title">What They Say</h2>
            </div>
            <div class="testimonials-grid">
                @foreach ([['name' => 'Amara K.', 'loc' => 'Lahore', 'text' => '"Found every textbook on my son\'s list in one order, and it arrived well packaged and on time."', 'color' => '#1b3a5c', 'initial' => 'A'], ['name' => 'Lucas M.', 'loc' => 'Karachi', 'text' => '"Book Depot has the best selection of novels and Islamic books in the city. My go-to store now."', 'color' => '#e8871e', 'initial' => 'L'], ['name' => 'Sofia R.', 'loc' => 'Islamabad', 'text' => '"Ordering online was effortless and the prices were fair. Already placed two more orders."', 'color' => '#3e6690', 'initial' => 'S']] as $i => $t)
                    <div class="testimonial-card reveal {{ $i > 0 ? 'delay-' . $i : '' }}">
                        <div class="stars">★★★★★</div>
                        <p class="review-text">{{ $t['text'] }}</p>
                        <div class="reviewer">
                            <div class="reviewer-avatar"
                                style="background:{{ $t['color'] }};color:#fff">
                                {{ $t['initial'] }}</div>
                            <div>
                                <strong>{{ $t['name'] }}</strong>
                                <span>Verified Buyer · {{ $t['loc'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── NEWSLETTER ── --}}
    <section class="newsletter-section" id="contact">
        <div class="newsletter-inner">
            <div class="newsletter-text">
                <p class="section-eyebrow" style="color:rgba(255,255,255,.6)">Stay Inspired</p>
                <h2>Join the Book Depot<br /><em>Reader's Circle</em></h2>
                <p>New arrivals, seasonal discounts, and reading recommendations — delivered to your inbox.</p>
            </div>
            <form class="newsletter-form" onsubmit="handleNewsletter(event)">
                <div class="newsletter-input-wrap">
                    <input type="email" placeholder="your@email.com" required aria-label="Email address" />
                    <button type="submit" class="btn-primary">Subscribe</button>
                </div>
                <p class="newsletter-note">No spam. Unsubscribe anytime. ✦</p>
            </form>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        // Slider
        let sliderIdx = 0;

        function updateSlider() {
            const track = document.getElementById('arrivalSlider');
            if (!track) return;
            const cards = track.querySelectorAll('[style*="flex"]');
            if (!cards.length) return;
            const w = window.innerWidth;
            const vis = w < 600 ? 1 : w < 900 ? 2 : w < 1100 ? 3 : 4;
            const max = Math.max(0, cards.length - vis);
            sliderIdx = Math.min(sliderIdx, max);
            const cardW = cards[0].offsetWidth + 24;
            track.style.transform = `translateX(-${sliderIdx * cardW}px)`;
        }
        document.getElementById('sliderNext')?.addEventListener('click', () => {
            sliderIdx++;
            updateSlider();
        });
        document.getElementById('sliderPrev')?.addEventListener('click', () => {
            sliderIdx = Math.max(0, sliderIdx - 1);
            updateSlider();
        });
        window.addEventListener('resize', updateSlider);

        // Hero cycle
        (function() {
            const i1 = document.querySelector('.hero-img-1');
            const i2 = document.querySelector('.hero-img-2');
            if (!i1 || !i2) return;
            let a = 1;
            setInterval(() => {
                if (a === 1) {
                    i1.style.opacity = '0';
                    i2.style.opacity = '1';
                    a = 2;
                } else {
                    i1.style.opacity = '1';
                    i2.style.opacity = '0';
                    a = 1;
                }
            }, 5000);
        })();

        // Newsletter
        function handleNewsletter(e) {
            e.preventDefault();
            showToast('✓ Welcome to the Book Depot Reader\'s Circle!', 'success');
            e.target.reset();
        }

        // Reveal
        const revealObs = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) e.target.classList.add('visible');
            });
        }, {
            threshold: 0.08,
            rootMargin: '0px 0px -40px 0px'
        });
        document.querySelectorAll('.reveal').forEach(el => revealObs.observe(el));
    </script>
@endpush
