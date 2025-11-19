<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RevenueExport implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(
        protected string $from,
        protected string $to,
        protected int $month,
        protected int $year,
    ) {}

    public function title(): string
    {
        return 'Bao cao doanh thu';
    }

    public function headings(): array
    {
        return [
            'Loại', 'Giá trị 1', 'Giá trị 2', 'Giá trị 3'
        ];
    }

    public function collection()
    {
        $fromAt = Carbon::parse($this->from)->startOfDay();
        $toAt   = Carbon::parse($this->to)->endOfDay();

        $orders = Order::query()
            ->whereBetween('created_at', [$fromAt, $toAt])
            ->whereIn('order_status', ['paid', 'completed', 'shipped']);

        $revenue = (float) $orders->sum('grand_total');
        $count   = (int) $orders->count();

        $items = OrderDetail::query()
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->whereBetween('orders.created_at', [$fromAt, $toAt])
            ->whereIn('orders.order_status', ['paid', 'completed', 'shipped'])
            ->sum('order_details.quantity');

        $summary = new Collection([
            ['Tổng doanh thu', $revenue, '', ''],
            ['Số đơn hàng', $count, '', ''],
            ['Số sản phẩm bán', (int) $items, '', ''],
            ['', '', '', ''],
            ['Top sản phẩm theo tháng', 'Số lượng', 'Doanh thu', ''],
        ]);

        $fromMonth = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $toMonth   = (clone $fromMonth)->endOfMonth();

        $top = OrderDetail::query()
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'products.id', '=', 'order_details.product_id')
            ->whereBetween('orders.created_at', [$fromMonth, $toMonth])
            ->whereIn('orders.order_status', ['paid', 'completed', 'shipped'])
            ->select([
                'products.name as product_name',
                DB::raw('SUM(order_details.quantity) as total_qty'),
                DB::raw('SUM(order_details.subtotal) as total_amount'),
            ])
            ->groupBy('products.name')
            ->orderByDesc('total_qty')
            ->limit(20)
            ->get();

        $topRows = $top->map(fn($r) => [
            $r->product_name,
            (int)$r->total_qty,
            (float)$r->total_amount,
            ''
        ]);

        return $summary->concat($topRows);
    }
}
