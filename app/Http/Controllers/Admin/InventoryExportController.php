<?php
namespace App\Http\Controllers\Admin;

use App\Exports\StockTransactionExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class InventoryExportController extends Controller
{
    public function stockTransactions()
    {
        return Excel::download(
            new StockTransactionExport(),
            'lich-su-nhap-xuat-kho.xlsx'
        );
    }
}
