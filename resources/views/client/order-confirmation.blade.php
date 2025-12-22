@extends('client.layouts.app')

@section('title', 'Xác nhận đơn hàng')

@section('content')
@php
    if (!isset($order)) {
        if (session('order')) {
            $order = session('order');
        } elseif (session('last_order_id')) {
            $order = \App\Models\Order::with([
                'details.product.galleries',
                'details.variant.size',
                'details.variant.scent',
                'details.variant.concentration',
                'discount',
                'payment'
            ])->find(session('last_order_id'));
        } elseif (auth()->check()) {
            $order = \App\Models\Order::with([
                'details.product.galleries',
                'details.variant.size',
                'details.variant.scent',
                'details.variant.concentration',
                'discount',
                'payment'
            ])
                        ->where('user_id', auth()->id())
                        ->latest()
                        ->first();
        }
    }
@endphp

<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('checkout.index') }}">Thanh toán</a></li>
                <li class="breadcrumb-item active" aria-current="page">Xác nhận</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid container-xl">
        @if(isset($order))
            {{-- Green Checkmark --}}
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center" 
                     style="width:80px;height:80px;background:#28a745;border-radius:50%;color:white;font-size:50px;font-weight:bold;">
                    ✓
                </div>
            </div>

            <h2 class="text-center fw-bold mb-2" style="color:#28a745;font-size:32px;">Cảm ơn bạn đã đặt hàng!</h2>
            <p class="text-center text-muted mb-4">Đơn hàng #{{ $order->order_code ?? str_pad($order->id, 2, '0', STR_PAD_LEFT) }} của bạn đã được xác nhận.</p>

            <div class="card mx-auto shadow-sm" style="max-width:900px;">
                <div class="card-header" style="background:#0056b3;color:white;font-weight:600;padding:12px 20px;font-size:16px;">
                    Thông tin đơn hàng
                </div>

                <div class="card-body">
                    {{-- Thông tin khách hàng và đơn hàng --}}
                    <div class="row mb-4">
                        <div class="col-md-6 border-end pe-md-4">
                            <h6 class="text-primary fw-bold mb-3">
                                <i class="bi bi-person-circle"></i> Thông tin người nhận
                            </h6>
                            <div class="mb-2">
                                <strong>Họ tên:</strong> 
                                <span class="text-dark">{{ $order->customer_name }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Email:</strong> 
                                <span class="text-dark">{{ $order->customer_email }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Số điện thoại:</strong> 
                                <span class="text-dark">{{ $order->customer_phone }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Địa chỉ giao hàng:</strong>
                                <div class="text-dark mt-1">{{ $order->shipping_address_line ?? $order->shipping_address ?? 'Chưa cập nhật' }}</div>
                            </div>
                            @if($order->customer_note)
                            <div class="mb-2">
                                <strong>Ghi chú:</strong>
                                <div class="text-muted small mt-1">{{ $order->customer_note }}</div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-6 ps-md-4">
                            <h6 class="text-primary fw-bold mb-3">
                                <i class="bi bi-receipt"></i> Thông tin đơn hàng
                            </h6>
                            <div class="mb-2">
                                <strong>Mã đơn hàng:</strong> 
                                <span class="badge bg-primary">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Ngày đặt:</strong> 
                                <span class="text-dark">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Trạng thái:</strong> 
                                @php
                                    $statusName = \App\Helpers\OrderStatusHelper::getStatusName($order->order_status);
                                    $statusClass = \App\Helpers\OrderStatusHelper::getStatusBadgeClass($order->order_status);
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $statusName }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Phương thức thanh toán:</strong> 
                                <span class="badge bg-info text-dark">
                                    {{ $order->payment_method === 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'Thanh toán online (VNPay/MoMo)' }}
                                </span>
                            </div>
                            @if($order->discount)
                            <div class="mb-2">
                                <strong>Mã giảm giá:</strong> 
                                <span class="badge bg-success">{{ $order->discount->code }}</span>
                                @if($order->discount_total > 0)
                                    <span class="text-danger">(-{{ number_format($order->discount_total, 0, ',', '.') }} đ)</span>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-box-seam"></i> Sản phẩm đã đặt
                    </h6>
                    <div class="table-responsive">
                        <table class="table align-middle table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">Hình ảnh</th>
                                    <th>Sản phẩm</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $details = $order->details ?? $order->items; @endphp
                                @foreach($details as $d)
                                    @php
                                        $product = $d->product;
                                        $variant = $d->variant;
                                        $primaryImage = $product->galleries->where('is_primary', true)->first() 
                                                         ?? $product->galleries->first();
                                    @endphp
                                    <tr>
                                        <td>
                                            @if($primaryImage)
                                                <img src="{{ asset('storage/' . $primaryImage->image_path) }}" 
                                                     alt="{{ $product->name ?? 'Sản phẩm' }}"
                                                     class="img-thumbnail"
                                                     style="width: 60px; height: 60px; object-fit: cover;"
                                                     onerror="this.onerror=null; this.src='{{ asset('assets/client/img/product/default.jpg') }}';">
                                            @else
                                                <img src="{{ asset('assets/client/img/product/default.jpg') }}" 
                                                     alt="{{ $product->name ?? 'Sản phẩm' }}"
                                                     class="img-thumbnail"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $product->name ?? 'Sản phẩm' }}</div>
                                            @if($variant)
                                                <div class="small mt-1">
                                                    @if($variant->size)
                                                        <span class="badge bg-secondary me-1" style="font-size: 0.75rem;">
                                                            <i class="bi bi-rulers"></i> Kích thước: {{ $variant->size->size_name }}
                                                        </span>
                                                    @endif
                                                    @if($variant->scent)
                                                        <span class="badge bg-info me-1" style="font-size: 0.75rem;">
                                                            <i class="bi bi-flower1"></i> Mùi hương: {{ $variant->scent->scent_name }}
                                                        </span>
                                                    @endif
                                                    @if($variant->concentration)
                                                        <span class="badge bg-warning text-dark me-1" style="font-size: 0.75rem;">
                                                            <i class="bi bi-droplet"></i> Nồng độ: {{ $variant->concentration->concentration_name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <small class="text-muted d-block mt-1">Không có biến thể</small>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-semibold">{{ number_format($d->price, 0, ',', '.') }} đ</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $d->quantity }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-primary">{{ number_format($d->subtotal ?? ($d->price * $d->quantity), 0, ',', '.') }} đ</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Tổng tiền đơn hàng --}}
                    <div class="row justify-content-end mt-4">
                        <div class="col-md-5">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3">Tổng tiền đơn hàng</h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td>Tạm tính:</td>
                                            <td class="text-end">{{ number_format($order->subtotal ?? 0, 0, ',', '.') }} đ</td>
                                        </tr>
                                        <tr>
                                            <td>Phí vận chuyển:</td>
                                            <td class="text-end">{{ number_format($order->shipping_cost ?? $order->shipping_fee ?? 0, 0, ',', '.') }} đ</td>
                                        </tr>
                                        @if($order->discount_total > 0)
                                        <tr>
                                            <td>
                                                Giảm giá:
                                                @if($order->discount)
                                                    <span class="badge bg-success ms-1">{{ $order->discount->code }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end text-danger fw-bold">-{{ number_format($order->discount_total, 0, ',', '.') }} đ</td>
                                        </tr>
                                        @endif
                                        <tr class="border-top pt-2">
                                            <td class="fw-bold fs-5">Tổng cộng:</td>
                                            <td class="text-end fw-bold fs-5 text-primary">
                                                {{ number_format($order->grand_total ?? $order->total_amount, 0, ',', '.') }} đ
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 pb-5">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 me-2">Quay lại trang chủ</a>
                <a href="{{ route('orders.index') }}" class="btn btn-primary px-4" style="background:#e84c89; border-color:#e84c89;">Xem lịch sử đơn hàng</a>
            </div>
        @else
            {{-- Fallback nếu không tìm thấy bất kỳ dữ liệu nào --}}
            <div class="text-center py-5">
                <h2 class="text-success fw-bold">Đặt hàng thành công!</h2>
                <p>Cảm ơn bạn đã tin tưởng. Kiểm tra email hoặc lịch sử đơn hàng để xem chi tiết.</p>
                <a class="btn btn-primary mt-3" href="{{ route('home') }}">Tiếp tục mua sắm</a>
            </div>
        @endif
    </div>
</section>
@endsection