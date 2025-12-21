<?php
namespace App\Exports;

use App\Models\StockTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockTransactionExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return StockTransaction::with([
            'warehouse',
            'product',
            'variant',
        ])->orderBy('created_at')->get();
    }

    public function headings(): array
    {
        return [
            'Thời gian',
            'Kho',
            'Sản phẩm',
            'Biến thể',
            'Mã lô',
            'Loại',
            'Số lượng',
            'Tồn trước',
            'Tồn sau',
            'Tham chiếu',
        ];
    }

    public function map($row): array
    {
        return [
            $row->created_at,
            $row->warehouse->name ?? '',
            $row->product->name ?? '',
            $row->variant->sku ?? '',
            $row->batch_code,
            $row->type,
            $row->quantity,
            $row->before_quantity,
            $row->after_quantity,
            $row->reference_type . ' #' . $row->reference_id,
        ];
    }
}
