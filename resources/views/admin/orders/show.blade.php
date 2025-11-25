@extends('admin.layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="page-content">
    <div class="container-xxl">
        <h3 class="mb-4">Chi tiết đơn hàng #{{ $order->id }}</h3>

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
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Biến thể</th>
                                <th>Giá</th>
                                <th>SL</th>
                                <th>Tạm tính</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($order->details as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>
                                        @if($item->variant)
                                            Size: {{ $item->variant->size->size_name ?? '' }} <br>
                                            Hương: {{ $item->variant->scent->scent_name ?? '' }} <br>
                                            Nồng độ: {{ $item->variant->concentration->concentration_name ?? '' }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->price) }} đ</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price * $item->quantity) }} đ</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Không có sản phẩm nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Cập nhật trạng thái đơn --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Trạng thái đơn hàng</span>
                @php
                    use App\Helpers\OrderStatusHelper;
                    $statusName = OrderStatusHelper::getStatusName($order->order_status);
                    $statusClass = OrderStatusHelper::getStatusBadgeClass($order->order_status);
                    $statusDescription = OrderStatusHelper::getStatusDescription($order->order_status);
                @endphp
                <div class="text-end">
                    <span class="badge {{ $statusClass }} fs-6">{{ $statusName }}</span>
                    @if($statusDescription)
                        <br><small class="text-muted">{{ $statusDescription }}</small>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}">
                    @csrf
                    @method('PUT')
                    <select name="order_status" id="orderStatusSelect" class="form-select mb-2">
                        @php
                            // Map trạng thái cũ sang trạng thái mới để hiển thị
                            $mappedCurrentStatus = \App\Helpers\OrderStatusHelper::mapOldStatus($order->order_status);
                        @endphp
                        @foreach(App\Helpers\OrderStatusHelper::getStatuses() as $value => $label)
                            @php
                                $canUpdate = App\Helpers\OrderStatusHelper::canUpdateStatus($order->order_status, $value);
                                // Hiển thị selected nếu là trạng thái hiện tại (sau khi map)
                                $isSelected = ($mappedCurrentStatus == $value) || ($order->order_status == $value);
                            @endphp
                            <option value="{{ $value }}"
                                    {{ $isSelected ? 'selected' : '' }}
                                    {{ !$canUpdate && !$isSelected ? 'disabled' : '' }}
                                    data-can-update="{{ $canUpdate ? '1' : '0' }}">
                                {{ $label }}
                                @if(!$canUpdate && !$isSelected)
                                    (Không thể chuyển)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <div id="statusWarning" class="alert alert-warning d-none mb-2">
                        <small>⚠️ Trạng thái này không thể chuyển đổi từ trạng thái hiện tại.</small>
                    </div>

                    {{-- Lý do hủy (chỉ hiển thị khi chọn "Đã hủy") --}}
                    <div id="cancellationReasonDiv" class="d-none mb-2">
                        <label class="form-label">Lý do hủy đơn hàng <span class="text-danger">*</span></label>
                        <textarea name="cancellation_reason" id="cancellationReason" class="form-control" rows="3"
                                  placeholder="Nhập lý do hủy đơn hàng...">{{ old('cancellation_reason', $order->cancellation_reason ?? '') }}</textarea>
                        <small class="text-muted">Lý do hủy sẽ được lưu lại và hiển thị trong lịch sử đơn hàng.</small>
                    </div>

                    @if($order->cancellation_reason)
                        <div class="alert alert-info mb-2">
                            <strong>Lý do hủy:</strong> {{ $order->cancellation_reason }}
                            @if($order->cancelled_at)
                                <br><small>Thời gian hủy: {{ $order->cancelled_at->format('d/m/Y H:i') }}</small>
                            @endif
                        </div>
                    @endif

                    <button type="submit" class="btn btn-primary" id="updateStatusBtn">Cập nhật</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('orderStatusSelect');
    const warningDiv = document.getElementById('statusWarning');
    const updateBtn = document.getElementById('updateStatusBtn');

    if (statusSelect && warningDiv && updateBtn) {
        const cancellationReasonDiv = document.getElementById('cancellationReasonDiv');
        const cancellationReason = document.getElementById('cancellationReason');

        statusSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const canUpdate = selectedOption.dataset.canUpdate === '1';
            const selectedValue = this.value;

            if (!canUpdate) {
                warningDiv.classList.remove('d-none');
                updateBtn.disabled = true;
            } else {
                warningDiv.classList.add('d-none');
                updateBtn.disabled = false;
            }

            // Hiển thị/ẩn form lý do hủy
            if (cancellationReasonDiv && cancellationReason) {
                if (selectedValue === 'cancelled') {
                    cancellationReasonDiv.classList.remove('d-none');
                    cancellationReason.setAttribute('required', 'required');
                } else {
                    cancellationReasonDiv.classList.add('d-none');
                    cancellationReason.removeAttribute('required');
                }
            }
        });

        // Trigger on load
        statusSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
