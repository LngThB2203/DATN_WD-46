<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarehouseProduct;
use Illuminate\Http\Request;

class WarehouseProductController extends Controller
{
    public function index()
    {
        $inventories   = WarehouseProduct::with(['warehouse', 'product'])->paginate(10);
        $lowStockItems = WarehouseProduct::with(['warehouse', 'product'])
            ->whereColumn('quantity', '<', 'min_stock_threshold')
            ->get();

        return view('admin.inventories.received-orders', compact('inventories', 'lowStockItems'));
    }

    public function updateQuantity(Request $request, $id)
    {
        $inventory = WarehouseProduct::findOrFail($id);
        $inventory->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Cập nhật tồn kho thành công!');
    }
}
