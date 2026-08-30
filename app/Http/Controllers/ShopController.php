<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only([
            'category',
            'min_price',
            'max_price',
            'author',
            'publisher',
            'sort',
            'sale',
            'search'
        ]);

        $products = Product::filter($filters)
            ->with('category')
            ->paginate(12)
            ->withQueryString();

        return view('shop.index', [
            'products'   => $products,
            'categories' => Category::active()->get(),
            'filters'    => $filters,
        ]);
    }
}
