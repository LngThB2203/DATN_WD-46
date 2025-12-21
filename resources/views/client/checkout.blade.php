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
                                value="{{ old('customer_name', $defaultCustomer['customer_name'] ?? '') }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="customer_email" class="form-control @error('customer_email') is-invalid @enderror"
                                value="{{ old('customer_email', $defaultCustomer['customer_email'] ?? '') }}" required>
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror"
                                value="{{ old('customer_phone', $defaultCustomer['customer_phone'] ?? '') }}"
                                placeholder="Nhập số điện thoại" required>
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                            <textarea name="shipping_address_line" class="form-control @error('shipping_address_line') is-invalid @enderror"
                                rows="3" placeholder="Nhập địa chỉ giao hàng" required>{{ old('shipping_address_line', $defaultCustomer['shipping_address_line'] ?? '') }}</textarea>
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
                                <span id="checkoutSubtotal">{{ number_format($cart['subtotal'] ?? 0) }} đ</span>
                            </div>

                            @php $discountShown = !empty($cart['discount_code']) ? ($cart['discount_total'] ?? 0) : 0; @endphp

                            <div class="d-flex justify-content-between mb-2">
                                <span>Giảm giá</span>
                                <span id="checkoutDiscount" data-code="{{ $cart['discount_code'] ?? '' }}" data-amount="{{ $discountShown }}">- {{ number_format($discountShown, 0, ',', '.') }} đ</span>
                            </div>

                            <div id="appliedCodeWrapper" class="mt-1 small text-success" style="{{ !empty($cart['discount_code']) ? '' : 'display:none' }}">
                                Đang áp dụng mã: <strong id="appliedDiscountCode">{{ $cart['discount_code'] ?? '' }}</strong>
                                <button type="button" id="removeDiscountBtn" class="btn btn-link btn-sm text-danger ms-2">Bỏ mã</button>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Phí vận chuyển</span>
                                <span id="checkoutShipping">{{ number_format($cart['shipping_fee'] ?? 0) }} đ</span>
                            </div>

                            <hr>

                            @php $displayTotal = ($cart['subtotal'] ?? 0) + ($cart['shipping_fee'] ?? 0) - $discountShown; @endphp
                            <div class="d-flex justify-content-between fw-semibold mb-3">
                                <span>Tổng cộng</span>
                                <span id="checkoutTotal">{{ number_format(max($displayTotal, 0), 0, ',', '.') }} đ</span>
                            </div>

                            <button class="btn btn-primary w-100" type="button" onclick="confirmOrder(this)">
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

                        // Update summary on page without reload
                        try {
                            const cartInfo = data.cart || {};
                            const subtotalEl = document.getElementById('checkoutSubtotal');
                            const discountEl = document.getElementById('checkoutDiscount');
                            const shippingEl = document.getElementById('checkoutShipping');
                            const totalEl = document.getElementById('checkoutTotal');

                            if (subtotalEl && cartInfo.subtotal !== undefined) subtotalEl.textContent = formatVND(cartInfo.subtotal);
                            if (discountEl) discountEl.textContent = '- ' + formatVND(cartInfo.discount_total || 0);
                            if (shippingEl && cartInfo.shipping_fee !== undefined) shippingEl.textContent = formatVND(cartInfo.shipping_fee);
                            if (totalEl && cartInfo.grand_total !== undefined) totalEl.textContent = formatVND(cartInfo.grand_total);

                            // Update discountCode and cartDiscount variables used by confirmOrder by setting data attributes
                            const wrapper = document.getElementById('checkoutDiscount');
                            if (wrapper) {
                                wrapper.dataset.code = cartInfo.code || '';
                                wrapper.dataset.amount = cartInfo.discount_total || 0;
                            }

                            // Show applied code block and set code text
                            const appliedWrapper = document.getElementById('appliedCodeWrapper');
                            const appliedCodeEl = document.getElementById('appliedDiscountCode');
                            if (appliedWrapper) appliedWrapper.style.display = '';
                            if (appliedCodeEl) appliedCodeEl.textContent = cartInfo.code || '';
                        } catch (e) {
                            // fallback: reload if dynamic update fails
                            window.location.reload();
                        }

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

    // Remove discount handler
    const removeBtn = document.getElementById('removeDiscountBtn');
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            if (!confirm('Bạn có chắc muốn bỏ mã giảm giá?')) return;
            const btn = this;
            btn.disabled = true;
            fetch('{{ route('api.remove-discount') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({})
            })
                .then(res => res.json().then(data => ({ ok: res.ok, data })))
                .then(({ ok, data }) => {
                    if (ok && data.success) {
                        const cartInfo = data.cart || {};
                        if (messageEl) {
                            messageEl.textContent = data.message || 'Đã bỏ mã giảm giá.';
                            messageEl.className = 'mt-2 small text-success';
                        }
                        const subtotalEl = document.getElementById('checkoutSubtotal');
                        const discountEl = document.getElementById('checkoutDiscount');
                        const shippingEl = document.getElementById('checkoutShipping');
                        const totalEl = document.getElementById('checkoutTotal');

                        if (subtotalEl && cartInfo.subtotal !== undefined) subtotalEl.textContent = formatVND(cartInfo.subtotal);
                        if (discountEl) {
                            discountEl.textContent = '- ' + formatVND(cartInfo.discount_total || 0);
                            discountEl.dataset.code = '';
                            discountEl.dataset.amount = 0;
                        }
                        if (shippingEl && cartInfo.shipping_fee !== undefined) shippingEl.textContent = formatVND(cartInfo.shipping_fee);
                        if (totalEl && cartInfo.grand_total !== undefined) totalEl.textContent = formatVND(cartInfo.grand_total);

                        const appliedWrapper = document.getElementById('appliedCodeWrapper');
                        if (appliedWrapper) appliedWrapper.style.display = 'none';
                        const appliedCodeEl = document.getElementById('appliedDiscountCode');
                        if (appliedCodeEl) appliedCodeEl.textContent = '';
                    } else {
                        if (messageEl) {
                            messageEl.textContent = data.message || 'Không thể bỏ mã giảm giá.';
                            messageEl.className = 'mt-2 small text-danger';
                        }
                    }
                })
                .catch(() => {
                    if (messageEl) {
                        messageEl.textContent = 'Có lỗi xảy ra khi bỏ mã giảm giá.';
                        messageEl.className = 'mt-2 small text-danger';
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                });
        });
    }

})();

