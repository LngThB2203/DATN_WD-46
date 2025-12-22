@extends('admin.layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="page-content">
    <div class="container-xxl">
        <h3 class="mb-4">Chi tiết đơn hàng #{{ $order->id }}</h3>

        {{-- Thông báo --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Thông tin khách hàng --}}
        <div class="card mb-4">
            <div class="card-header">Thông tin khách hàng</div>
            <div class="card-body">
                <p><strong>Tên:</strong> {{ $order->user->name ?? $order->customer_name }}</p>
                <p><strong>Email:</strong> {{ $order->user->email ?? $order->customer_email }}</p>
                <p><strong>SĐT:</strong> {{ $order->customer_phone }}</p>
                <p><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
            </div>
        </div>

        {{-- Lý do hủy (hiển thị với admin nếu có) --}}
        @if($order->cancellation_reason)
        <div class="card mb-4">
            <div class="card-header">Lý do hủy</div>
            <div class="card-body">
                <p><strong>Lý do:</strong> {{ $order->cancellation_reason }}</p>
                <p><strong>Thời điểm hủy:</strong> {{ $order->cancelled_at ? $order->cancelled_at->format('Y-m-d H:i') : '—' }}</p>
            </div>
        </div>
        @endif

        {{-- Sản phẩm --}}
        <div class="card mb-4">
            <div class="card-header">Sản phẩm trong đơn</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Hình ảnh</th>
                                <th>Sản phẩm</th>
                                <th>Biến thể</th>
                                <th>Giá</th>
                                <th>SL</th>
                                <th>Tạm tính</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($order->details as $item)
                                    @php
                                    $product = $item->product;
                                    $primaryImage = $product ? ($product->galleries->where('is_primary', true)->first() ??
                                    $product->galleries->first()) : null;
                                    $imageUrl = $primaryImage
                                        ? asset('storage/' . $primaryImage->image_path)
                                        : asset('assets/client/img/product/product-1.webp');
                                    $isDeleted = $product && $product->trashed();
                                @endphp
                                <tr>
                                    <td>
                                        @php
                                            $fallbackImage = asset('assets/client/img/product/product-1.webp');
                                        @endphp
                                        <img src="{{ $imageUrl }}" 
                                            class="rounded border"
                                            style="width:60px;height:60px;object-fit:cover"
                                            onerror="this.onerror=null;this.src='{{ $fallbackImage }}';">
                                    </td>
                                    <td>
                                        @if($product)
                                            {{ $product->name }}
                                            @if($isDeleted)
                                                <span class="badge bg-warning text-dark ms-1">Đã xóa</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Sản phẩm đã bị xóa</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->variant)
                                            <div class="small">
                                                @if($item->variant->size)
                                                    <div><strong>Kích thước:</strong> {{ $item->variant->size->size_name ?? $item->variant->size->name ?? 'N/A' }}</div>
                                                @endif
                                                @if($item->variant->scent)
                                                    <div><strong>Hương:</strong> {{ $item->variant->scent->scent_name ?? $item->variant->scent->name ?? 'N/A' }}</div>
                                                @endif
                                                @if($item->variant->concentration)
                                                    <div><strong>Nồng độ:</strong> {{ $item->variant->concentration->concentration_name ?? $item->variant->concentration->name ?? 'N/A' }}</div>
                                                @endif
                                                @if($item->variant->gender)
                                                    <div><strong>Giới tính:</strong> 
                                                        @if($item->variant->gender === 'male') Nam
                                                        @elseif($item->variant->gender === 'female') Nữ
                                                        @elseif($item->variant->gender === 'unisex') Unisex
                                                        @else {{ $item->variant->gender }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small">Không có biến thể</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
                                    <td>{{ $item->quantity }}</td>
                                <td>
                                    <strong>
                                        {{ number_format($item->subtotal ?? ($item->price * $item->quantity), 0, ',',
                                        '.') }} đ
                                    </strong>
                                </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Không có sản phẩm nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tổng tiền --}}
        <div class="card mb-4">
            <div class="card-header">Tổng tiền đơn hàng</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-end"><strong>Tạm tính:</strong></td>
                                    <td class="text-end">
                                        @php
                                            $subtotal = $order->subtotal ?? $order->details->sum(function($item) {
                                                return ($item->subtotal ?? ($item->price * $item->quantity));
                                            });
                                        @endphp
                                        {{ number_format($subtotal, 0, ',', '.') }} đ
                                    </td>
                                </tr>
                                @if($order->shipping_cost > 0)
                                <tr>
                                    <td class="text-end"><strong>Phí vận chuyển:</strong></td>
                                    <td class="text-end">{{ number_format($order->shipping_cost, 0, ',', '.') }} đ</td>
                                </tr>
                                @endif
                                @if($order->discount_total > 0)
                                <tr>
                                    <td class="text-end"><strong>Giảm giá:</strong></td>
                                    <td class="text-end text-danger">
                                        - {{ number_format($order->discount_total, 0, ',', '.') }} đ
                                    </td>
                                </tr>
                                @endif
                                <tr class="border-top">
                                    <td class="text-end"><strong class="fs-5">Tổng cộng:</strong></td>
                                    <td class="text-end">
                                        <strong class="fs-5 text-primary">
                                            {{ number_format($order->grand_total ?? $order->total_price, 0, ',', '.') }}
                                            đ
                                        </strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kho xuất hàng --}}
        <div class="card mb-4">
            <div class="card-header">Kho xuất hàng</div>
            <div class="card-body">
                @if ($order->warehouse_id && $order->warehouse)
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-check-circle"></i> Kho đã chọn: 
                        <strong>{{ $order->warehouse->warehouse_name }}</strong>
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.orders.update-warehouse', $order->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Chọn kho xuất hàng <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                <option value="">-- Chọn kho --</option>
                                @foreach ($allWarehouses as $warehouse)
                                    @php
                                        $canFulfill = true;
                                        $missingItems = [];
                                        foreach ($order->details as $item) {
                                            $stockQuery = \App\Models\WarehouseProduct::where('warehouse_id', $warehouse->id)
                                                ->where('product_id', $item->product_id);
                                            
                                            if (is_null($item->variant_id)) {
                                                $stockQuery->whereNull('variant_id');
                                            } else {
                                                $stockQuery->where('variant_id', $item->variant_id);
                                            }
                                            
                                            $stock = $stockQuery->value('quantity') ?? 0;
                                            
                                            if ($stock < $item->quantity) {
                                                $canFulfill = false;
                                                $missingItems[] = $item->product->name ?? 'N/A' . ' (cần: ' . $item->quantity . ', có: ' . $stock . ')';
                                            }
                                        }
                                    @endphp
                                    <option value="{{ $warehouse->id }}" {{ !$canFulfill ? 'disabled' : '' }}>
                                        {{ $warehouse->warehouse_name }}
                                        @if(!$canFulfill) - Không đủ hàng @endif
                                    </option>
                                @endforeach
                            </select>
                            @if($order->details->count() > 0)
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-info-circle"></i> Hệ thống sẽ tự động kiểm tra tồn kho của từng sản phẩm trong kho được chọn.
                                </small>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Xác nhận kho
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Trạng thái --}}
        @php
            $statusName = \App\Helpers\OrderStatusHelper::getStatusName($order->order_status);
            $statusClass = \App\Helpers\OrderStatusHelper::getStatusBadgeClass($order->order_status);
            $currentStatusMapped = \App\Helpers\OrderStatusHelper::mapOldStatus($order->order_status);
            // Các trạng thái yêu cầu phải có warehouse (trừ CANCELLED)
            $statusesRequiringWarehouse = [
                \App\Helpers\OrderStatusHelper::PREPARING,
                \App\Helpers\OrderStatusHelper::SHIPPING,
                \App\Helpers\OrderStatusHelper::DELIVERED,
                \App\Helpers\OrderStatusHelper::COMPLETED
            ];
        @endphp
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Trạng thái đơn hàng</span>
                <span class="badge {{ $statusClass }} fs-6">{{ $statusName }}</span>
            </div>
            <div class="card-body">
                @if (!$order->warehouse_id && $currentStatusMapped === \App\Helpers\OrderStatusHelper::PENDING)
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Lưu ý:</strong> Vui lòng chọn kho xuất hàng trước khi chuyển sang "Chờ lấy hàng" hoặc các trạng thái tiếp theo.
                    </div>
                @endif

                @php
                    // Lấy danh sách trạng thái có thể chuyển
                    $availableStatuses = [];
                    foreach (\App\Helpers\OrderStatusHelper::getStatuses() as $key => $label) {
                        if (\App\Helpers\OrderStatusHelper::canUpdateStatus($order->order_status, $key)) {
                            $requiresWarehouse = in_array($key, $statusesRequiringWarehouse);
                            $isDisabled = $requiresWarehouse && !$order->warehouse_id;
                            if (!$isDisabled) {
                                $availableStatuses[$key] = $label;
                            }
                        }
                    }
                @endphp

                <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}" id="updateStatusForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chọn trạng thái mới:</label>
                        <select name="order_status" class="form-select" required id="statusSelect" onchange="handleStatusChange(this)">
                            <option value="">-- Chọn trạng thái --</option>
                            @foreach (\App\Helpers\OrderStatusHelper::getStatuses() as $key => $label)
                                @php
                                    $canUpdate = \App\Helpers\OrderStatusHelper::canUpdateStatus($order->order_status, $key);
                                    $requiresWarehouse = in_array($key, $statusesRequiringWarehouse);
                                    $isDisabled = $requiresWarehouse && !$order->warehouse_id;
                                    $isCurrent = ($order->order_status == $key || $currentStatusMapped == $key);
                                    
                                    // ĐIỀU KIỆN QUAN TRỌNG: Chỉ hiển thị option "Đã hủy" khi trạng thái là PENDING
                                    $isCancelledOption = ($key === \App\Helpers\OrderStatusHelper::CANCELLED);
                                    $isPending = ($currentStatusMapped === \App\Helpers\OrderStatusHelper::PENDING);
                                    
                                    // Nếu là option hủy và KHÔNG phải PENDING thì KHÔNG hiển thị
                                    $showOption = $canUpdate && !($isCancelledOption && !$isPending);
                                @endphp
                                @if ($showOption)
                                    <option value="{{ $key }}" 
                                        {{ $isCurrent ? 'selected' : '' }}
                                        {{ $isDisabled ? 'disabled' : '' }}
                                        data-requires-warehouse="{{ $requiresWarehouse ? '1' : '0' }}"
                                        data-is-cancelled="{{ $isCancelledOption ? '1' : '0' }}">
                                        {{ $label }}
                                        @if($isDisabled) (Cần chọn kho trước) @endif
                                        @if($isCurrent) - Hiện tại @endif
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    @if (empty($availableStatuses))
                        <div class="alert alert-info mb-2">
                            <small><i class="bi bi-info-circle"></i> Đơn hàng đã ở trạng thái cuối cùng, không thể chuyển đổi thêm.</small>
                        </div>
                        <button type="button" class="btn btn-secondary" disabled>Cập nhật trạng thái</button>
                    @else
                        <button type="submit" class="btn btn-success" id="submitStatusBtn">
                            <i class="bi bi-check-circle"></i> Cập nhật trạng thái
                        </button>
                    @endif
                </form>

                <script>
                    // Ẩn/disable option "Đã hủy" khi trạng thái không phải PENDING
                    document.addEventListener('DOMContentLoaded', function() {
                        const statusSelect = document.getElementById('statusSelect');
                        const currentStatus = '{{ $currentStatusMapped }}';
                        const pendingStatus = '{{ \App\Helpers\OrderStatusHelper::PENDING }}';
                        const cancelledValue = '{{ \App\Helpers\OrderStatusHelper::CANCELLED }}';
                        
                        if (statusSelect && currentStatus !== pendingStatus) {
                            // Ẩn hoặc disable option "Đã hủy" nếu không phải PENDING
                            Array.from(statusSelect.options).forEach(option => {
                                if (option.value === cancelledValue) {
                                    option.style.display = 'none';
                                    option.disabled = true;
                                }
                            });
                        }
                    });
                    
                    function handleStatusChange(select) {
                        const selectedOption = select.options[select.selectedIndex];
                        const submitBtn = document.getElementById('submitStatusBtn');
                        const currentStatus = '{{ $currentStatusMapped }}';
                        const pendingStatus = '{{ \App\Helpers\OrderStatusHelper::PENDING }}';
                        const cancelledValue = '{{ \App\Helpers\OrderStatusHelper::CANCELLED }}';
                        
                        // Ngăn chặn chọn "Đã hủy" nếu không phải PENDING
                        if (selectedOption.value === cancelledValue && currentStatus !== pendingStatus) {
                            select.value = '';
                            alert('Chỉ có thể hủy đơn hàng khi đơn hàng ở trạng thái "Chờ xác nhận"!');
                            if (submitBtn) submitBtn.disabled = true;
                            return;
                        }
                        
                        if (selectedOption.disabled) {
                            select.value = '';
                            alert('Vui lòng chọn kho xuất hàng trước khi chuyển sang trạng thái này!');
                            if (submitBtn) submitBtn.disabled = true;
                        } else if (selectedOption.value) {
                            if (submitBtn) submitBtn.disabled = false;
                        }
                    }
                    
                    document.getElementById('updateStatusForm')?.addEventListener('submit', function(e) {
                        const select = document.getElementById('statusSelect');
                        const selectedOption = select.options[select.selectedIndex];
                        const currentStatus = '{{ $currentStatusMapped }}';
                        const pendingStatus = '{{ \App\Helpers\OrderStatusHelper::PENDING }}';
                        const cancelledValue = '{{ \App\Helpers\OrderStatusHelper::CANCELLED }}';
                        
                        if (!selectedOption || !selectedOption.value) {
                            e.preventDefault();
                            alert('Vui lòng chọn trạng thái!');
                            return false;
                        }
                        
                        // Kiểm tra lại: Không cho phép hủy nếu không phải PENDING
                        if (selectedOption.value === cancelledValue && currentStatus !== pendingStatus) {
                            e.preventDefault();
                            alert('Chỉ có thể hủy đơn hàng khi đơn hàng ở trạng thái "Chờ xác nhận"!');
                            return false;
                        }
                        
                        if (selectedOption.disabled) {
                            e.preventDefault();
                            alert('Không thể chuyển sang trạng thái này! Vui lòng chọn kho trước.');
                            return false;
                        }
                        
                        // Xác nhận trước khi submit
                        const confirmMsg = `Bạn có chắc muốn chuyển đơn hàng sang trạng thái "${selectedOption.textContent.replace(/\s*-\s*Hiện tại|\s*\([^)]+\)/g, '').trim()}"?`;
                        if (!confirm(confirmMsg)) {
                            e.preventDefault();
                            return false;
                        }
                        
                        return true;
                    });
                </script>
            </div>
        </div>
    </div>
</div>
@endsection
