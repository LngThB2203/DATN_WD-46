<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::with([
            'galleries',
            'category',
            'reviews.user',
            'warehouseProducts',
            'variants.size',
            'variants.scent',
            'variants.concentration',
        ])->where('slug', $slug)->firstOrFail();

        $reviews = $product->reviews()->where('status', 1)->latest()->get();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(6)
            ->get();

        return view('client.product', [
            'product' => $product,
            'galleries' => $product->galleries,
            'reviews' => $reviews,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
