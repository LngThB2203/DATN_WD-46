<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductDetailController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::with([
            'galleries',
            'category',
            'brand',
            'reviews.user',
            'variants.size',
            'variants.scent',
            'variants.concentration',
            'warehouseProducts.warehouse',
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        $totalStock = $product->warehouseProducts->sum('quantity');

        $reviews = $product->reviews()
            ->where('status', 1)
            ->latest()
            ->get();

        $relatedProducts = Product::with('primaryImage')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(6)
            ->get();

        return view('client.product', [
            'product'         => $product,
            'galleries'       => $product->galleries,
            'totalStock'      => $totalStock,
            'reviews'         => $reviews,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
