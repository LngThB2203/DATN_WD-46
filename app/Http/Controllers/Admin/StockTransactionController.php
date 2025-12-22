<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::with([
            'warehouse',
            'product',
            'variant.size',
            'variant.scent',
            'variant.concentration',
        ])->orderByDesc('created_at');

        // filter kho
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // filter loại
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // filter ngày
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $query->paginate(20)->withQueryString();
        $warehouses   = Warehouse::all();

        return view('admin.inventories.transactions', compact(
            'transactions',
            'warehouses'
        ));
    }

    public function printInvoice($id)
    {
        $transaction = StockTransaction::with([
            'warehouse',
            'product',
            'variant',
        ])->findOrFail($id);

        return view('admin.inventories.invoice', compact('transaction'));
    }
}
