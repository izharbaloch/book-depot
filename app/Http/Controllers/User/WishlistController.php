<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function index()
    {
        $products = auth()->user()->wishlistProducts()->with('category')->paginate(12);
        return view('user.wishlist', compact('products'));
    }

    public function toggle(Product $product)
    {
        $user = auth()->user();
        $wl   = Wishlist::where('user_id', $user->id)->where('product_id', $product->id)->first();

        if ($wl) {
            $wl->delete();
            $msg = 'Removed from wishlist';
        } else {
            Wishlist::create(['user_id' => $user->id, 'product_id' => $product->id]);
            $msg = '♡ Added to wishlist';
        }

        if (request()->ajax()) {
            return response()->json(['message' => $msg, 'in_wishlist' => !$wl]);
        }
        return back()->with('success', $msg);
    }
}
