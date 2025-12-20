@extends('client.layouts.app')

@section('title', 'Thanh toán')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Giỏ hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid container-xl">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                Vui lòng kiểm tra lại thông tin. {{ $errors->first() }}
            </div>
        @endif


        <form method="POST" action="{{ route('checkout.store') }}">
            @csrf

            {{-- Hidden selected cart items --}}
            <input type="hidden" name="selected_items" value="{{
                isset($selectedItems) && !empty($selectedItems)
                    ? implode(',', $selectedItems)
                    : implode(',', collect($cart['items'] ?? [])->pluck('cart_item_id')->all())
            }}">

            {{-- CUSTOMER INFO --}}
            <div class="card mb-4">
                <div class="card-header fw-semibold">Thông tin người nhận</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror"
                                value="{{ old('customer_name', $defaultCustomer['name'] ?? '') }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="customer_email" class="form-control @error('customer_email') is-invalid @enderror"
                                value="{{ old('customer_email', $defaultCustomer['email'] ?? '') }}" required>
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror"
                                value="{{ old('customer_phone', $defaultCustomer['phone'] ?? '') }}"
                                placeholder="Nhập số điện thoại" required>
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                            <textarea name="shipping_address_line" class="form-control @error('shipping_address_line') is-invalid @enderror"
                                rows="3" placeholder="Nhập địa chỉ giao hàng" required>{{ old('shipping_address_line', $defaultCustomer['address'] ?? '') }}</textarea>
                            @error('shipping_address_line')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>


            <div class="row g-4">
                <div class="col-lg-7">

                    {{-- CUSTOMER NOTE --}}
                    <div class="card mb-4">
                        <div class="card-header fw-semibold">Thông tin giao hàng</div>
                        <div class="card-body">

                            <label class="form-label">Ghi chú cho đơn hàng</label>
                            <textarea name="customer_note"
                                      class="form-control @error('customer_note') is-invalid @enderror"
                                      rows="3">{{ old('customer_note') }}</textarea>

                            @error('customer_note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                        </div>
                    </div>

                    {{-- PAYMENT METHODS --}}
                    <div class="card">
                        <div class="card-header fw-semibold">Phương thức thanh toán</div>
                        <div class="card-body">

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method"
                                       id="payment_cod" value="cod"
                                       {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_cod">
                                    Thanh toán khi nhận hàng (COD)
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method"
                                       id="payment_bank" value="bank_transfer"
                                       {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_bank">
                                    Chuyển khoản ngân hàng
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method"
                                       id="payment_online" value="online"
                                       {{ old('payment_method') === 'online' ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_online">
                                    Thanh toán online (VNPay/MoMo)
                                </label>
                            </div>

                            {{-- Bank instruction --}}
                            <div class="alert alert-secondary mt-3" id="bank_instructions" style="display:none">
                                <h6 class="fw-semibold mb-2">Thông tin chuyển khoản</h6>
                                <ul class="mb-2 ps-3">
                                    <li>Ngân hàng: Vietcombank</li>
                                    <li>Số tài khoản: 0123456789</li>
                                    <li>Chủ tài khoản: Công ty TNHH ABC</li>
                                </ul>
                                <p class="mb-0">
                                    Nội dung chuyển khoản:
                                    <strong>Thanh toán đơn hàng #{{ now()->format('His') }}</strong>
                                </p>
                            </div>

                            {{-- Online instruction --}}
                            <div class="alert alert-info mt-3" id="online_instructions" style="display:none">
                                <h6 class="fw-semibold mb-2">Thanh toán online</h6>
                                <p class="mb-0">
                                    Bạn sẽ được chuyển đến cổng thanh toán VNPay/MoMo để hoàn tất thanh toán.
                                </p>
                            </div>

                        </div>
                    </div>

                </div>


                {{-- CART SUMMARY --}}
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                            <span>Đơn hàng</span>
                            <span class="badge bg-secondary">{{ count($cart['items'] ?? []) }} sản phẩm</span>
                        </div>

                        <div class="card-body">

                            @if(!empty($cart['items']))
                                <div class="mb-3">
                                    @foreach($cart['items'] as $item)
                                        <div class="d-flex justify-content-between py-2 border-bottom">
                                            <div>
                                                <div class="fw-semibold">{{ $item['name'] ?? 'Sản phẩm' }}</div>
                                                <small class="text-muted">x{{ $item['quantity'] ?? 1 }}</small>
                                            </div>
                                            <div class="text-end">
                                                {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }} đ
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-3">Giỏ hàng của bạn đang trống.</p>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">Mã giảm giá</label>
                                <div class="input-group">
                                    <input type="text" id="discount_code" class="form-control" placeholder="Nhập mã giảm giá" autocomplete="off" value="{{ $cart['discount_code'] ?? '' }}">
                                    <button class="btn btn-outline-primary" type="button" id="applyDiscountBtn">Áp dụng</button>
                                </div>

                                @auth
                                    @if(isset($myVouchers) && $myVouchers->count())
                                        <div class="mt-2">
                                            <label class="form-label small mb-1">Hoặc chọn từ voucher của bạn</label>
                                            <select id="savedVoucherSelect" class="form-select form-select-sm">
                                                <option value="">-- Chọn voucher --</option>
                                                @foreach($myVouchers as $voucher)
                                                    <option value="{{ $voucher->code }}">
                                                        {{ $voucher->code }} -
                                                        @if($voucher->discount_type === 'percent')
                                                            Giảm {{ $voucher->discount_value }}%
                                                        @else
                                                            Giảm {{ number_format($voucher->discount_value, 0, ',', '.') }} đ
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                @endauth

                                @if(!empty($cart['discount_code']))
                                    <div class="mt-1 small text-success">
                                        Đang áp dụng mã: <strong>{{ $cart['discount_code'] }}</strong>
                                    </div>
                                @endif

                                <div class="mt-1 small">
                                    <a href="{{ route('client.vouchers.index') }}" class="text-decoration-underline">Xem kho voucher</a>
                                </div>
                                <div id="discountMessage" class="mt-2 small"></div>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính</span>
                                <span>{{ number_format($cart['subtotal'] ?? 0) }} đ</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Giảm giá</span>
                                <span>- {{ number_format($cart['discount_total'] ?? 0) }} đ</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển</span>
                                <span>{{ number_format($cart['shipping_fee'] ?? 0) }} đ</span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between fw-semibold mb-3">
                                <span>Tổng cộng</span>
                                <span>{{ number_format($cart['grand_total'] ?? 0) }} đ</span>
                            </div>

                            <button class="btn btn-primary w-100" type="button" onclick="confirmOrder('{{ $item['name'] }}','{{ number_format($item['price']) }} đ', this)">
                                Đặt hàng
                            </button>

                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>
</section>


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const bankBlock = document.getElementById('bank_instructions');
    const onlineBlock = document.getElementById('online_instructions');

    function togglePaymentBlocks() {
        const selected = document.querySelector('input[name="payment_method"]:checked');
        if (!selected) return;

        bankBlock.style.display = (selected.value === 'bank_transfer') ? 'block' : 'none';
        onlineBlock.style.display = (selected.value === 'online') ? 'block' : 'none';
    }

    paymentRadios.forEach(r => r.addEventListener('change', togglePaymentBlocks));
    togglePaymentBlocks();

    const applyBtn = document.getElementById('applyDiscountBtn');
    const codeInput = document.getElementById('discount_code');
    const messageEl = document.getElementById('discountMessage');
    const savedSelect = document.getElementById('savedVoucherSelect');

    if (savedSelect && codeInput) {
        savedSelect.addEventListener('change', function () {
            const code = this.value;
            if (!code) return;
            codeInput.value = code;
            if (applyBtn) {
                applyBtn.click();
            }
        });
    }

    if (applyBtn && codeInput && messageEl) {
        applyBtn.addEventListener('click', function () {
            const code = codeInput.value.trim();
            if (!code) {
                messageEl.textContent = 'Vui lòng nhập mã giảm giá.';
                messageEl.className = 'mt-2 small text-danger';
                return;
            }

            applyBtn.disabled = true;
            messageEl.textContent = 'Đang kiểm tra mã giảm giá...';
            messageEl.className = 'mt-2 small text-muted';

            fetch('{{ route('api.apply-discount') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ code })
            })
                .then(res => res.json().then(data => ({ ok: res.ok, status: res.status, data })))
                .then(({ ok, data }) => {
                    if (ok && data.success) {
                        messageEl.textContent = data.message || 'Áp dụng mã giảm giá thành công!';
                        messageEl.className = 'mt-2 small text-success';
                        window.location.reload();
                    } else {
                        messageEl.textContent = data.message || 'Mã giảm giá không hợp lệ.';
                        messageEl.className = 'mt-2 small text-danger';
                    }
                })
                .catch(() => {
                    messageEl.textContent = 'Có lỗi xảy ra khi áp dụng mã giảm giá.';
                    messageEl.className = 'mt-2 small text-danger';
                })
                .finally(() => {
                    applyBtn.disabled = false;
                });
        });
    }
})();

function confirmOrder(name, price, btn) {
    let timerInterval;
    let countdown = 5;

    Swal.fire({
        title: 'Xác nhận đơn hàng',
        html: `Bạn chắc chắn muốn đặt sản phẩm này?<br>Tên: <b>${name}</b><br>Giá: <b>${price}</b>`,
        icon: 'question',
        showCancelButton: true, 
        confirmButtonText: `OK (${countdown})`,
        cancelButtonText: 'Hủy',
        didOpen: () => {
            const confirmBtn = Swal.getConfirmButton();
            confirmBtn.disabled = true; 
            timerInterval = setInterval(() => {
                countdown--;
                confirmBtn.innerText = `OK (${countdown})`;
                if(countdown <= 0){
                    clearInterval(timerInterval);
                    confirmBtn.disabled = false;
                    confirmBtn.innerText = 'OK';
                }
            }, 1000);
        }
    }).then((result) => {
        if (result.isConfirmed) {
            btn.closest('form').submit();
        } 
    });
}
</script>
@endsection

@endsection
