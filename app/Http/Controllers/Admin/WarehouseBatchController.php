<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\StockService;
use App\Helpers\WarehouseLockHelper;
use Illuminate\Http\Request;

class WarehouseBatchController extends Controller
{
    // Form nhập kho
    public function createImport()
    {
        return view('admin.inventories.import', [
            'warehouses' => Warehouse::orderBy('warehouse_name')->get(),
            'products'   => Product::orderBy('name')->get(),
        ]);
    }

    // Xử lý nhập kho thủ công
    public function storeImport(Request $request, StockService $stockService)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id'   => 'required|exists:products,id',
            'variant_id'   => 'nullable|exists:product_variants,id',
            'batch_code'   => 'required|string|max:100',
            'expired_at'   => 'nullable|date',
            'quantity'     => 'required|integer|min:1',
            'import_price' => 'nullable|numeric|min:0',
        ]);

        if (WarehouseLockHelper::isWarehouseLocked($request->warehouse_id)) {
            return back()->withErrors(
                'Kho đang có đơn hàng xử lý, không thể nhập kho thủ công'
            );
        }

        $stockService->import(
            [
                'warehouse_id' => $request->warehouse_id,
                'product_id'   => $request->product_id,
                'variant_id'   => $request->variant_id,
                'batch_code'   => $request->batch_code,
                'expired_at'   => $request->expired_at,
                'quantity'     => $request->quantity,
                'import_price' => $request->import_price ?? 0,
            ],
            'manual_import',
            auth()->id()
        );

        return back()->with('success', 'Nhập kho thành công');
    }
}
