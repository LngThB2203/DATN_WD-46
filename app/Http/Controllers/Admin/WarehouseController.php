<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    // Danh sách kho
    public function index()
    {
        $warehouses = Warehouse::paginate(10);
        return view('admin.inventories.warehouse', compact('warehouses'));
    }

    // Form thêm kho
    public function create()
    {
        $managers = \App\Models\User::all();
        return view('admin.inventories.add', compact('managers'));
    }

    // Lưu kho mới
    public function store(Request $request)
    {
        $request->validate([
            'warehouse_name' => 'required|string|max:200',
            'address'        => 'nullable|string|max:255',
            'manager_id'     => 'nullable|exists:users,id',
            'phone'          => 'nullable|string|max:20',
        ]);

        Warehouse::create([
            'warehouse_name' => $request->warehouse_name,
            'address'        => $request->address,
            'manager_id'     => $request->manager_id,
            'phone'          => $request->phone,
        ]);

        return redirect()->route('inventories.warehouse')
            ->with('success', 'Thêm kho thành công!');
    }

    // Sửa thông tin kho
// Sửa thông tin kho
    public function edit(Warehouse $warehouse)
    {

        $managers = \App\Models\User::all();

        return view('admin.inventories.edit', compact('warehouse', 'managers'));
    }

    // Cập nhật kho
    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'warehouse_name' => 'required|string|max:200',
            'address'        => 'nullable|string|max:255',
            'manager_id'     => 'nullable|exists:users,id',
            'phone'          => 'nullable|string|max:20',
        ]);

        $warehouse->update([
            'warehouse_name' => $request->warehouse_name,
            'address'        => $request->address,
            'manager_id'     => $request->manager_id,
            'phone'          => $request->phone,
        ]);

        return redirect()->route('inventories.warehouse')->with('success', 'Cập nhật kho thành công!');
    }

    // Xóa kho
    public function destroy(Warehouse $warehouse)
    {
        // Kiểm tra xem kho có sản phẩm không
        if ($warehouse->products()->count() > 0) {
            return redirect()->route('inventories.warehouse')
                ->with('error', 'Không thể xóa kho vì vẫn còn sản phẩm trong kho này!');
        }

        $warehouse->delete();
        return redirect()->route('inventories.warehouse')->with('success', 'Xóa kho thành công!');
    }
}
