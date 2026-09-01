<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'trendingProducts'  => Product::trending()->with('category')->limit(8)->get(),
            'newArrivals'       => Product::newArrivals()->with('category')->limit(8)->get(),
            'bestSellers'       => Product::bestseller()->with('category')->limit(4)->get(),
            'schoolEssentials'  => Product::active()
                ->whereHas('category', fn($q) => $q->whereIn('slug', ['stationery', 'office-supplies']))
                ->with('category')->limit(8)->get(),
            'categories'        => Category::active()->get(),
        ]);
    }
}
