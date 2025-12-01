<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RevenueExport;
use Barryvdh\DomPDF\Facade\Pdf;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', Carbon::now()->startOfMonth()->toDateString());
        $to   = $request->input('to', Carbon::now()->endOfMonth()->toDateString());

        $summary = $this->calculateSummary($from, $to);

        return view('admin.statistics.index', [
            'from' => $from,
            'to' => $to,
            'summary' => $summary,
        ]);
    }

    public function revenueData(Request $request)
    {
        $from = Carbon::parse($request->input('from', Carbon::now()->startOfMonth()->toDateString()))->startOfDay();
        $to   = Carbon::parse($request->input('to', Carbon::now()->endOfMonth()->toDateString()))->endOfDay();

        $dateFormat = $from->diffInDays($to) > 31 ? '%Y-%m' : '%Y-%m-%d';

        $rows = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereIn('order_status', ['paid', 'completed', 'shipped'])
            ->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period, SUM(grand_total) as revenue, COUNT(id) as orders")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return response()->json([
            'labels' => $rows->pluck('period'),
            'revenue' => $rows->pluck('revenue')->map(fn($v) => (float)$v),
            'orders' => $rows->pluck('orders')->map(fn($v) => (int)$v),
        ]);
    }

    public function topProducts(Request $request)
    {
        $month = (int)($request->input('month', Carbon::now()->month));
        $year  = (int)($request->input('year', Carbon::now()->year));
        $from = Carbon::create($year, $month, 1)->startOfMonth();
        $to   = (clone $from)->endOfMonth();

        $rows = OrderDetail::query()
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'products.id', '=', 'order_details.product_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->whereIn('orders.order_status', ['paid', 'completed', 'shipped'])
            ->select([
                'products.id as product_id',
                'products.name as product_name',
                DB::raw('SUM(order_details.quantity) as total_qty'),
                DB::raw('SUM(order_details.subtotal) as total_amount'),
            ])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit((int) $request->input('limit', 10))
            ->get();

        return response()->json($rows);
    }

    public function exportExcel(Request $request)
    {
        $from = $request->input('from', Carbon::now()->startOfMonth()->toDateString());
        $to   = $request->input('to', Carbon::now()->endOfMonth()->toDateString());
        $month = (int)($request->input('month', Carbon::now()->month));
        $year  = (int)($request->input('year', Carbon::now()->year));

        $fileName = "bao-cao-thong-ke-{$from}-to-{$to}.xlsx";
        return Excel::download(new RevenueExport($from, $to, $month, $year), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $from = $request->input('from', Carbon::now()->startOfMonth()->toDateString());
        $to   = $request->input('to', Carbon::now()->endOfMonth()->toDateString());
        $month = (int)($request->input('month', Carbon::now()->month));
        $year  = (int)($request->input('year', Carbon::now()->year));

        $summary = $this->calculateSummary($from, $to);

        $top = $this->getTopProductsCollection($month, $year, 10);

        $pdf = Pdf::loadView('admin.statistics.report-pdf', [
            'from' => $from,
            'to' => $to,
            'summary' => $summary,
            'top' => $top,
            'month' => $month,
            'year' => $year,
        ]);

        return $pdf->download("bao-cao-thong-ke-{$from}-to-{$to}.pdf");
    }

    private function calculateSummary(string $from, string $to): array
    {
        $fromAt = Carbon::parse($from)->startOfDay();
        $toAt   = Carbon::parse($to)->endOfDay();

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

        return [
            'revenue' => $revenue,
            'orders' => $count,
            'items' => (int) $items,
        ];
    }

    private function getTopProductsCollection(int $month, int $year, int $limit)
    {
        $from = Carbon::create($year, $month, 1)->startOfMonth();
        $to   = (clone $from)->endOfMonth();

        return OrderDetail::query()
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'products.id', '=', 'order_details.product_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->whereIn('orders.order_status', ['paid', 'completed', 'shipped'])
            ->select([
                'products.name as product_name',
                DB::raw('SUM(order_details.quantity) as total_qty'),
                DB::raw('SUM(order_details.subtotal) as total_amount'),
            ])
            ->groupBy('products.name')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();
    }
}
