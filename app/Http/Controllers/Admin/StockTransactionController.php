<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\WarehouseProduct;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class StockTransactionController extends Controller
{
    /** Trang nhập hàng */
    public function createImport()
    {
        $warehouses = Warehouse::all();
        $products = Product::all();
        return view('admin.inventories.import', compact('warehouses', 'products'));
    }

    /** Xử lý lưu nhập kho */
    public function storeImport(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouse,id',
            'product_id'   => 'required|exists:products,id',
            'quantity'     => 'required|integer|min:1',
            'note'         => 'nullable|string|max:255',
        ]);

        // Cập nhật tồn kho
        $wp = WarehouseProduct::firstOrCreate([
            'warehouse_id' => $data['warehouse_id'],
            'product_id'   => $data['product_id'],
        ]);

        $wp->quantity += $data['quantity'];
        $wp->save();

        // Ghi lịch sử nhập hàng
        $data['type']    = 'import';
        $data['user_id'] = Auth::id();
        StockTransaction::create($data);

        return redirect()->route('inventories.transactions')
            ->with('success', 'Nhập hàng thành công!');
    }

    /** Trang xuất hàng */
    public function createExport()
    {
        $warehouses = Warehouse::all();
        $products = Product::all();
        return view('admin.inventories.export', compact('warehouses', 'products'));
    }

    /** Xử lý lưu xuất kho */
    public function storeExport(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouse,id',
            'product_id'   => 'required|exists:products,id',
            'quantity'     => 'required|integer|min:1',
            'note'         => 'nullable|string|max:255',
        ]);

        $wp = WarehouseProduct::where('warehouse_id', $data['warehouse_id'])
            ->where('product_id', $data['product_id'])
            ->first();

        if (!$wp || $wp->quantity < $data['quantity']) {
            return back()->with('error', 'Không đủ hàng trong kho để xuất!');
        }

        $wp->quantity -= $data['quantity'];
        $wp->save();

        // Ghi lịch sử xuất hàng
        $data['type']    = 'export';
        $data['user_id'] = Auth::id();
        StockTransaction::create($data);

        return redirect()->route('inventories.transactions')
            ->with('success', 'Xuất hàng thành công!');
    }

    /** Danh sách lịch sử nhập xuất */
    public function log()
    {
        $logs = StockTransaction::with(['warehouse', 'product', 'user'])
            ->latest()
            ->paginate(10);

        return view('admin.inventories.transactions', compact('logs'));
    }

    /** In phiếu PDF */
    public function printInvoice($id)
    {
        $transaction = StockTransaction::with(['warehouse', 'product', 'user'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('admin.inventories.invoice', compact('transaction'));
        return $pdf->download('phieu-' . $transaction->type . '-' . $transaction->id . '.pdf');
    }
}
