<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }


    public function collection()
    {
        return $this->products;
    }


    public function headings(): array
    {
        return [
            'STT',
            'Tên sản phẩm',
            'SKU',
            'Giá gốc',
            'Giá khuyến mãi',
            'Danh mục',
            'Thương hiệu',
            'Trạng thái',
            'Ngày tạo',
        ];
    }


    public function map($product): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $product->name,
            $product->sku ?? 'N/A',
            number_format($product->price, 0, ',', '.') . ' VNĐ',
            $product->sale_price ? number_format($product->sale_price, 0, ',', '.') . ' VNĐ' : 'N/A',
            $product->category->category_name ?? 'N/A',
            $product->brand ?? 'N/A',
            $product->status ? 'Hoạt động' : 'Không hoạt động',
            $product->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
