<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductDetailController extends Controller
{
    public function show(string $slug)
    {
        // Tìm sản phẩm theo slug hoặc ID (fallback)
        $product = Product::with([
            'galleries',
            'category',
            'reviews.user',
        ])->where(function($query) use ($slug) {
            $query->where('slug', $slug)
                  ->orWhere('id', $slug);
        })->firstOrFail();

        // Lấy tất cả reviews
        $reviews = $product->reviews()->with('user')->latest()->get();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(6)
            ->get();

        return view('client.product', [
            'product'         => $product,
            'galleries'       => $product->galleries,
            'reviews'         => $reviews,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}

