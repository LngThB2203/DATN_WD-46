@extends('client.layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Đơn hàng của tôi</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chi tiết đơn hàng</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid container-xl">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                {{-- Thông tin sản phẩm --}}
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5>Mã đơn hàng: <strong>#{{ str_pad($order->id,6,'0',STR_PAD_LEFT) }}</strong></h5>
                        @php
                            $statusName = \App\Helpers\OrderStatusHelper::getStatusName($order->order_status);
                            $statusClass = \App\Helpers\OrderStatusHelper::getStatusBadgeClass($order->order_status);
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $statusName }}</span>
                    </div>
                    <div class="card-body">
                        <h6>Sản phẩm</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->details as $detail)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                @if($detail->product && $detail->product->primaryImage())
                                                <img src="{{ asset('storage/'.$detail->product->primaryImage()->image_path) }}" alt="{{ $detail->product->name }}" class="rounded" style="width:60px;height:60px;object-fit:cover;">
                                                @endif
                                                <div>
                                                    <strong>{{ $detail->product->name ?? 'Sản phẩm đã bị xóa' }}</strong>
                                                    @if($detail->variant)
                                                        <div class="small text-muted">
                                                            @if($detail->variant->size) Kích thước: {{ $detail->variant->size->size_name ?? $detail->variant->size->name ?? '' }} @endif
                                                            @if($detail->variant->scent) | Mùi: {{ $detail->variant->scent->scent_name ?? $detail->variant->scent->name ?? '' }} @endif
                                                            @if($detail->variant->concentration) | Nồng độ: {{ $detail->variant->concentration->concentration_name ?? $detail->variant->concentration->name ?? '' }} @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ number_format($detail->price,0,',','.') }} đ</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>{{ number_format($detail->subtotal,0,',','.') }} đ</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Thông tin giao hàng --}}
                <div class="card mt-4">
                    <div class="card-header"><h5>Thông tin giao hàng</h5></div>
                    <div class="card-body">
                        <p><strong>Họ tên:</strong> {{ $order->customer_name }}</p>
                        @if($order->customer_email)<p><strong>Email:</strong> {{ $order->customer_email }}</p>@endif
                        <p><strong>Điện thoại:</strong> {{ $order->customer_phone }}</p>
                        <p><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
                        @if($order->customer_note)<p><strong>Ghi chú:</strong> {{ $order->customer_note }}</p>@endif

                        {{-- Nút hủy đơn --}}
                        @php
                            $mappedStatus = \App\Helpers\OrderStatusHelper::mapOldStatus($order->order_status);
                            $canCancel = in_array($mappedStatus, [
                                \App\Helpers\OrderStatusHelper::PENDING, 
                                \App\Helpers\OrderStatusHelper::PREPARING
                            ]);
                        @endphp
                        @if($canCancel)
                        <form method="POST" action="{{ route('orders.cancel', $order->id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-danger mt-3 w-100">Hủy đơn hàng</button>
                        </form>
                        @endif

                        {{-- Nút xác nhận đã nhận hàng --}}
                        @if($mappedStatus === \App\Helpers\OrderStatusHelper::DELIVERED)
                        <form method="POST" action="{{ route('orders.confirm-received', $order->id) }}">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success mt-3 w-100">
                                Xác nhận đã nhận hàng
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tóm tắt đơn hàng --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5>Tóm tắt đơn hàng</h5></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between"><span>Tạm tính</span><span>{{ number_format($order->subtotal ?? $order->total_price,0,',','.') }} đ</span></div>
                        @if($order->discount_total > 0)
                        <div class="d-flex justify-content-between text-success"><span>Giảm giá</span><span>-{{ number_format($order->discount_total,0,',','.') }} đ</span></div>
                        @endif
                        <div class="d-flex justify-content-between"><span>Phí vận chuyển</span><span>{{ number_format($order->shipping_cost ?? 0,0,',','.') }} đ</span></div>
                        <hr>
                        <div class="d-flex justify-content-between fw-semibold mb-3"><span>Tổng cộng</span><span class="text-primary fs-5">{{ number_format($order->grand_total ?? $order->total_price,0,',','.') }} đ</span></div>

                        <strong>Phương thức thanh toán:</strong>
                        <p class="mb-0">
                            @if($order->payment_method === 'cod') Thanh toán khi nhận hàng (COD)
                            @elseif($order->payment_method === 'bank_transfer') Chuyển khoản ngân hàng
                            @elseif($order->payment_method === 'online') Thanh toán online
                            @else {{ ucfirst(str_replace('_',' ',$order->payment_method)) }} @endif
                        </p>

                        <div class="mt-3">
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100">Quay lại danh sách</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
