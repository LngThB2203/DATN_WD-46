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
                                        <img src="{{ $imageUrl }}" class="rounded border"
                                            style="width:60px;height:60px;object-fit:cover"
                                            onerror="this.src='{{ asset('assets/client/img/product/product-1.webp') }}';">
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
                                        {{ number_format($order->subtotal ?? $order->details->sum(fn($i) => $i->price *
                                        $i->quantity), 0, ',', '.') }} đ
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
                    <div class="alert alert-info mb-0">
                    Kho:
                    <strong>{{ $order->warehouse->warehouse_name }}</strong>
                    </div>
            </div>
        </div>

        {{-- Trạng thái --}}
            @php
                $statusName = \App\Helpers\OrderStatusHelper::getStatusName($order->order_status);
                $statusClass = \App\Helpers\OrderStatusHelper::getStatusBadgeClass($order->order_status);
            // Các trạng thái yêu cầu phải có warehouse
            $statusesRequiringWarehouse = [\App\Helpers\OrderStatusHelper::PREPARING, \App\Helpers\OrderStatusHelper::AWAITING_PICKUP, \App\Helpers\OrderStatusHelper::DELIVERED, \App\Helpers\OrderStatusHelper::COMPLETED];
            @endphp
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Trạng thái đơn hàng</span>
                    <span class="badge {{ $statusClass }}">{{ $statusName }}</span>
                </div>
                <div class="card-body">
                @if (!$order->warehouse_id)
                    <div class="alert alert-warning mb-3">
                        <small><i class="bi bi-exclamation-triangle"></i> Vui lòng chọn kho xuất hàng trước khi cập nhật trạng thái sang "Đang chuẩn bị hàng" hoặc các trạng thái tiếp theo.</small>
                    </div>
                @endif
                @php
                    $availableStatuses = [];
                    $currentStatusMapped = \App\Helpers\OrderStatusHelper::mapOldStatus($order->order_status);
                    foreach (\App\Helpers\OrderStatusHelper::getStatuses() as $key => $label) {
                        if (\App\Helpers\OrderStatusHelper::canUpdateStatus($order->order_status, $key)) {
                            $requiresWarehouse = in_array($key, $statusesRequiringWarehouse);
                            $isDisabled = $requiresWarehouse && !$order->warehouse_id;
                            if (!$isDisabled) {
                                $availableStatuses[] = $key;
                            }
                        }
                    }
                    // Luôn cho phép submit nếu có ít nhất 1 trạng thái có thể chuyển (kể cả cancelled)
                    $hasAvailableStatus = !empty($availableStatuses);
                @endphp
                <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}" id="updateStatusForm">
                        @csrf
                        @method('PUT')
                    <select name="order_status" class="form-select mb-2" required id="statusSelect">
                            @foreach (\App\Helpers\OrderStatusHelper::getStatuses() as $key => $label)
                                @if (\App\Helpers\OrderStatusHelper::canUpdateStatus($order->order_status, $key))
                                @php
                                    $requiresWarehouse = in_array($key, $statusesRequiringWarehouse);
                                    $isDisabled = $requiresWarehouse && !$order->warehouse_id;
                                    $isCurrent = ($order->order_status == $key || $currentStatusMapped == $key);
                                @endphp
                                <option value="{{ $key }}" 
                                    {{ $isCurrent ? 'selected' : '' }}
                                    {{ $isDisabled ? 'disabled' : '' }}>
                                        {{ $label }}
                                    @if($isDisabled) (Cần chọn kho) @endif
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    @if (empty($availableStatuses))
                        <div class="alert alert-warning mb-2">
                            <small>Không thể chuyển trạng thái từ trạng thái hiện tại.</small>
                        </div>
                        <button type="button" class="btn btn-success" disabled>Cập nhật trạng thái</button>
                    @else
                        <button type="submit" class="btn btn-success" id="submitStatusBtn">Cập nhật trạng thái</button>
                    @endif
                    </form>
                <script>
                    function handleStatusChange(select) {
                        const selectedOption = select.options[select.selectedIndex];
                        if (selectedOption.disabled) {
                            const currentValue = '{{ $order->order_status }}';
                            select.value = currentValue;
                            const reason = selectedOption.textContent.match(/\(([^)]+)\)/);
                            alert(reason ? reason[1] : 'Không thể chuyển sang trạng thái này!');
                        }
                    }
                    
                    document.getElementById('updateStatusForm')?.addEventListener('submit', function(e) {
                        const select = document.getElementById('statusSelect');
                        const selectedOption = select.options[select.selectedIndex];
                        
                        if (selectedOption && selectedOption.disabled) {
                            e.preventDefault();
                            e.stopPropagation();
                            const reason = selectedOption.textContent.match(/\(([^)]+)\)/);
                            alert(reason ? reason[1] : 'Không thể chuyển sang trạng thái này!');
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
