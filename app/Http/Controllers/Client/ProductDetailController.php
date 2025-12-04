<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

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
            'warehouseProducts',
        ])->where('slug', $slug)->firstOrFail();

        // tổng tồn kho theo product
        $totalStock = $product->warehouseProducts->sum('quantity');

        // Reviews
        $perPage = (int) request('per_page', 5);
        $reviews = $product->reviews()->with('user')->where('status', 1)->latest()->paginate($perPage);

        // Related
        $relatedProducts = Product::with('galleries')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(6)->get();

        $galleries = $product->galleries;

        // Lấy dữ liệu biến thể
        $variantMatrix = $product->variants->map(function ($v) use ($product) {
            return [
                'id'            => $v->id,
                'size'          => $v->size->size_name ?? null,
                'scent'         => $v->scent->scent_name ?? null,
                'concentration' => $v->concentration->concentration_name ?? null,
                'price'         => $v->price ?? ($product->price + ($v->price_adjustment ?? 0)),
                'stock'         => $v->stock,
                'image'         => $v->image ? asset('storage/' . $v->image) : null,
            ];
        });

        // Lấy unique nhóm biến thể
        $sizes          = $product->variants->pluck('size.size_name')->unique()->filter();
        $scents         = $product->variants->pluck('scent.scent_name')->unique()->filter();
        $concentrations = $product->variants->pluck('concentration.concentration_name')->unique()->filter();

        return view('client.product', compact(
            'product', 'galleries', 'totalStock', 'reviews', 'relatedProducts',
            'variantMatrix', 'sizes', 'scents', 'concentrations'
        ));
    }
}
