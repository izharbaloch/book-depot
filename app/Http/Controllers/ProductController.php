<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        return view('shop.product', [
            'product'         => $product->load('category', 'author', 'publisher', 'reviews.user'),
            'relatedProducts' => $product->related(4),
        ]);
    }

    public function quickView(Product $product): JsonResponse
    {
        $html = view('livewire.partials.quick-view', compact('product'))->render();
        return response()->json(['html' => $html]);
    }
}
