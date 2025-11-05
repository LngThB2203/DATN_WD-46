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
<<<<<<< Updated upstream
            ->whereColumn('quantity', '<', 'min_stock_threshold')
            ->get();

        return view('admin.inventories.received-orders', compact('inventories', 'lowStockItems'));
=======
            ->whereColumn('quantity', '<', 'min_stock_threshold')->get();

        return view('admin.inventories.stock', compact('inventories', 'lowStockItems'));
>>>>>>> Stashed changes
    }

    public function updateQuantity(Request $request, $id)
    {
<<<<<<< Updated upstream
        $inventory = WarehouseProduct::findOrFail($id);
        $inventory->update(['quantity' => $request->quantity]);

=======
        $request->validate(['quantity' => 'required|integer|min:0']);
        $inventory = WarehouseProduct::findOrFail($id);
        $inventory->update(['quantity' => $request->quantity]);
>>>>>>> Stashed changes
        return back()->with('success', 'Cập nhật tồn kho thành công!');
    }
}
