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
                                                        <div class="small text-muted mt-1">
                                                            @if($detail->variant->size)
                                                                <div><strong>Kích thước:</strong> {{ $detail->variant->size->size_name ?? $detail->variant->size->name ?? 'N/A' }}</div>
                                                            @endif
                                                            @if($detail->variant->scent)
                                                                <div><strong>Hương:</strong> {{ $detail->variant->scent->scent_name ?? $detail->variant->scent->name ?? 'N/A' }}</div>
                                                            @endif
                                                            @if($detail->variant->concentration)
                                                                <div><strong>Nồng độ:</strong> {{ $detail->variant->concentration->concentration_name ?? $detail->variant->concentration->name ?? 'N/A' }}</div>
                                                            @endif
                                                            @if($detail->variant->gender)
                                                                <div><strong>Giới tính:</strong> 
                                                                    @if($detail->variant->gender === 'male') Nam
                                                                    @elseif($detail->variant->gender === 'female') Nữ
                                                                    @elseif($detail->variant->gender === 'unisex') Unisex
                                                                    @else {{ $detail->variant->gender }}
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="small text-muted mt-1">Không có biến thể</div>
                                                    @endif
                                                    @php
                                                        $canReview = false;
                                                        if(auth()->check() && $detail->product) {
                                                            $user = auth()->user();
                                                            if($order->user_id === $user->id && $order->order_status === 'completed' && $order->completed_at) {
                                                                if(\Carbon\Carbon::now()->diffInDays($order->completed_at) <= 15) {
                                                                    $alreadyReviewed = \App\Models\Review::where('user_id', $user->id)
                                                                        ->where('product_id', $detail->product->id)
                                                                        ->where('order_id', $order->id)
                                                                        ->exists();
                                                                    $canReview = ! $alreadyReviewed;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @if($canReview)
                                                        <div class="mt-2">
                                                            <a href="{{ route('orders.review.form', [$order->id, $detail->product->id]) }}" class="btn btn-sm btn-outline-primary">
                                                                ⭐ Đánh giá sản phẩm
                                                            </a>
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Thông tin giao hàng</h5>
                        @if($isPaid)
                            <span class="badge bg-success">Đã thanh toán</span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($canUpdateShipping)
                            {{-- Form cập nhật thông tin --}}
                            <form method="POST" action="{{ route('orders.update-shipping', $order->id) }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <label class="form-label"><strong>Họ tên</strong> <span class="text-danger">*</span></label>
<<<<<<< Updated upstream
                                    <input type="text" name="customer_name" class="form-control" 
                                           value="{{ old('customer_name', $order->customer_name) }}" required>
=======
                                    @if(auth()->check())
                                        <input type="text" name="customer_name" class="form-control" 
                                               value="{{ old('customer_name', auth()->user()->name) }}" readonly disabled>
                                        <input type="hidden" name="customer_name" value="{{ auth()->user()->name }}">
                                        <small class="text-muted">Thông tin từ tài khoản</small>
                                    @else
                                        <input type="text" name="customer_name" class="form-control" 
                                               value="{{ old('customer_name', $order->customer_name) }}" required>
                                    @endif
>>>>>>> Stashed changes
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><strong>Email</strong></label>
<<<<<<< Updated upstream
                                    <input type="email" name="customer_email" class="form-control" 
                                           value="{{ old('customer_email', $order->customer_email) }}">
=======
                                    @if(auth()->check())
                                        <input type="email" name="customer_email" class="form-control" 
                                               value="{{ old('customer_email', auth()->user()->email) }}" readonly disabled>
                                        <input type="hidden" name="customer_email" value="{{ auth()->user()->email }}">
                                        <small class="text-muted">Thông tin từ tài khoản</small>
                                    @else
                                        <input type="email" name="customer_email" class="form-control" 
                                               value="{{ old('customer_email', $order->customer_email) }}">
                                    @endif
>>>>>>> Stashed changes
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><strong>Số điện thoại</strong> <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_phone" class="form-control" 
                                           value="{{ old('customer_phone', $order->customer_phone) }}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><strong>Địa chỉ</strong> <span class="text-danger">*</span></label>
<<<<<<< Updated upstream
                                    <textarea name="shipping_address" class="form-control" rows="3" required>{{ old('shipping_address', $order->shipping_address_line ?? $order->shipping_address) }}</textarea>
=======
                                    <textarea name="shipping_address_line" class="form-control" rows="3" required>{{ old('shipping_address_line', $order->shipping_address_line ?? $order->shipping_address ?? '') }}</textarea>
>>>>>>> Stashed changes
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><strong>Ghi chú</strong></label>
                                    <textarea name="customer_note" class="form-control" rows="2">{{ old('customer_note', $order->customer_note) }}</textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                            </form>
                        @else
                            {{-- Chỉ hiển thị thông tin (đã thanh toán hoặc không cho phép chỉnh sửa) --}}
                            <div class="mb-3">
                                @if($isPaid)
                                    <div class="alert alert-info">
                                        <small><i class="bi bi-info-circle"></i> Đơn hàng đã thanh toán, không thể thay đổi thông tin giao hàng.</small>
                                    </div>
                                @endif
                            </div>
                            <p><strong>Họ tên:</strong> {{ $order->customer_name }}</p>
                            @if($order->customer_email)<p><strong>Email:</strong> {{ $order->customer_email }}</p>@endif
                            <p><strong>Điện thoại:</strong> {{ $order->customer_phone }}</p>
                            <p><strong>Địa chỉ:</strong> {{ $order->shipping_address_line ?? $order->shipping_address }}</p>
                            @if($order->shipping_province || $order->shipping_district || $order->shipping_ward)
                                <p><strong>Địa chỉ đầy đủ:</strong><br>
                                    {{ $order->shipping_address_line }},
                                    {{ $order->shipping_ward }},
                                    {{ $order->shipping_district }},
                                    {{ $order->shipping_province }}
                                </p>
                            @endif
                            @if($order->customer_note)<p><strong>Ghi chú:</strong> {{ $order->customer_note }}</p>@endif
                        @endif

                        <hr>

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
