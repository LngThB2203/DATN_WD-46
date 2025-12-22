@extends('client.layouts.app')

@section('title', 'Kho voucher của tôi')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('client.vouchers.index') }}">Tất cả voucher</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kho voucher của tôi</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid container-xl">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 mb-0">Kho voucher của tôi</h1>
            <a href="{{ route('client.vouchers.index') }}" class="btn btn-outline-secondary btn-sm">Xem tất cả voucher</a>
        </div>

        @if($discounts->isEmpty())
            <p class="text-muted">Bạn chưa lưu voucher nào. Hãy vào trang "Tất cả voucher" để lưu mã.</p>
        @else
            <div class="row g-3">
                @foreach($discounts as $discount)
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary">Mã</span>
                                    @if($discount->discount_type === 'percent')
                                        <div class="text-end">
                                            <span class="badge bg-info">Giảm {{ $discount->discount_value }}%</span>
                                            @if(!is_null($discount->max_discount_amount))
                                                <div class="small text-muted mt-1">
                                                    Tối đa {{ number_format($discount->max_discount_amount, 0, ',', '.') }} đ
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge bg-success">Giảm {{ number_format($discount->discount_value, 0, ',', '.') }} đ</span>
                                    @endif
                                </div>

                                @php
                                    $isValid = $discount->isValid();
                                    $isUsed  = in_array($discount->id, $usedDiscountIds ?? []);
                                @endphp

                                <h5 class="card-title d-flex justify-content-between align-items-center">
                                    <span>{{ $discount->code }}</span>
                                    @if($isUsed)
                                        <span class="badge bg-secondary ms-2">Đã sử dụng</span>
                                    @elseif(! $isValid)
                                        <span class="badge bg-secondary ms-2">Không còn hiệu lực</span>
                                    @endif
                                </h5>

                                @if($discount->min_order_value)
                                    <p class="mb-1 small text-muted">Đơn tối thiểu: {{ number_format($discount->min_order_value, 0, ',', '.') }} đ</p>
                                @endif

                                <p class="mb-1 small text-muted">
                                    @if($discount->start_date)
                                        Bắt đầu: {{ $discount->start_date->format('d/m/Y') }}
                                    @endif
                                    @if($discount->expiry_date)
                                        <br>Hết hạn: {{ $discount->expiry_date->format('d/m/Y') }}
                                    @endif
                                </p>

                                @if($discount->usage_limit)
                                    <p class="mb-1 small text-muted">
                                        Đã dùng: {{ $discount->used_count }}/{{ $discount->usage_limit }} lượt
                                    </p>
                                @endif

                                <div class="mt-auto d-flex justify-content-end">
                                    @php
                                        $canUse = $isValid && ! $isUsed;
                                    @endphp

                                    <a href="{{ route('checkout.index') }}"
                                       class="btn btn-sm {{ $canUse ? 'btn-primary' : 'btn-outline-secondary disabled' }}"
                                       {{ $canUse ? '' : 'aria-disabled=true' }}>
                                        @if($isUsed)
                                            Đã sử dụng
                                        @elseif($isValid)
                                            Dùng ngay
                                        @else
                                            Không dùng được
                                        @endif
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $discounts->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
