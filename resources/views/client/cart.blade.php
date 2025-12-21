@extends('client.layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<!-- Nếu navbar của bạn là fixed-top, thêm spacer hoặc padding-top -->
<section class="py-4 border-bottom" style="padding-top: 100px;">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
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
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold">Sản phẩm trong giỏ</h5>
                        @if(!empty($cart['items']))
                            <form method="POST" action="{{ route('cart.clear') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger">Xóa tất cả</button>
                            </form>
                        @endif
                    </div>
                    <div class="card-body">
                        @if(empty($cart['items']))
                            <div class="text-center py-5">
                                <i class="bi bi-cart-x" style="font-size: 4rem; color: #ccc;"></i>
                                <p class="text-muted mt-3 mb-4">Giỏ hàng của bạn đang trống</p>
                                <a href="{{ route('home') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" id="selectAllHeader" class="form-check-input">
                                            </th>
                                            <th>Sản phẩm</th>
                                            <th>Giá</th>
                                            <th>Số lượng</th>
                                            <th>Thành tiền</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cart['items'] as $index => $item)
                                            <tr class="cart-item-row" data-index="{{ $index }}" data-price="{{ $item['price'] ?? 0 }}">
                                                <td>
                                                    <input type="checkbox"
                                                           name="selected_items[]"
                                                           value="{{ $item['cart_item_id'] }}"
                                                           class="form-check-input item-checkbox"
                                                           checked>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-3">
                                                        @if($item['image'] ?? null)
                                                            <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                                        @else
                                                            <img src="{{ asset('assets/client/img/product/product-1.webp') }}" alt="{{ $item['name'] }}" class="rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                                        @endif
                                                        <div>
                                                            <strong>{{ $item['name'] ?? 'Sản phẩm' }}</strong>
                                                            @if(!empty($item['variant_name']))
                                                                <br><small class="text-primary fw-semibold">
                                                                    <i class="bi bi-tag-fill"></i> Biến thể: {{ $item['variant_name'] }}
                                                                </small>
                                                            @elseif(isset($item['variant_id']) && $item['variant_id'])
                                                                <br><small class="text-primary fw-semibold">
                                                                    <i class="bi bi-tag-fill"></i> Biến thể #{{ $item['variant_id'] }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ number_format($item['price'] ?? 0, 0, ',', '.') }} VNĐ</td>
                                                <td>
                                                    <form method="POST" action="{{ route('cart.update') }}" class="d-inline cart-update-form">
                                                        @csrf
                                                        <input type="hidden" name="cart_item_id" value="{{ $item['cart_item_id'] }}">
                                                        <div class="d-flex gap-2 align-items-center">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary quantity-decrease">-</button>
                                                            <input type="number" name="quantity" class="form-control form-control-sm text-center quantity-input" value="{{ $item['quantity'] ?? 1 }}" min="1" max="100" style="width: 70px;">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary quantity-increase">+</button>
                                                        </div>
                                                    </form>
                                                </td>
                                                <td><strong>{{ number_format(($item['quantity'] ?? 1) * ($item['price'] ?? 0), 0, ',', '.') }} VNĐ</strong></td>
                                                <td>
                                                    <form method="POST" action="{{ route('cart.remove') }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="cart_item_id" value="{{ $item['cart_item_id'] }}">
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header fw-semibold">Tóm tắt đơn hàng</div>
                    <div class="card-body" id="cartSummary"
                         data-shipping-fee="{{ (int)($cart['shipping_fee'] ?? 30000) }}"
                         data-discount-total="{{ request()->routeIs('checkout.*') ? (int)($cart['discount_total'] ?? 0) : 0 }}">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span>
                            <span id="selectedSubtotal">{{ number_format($cart['subtotal'] ?? 0, 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển</span>
                            <span id="selectedShipping">{{ number_format($cart['shipping_fee'] ?? 0, 0, ',', '.') }} VNĐ</span>
                        </div>
                        @if(request()->routeIs('checkout.*') && (($cart['discount_total'] ?? 0) > 0))
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Giảm giá</span>
                                <span>-{{ number_format($cart['discount_total'] ?? 0, 0, ',', '.') }} VNĐ</span>
                            </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between fw-semibold mb-3">
                            <span>Tổng cộng</span>
                            <span class="text-primary fs-5" id="selectedTotal">{{ number_format($cart['grand_total'] ?? 0, 0, ',', '.') }} VNĐ</span>
                        </div>

                        @if(!empty($cart['items']))
                            <form method="GET" action="{{ route('checkout.index') }}" id="checkoutForm">
                                <input type="hidden" name="selected_items" id="selectedItemsInput" value="">
                                <button type="submit" class="btn btn-primary w-100" id="checkoutBtn">Tiến hành thanh toán</button>
                            </form>
                        @else
                            <button class="btn btn-primary w-100" disabled>Tiến hành thanh toán</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const shippingFee = parseInt(document.getElementById('cartSummary')?.dataset.shippingFee) || 30000;
    const discountTotal = parseInt(document.getElementById('cartSummary')?.dataset.discountTotal) || 0;

    function calculateSelectedTotal() {
        let subtotal = 0;
        const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
        selectedCheckboxes.forEach(cb => {
            const row = cb.closest('.cart-item-row');
            const price = parseFloat(row.dataset.price) || 0;
            const quantity = parseInt(row.querySelector('.quantity-input').value) || 1;
            subtotal += price * quantity;
        });

        const grandTotal = Math.max(subtotal + shippingFee - discountTotal, 0);
        document.getElementById('selectedSubtotal').textContent = subtotal.toLocaleString('vi-VN') + ' VNĐ';
        document.getElementById('selectedShipping').textContent = shippingFee.toLocaleString('vi-VN') + ' VNĐ';
        document.getElementById('selectedTotal').textContent = grandTotal.toLocaleString('vi-VN') + ' VNĐ';

        const checkoutBtn = document.getElementById('checkoutBtn');
        const hasSelected = selectedCheckboxes.length > 0;
        checkoutBtn.disabled = !hasSelected;

        document.getElementById('selectedItemsInput').value = Array.from(selectedCheckboxes).map(cb => cb.value).join(',');
    }

    document.querySelectorAll('.item-checkbox').forEach(cb => cb.addEventListener('change', calculateSelectedTotal));
    document.getElementById('selectAllHeader')?.addEventListener('change', function() {
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = this.checked);
        calculateSelectedTotal();
    });

    document.querySelectorAll('.quantity-increase').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const form = this.closest('.cart-update-form');
            const input = form.querySelector('.quantity-input');
            const currentValue = parseInt(input.value) || 1;
            const maxValue = parseInt(input.getAttribute('max')) || 100;
            const newValue = Math.min(currentValue + 1, maxValue);
            if (newValue !== currentValue) {
                input.value = newValue;
                input.dispatchEvent(new Event('change', { bubbles: true, cancelable: true }));
            }
        });
    });

    document.querySelectorAll('.quantity-decrease').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const form = this.closest('.cart-update-form');
            const input = form.querySelector('.quantity-input');
            const currentValue = parseInt(input.value) || 1;
            const minValue = parseInt(input.getAttribute('min')) || 1;
            const newValue = Math.max(currentValue - 1, minValue);
            if (newValue !== currentValue) {
                input.value = newValue;
                input.dispatchEvent(new Event('change', { bubbles: true, cancelable: true }));
            }
        });
    });

    // Quantity input change (AJAX)
    document.querySelectorAll('.quantity-input').forEach(input => {
        let updateTimeout;
        input.addEventListener('change', function(e) {
            e.stopPropagation();
            const row = this.closest('.cart-item-row');
            const form = this.closest('.cart-update-form');
            if (!form) return;
            if (row) row.dataset.quantity = this.value;
            calculateSelectedTotal();

            if (updateTimeout) clearTimeout(updateTimeout);
            updateTimeout = setTimeout(() => {
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                input.disabled = true;
                if (submitBtn) submitBtn.disabled = true;

                const increaseBtn = form.querySelector('.quantity-increase');
                const decreaseBtn = form.querySelector('.quantity-decrease');
                if (increaseBtn) increaseBtn.disabled = true;
                if (decreaseBtn) decreaseBtn.disabled = true;

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (window.updateCartBadge && data.cart_count !== undefined) window.updateCartBadge(data.cart_count);
                        if (row) {
                            const price = parseFloat(row.dataset.price) || 0;
                            const quantity = parseInt(input.value) || 1;
                            const cells = row.querySelectorAll('td');
                            if (cells.length >= 5) {
                                const subtotalCell = cells[4].querySelector('strong');
                                if (subtotalCell) subtotalCell.textContent = (price * quantity).toLocaleString('vi-VN') + ' VNĐ';
                            }
                        }
                        calculateSelectedTotal();
                    } else {
                        input.value = row ? row.dataset.quantity : 1;
                        if (window.showNotification) window.showNotification(data.message || 'Có lỗi xảy ra!', 'error');
                    }
                })
                .catch(() => { input.value = row ? row.dataset.quantity : 1; })
                .finally(() => {
                    input.disabled = false;
                    if (submitBtn) submitBtn.disabled = false;
                    if (increaseBtn) increaseBtn.disabled = false;
                    if (decreaseBtn) decreaseBtn.disabled = false;
                });
            }, 300);
        });

        input.addEventListener('blur', calculateSelectedTotal);
    });

    // Remove & clear cart AJAX
    ['remove', 'clear'].forEach(type => {
        document.querySelectorAll(`form[action*="cart.${type}"]`).forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!confirm(type === 'clear' ? 'Bạn có chắc muốn xóa toàn bộ giỏ hàng?' : 'Bạn có chắc muốn xóa sản phẩm này?')) return;

                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (window.updateCartBadge && data.cart_count !== undefined) window.updateCartBadge(data.cart_count);
                        window.location.reload();
                    } else if (window.showNotification) {
                        window.showNotification(data.message || 'Có lỗi xảy ra!', 'error');
                    }
                })
                .catch(() => form.submit());
            });
        });
    });

    calculateSelectedTotal();
});
</script>
@endsection
