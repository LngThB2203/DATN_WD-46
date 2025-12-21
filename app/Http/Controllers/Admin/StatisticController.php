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

        $days = $from->diffInDays($to) + 1;

        if ($days <= 14) {
            // Group theo ngày
            $dateFormat = '%Y-%m-%d';

            $rows = Order::query()
                ->whereBetween('created_at', [$from, $to])
                ->whereIn('order_status', ['paid', 'completed', 'shipped'])
                ->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period, SUM(grand_total) as revenue, COUNT(id) as orders")
                ->groupBy('period')
                ->orderBy('period')
                ->get();
        } elseif ($days <= 35) {
            // Xem 1 tháng (xấp xỉ) -> group theo tuần
            $rows = Order::query()
                ->whereBetween('created_at', [$from, $to])
                ->whereIn('order_status', ['paid', 'completed', 'shipped'])
                ->selectRaw('YEAR(created_at) as y, WEEK(created_at, 3) as w, CONCAT("Tuần ", WEEK(created_at, 3)) as period')
                ->selectRaw('SUM(grand_total) as revenue')
                ->selectRaw('COUNT(id) as orders')
                ->groupBy('y', 'w', 'period')
                ->orderBy('y')
                ->orderBy('w')
                ->get();
        } else {
            // >= 3 tháng -> group theo tháng
            $dateFormat = '%Y-%m';

            $rows = Order::query()
                ->whereBetween('created_at', [$from, $to])
                ->whereIn('order_status', ['paid', 'completed', 'shipped'])
                ->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period, SUM(grand_total) as revenue, COUNT(id) as orders")
                ->groupBy('period')
                ->orderBy('period')
                ->get();
        }

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

    public function productStats(Request $request)
    {
        $from = $request->input('from', Carbon::now()->startOfMonth()->toDateString());
        $to   = $request->input('to', Carbon::now()->endOfMonth()->toDateString());

        $fromAt = Carbon::parse($from)->startOfDay();
        $toAt   = Carbon::parse($to)->endOfDay();

        $salesQuery = OrderDetail::query()
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->whereBetween('orders.created_at', [$fromAt, $toAt])
            ->whereIn('orders.order_status', ['paid', 'completed', 'shipped'])
            ->select([
                'order_details.product_id',
                DB::raw('SUM(order_details.quantity) as total_sold'),
            ])
            ->groupBy('order_details.product_id');

        $stockQuery = DB::table('warehouse_products')
            ->select([
                'product_id',
                DB::raw('SUM(quantity) as stock_qty'),
            ])
            ->groupBy('product_id');

        // Low stock: tồn kho < 10
        $lowStock = Product::query()
            ->leftJoinSub($stockQuery, 'w', 'w.product_id', '=', 'products.id')
            ->leftJoin('product_galleries as pg', function ($join) {
                $join->on('pg.product_id', '=', 'products.id')
                    ->where('pg.is_primary', true);
            })
            ->select([
                'products.id',
                'products.name',
                DB::raw('COALESCE(w.stock_qty, 0) as stock_qty'),
                'pg.image_path as image_path',
            ])
            ->having('stock_qty', '<', 10)
            ->orderBy('stock_qty')
            ->limit(5)
            ->get();

        // Dead stock: không bán được (total_sold = 0) nhưng tồn kho > 0
        $deadStock = Product::query()
            ->leftJoinSub($salesQuery, 's', 's.product_id', '=', 'products.id')
            ->leftJoinSub($stockQuery, 'w', 'w.product_id', '=', 'products.id')
            ->leftJoin('product_galleries as pg', function ($join) {
                $join->on('pg.product_id', '=', 'products.id')
                    ->where('pg.is_primary', true);
            })
            ->select([
                'products.id',
                'products.name',
                DB::raw('COALESCE(s.total_sold, 0) as total_sold'),
                DB::raw('COALESCE(w.stock_qty, 0) as stock_qty'),
                'pg.image_path as image_path',
            ])
            ->having('stock_qty', '>', 0)
            ->having('total_sold', '=', 0)
            ->orderByDesc('stock_qty')
            ->limit(5)
            ->get();

        $deadIds = $deadStock->pluck('id')->all();

        // Slow moving: có bán nhưng tổng bán < 3, ưu tiên sau dead stock
        $slowMoving = Product::query()
            ->joinSub($salesQuery, 's', 's.product_id', '=', 'products.id')
            ->leftJoin('product_galleries as pg', function ($join) {
                $join->on('pg.product_id', '=', 'products.id')
                    ->where('pg.is_primary', true);
            })
            ->when(!empty($deadIds), fn($q) => $q->whereNotIn('products.id', $deadIds))
            ->select([
                'products.id',
                'products.name',
                DB::raw('s.total_sold as total_sold'),
                'pg.image_path as image_path',
            ])
            ->having('total_sold', '>', 0)
            ->having('total_sold', '<', 3)
            ->orderBy('total_sold')
            ->limit(5)
            ->get();

        $slowIds = $slowMoving->pluck('id')->all();

        // Best sellers: top 5 theo số lượng bán DESC, loại trừ sản phẩm đã nằm trong slow/dead
        $excludeIds = array_merge($deadIds, $slowIds);

        $bestSellers = Product::query()
            ->joinSub($salesQuery, 's', 's.product_id', '=', 'products.id')
            ->leftJoin('product_galleries as pg', function ($join) {
                $join->on('pg.product_id', '=', 'products.id')
                    ->where('pg.is_primary', true);
            })
            ->when(!empty($excludeIds), fn($q) => $q->whereNotIn('products.id', $excludeIds))
            ->select([
                'products.id',
                'products.name',
                DB::raw('s.total_sold as total_sold'),
                'pg.image_path as image_path',
            ])
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return response()->json([
            'low_stock'    => $lowStock,
            'best_sellers' => $bestSellers,
            'slow_moving'  => $slowMoving,
            'dead_stock'   => $deadStock,
        ]);
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
