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
            'variants.warehouseStock',
            'warehouseProducts',
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        // Ưu tiên tính tồn kho theo biến thể, nếu không có thì fallback về tồn kho theo product_id
        $variantStock = $product->variants->sum(function ($variant) {
            return (int) $variant->stock;
        });

        if ($variantStock > 0) {
            $totalStock = $variantStock;
        } else {
            $totalStock = (int) $product->warehouseProducts->sum('quantity');
        }

        // Reviews: phân trang để phù hợp với view, chỉ review đã duyệt
        $perPage = (int) request('per_page', 5);
        if ($perPage < 1) { $perPage = 5; }
        if ($perPage > 10) { $perPage = 10; }
        $reviews = $product->reviews()
            ->with('user')
            ->where('status', 1)
            ->latest()
            ->paginate($perPage);

        $relatedProducts = Product::with('galleries')
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