const cartItems = @json($cart['items'] ?? []);
const cartSubtotal = {{ $cart['subtotal'] ?? 0 }};
const cartDiscount = {{ !empty($cart['discount_code']) ? (int)($cart['discount_total'] ?? 0) : 0 }};
const cartShipping = {{ $cart['shipping_fee'] ?? 0 }};
const cartTotal = {{ $cart['grand_total'] ?? 0 }};
const discountCode = @json($cart['discount_code'] ?? null);

function formatVND(amount) {
    return new Intl.NumberFormat('vi-VN').format(Number(amount || 0)) + ' đ';
}

function parseNumberFromText(str) {
    if (!str) return 0;
    return Number(String(str).replace(/[^0-9]/g, '')) || 0;
}

function confirmOrder(btn) {
    let timerInterval;
    let countdown = 5;

    if (!cartItems || !cartItems.length) {
        Swal.fire('Giỏ hàng trống', '', 'warning');
        return;
    }

    // Build table HTML for items
    let itemsHtml = '<div style="text-align:left;">';
    itemsHtml += '<table style="width:100%; border-collapse:collapse;">';
    itemsHtml += '<thead><tr><th style="text-align:left; padding:6px 8px">Sản phẩm</th><th style="text-align:right; padding:6px 8px">Đơn giá</th><th style="text-align:right; padding:6px 8px">Số lượng</th><th style="text-align:right; padding:6px 8px">Thành tiền</th></tr></thead><tbody>';

    cartItems.forEach(item => {
        const name = item.name || 'Sản phẩm';
        const qty = Number(item.quantity || 1);
        const subtotal = Number(item.subtotal ?? (item.price * qty) ?? 0);
        const unit = Number(item.price ?? (qty ? subtotal/qty : 0));
        itemsHtml += `<tr><td style="padding:6px 8px; vertical-align:top">${name}</td><td style="padding:6px 8px; vertical-align:top; text-align:right">${formatVND(unit)}</td><td style="padding:6px 8px; vertical-align:top; text-align:right">${qty}</td><td style="padding:6px 8px; vertical-align:top; text-align:right">${formatVND(subtotal)}</td></tr>`;
    });

    itemsHtml += `</tbody></table>`;

    // Read latest values from DOM (allow dynamic updates)
    const subtotalText = document.getElementById('checkoutSubtotal')?.textContent || '';
    const discountText = document.getElementById('checkoutDiscount')?.textContent || '';
    const shippingText = document.getElementById('checkoutShipping')?.textContent || '';
    const totalText = document.getElementById('checkoutTotal')?.textContent || '';

    const currentSubtotal = parseNumberFromText(subtotalText);
    const currentDiscount = parseNumberFromText(discountText); // discount shown with leading '-', parse digits only
    const currentShipping = parseNumberFromText(shippingText);
    const currentTotal = parseNumberFromText(totalText);

    // Summary (subtotal, discount if any, shipping, total)
    itemsHtml += `<div style="margin-top:12px; text-align:right">`;
    itemsHtml += `<div>Tạm tính: ${formatVND(currentSubtotal)}</div>`;
    if (currentDiscount > 0) {
        // find code if available in data attribute
        const discountEl = document.getElementById('checkoutDiscount');
        const code = discountEl?.dataset?.code || '';
        if (code) {
            itemsHtml += `<div>Giảm giá (Mã: <strong>${code}</strong>): -${formatVND(currentDiscount)}</div>`;
        } else {
            itemsHtml += `<div>Giảm giá: -${formatVND(currentDiscount)}</div>`;
        }
    }
    itemsHtml += `<div>Phí vận chuyển: ${formatVND(currentShipping)}</div>`;
    itemsHtml += `<hr><div class="fw-semibold">Tổng: <b>${formatVND(currentTotal)}</b></div>`;
    itemsHtml += `</div>`;
    itemsHtml += '</div>';

    Swal.fire({
        title: 'Xác nhận đơn hàng',
        html: `Bạn chắc chắn muốn đặt các sản phẩm sau?<br>${itemsHtml}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `OK (${countdown})`,
        cancelButtonText: 'Hủy',
        width: 680,
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
