<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarehouseProduct;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;

// Barryvdh DomPDF

class WarehouseProductController extends Controller
{
    // Danh sách tồn kho
    public function index()
    {
        $inventories   = WarehouseProduct::with(['product', 'warehouse'])->paginate(10);
        $lowStockItems = $inventories->filter(function ($item) {
            return $item->quantity < ($item->min_stock_threshold ?? 0);
        });

        return view('admin.inventories.received-orders', compact('inventories', 'lowStockItems'));
    }

    // Nhập hàng
    public function import(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id'   => 'required|exists:products,id',
            'quantity'     => 'required|integer|min:1',
        ]);

        $stock = WarehouseProduct::firstOrCreate(
            ['warehouse_id' => $request->warehouse_id, 'product_id' => $request->product_id]
        );

        $stock->quantity += $request->quantity;
        $stock->save();

        return back()->with('success', 'Nhập hàng thành công!');
    }

    // Xuất hàng
    public function export(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id'   => 'required|exists:products,id',
            'quantity'     => 'required|integer|min:1',
        ]);

        $stock = WarehouseProduct::where('warehouse_id', $request->warehouse_id)
            ->where('product_id', $request->product_id)
            ->firstOrFail();

        if ($stock->quantity < $request->quantity) {
            return back()->with('error', 'Không đủ hàng trong kho!');
        }

        $stock->quantity -= $request->quantity;
        $stock->save();

        return back()->with('success', 'Xuất hàng thành công!');
    }

    // Xuất hóa đơn PDF
    public function exportInvoice($id)
    {
        $stock = WarehouseProduct::with(['warehouse', 'product'])->findOrFail($id);
        $pdf   = PDF::loadView('admin.inventories.invoice', compact('stock'));
        return $pdf->download('invoice-' . $stock->id . '.pdf');
    }
    // Cập nhật số lượng cho sản phẩm trong kho
    public function updateQuantity(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $item           = WarehouseProduct::findOrFail($id);
        $item->quantity = $request->quantity;
        $item->save();

        return redirect()->route('inventories.received-orders')
            ->with('success', 'Cập nhật số lượng thành công!');
    }

}
