<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Schema;

class CustomersExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        // Lấy tất cả cột
        return Customer::all();
    }

    public function headings(): array
    {
        // Lấy tên cột động từ bảng customers
        $columns = Schema::getColumnListing('customers');
        return $columns;
    }
}
