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
    public function index()
    {
        $inventories = WarehouseProduct::with(['warehouse', 'product', 'variant'])->paginate(10);

        // Lọc tồn kho thấp
        $lowStockItems = $inventories->filter(function ($item) {
            return $item->quantity < ($item->min_stock_threshold ?? 0);
        });

        return view('admin.inventories.received-orders', compact('inventories', 'lowStockItems'));
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
            'variant_id'   => 'required|exists:product_variants,id',
            'quantity'     => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);

        $stock = WarehouseProduct::firstOrCreate([
            'warehouse_id' => $request->warehouse_id,
            'product_id'   => $variant->product_id,
            'variant_id'   => $variant->id,
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
            'variant_id'   => 'required|exists:product_variants,id',
            'quantity'     => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::findOrFail($request->variant_id);

        $stock = WarehouseProduct::where('warehouse_id', $request->warehouse_id)
            ->where('product_id', $variant->product_id)
            ->where('variant_id', $variant->id)
            ->firstOrFail();

        if ($stock->quantity < $request->quantity) {
            return back()->with('error', 'Không đủ hàng trong kho!');
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
