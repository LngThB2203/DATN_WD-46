@extends('client.layouts.client')

@section('title', 'My account')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="border p-3 rounded mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-person"></i> THÔNG TIN TÀI KHOẢN</h5>
                    <a href="{{ route('account.edit') }}" class="text-decoration-none">Chỉnh sửa</a>
                </div>
                <div>
                    <p><strong>Họ và tên:</strong> {{ Auth::user()->name ?? 'Chưa có' }}</p>
                    <div class="d-flex align-items-center flex-wrap mb-2">
                        <strong class="me-1">Email:</strong>
                        <span>{{ Auth::user()->email }}</span>
                        @if(Auth::user()->hasVerifiedEmail())
                            <span class="badge bg-success ms-2">Đã xác thực</span>
                        @else
                            <form action="{{ route('verification.send') }}" method="POST" class="ms-2 d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.8rem;">
                                    Xác thực email
                                </button>
                            </form>
                        @endif
                    </div>
                    <p><strong>Giới tính:</strong>
    {{ Auth::user()->gender === 'male' ? 'Nam' : (Auth::user()->gender === 'female' ? 'Nữ' : 'Chưa có') }}</p>
                    <p><strong>Số điện thoại:</strong> {{ Auth::user()->phone ?? 'Chưa có' }}</p>
                    <p><strong>Địa chỉ:</strong> {{ Auth::user()->address ?? 'Chưa có' }}</p>
                </div>
            </div>

            {{-- Sản phẩm yêu thích --}}
            <div class="border p-3 rounded">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-heart"></i> SẢN PHẨM YÊU THÍCH</h5>
                    <a href="#" class="text-decoration-none">Xem tất cả</a>
                </div>
                <p>Bạn chưa có sản phẩm yêu thích nào trong danh sách!</p>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="border p-3 rounded">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> LỊCH SỬ MUA HÀNG</h5>
                    <a href="{{ route('orders.index') }}" class="text-decoration-none">Xem tất cả</a>
                </div>

                <div style="max-height: 650px; overflow-y: auto;">
                    @php
                    use App\Models\Order;
                    $orders = Order::with([
                        'details.product.galleries',
                        'details.variant.size',
                        'details.variant.scent',
                        'details.variant.concentration'
                    ])
                        ->where('user_id', auth()->id())
                        ->where('order_status', 'completed')
                        ->orderBy('created_at', 'desc')
                        ->get();

                    function translateStatus($status) {
                        return match($status) {
                            'completed'   => 'Hoàn thành',
                            default       => $status,
                        };
                    }
                @endphp

            @if($orders->count() == 0)
                <p>Bạn chưa có đơn hàng nào. Tiếp tục mua hàng!</p>
            @else
                @foreach($orders as $order)
                    <div class="mb-3 p-3 border rounded bg-light">
                        {{-- Thông tin đơn hàng --}}
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Mã đơn: #{{ $order->id }}</strong>
                            <span class="text-success">{{ translateStatus($order->order_status) }}</span>
                        </div>
                        {{-- Danh sách sản phẩm --}}
                        @foreach($order->details as $detail)
                            <div class="d-flex mb-2">
                                @php
                                    $primaryImage = $detail->product ? ($detail->product->galleries->where('is_primary', true)->first() ?? $detail->product->galleries->first()) : null;
                                    $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->image_path) : asset('assets/client/img/product/product-1.webp');
                                @endphp
                                <img src="{{ $imageUrl }}"
                                    style="width: 80px; height: 80px; object-fit: cover;"
                                    class="rounded border me-3"
                                    onerror="this.onerror=null;this.src='{{ asset('assets/client/img/product/product-1.webp') }}';">
                                {{-- Thông tin sản phẩm --}}
                                <div class="flex-grow-1">
                                    <div class="fw-bold mb-1">{{ $detail->product->name ?? 'Sản phẩm đã bị xóa' }}</div>
                                    
                                    {{-- Hiển thị thông tin biến thể --}}
                                    @if($detail->variant)
                                        <div class="small text-muted mb-1">
                                            @if($detail->variant->size)
                                                <span class="badge bg-secondary me-1">
                                                    Kích thước: {{ $detail->variant->size->size_name ?? $detail->variant->size->name ?? 'N/A' }}
                                                </span>
                                            @endif
                                            @if($detail->variant->scent)
                                                <span class="badge bg-info me-1">
                                                    Mùi: {{ $detail->variant->scent->scent_name ?? $detail->variant->scent->name ?? 'N/A' }}
                                                </span>
                                            @endif
                                            @if($detail->variant->concentration)
                                                <span class="badge bg-warning text-dark me-1">
                                                    Nồng độ: {{ $detail->variant->concentration->concentration_name ?? $detail->variant->concentration->name ?? 'N/A' }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="small text-muted mb-1">
                                            <span class="badge bg-light text-dark">Không có biến thể</span>
                                        </div>
                                    @endif
                                    
                                    <div class="small">
                                        <span>Giá: <strong>{{ number_format($detail->price, 0, ',', '.') }}₫</strong></span>
                                        <span class="ms-2">Số lượng: <strong>x{{ $detail->quantity }}</strong></span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        {{-- Tổng đơn hàng --}}
                        <div class="mt-2 border-top pt-2 fw-bold">
                            Tổng tiền: {{ number_format($order->grand_total ?? $order->total_price, 0, ',', '.') }}₫
                        </div>

                        <a href="{{ route('orders.show', $order->id) }}" class="text-primary">Xem chi tiết</a>
                    </div>
                @endforeach
            @endif
        </div>


            </div>
        </div>
    </div>

    {{-- Đăng xuất --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-dark">ĐĂNG XUẤT</button>
    </form>
</div>
@endsection
