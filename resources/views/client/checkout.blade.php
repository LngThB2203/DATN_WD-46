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

            <div class="row g-4">
                <div class="col-lg-7">
            {{-- CUSTOMER INFO --}}
                    <div class="card mb-3">
                        <div class="card-header fw-semibold py-2">Thông tin người nhận</div>
                        <div class="card-body py-2">
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label small mb-1">Họ tên <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_name" class="form-control form-control-sm @error('customer_name') is-invalid @enderror"
                                        value="{{ old('customer_name', $defaultCustomer['customer_name'] ?? '') }}" 
                                        placeholder="Nhập họ tên người nhận" required>
                                    @if($isLoggedIn)
                                        <small class="text-muted small"><i class="bi bi-info-circle"></i> Đã điền sẵn từ tài khoản, bạn có thể chỉnh sửa</small>
                                    @endif
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label small mb-1">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="customer_email" class="form-control form-control-sm @error('customer_email') is-invalid @enderror"
                                        value="{{ old('customer_email', $defaultCustomer['customer_email'] ?? '') }}" 
                                        placeholder="Nhập email người nhận" required>
                                    @if($isLoggedIn)
                                        <small class="text-muted small"><i class="bi bi-info-circle"></i> Đã điền sẵn từ tài khoản, bạn có thể chỉnh sửa</small>
                                    @endif
                                    @error('customer_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                        <div class="col-12">
                                    <label class="form-label small mb-1">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_phone" class="form-control form-control-sm @error('customer_phone') is-invalid @enderror"
                                value="{{ old('customer_phone', $defaultCustomer['customer_phone'] ?? '') }}"
                                placeholder="Nhập số điện thoại" required>
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                                    <label class="form-label small mb-1">Địa chỉ <span class="text-danger">*</span></label>
                                    <textarea name="shipping_address_line" class="form-control form-control-sm @error('shipping_address_line') is-invalid @enderror"
                                        rows="2" placeholder="Nhập địa chỉ giao hàng" required>{{ old('shipping_address_line', $defaultCustomer['shipping_address_line'] ?? '') }}</textarea>
                            @error('shipping_address_line')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

                    {{-- CUSTOMER NOTE --}}
                    <div class="card mb-3">
                        <div class="card-header fw-semibold py-2">Thông tin giao hàng</div>
                        <div class="card-body py-2">

                            <label class="form-label small mb-1">Ghi chú cho đơn hàng</label>
                            <textarea name="customer_note"
                                      class="form-control form-control-sm @error('customer_note') is-invalid @enderror"
                                      rows="2">{{ old('customer_note') }}</textarea>

                            @error('customer_note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                        </div>
                    </div>

                    {{-- PAYMENT METHODS --}}
                    <div class="card">
                        <div class="card-header fw-semibold py-2">Phương thức thanh toán</div>
                        <div class="card-body py-2">

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
                    <div class="card h-100">
                        <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                            <span>Đơn hàng</span>
                            <span class="badge bg-secondary">{{ count($cart['items'] ?? []) }} sản phẩm</span>
                        </div>

                        <div class="card-body">

                            @if(!empty($cart['items']))
                                <div class="mb-3">
                                    @foreach($cart['items'] as $item)
                                        <div class="py-2 border-bottom">
                                            <div class="d-flex align-items-start gap-2">
                                                {{-- Product Image --}}
                                                <div class="flex-shrink-0">
                                                    @if(!empty($item['image']))
                                                        <img src="{{ asset('storage/' . $item['image']) }}" 
                                                             alt="{{ $item['name'] ?? 'Sản phẩm' }}"
                                                             class="img-thumbnail"
                                                             style="width: 60px; height: 60px; object-fit: cover;"
                                                             onerror="this.onerror=null; this.src='{{ asset('assets/client/img/product/default.jpg') }}';">
                                                    @else
                                                        <img src="{{ asset('assets/client/img/product/default.jpg') }}" 
                                                             alt="{{ $item['name'] ?? 'Sản phẩm' }}"
                                                             class="img-thumbnail"
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                    @endif
                                                </div>
                                                {{-- Product Info --}}
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold small">{{ $item['name'] ?? 'Sản phẩm' }}</div>
                                                    @if(!empty($item['variant_name']))
                                                        <div class="small mt-1">
                                                            @php
                                                                $variantParts = explode(' • ', $item['variant_name']);
                                                            @endphp
                                                            @foreach($variantParts as $part)
                                                                @if(strpos($part, 'Kích thước:') !== false)
                                                                    <span class="badge bg-secondary me-1" style="font-size: 0.7rem;">
                                                                        <i class="bi bi-rulers"></i> {{ $part }}
                                                                    </span>
                                                                @elseif(strpos($part, 'Mùi hương:') !== false)
                                                                    <span class="badge bg-info me-1" style="font-size: 0.7rem;">
                                                                        <i class="bi bi-flower1"></i> {{ $part }}
                                                                    </span>
                                                                @elseif(strpos($part, 'Nồng độ:') !== false)
                                                                    <span class="badge bg-warning text-dark me-1" style="font-size: 0.7rem;">
                                                                        <i class="bi bi-droplet"></i> {{ $part }}
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-light text-dark me-1" style="font-size: 0.7rem;">{{ $part }}</span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="small text-muted mt-1">
                                                            <span class="badge bg-light text-dark" style="font-size: 0.7rem;">Không có biến thể</span>
                                                        </div>
                                                    @endif
                                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                                        <div class="small text-muted">
                                                            Số lượng: <strong>x{{ $item['quantity'] ?? 1 }}</strong>
                                            </div>
                                                        <div class="text-end fw-semibold small">
                                                {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }} đ
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mb-3 small text-muted">
                                    Mỗi đơn hàng được mua tối đa 10 sản phẩm. Nếu có nhu cầu mua số lượng lớn, vui lòng liên hệ.
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

    const subtotalEl = document.getElementById('checkoutSubtotal');
    const discountEl = document.getElementById('checkoutDiscount');
    const shippingEl = document.getElementById('checkoutShipping');
    const grandTotalEl = document.getElementById('checkoutTotal');
    const appliedInfoEl = document.getElementById('appliedDiscountInfo');
    const appliedCodeEl = document.getElementById('appliedDiscountCode');

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
                        messageEl.textContent = (data && data.message) || 'Mã giảm giá không hợp lệ.';
                        messageEl.className = 'mt-2 small text-danger';

                        const cart = (data && data.cart) || null;
                        if (cart) {
                            const formatNumber = (value) => {
                                const num = Number(value) || 0;
                                return num.toLocaleString('vi-VN');
                            };

                            if (subtotalEl && typeof cart.subtotal !== 'undefined') {
                                subtotalEl.textContent = formatNumber(cart.subtotal) + ' đ';
                            }
                            if (discountEl && typeof cart.discount_total !== 'undefined') {
                                discountEl.textContent = '- ' + formatNumber(cart.discount_total) + ' đ';
                            }
                            if (shippingEl && typeof cart.shipping_fee !== 'undefined') {
                                shippingEl.textContent = formatNumber(cart.shipping_fee) + ' đ';
                            }
                            if (grandTotalEl && typeof cart.grand_total !== 'undefined') {
                                grandTotalEl.textContent = formatNumber(cart.grand_total) + ' đ';
                            }

                            if (appliedInfoEl && appliedCodeEl) {
                                appliedInfoEl.style.display = 'none';
                                appliedCodeEl.textContent = '';
                            }
                        }
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

    // Lấy thông tin từ form
    const form = btn.closest('form');
    const customerName = form.querySelector('input[name="customer_name"]')?.value || 
                        form.querySelector('input[name="customer_name"]')?.textContent || 
                        'Chưa nhập';
    const customerEmail = form.querySelector('input[name="customer_email"]')?.value || 
                         form.querySelector('input[name="customer_email"]')?.textContent || 
                         'Chưa nhập';
    const customerPhone = form.querySelector('input[name="customer_phone"]')?.value || 
                         'Chưa nhập';
    const shippingAddress = form.querySelector('textarea[name="shipping_address_line"]')?.value || 
                           form.querySelector('input[name="shipping_address_line"]')?.value || 
                           'Chưa nhập';
    const customerNote = form.querySelector('textarea[name="customer_note"]')?.value || '';
    const paymentMethod = form.querySelector('input[name="payment_method"]:checked')?.value === 'cod' 
                         ? 'Thanh toán khi nhận hàng (COD)' 
                         : 'Thanh toán online (VNPay/MoMo)';

    // Build HTML content
    let contentHtml = '<div style="text-align:left; max-height:70vh; overflow-y:auto;">';
    
    // Thông tin người nhận
    contentHtml += '<div style="background:#f8f9fa; padding:12px; border-radius:6px; margin-bottom:16px;">';
    contentHtml += '<h6 style="margin:0 0 8px 0; color:#0056b3; font-weight:600;">📋 Thông tin người nhận</h6>';
    contentHtml += `<div style="margin-bottom:4px;"><strong>Họ tên:</strong> ${customerName}</div>`;
    contentHtml += `<div style="margin-bottom:4px;"><strong>Email:</strong> ${customerEmail}</div>`;
    contentHtml += `<div style="margin-bottom:4px;"><strong>Số điện thoại:</strong> ${customerPhone}</div>`;
    contentHtml += `<div style="margin-bottom:4px;"><strong>Địa chỉ giao hàng:</strong> ${shippingAddress}</div>`;
    if (customerNote) {
        contentHtml += `<div style="margin-bottom:4px;"><strong>Ghi chú:</strong> <em class="text-muted">${customerNote}</em></div>`;
    }
    contentHtml += `<div style="margin-top:8px;"><strong>Phương thức thanh toán:</strong> <span class="badge bg-info">${paymentMethod}</span></div>`;
    contentHtml += '</div>';

    // Sản phẩm
    contentHtml += '<h6 style="margin:0 0 12px 0; color:#0056b3; font-weight:600;">🛍️ Sản phẩm đã chọn</h6>';
    contentHtml += '<table style="width:100%; border-collapse:collapse; margin-bottom:16px;">';
    contentHtml += '<thead><tr style="background:#e9ecef;">';
    contentHtml += '<th style="text-align:left; padding:8px; font-size:12px; width:60px;">Hình ảnh</th>';
    contentHtml += '<th style="text-align:left; padding:8px; font-size:12px;">Sản phẩm</th>';
    contentHtml += '<th style="text-align:right; padding:8px; font-size:12px;">Đơn giá</th>';
    contentHtml += '<th style="text-align:center; padding:8px; font-size:12px;">SL</th>';
    contentHtml += '<th style="text-align:right; padding:8px; font-size:12px;">Thành tiền</th>';
    contentHtml += '</tr></thead><tbody>';

    cartItems.forEach(item => {
        const name = item.name || 'Sản phẩm';
        const qty = Number(item.quantity || 1);
        const subtotal = Number(item.subtotal ?? (item.price * qty) ?? 0);
        const unit = Number(item.price ?? (qty ? subtotal/qty : 0));
        const variantName = item.variant_name || '';
        const imageUrl = item.image ? `{{ asset('storage/') }}/${item.image}` : '{{ asset('assets/client/img/product/default.jpg') }}';
        
        contentHtml += '<tr style="border-bottom:1px solid #dee2e6;">';
        // Hình ảnh
        contentHtml += '<td style="padding:8px; vertical-align:top;">';
        contentHtml += `<img src="${imageUrl}" alt="${name}" style="width:50px; height:50px; object-fit:cover; border-radius:4px; border:1px solid #dee2e6;" onerror="this.onerror=null; this.src='{{ asset('assets/client/img/product/default.jpg') }}';">`;
        contentHtml += '</td>';
        // Thông tin sản phẩm
        contentHtml += '<td style="padding:8px; vertical-align:top;">';
        contentHtml += `<div style="font-weight:600; margin-bottom:4px;">${name}</div>`;
        if (variantName) {
            const variantParts = variantName.split(' • ');
            variantParts.forEach(part => {
                if (part.includes('Kích thước:')) {
                    contentHtml += `<span style="background:#6c757d; color:white; padding:2px 6px; border-radius:3px; font-size:10px; margin-right:4px; display:inline-block; margin-bottom:2px;">${part}</span>`;
                } else if (part.includes('Mùi hương:')) {
                    contentHtml += `<span style="background:#0dcaf0; color:white; padding:2px 6px; border-radius:3px; font-size:10px; margin-right:4px; display:inline-block; margin-bottom:2px;">${part}</span>`;
                } else if (part.includes('Nồng độ:')) {
                    contentHtml += `<span style="background:#ffc107; color:black; padding:2px 6px; border-radius:3px; font-size:10px; margin-right:4px; display:inline-block; margin-bottom:2px;">${part}</span>`;
                } else {
                    contentHtml += `<span style="background:#f8f9fa; color:black; padding:2px 6px; border-radius:3px; font-size:10px; margin-right:4px; display:inline-block; margin-bottom:2px;">${part}</span>`;
                }
            });
        }
        contentHtml += '</td>';
        contentHtml += `<td style="padding:8px; vertical-align:top; text-align:right; font-size:12px;">${formatVND(unit)}</td>`;
        contentHtml += `<td style="padding:8px; vertical-align:top; text-align:center; font-size:12px;"><span style="background:#0d6efd; color:white; padding:2px 8px; border-radius:12px;">${qty}</span></td>`;
        contentHtml += `<td style="padding:8px; vertical-align:top; text-align:right; font-weight:600; font-size:12px; color:#0056b3;">${formatVND(subtotal)}</td>`;
        contentHtml += '</tr>';
    });

    contentHtml += '</tbody></table>';

    // Read latest values from DOM
    const subtotalText = document.getElementById('checkoutSubtotal')?.textContent || '';
    const discountText = document.getElementById('checkoutDiscount')?.textContent || '';
    const shippingText = document.getElementById('checkoutShipping')?.textContent || '';
    const totalText = document.getElementById('checkoutTotal')?.textContent || '';

    const currentSubtotal = parseNumberFromText(subtotalText);
    const currentDiscount = parseNumberFromText(discountText);
    const currentShipping = parseNumberFromText(shippingText);
    const currentTotal = parseNumberFromText(totalText);

    // Tổng tiền
    contentHtml += '<div style="background:#f8f9fa; padding:12px; border-radius:6px; margin-top:12px;">';
    contentHtml += '<h6 style="margin:0 0 8px 0; color:#0056b3; font-weight:600;">💰 Tổng tiền đơn hàng</h6>';
    contentHtml += '<div style="display:flex; justify-content:space-between; margin-bottom:4px;">';
    contentHtml += '<span>Tạm tính:</span>';
    contentHtml += `<span><strong>${formatVND(currentSubtotal)}</strong></span>`;
    contentHtml += '</div>';
    
    if (currentDiscount > 0) {
        const discountEl = document.getElementById('checkoutDiscount');
        const code = discountEl?.dataset?.code || '';
        contentHtml += '<div style="display:flex; justify-content:space-between; margin-bottom:4px;">';
        contentHtml += `<span>Giảm giá${code ? ' (Mã: <strong>' + code + '</strong>)' : ''}:</span>`;
        contentHtml += `<span style="color:#dc3545;"><strong>-${formatVND(currentDiscount)}</strong></span>`;
        contentHtml += '</div>';
    }
    
    contentHtml += '<div style="display:flex; justify-content:space-between; margin-bottom:4px;">';
    contentHtml += '<span>Phí vận chuyển:</span>';
    contentHtml += `<span><strong>${formatVND(currentShipping)}</strong></span>`;
    contentHtml += '</div>';
    
    contentHtml += '<hr style="margin:8px 0;">';
    contentHtml += '<div style="display:flex; justify-content:space-between; font-size:18px; font-weight:700; color:#0056b3;">';
    contentHtml += '<span>Tổng cộng:</span>';
    contentHtml += `<span>${formatVND(currentTotal)}</span>`;
    contentHtml += '</div>';
    contentHtml += '</div>';
    
    contentHtml += '</div>';

    Swal.fire({
        title: 'Xác nhận đơn hàng',
        html: `Bạn chắc chắn muốn đặt đơn hàng này?<br><br>${contentHtml}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: `Xác nhận (${countdown})`,
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#0056b3',
        width: 750,
        didOpen: () => {
            const confirmBtn = Swal.getConfirmButton();
            confirmBtn.disabled = true;
            timerInterval = setInterval(() => {
                countdown--;
                confirmBtn.innerText = `Xác nhận (${countdown})`;
                if(countdown <= 0){
                    clearInterval(timerInterval);
                    confirmBtn.disabled = false;
                    confirmBtn.innerText = 'Xác nhận';
                }
            }, 1000);
        },
        willClose: () => {
            if (timerInterval) {
                clearInterval(timerInterval);
            }
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
