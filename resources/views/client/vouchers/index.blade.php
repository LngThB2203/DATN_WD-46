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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 mb-0">Tất cả voucher</h1>
            <a href="{{ route('client.vouchers.my') }}" class="btn btn-outline-primary btn-sm">Kho voucher của tôi</a>
        </div>

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
                                    $isUsed = in_array($discount->id, $usedDiscountIds ?? []);
                                @endphp

                                <h5 class="card-title d-flex justify-content-between align-items-center">
                                    <span>{{ $discount->code }}</span>
                                    @if($isUsed)
                                        <span class="badge bg-secondary ms-2">Đã sử dụng</span>
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

                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    @auth
                                        @php $isSaved = in_array($discount->id, $savedIds ?? []); @endphp
                                        <button type="button"
                                                class="btn btn-sm {{ $isSaved ? 'btn-success' : 'btn-outline-primary' }} save-voucher-btn"
                                                data-id="{{ $discount->id }}"
                                                {{ $isSaved ? 'disabled' : '' }}>
                                            {{ $isSaved ? 'Đã lưu' : 'Lưu voucher' }}
                                        </button>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">
                                            Đăng nhập để lưu
                                        </a>
                                    @endauth

                                    <a href="{{ route('checkout.index') }}"
                                       class="btn btn-sm {{ $isUsed ? 'btn-outline-secondary disabled' : 'btn-primary' }}"
                                       {{ $isUsed ? 'aria-disabled=true' : '' }}>
                                        {{ $isUsed ? 'Đã sử dụng' : 'Dùng ngay' }}
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

@section('scripts')
<script>
(function () {
    const buttons = document.querySelectorAll('.save-voucher-btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            if (!id) return;

            const button = this;
            button.disabled = true;

            fetch("{{ route('client.vouchers.save') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ discount_id: id })
            })
                .then(res => res.json().then(data => ({ ok: res.ok, data })))
                .then(({ ok, data }) => {
                    const message = data.message || (ok ? 'Đã lưu voucher.' : 'Không thể lưu voucher.');
                    alert(message);
                    if (ok && data.success) {
                        button.classList.remove('btn-outline-primary');
                        button.classList.add('btn-success');
                        button.textContent = 'Đã lưu';
                    } else {
                        button.disabled = false;
                    }
                })
                .catch(() => {
                    alert('Có lỗi xảy ra khi lưu voucher.');
                    button.disabled = false;
                });
        });
    });
})();

</script>
@endsection
