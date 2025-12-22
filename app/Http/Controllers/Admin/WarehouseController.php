<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\WarehouseProduct;
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
        $managers = User::all();
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

        Warehouse::create($request->only([
            'warehouse_name',
            'address',
            'manager_id',
            'phone'
        ]));

        return redirect()
            ->route('inventories.warehouse')
            ->with('success', 'Thêm kho thành công!');
    }

    // Form sửa kho
    public function edit(Warehouse $warehouse)
    {
        $managers = User::all();
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

        $warehouse->update($request->only([
            'warehouse_name',
            'address',
            'manager_id',
            'phone'
        ]));

        return redirect()
            ->route('inventories.warehouse')
            ->with('success', 'Cập nhật kho thành công!');
    }

    // Xóa kho
    public function destroy(Warehouse $warehouse)
    {
        // Soft delete
        $warehouse->delete();

        return redirect()
            ->route('inventories.warehouse')
            ->with('success', 'Kho đã được xóa (có thể khôi phục)!');
    }

    public function forceDelete($id)
    {
        $warehouse = Warehouse::withTrashed()->findOrFail($id);

        // Kiểm tra tồn kho thực tế
        if (
            WarehouseProduct::where('warehouse_id', $warehouse->id)
                ->where('quantity', '>', 0)
                ->exists()
        ) {
            return redirect()
                ->route('admin.trash.index', ['type' => 'warehouses'])
                ->with('error', 'Không thể xóa vĩnh viễn kho vì vẫn còn tồn kho!');
        }

        $warehouse->forceDelete();

        return redirect()
            ->route('admin.trash.index', ['type' => 'warehouses'])
            ->with('success', 'Kho đã được xóa vĩnh viễn!');
    }

    public function restore($id)
    {
        $warehouse = Warehouse::withTrashed()->findOrFail($id);
        $warehouse->restore();

        return redirect()
            ->route('admin.trash.index', ['type' => 'warehouses'])
            ->with('success', 'Kho đã được khôi phục!');
    }

    public function trashed(Request $request)
    {
        $query = Warehouse::onlyTrashed()->with('manager');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('warehouse_name', 'like', "%{$search}%");
        }

        $warehouses = $query->orderBy('deleted_at', 'desc')->paginate(10);
        $warehouses->appends($request->only('search'));

        return view('admin.inventories.warehouse.trashed', compact('warehouses'));
    }
}
