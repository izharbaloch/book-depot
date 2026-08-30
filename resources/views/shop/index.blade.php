@extends('layouts.app')
@section('title', 'Shop — Book Depot')

@section('content')
    <div class="page-hero-mini">
        <div class="container">
            <p class="breadcrumb"><a href="{{ route('home') }}">Home</a> / Shop</p>
            <h1>Shop</h1>
        </div>
    </div>

    <section class="shop-section">
        <div class="container">
            @livewire('shop-filter', ['initialCategory' => request('category', '')])
        </div>
    </section>
@endsection
