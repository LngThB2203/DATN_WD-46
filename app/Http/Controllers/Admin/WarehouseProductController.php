<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseProductController extends Controller
{
    public function index(Request $request)
    {
        $inventories = WarehouseProduct::select(
            'product_id',
            'variant_id',
            DB::raw('SUM(quantity) as total_quantity')
        )
            ->with([
                'product',
                'variant.size',
                'variant.scent',
                'variant.concentration',
            ])
            ->groupBy('product_id', 'variant_id');

        // search
        if ($request->filled('search')) {
            $inventories->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // filter product
        if ($request->filled('product_id')) {
            $inventories->where('product_id', $request->product_id);
        }

        // low stock
        if ($request->filled('low_stock')) {
            $inventories->havingRaw('SUM(quantity) <= 10');
        }

        $inventories = $inventories
            ->paginate(15)
            ->withQueryString();

        /* ===============================
        DASHBOARD STATS (BẮT BUỘC)
    =============================== */

        $totalProducts = Product::count();
        $totalVariants = ProductVariant::count();
        $totalQuantity = WarehouseProduct::sum('quantity');

        $productsWithStock = WarehouseProduct::select('product_id')
            ->groupBy('product_id')
            ->havingRaw('SUM(quantity) > 0')
            ->count();

        $totalStockItems = WarehouseProduct::count(); // số dòng batch

        /* ===============================
        LOW STOCK LIST
    =============================== */

        $lowStockItems = WarehouseProduct::select(
            'product_id',
            'variant_id',
            DB::raw('SUM(quantity) as total_quantity')
        )
            ->with(['product', 'variant'])
            ->groupBy('product_id', 'variant_id')
            ->having('total_quantity', '<=', 10)
            ->get();

        /* ===============================
        FILTER DATA
    =============================== */

        $warehouses = Warehouse::all();
        $products   = Product::all();

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

        $batches = WarehouseProduct::with('warehouse')
            ->where('product_id', $productId)
            ->when($variantId, function ($q) use ($variantId) {
                $q->where('variant_id', $variantId);
            }, function ($q) {
                $q->whereNull('variant_id');
            })
            ->where('quantity', '>', 0)
            ->orderBy('expired_at')
            ->paginate(15);

        // Tổng tồn để hiển thị header
        $totalQuantity = $batches->sum('quantity');

        return view('admin.inventories.show', compact(
            'product',
            'variant',
            'batches',
            'totalQuantity'
        ));
    }

    public function getVariants($productId)
    {
        return ProductVariant::where('product_id', $productId)
            ->get(['id', 'sku as name']);
    }

    public function createImport()
    {
        return view('admin.inventories.import', [
            'warehouses' => Warehouse::all(),
            'products'   => Product::all(),
        ]);
    }

    public function import(Request $request, StockService $stockService)
    {
        $request->validate([
            'warehouse_id' => 'required',
            'product_id'   => 'required',
            'variant_id'   => 'nullable',
            'batch_code'   => 'required',
            'quantity'     => 'required|integer|min:1',
        ]);

        $stockService->import(
            [
                'warehouse_id' => $request->warehouse_id,
                'product_id'   => $request->product_id,
                'variant_id'   => $request->variant_id,
                'batch_code'   => $request->batch_code,
                'quantity'     => $request->quantity,
                'expired_at'   => $request->expired_at,
            ],
            'manual_import',
            auth()->id()
        );

        return back()->with('success', 'Nhập kho thành công');
    }

    public function createExport()
    {
        return view('admin.inventories.export', [
            'warehouses' => Warehouse::all(),
            'products'   => Product::all(),
        ]);
    }

    public function export(Request $request, StockService $stockService)
    {
        $request->validate([
            'warehouse_id' => 'required',
            'product_id'   => 'required',
            'variant_id'   => 'nullable',
            'quantity'     => 'required|integer|min:1',
        ]);

        $stockService->exportManual(
            $request->warehouse_id,
            $request->product_id,
            $request->variant_id,
            $request->quantity
        );

        return back()->with('success', 'Xuất kho thành công');
    }
}
