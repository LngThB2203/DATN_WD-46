@extends('client.layouts.app')

@section('title', 'Kho voucher')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kho voucher</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid container-xl">
        <h1 class="h4 mb-4">Kho voucher của bạn</h1>

        @if($discounts->isEmpty())
            <p class="text-muted">Hiện tại chưa có mã giảm giá nào khả dụng.</p>
        @else
            <div class="row g-3">
                @foreach($discounts as $discount)
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary">Mã</span>
                                    @if($discount->discount_type === 'percent')
                                        <span class="badge bg-info">Giảm {{ $discount->discount_value }}%</span>
                                    @else
                                        <span class="badge bg-success">Giảm {{ number_format($discount->discount_value, 0, ',', '.') }} đ</span>
                                    @endif
                                </div>

                                <h5 class="card-title">{{ $discount->code }}</h5>

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

                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary copy-code-btn" data-code="{{ $discount->code }}">
                                        Sao chép mã
                                    </button>
                                    <a href="{{ route('checkout.index') }}" class="btn btn-sm btn-primary">
                                        Dùng ngay
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

@push('scripts')
<script>
(function () {
    document.querySelectorAll('.copy-code-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const code = this.dataset.code;
            if (!navigator.clipboard) {
                const textarea = document.createElement('textarea');
                textarea.value = code;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
            } else {
                navigator.clipboard.writeText(code).catch(() => {});
            }
            alert('Đã sao chép mã: ' + code);
        });
    });
})();
</script>
@endpush
