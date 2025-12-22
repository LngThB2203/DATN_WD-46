<?php
namespace App\Http\Controllers\Admin;

use App\Helpers\OrderStatusHelper;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Danh sách đơn hàng
    public function index(Request $request)
    {
        $query = Order::with(['user', 'details.product.galleries']);

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        $orders         = $query->orderByDesc('id')->paginate(15)->withQueryString();
        $statuses       = OrderStatusHelper::getStatuses();
        $selectedStatus = $request->status;

        return view('admin.orders.list', compact('orders', 'statuses', 'selectedStatus'));
    }

    // Tự động gán kho nếu chưa có
    protected function autoAssignWarehouse(Order $order): void
    {
        // Đảm bảo details đã được load
        if (!$order->relationLoaded('details')) {
            $order->load('details');
        }

        if ($order->details->isEmpty()) {
            throw new \Exception('Đơn hàng không có sản phẩm nào');
        }

        $warehouses = Warehouse::orderBy('id')->get();

        if ($warehouses->isEmpty()) {
            throw new \Exception('Không có kho nào trong hệ thống');
        }

        foreach ($warehouses as $warehouse) {
            $canFulfill = true;

            foreach ($order->details as $item) {
                $stockQuery = WarehouseProduct::where('warehouse_id', $warehouse->id)
                    ->where('product_id', $item->product_id);
                
                // Xử lý variant_id null
                if (is_null($item->variant_id)) {
                    $stockQuery->whereNull('variant_id');
                } else {
                    $stockQuery->where('variant_id', $item->variant_id);
                }
                
                $stock = $stockQuery->value('quantity') ?? 0;

                if ($stock < $item->quantity) {
                    $canFulfill = false;
                    break;
                }
            }

            if ($canFulfill) {
                $order->warehouse_id = $warehouse->id;
                $order->save();
                return;
            }
        }

        throw new \Exception('Không có kho nào đủ hàng cho đơn này');
    }

    // Lấy danh sách kho có đủ sản phẩm cho đơn hàng
    protected function getAvailableWarehouses(Order $order): array
    {
        $availableWarehouses = [];
        
        // Đảm bảo details đã được load
        if (!$order->relationLoaded('details')) {
            $order->load('details');
        }
        
        if ($order->details->isEmpty()) {
            return $availableWarehouses;
        }

        $warehouses = Warehouse::orderBy('warehouse_name')->get();

        foreach ($warehouses as $warehouse) {
            $canFulfill = true;
            $missingItems = [];

            foreach ($order->details as $item) {
                $stockQuery = WarehouseProduct::where('warehouse_id', $warehouse->id)
                    ->where('product_id', $item->product_id);
                
                // Xử lý variant_id null
                if (is_null($item->variant_id)) {
                    $stockQuery->whereNull('variant_id');
                } else {
                    $stockQuery->where('variant_id', $item->variant_id);
                }
                
                $stock = $stockQuery->value('quantity') ?? 0;

                if ($stock < $item->quantity) {
                    $canFulfill = false;
                    $missingItems[] = [
                        'product' => $item->product->name ?? 'N/A',
                        'required' => $item->quantity,
                        'available' => $stock,
                    ];
                }
            }

            $availableWarehouses[] = [
                'warehouse' => $warehouse,
                'can_fulfill' => $canFulfill,
                'missing_items' => $missingItems,
            ];
        }

        return $availableWarehouses;
    }

    // Chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::with([
            'user',
            'discount',
            'payment',
            'shipment',
            'details.product.galleries',
            'details.product',
            'details.variant.size',
            'details.variant.scent',
            'details.variant.concentration',
            'warehouse',
        ])->findOrFail($id);
        
        // Kiểm tra xem đơn hàng đã thanh toán chưa
        $isPaid = ($order->payment && $order->payment->status === 'paid') || $order->payment_method !== null;

        // Tự động gán kho nếu chưa có và có kho đủ hàng
        if (! $order->warehouse_id && $order->details->isNotEmpty()) {
            try {
                $this->autoAssignWarehouse($order);
                // Reload lại order từ database với tất cả relationships
                $order = Order::with([
                    'user',
                    'discount',
                    'payment',
                    'shipment',
                    'details.product.galleries',
                    'details.product',
                    'details.variant.size',
                    'details.variant.scent',
                    'details.variant.concentration',
                    'warehouse',
                ])->findOrFail($id);
            } catch (\Exception $e) {
                // Nếu không có kho nào đủ hàng, vẫn cho phép xem đơn hàng
                // Admin vẫn có thể chuyển trạng thái hoặc chọn kho thủ công
            }
        }

        // Lấy danh sách kho có đủ sản phẩm (để admin có thể đổi kho nếu cần)
        $availableWarehouses = $this->getAvailableWarehouses($order);
        $allWarehouses = Warehouse::orderBy('warehouse_name')->get();

        return view('admin.orders.show', compact('order', 'isPaid', 'availableWarehouses', 'allWarehouses'));
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, $id, StockService $stockService)
    {
        $order = Order::with('details')->findOrFail($id);
        
        $request->validate([
            'order_status' => 'required|string',
        ]);
        
        $newStatus = $request->input('order_status');
        $currentStatus = OrderStatusHelper::mapOldStatus($order->order_status);

        // Kiểm tra trạng thái có thể cập nhật
        if (! OrderStatusHelper::canUpdateStatus($order->order_status, $newStatus)) {
            return back()->withErrors(
                'Không thể chuyển từ trạng thái "' .
                OrderStatusHelper::getStatusName($order->order_status) .
                '" sang "' .
                OrderStatusHelper::getStatusName($newStatus) . '"'
            );
        }

        // Bắt buộc chọn kho trước khi PREPARING, SHIPPING, DELIVERED, COMPLETED
        $statusesRequiringWarehouse = [
            OrderStatusHelper::PREPARING,
            OrderStatusHelper::SHIPPING,
            OrderStatusHelper::DELIVERED,
            OrderStatusHelper::COMPLETED
        ];
        
        if (in_array($newStatus, $statusesRequiringWarehouse) && ! $order->warehouse_id) {
            // Tự động thử gán kho trước khi báo lỗi
            try {
                if (!$order->relationLoaded('details')) {
                    $order->load('details');
                }
                $this->autoAssignWarehouse($order);
                $order->refresh();
            } catch (\Exception $e) {
                return back()->withErrors('Vui lòng chọn kho trước khi chuyển trạng thái. ' . $e->getMessage());
            }
        }

        try {
            DB::beginTransaction();

            // Trừ kho khi chuyển từ PENDING sang PREPARING (bước đầu tiên chuẩn bị hàng)
            // Chỉ trừ kho một lần duy nhất khi bắt đầu chuẩn bị hàng
            if ($currentStatus === OrderStatusHelper::PENDING && 
                $newStatus === OrderStatusHelper::PREPARING &&
                !$stockService->isOrderExported($order->id)) {
                try {
                    // Đảm bảo có warehouse_id
                    if (!$order->warehouse_id) {
                        if (!$order->relationLoaded('details')) {
                            $order->load('details');
                        }
                        $this->autoAssignWarehouse($order);
                        $order->refresh();
                    }
                    
                    // Trừ tồn kho
                    $stockService->exportByOrder($order);
                    $order->refresh();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->withErrors('Không thể trừ tồn kho: ' . $e->getMessage());
                }
            }

            // Hủy → hoàn kho
            if ($newStatus === OrderStatusHelper::CANCELLED) {
                try {
                    $stockService->cancelOrder($order);
                    DB::commit();
                    return redirect()->route('admin.orders.show', $order->id)->with('success', 'Hủy đơn hàng và hoàn kho thành công');
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->withErrors('Không thể hủy đơn hàng: ' . $e->getMessage());
                }
            }

            // Cập nhật trạng thái khác
            $order->order_status = $newStatus;
            $order->save();

            DB::commit();
            
            return redirect()->route('admin.orders.show', $order->id)->with('success', 'Cập nhật trạng thái thành công');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Có lỗi xảy ra khi cập nhật trạng thái: ' . $e->getMessage());
        }
    }

    // Cập nhật kho xuất hàng
    public function updateWarehouse(Request $request, $id)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouse,id',
        ]);

        $order = Order::with('details')->findOrFail($id);

        // Kiểm tra kho có đủ sản phẩm không
        $warehouseId = $request->input('warehouse_id');
        $canFulfill = true;
        $missingItems = [];

        // Đảm bảo details đã được load
        if (!$order->relationLoaded('details')) {
            $order->load('details');
        }

        foreach ($order->details as $item) {
            $stockQuery = WarehouseProduct::where('warehouse_id', $warehouseId)
                ->where('product_id', $item->product_id);
            
            // Xử lý variant_id null
            if (is_null($item->variant_id)) {
                $stockQuery->whereNull('variant_id');
            } else {
                $stockQuery->where('variant_id', $item->variant_id);
            }
            
            $stock = $stockQuery->value('quantity') ?? 0;

            if ($stock < $item->quantity) {
                $canFulfill = false;
                $missingItems[] = [
                    'product' => $item->product->name ?? 'N/A',
                    'required' => $item->quantity,
                    'available' => $stock,
                ];
            }
        }

        if (!$canFulfill) {
            $message = 'Kho không đủ hàng. Sản phẩm thiếu: ';
            foreach ($missingItems as $missing) {
                $message .= $missing['product'] . ' (cần: ' . $missing['required'] . ', có: ' . $missing['available'] . '); ';
            }
            return back()->withErrors($message);
        }

        $order->update(['warehouse_id' => $warehouseId]);
        $order->refresh();

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Đã cập nhật kho xuất hàng thành công. Bây giờ bạn có thể chuyển trạng thái đơn hàng.');
    }
}
