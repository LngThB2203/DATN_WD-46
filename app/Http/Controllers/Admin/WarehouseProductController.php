<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Illuminate\Http\Request;

class WarehouseProductController extends Controller
{
    /** Danh sách tồn kho */
    public function index(Request $request)
    {
        $query = WarehouseProduct::with(['warehouse', 'product', 'variant.size', 'variant.scent', 'variant.concentration']);

        // Tìm kiếm theo tên sản phẩm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Lọc theo kho
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Lọc theo sản phẩm
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Lọc tồn kho thấp
        if ($request->filled('low_stock')) {
            $query->where('quantity', '<=', 10);
        }

        $inventories = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Lọc tồn kho thấp cho cảnh báo
        $lowStockItems = WarehouseProduct::with(['warehouse', 'product', 'variant'])
            ->get()
            ->filter(function ($item) {
                return $item->quantity <= 10;
            });

        // Thống kê
        $totalProducts = \App\Models\Product::count();
        $totalVariants = \App\Models\ProductVariant::count();
        $totalWarehouses = \App\Models\Warehouse::count();
        $totalStockItems = WarehouseProduct::count();
        $totalQuantity = WarehouseProduct::sum('quantity');
        $productsWithStock = WarehouseProduct::distinct('product_id')->count('product_id');

        // Dữ liệu cho filter
        $warehouses = Warehouse::all();
        $products = Product::all();

        return view('admin.inventories.received-orders', compact(
            'inventories', 
            'lowStockItems',
            'totalProducts',
            'totalVariants',
            'totalWarehouses',
            'totalStockItems',
            'totalQuantity',
            'productsWithStock',
            'warehouses',
            'products'
        ));
    }

    /** API lấy biến thể theo sản phẩm */
    public function getVariantsByProduct($productId)
    {
        $variants = ProductVariant::where('product_id', $productId)->get();
        return response()->json($variants);
    }

    /** Form nhập kho */
    public function createImport()
    {
        $warehouses = Warehouse::all();
        $products   = Product::all();

        return view('admin.inventories.import', compact('warehouses', 'products'));
    }

    /** Xử lý nhập kho */
    public function import(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouse,id',
            'product_id'   => 'required|exists:products,id',
            'variant_id'   => 'nullable|exists:product_variants,id',
            'quantity'     => 'required|integer|min:1',
        ]);

        // Nếu có variant_id, kiểm tra variant thuộc về product
        if ($request->variant_id) {
            $variant = ProductVariant::where('id', $request->variant_id)
                ->where('product_id', $request->product_id)
                ->firstOrFail();
        }

        $stock = WarehouseProduct::firstOrCreate([
            'warehouse_id' => $request->warehouse_id,
            'product_id'   => $request->product_id,
            'variant_id'   => $request->variant_id ?? null,
        ]);

        $stock->quantity += $request->quantity;
        $stock->save();

        return back()->with('success', 'Nhập kho thành công!');
    }

    /** Form xuất kho */
    public function createExport()
    {
        $warehouses = Warehouse::all();
        $products   = Product::all();

        return view('admin.inventories.export', compact('warehouses', 'products'));
    }

    /** Xử lý xuất kho */
    public function export(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouse,id',
            'product_id'   => 'required|exists:products,id',
            'variant_id'   => 'nullable|exists:product_variants,id',
            'quantity'     => 'required|integer|min:1',
        ]);

        $stock = WarehouseProduct::where('warehouse_id', $request->warehouse_id)
            ->where('product_id', $request->product_id)
            ->where('variant_id', $request->variant_id ?? null)
            ->first();

        if (!$stock) {
            return back()->with('error', 'Không tìm thấy sản phẩm trong kho này!');
        }

        if ($stock->quantity < $request->quantity) {
            return back()->with('error', 'Không đủ hàng trong kho! Hiện có: ' . $stock->quantity);
        }

        $stock->quantity -= $request->quantity;
        $stock->save();

        return back()->with('success', 'Xuất kho thành công!');
    }

    /** Cập nhật số lượng tồn kho */
    public function updateQuantity(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:0']);

        $item           = WarehouseProduct::findOrFail($id);
        $item->quantity = $request->quantity;
        $item->save();

        return back()->with('success', 'Cập nhật số lượng thành công!');
    }
    public function getVariants($productId)
{
    $variants = ProductVariant::where('product_id', $productId)
        ->get(['id', 'sku as name']);

    return response()->json($variants);
}
}
