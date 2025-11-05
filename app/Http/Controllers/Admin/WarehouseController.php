<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
<<<<<<< Updated upstream
    /** Hiển thị danh sách kho */
=======
>>>>>>> Stashed changes
    public function index()
    {
        $warehouses = Warehouse::with('manager')->latest()->paginate(10);
        return view('admin.inventories.warehouse', compact('warehouses'));
    }

<<<<<<< Updated upstream
    /** Hiển thị form thêm kho */
    public function create()
    {
        $managers = User::all(); // nếu muốn chọn người quản lý từ bảng users
        return view('admin.inventories.add', compact('managers'));
    }

    /** Lưu kho mới */
=======
    public function create()
    {
        $managers = User::all();
        return view('admin.inventories.add', compact('managers'));
    }

>>>>>>> Stashed changes
    public function store(Request $request)
    {
        $request->validate([
            'warehouse_name' => 'required|string|max:255',
            'address'        => 'required|string|max:255',
            'manager_id'     => 'nullable|exists:users,id',
            'phone'          => 'nullable|string|max:20',
        ]);

<<<<<<< Updated upstream
        Warehouse::create([
            'warehouse_name' => $request->warehouse_name,
            'address'        => $request->address,
            'manager_id'     => $request->manager_id,
            'phone'          => $request->phone,
        ]);

        return redirect()->route('inventories.warehouse')
            ->with('success', 'Thêm kho mới thành công!');
    }

    /** Hiển thị form sửa */
=======
        Warehouse::create($request->only('warehouse_name', 'address', 'manager_id', 'phone'));
        return redirect()->route('inventories.warehouse')->with('success', 'Thêm kho mới thành công!');
    }

>>>>>>> Stashed changes
    public function edit(Warehouse $warehouse)
    {
        $managers = User::all();
        return view('admin.inventories.warehouse_edit', compact('warehouse', 'managers'));
    }

<<<<<<< Updated upstream
    /** Cập nhật kho */
=======
>>>>>>> Stashed changes
    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'warehouse_name' => 'required|string|max:255',
            'address'        => 'required|string|max:255',
            'manager_id'     => 'nullable|exists:users,id',
            'phone'          => 'nullable|string|max:20',
        ]);

<<<<<<< Updated upstream
        $warehouse->update([
            'warehouse_name' => $request->warehouse_name,
            'address'        => $request->address,
            'manager_id'     => $request->manager_id,
            'phone'          => $request->phone,
        ]);

        return redirect()->route('inventories.warehouse')
            ->with('success', 'Cập nhật kho thành công!');
    }

    /** Xóa kho */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->route('inventories.warehouse')
            ->with('success', 'Đã xóa kho!');
=======
        $warehouse->update($request->only('warehouse_name', 'address', 'manager_id', 'phone'));
        return redirect()->route('inventories.warehouse')->with('success', 'Cập nhật kho thành công!');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->route('inventories.warehouse')->with('success', 'Đã xóa kho!');
>>>>>>> Stashed changes
    }
}
