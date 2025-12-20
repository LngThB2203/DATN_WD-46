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

        // Ưu tiên tính tồn kho theo biến thể
        $variantStock = $product->variants->sum(function ($variant) {
            return (int) $variant->stock;
        });

        if ($variantStock > 0) {
            $totalStock = $variantStock;
        } else {
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
            return [
                'id'            => $v->id,
                'size'          => $v->size?->name,
                'scent'         => $v->scent?->name,
                'concentration' => $v->concentration?->name,
                'price'         => $v->price ?? ($product->price + ($v->price_adjustment ?? 0)),
                'stock'         => (int) $v->stock,
                'image'         => $v->image
                    ? asset('storage/' . $v->image)
                    : null,
            ];
        })->values();

        $sizes = $product->variants
            ->pluck('size.name')
            ->filter()
            ->unique()
            ->values();

        $scents = $product->variants
            ->pluck('scent.name')
            ->filter()
            ->unique()
            ->values();

        $concentrations = $product->variants
            ->pluck('concentration.name')
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
