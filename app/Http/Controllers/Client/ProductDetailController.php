<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;

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
        ])->where('slug', $slug)->firstOrFail();

        // Tính tồn kho: ưu tiên tính theo biến thể từ warehouse
        $variantStock = 0;
        foreach ($product->variants as $variant) {
            // Load warehouseStock relation nếu chưa load
            if (!$variant->relationLoaded('warehouseStock')) {
                $variant->load('warehouseStock');
            }
            // Tính tổng stock từ warehouse cho variant này
            $variantStock += (int) $variant->warehouseStock->sum('quantity');
        }

        // Nếu có variant và có stock từ variant thì dùng, nếu không thì tính từ warehouseProducts
        if ($product->variants->count() > 0 && $variantStock > 0) {
            $totalStock = $variantStock;
        } elseif ($product->variants->count() > 0 && $variantStock == 0) {
            // Nếu có variant nhưng stock = 0, kiểm tra lại xem có warehouseProducts không
            $warehouseStock = (int) $product->warehouseProducts->sum('quantity');
            $totalStock = $warehouseStock;
        } else {
            // Không có variant, tính từ warehouseProducts
            $totalStock = (int) $product->warehouseProducts->sum('quantity');
        }

        // Reviews
        $perPage = (int) request('per_page', 5);
        $reviews = $product->reviews()
            ->with('user')
            ->where('status', 1)
            ->latest()
            ->paginate($perPage);

        // Related
        $relatedProducts = Product::with('galleries')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(6)
            ->get();

        $galleries = $product->galleries;

        $variantMatrix = $product->variants->map(function ($v) use ($product) {
            // Đảm bảo warehouseStock đã được load
            if (!$v->relationLoaded('warehouseStock')) {
                $v->load('warehouseStock');
            }
            
            // Tính stock từ warehouse cho variant này
            $variantStock = (int) $v->warehouseStock->sum('quantity');
            
            return [
                'id'            => $v->id,
                'size'          => $v->size?->size_name,
                'scent'         => $v->scent?->scent_name,
                'concentration' => $v->concentration?->concentration_name,
                'price'         => $v->price ?? ($product->price + ($v->price_adjustment ?? 0)),
                'stock'         => $variantStock,
                'image'         => $v->image
                    ? asset('storage/' . $v->image)
                    : null,
            ];
        })->values();

        $sizes = $product->variants
            ->pluck('size.size_name')
            ->filter()
            ->unique()
            ->values();

        $scents = $product->variants
            ->pluck('scent.scent_name')
            ->filter()
            ->unique()
            ->values();

        $concentrations = $product->variants
            ->pluck('concentration.concentration_name')
            ->filter()
            ->unique()
            ->values();

        // Wishlist
        $isFavorite = false;
        if (auth()->check()) {
            $isFavorite = Wishlist::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->exists();
        }

        return view('client.product', compact(
            'product',
            'galleries',
            'totalStock',
            'reviews',
            'relatedProducts',
            'variantMatrix',
            'sizes',
            'scents',
            'concentrations',
            'isFavorite'
        ));
    }
}
