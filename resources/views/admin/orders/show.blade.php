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
            <div class="card-header">Trạng thái đơn hàng</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.update-status', $order->id) }}">
                    @csrf
                    <select name="order_status" class="form-select mb-2">
                        <option value="pending" {{ $order->order_status=='pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="processing" {{ $order->order_status=='processing' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="shipping" {{ $order->order_status=='shipping' ? 'selected' : '' }}>Đang giao</option>
                        <option value="completed" {{ $order->order_status=='completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="cancelled" {{ $order->order_status=='cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                    <button class="btn btn-primary">Cập nhật</button>
                </form>
            </div>
        </div>

        {{-- Cập nhật vận chuyển --}}
        <div class="card mb-4">
            <div class="card-header">Thông tin vận chuyển</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.update-shipment', $order->id) }}">
                    @csrf
                    <label class="form-label">Trạng thái vận chuyển</label>
                    <select name="shipping_status" class="form-select mb-2">
                        <option value="">Chọn...</option>
                        <option value="preparing" {{ $order->shipment?->shipping_status=='preparing' ? 'selected' : '' }}>Chuẩn bị hàng</option>
                        <option value="shipping" {{ $order->shipment?->shipping_status=='shipping' ? 'selected' : '' }}>Đang giao</option>
                        <option value="delivered" {{ $order->shipment?->shipping_status=='delivered' ? 'selected' : '' }}>Đã giao</option>
                    </select>

                    <label class="form-label">Mã vận đơn</label>
                    <input name="tracking_number" class="form-control mb-2" value="{{ $order->shipment->tracking_number ?? '' }}">

                    <label class="form-label">Đơn vị vận chuyển</label>
                    <input name="carrier" class="form-control mb-2" value="{{ $order->shipment->carrier ?? '' }}">

                    <button class="btn btn-success">Cập nhật vận chuyển</button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
