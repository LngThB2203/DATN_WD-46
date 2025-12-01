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

        // Lấy reviews (phân trang để phù hợp với view) và chỉ lấy review đã duyệt
        $perPage = (int) request('per_page', 5);
        if ($perPage < 1) { $perPage = 5; }
        if ($perPage > 10) { $perPage = 10; }
        $reviews = $product->reviews()
            ->with('user')
            ->where('status', 1)
            ->latest()
            ->paginate($perPage);

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

