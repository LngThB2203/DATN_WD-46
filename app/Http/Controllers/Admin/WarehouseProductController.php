<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use App\Models\WarehouseBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseProductController extends Controller
{
    public function index(Request $request)
    {
        $inventories = WarehouseProduct::select(
                'warehouse_id',
                'product_id',
                'variant_id',
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->with([
                'warehouse',
                'product',
                'variant.size',
                'variant.scent',
                'variant.concentration',
            ])
            ->groupBy('warehouse_id', 'product_id', 'variant_id');

        // tìm theo tên sản phẩm
        if ($request->filled('search')) {
            $inventories->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // lọc kho
        if ($request->filled('warehouse_id')) {
            $inventories->where('warehouse_id', $request->warehouse_id);
        }

        // lọc sản phẩm
        if ($request->filled('product_id')) {
            $inventories->where('product_id', $request->product_id);
        }

        // tồn kho thấp
        if ($request->filled('low_stock')) {
            $inventories->havingRaw('SUM(quantity) <= 10');
        }

        $inventories = $inventories
            ->orderByDesc('total_quantity')
            ->paginate(15)
            ->withQueryString();
        $totalProducts = Product::count();
        $totalVariants = ProductVariant::count();
        $totalQuantity = WarehouseProduct::sum('quantity');

        $productsWithStock = WarehouseProduct::select('product_id')
            ->groupBy('product_id')
            ->havingRaw('SUM(quantity) > 0')
            ->count();

        $totalStockItems = WarehouseProduct::where('quantity', '>', 0)->count();

        $lowStockItems = WarehouseProduct::select(
                'warehouse_id',
                'product_id',
                'variant_id',
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->with(['warehouse', 'product', 'variant'])
            ->groupBy('warehouse_id', 'product_id', 'variant_id')
            ->having('total_quantity', '<=', 10)
            ->limit(5)
            ->get();

        $warehouses = Warehouse::orderBy('warehouse_name')->get();
        $products   = Product::orderBy('name')->get();

        return view('admin.inventories.received-orders', compact(
            'inventories',
            'totalProducts',
            'totalVariants',
            'totalQuantity',
            'productsWithStock',
            'totalStockItems',
            'lowStockItems',
            'warehouses',
            'products'
        ));
    }

    public function show($productId, $variantId = null)
    {
        $product = Product::findOrFail($productId);

        $variant = null;
        if ($variantId) {
            $variant = ProductVariant::where('id', $variantId)
                ->where('product_id', $productId)
                ->firstOrFail();
        }

        $batches = WarehouseBatch::with('warehouse')
            ->where('product_id', $productId)
            ->when($variantId, fn ($q) => $q->where('variant_id', $variantId))
            ->where('quantity', '>', 0)
            ->orderByRaw('expired_at IS NULL')
            ->orderBy('expired_at')
            ->paginate(15);

        // Tổng tồn ĐÚNG (không phụ thuộc paginate)
        $totalQuantity = WarehouseBatch::where('product_id', $productId)
            ->when($variantId, fn ($q) => $q->where('variant_id', $variantId))
            ->sum('quantity');

        return view('admin.inventories.show', compact(
            'product',
            'variant',
            'batches',
            'totalQuantity'
        ));
    }

    // Ajax load variant
    public function getVariants($productId)
    {
        return ProductVariant::where('product_id', $productId)
            ->get(['id', 'sku as name']);
    }
}
