@extends('layouts.app')
@section('title', 'Wishlist — Book Depot')

@section('content')
    <div class="page-hero-mini">
        <div class="container">
            <p class="breadcrumb"><a href="{{ route('home') }}">Home</a> / <a href="{{ route('dashboard') }}">Account</a>
                / Wishlist</p>
            <h1>My Wishlist</h1>
        </div>
    </div>

    <section style="padding:3rem 0 6rem">
        <div class="container">
            @if ($products->isEmpty())
                <div style="text-align:center;padding:5rem 2rem;color:var(--text-muted)">
                    <div style="font-size:3rem;margin-bottom:1rem;opacity:.3">♡</div>
                    <p style="font-size:1.1rem;margin-bottom:1.5rem">Your wishlist is empty.</p>
                    <a href="{{ route('shop') }}" class="btn-primary">Discover Products</a>
                </div>
            @else
                <div class="products-grid">
                    @foreach ($products as $product)
                        @livewire('product-card', ['product' => $product], key('wl-' . $product->id))
                    @endforeach
                </div>
                <div style="margin-top:2rem">{{ $products->links() }}</div>
            @endif
        </div>
    </section>
@endsection
