<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProductDetailController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::with(['galleries', 'category', 'reviews.user'])->where('slug', $slug)->firstOrFail();

        $perPage = (int) request('per_page', 5);
        if ($perPage < 1) { $perPage = 5; }
        if ($perPage > 10) { $perPage = 10; }

        $reviews = $product->reviews()
            ->where('status', 1)
            ->latest()
            ->paginate($perPage);

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
