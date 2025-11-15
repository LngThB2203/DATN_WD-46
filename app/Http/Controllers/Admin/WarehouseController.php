<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warehouse;

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
        return view('admin.inventories.add-warehouse');
    }

    // Lưu kho mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'manager' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
        ]);

        Warehouse::create($request->all());

        return redirect()->route('inventories.warehouse')
            ->with('success', 'Thêm kho thành công!');
    }

    // Sửa thông tin kho
    public function edit(Warehouse $warehouse)
    {
        return view('admin.inventories.edit-warehouse', compact('warehouse'));
    }

    // Cập nhật kho
    public function update(Request $request, Warehouse $warehouse)
    {
        $warehouse->update($request->all());
        return redirect()->route('inventories.warehouse')->with('success', 'Cập nhật kho thành công!');
    }

    // Xóa kho
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->route('inventories.warehouse')->with('success', 'Xóa kho thành công!');
    }
}
