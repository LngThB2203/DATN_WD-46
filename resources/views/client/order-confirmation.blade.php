@extends('client.layouts.app')

@section('title', 'Xác nhận đơn hàng')

@section('content')
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

            {{-- Heading --}}
            <h2 class="text-center fw-bold mb-2" style="color:#28a745;font-size:32px;">Cảm ơn bạn đã đặt hàng!</h2>
            <p class="text-center text-muted mb-4">Đơn hàng #{{ str_pad($order->id, 2, '0', STR_PAD_LEFT) }} của bạn đã được xác nhận.</p>

            {{-- Main Card --}}
            <div class="card mx-auto" style="max-width:900px;">
                {{-- Blue Header --}}
                <div class="card-header" style="background:#0056b3;color:white;font-weight:600;padding:12px 20px;font-size:16px;">
                    Thông tin đơn hàng
                </div>

                {{-- Content --}}
                <div class="card-body">
                    {{-- Two Column Info --}}
                    <div class="row mb-4">
                        {{-- Left Column --}}
                        <div class="col-md-6">
                            <h6 style="font-weight:600;margin-bottom:12px;">Thông tin giao hàng</h6>
                            <div style="margin-bottom:8px;"><strong>Họ tên:</strong> {{ $order->customer_name }}</div>
                            <div style="margin-bottom:8px;"><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</div>
                            <div style="margin-bottom:8px;"><strong>Địa chỉ:</strong> {{ $order->shipping_address_line }}</div>
                        </div>

                        {{-- Right Column --}}
                        <div class="col-md-6">
                            <h6 style="font-weight:600;margin-bottom:12px;">Chi tiết đơn hàng</h6>
                            <div style="margin-bottom:8px;"><strong>Mã đơn hàng:</strong> #{{ str_pad($order->id, 2, '0', STR_PAD_LEFT) }}</div>
                            <div style="margin-bottom:8px;"><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</div>
                            <div style="margin-bottom:8px;">
                                <strong>Phương thức thanh toán:</strong> 
                                @if($order->payment_method === 'cod')
                                    Thanh toán khi nhận hàng
                                @elseif($order->payment_method === 'online')
                                    Thanh toán online
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                                @endif
                            </div>
                            
                        </div>
                    </div>

                    {{-- Products Section --}}
                    <h6 style="font-weight:600;margin-bottom:12px;">Sản phẩm đã đặt</h6>
                    <div class="table-responsive">
                        <table class="table" style="margin-bottom:0;">
                            <thead>
                                <tr style="border-bottom:2px solid #ddd;">
                                    <th style="padding:10px;text-align:left;font-weight:600;">Sản phẩm</th>
                                    <th style="padding:10px;text-align:right;font-weight:600;width:120px;">Đơn giá</th>
                                    <th style="padding:10px;text-align:center;font-weight:600;width:100px;">Số lượng</th>
                                    <th style="padding:10px;text-align:right;font-weight:600;width:120px;">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->details as $d)
                                    <tr style="border-bottom:1px solid #ddd;">
                                        <td style="padding:12px;text-align:left;">
                                            <div style="display:flex;gap:10px;align-items:flex-start;">
                                                @if($d->product && $d->product->primaryImage())
                                                    <img src="{{ $d->product->primaryImage()->image_url }}" alt="" 
                                                         style="width:50px;height:50px;object-fit:cover;border-radius:4px;flex-shrink:0;">
                                                @else
                                                    <div style="width:50px;height:50px;background:#f0f0f0;border-radius:4px;flex-shrink:0;"></div>
                                                @endif
                                                <div>
                                                    <div style="font-weight:600;margin-bottom:4px;">{{ $d->product->name ?? 'Sản phẩm' }}</div>
                                                    @if($d->variant)
                                                        <div style="font-size:12px;color:#999;line-height:1.4;">
                                                            @if($d->variant->size)
                                                                <div>{{ $d->variant->size->size_name ?? '' }}</div>
                                                            @endif
                                                            @if($d->variant->scent)
                                                                <div>{{ $d->variant->scent->scent_name ?? '' }}</div>
                                                            @endif
                                                            @if($d->variant->concentration)
                                                                <div>{{ $d->variant->concentration->concentration_name ?? '' }}</div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding:12px;text-align:right;">{{ number_format($d->price, 0, ',', '.') }} đ</td>
                                        <td style="padding:12px;text-align:center;">{{ $d->quantity }}</td>
                                        <td style="padding:12px;text-align:right;font-weight:600;">{{ number_format($d->subtotal, 0, ',', '.') }} đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals --}}
                    <div style="display:flex;justify-content:flex-end;margin-top:20px;padding-top:12px;border-top:2px solid #ddd;">
                        <div style="min-width:250px;">
                            @if($order->discount_total > 0 && $order->discount_id)
                                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                    <span>Giảm giá:</span>
                                    <span>-{{ number_format($order->discount_total, 0, ',', '.') }} đ</span>
                                </div>
                            @endif
                            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                <span>Phí vận chuyển:</span>
                                <span>{{ number_format($order->shipping_cost, 0, ',', '.') }} đ</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-weight:700;font-size:16px;padding-top:12px;border-top:1px solid #ddd;">
                                <span>Tổng cộng:</span>
                                <span style="color:#0056b3;">{{ number_format($order->grand_total, 0, ',', '.') }} đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="text-center mt-4">
                <a href="{{ route('home') }}" class="btn" style="background:#e84c89;color:white;padding:10px 30px;border:none;border-radius:4px;margin-right:10px;text-decoration:none;">
                    Trang chủ
                </a>
                <a href="{{ route('orders.index') }}" class="btn" style="background:white;color:#e84c89;padding:10px 30px;border:1px solid #e84c89;border-radius:4px;text-decoration:none;">
                    Xem đơn hàng của tôi
                </a>
            </div>
        @else
            <div class="text-center">
                <h2 class="fw-bold mb-3" style="color:#28a745;">Cảm ơn bạn đã đặt hàng!</h2>
                <p class="mb-4">Đơn hàng đã được xác nhận. Bạn có thể xem chi tiết trong trang <a href="{{ route('orders.index') }}">Đơn hàng của tôi</a>.</p>
                <a class="btn btn-primary" href="{{ route('home') }}">Tiếp tục mua sắm</a>
            </div>
        @endif
    </div>
</section>
@endsection
