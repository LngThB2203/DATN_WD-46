@extends('client.layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<section class="py-4 border-bottom">
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
                            <form method="POST" action="{{ route('cart.clear') }}" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?');">
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
                            <div class="mb-3">
                                <label class="form-check-label">
                                    <input type="checkbox" id="selectAll" class="form-check-input me-2">
                                    <strong>Chọn tất cả</strong>
                                </label>
                            </div>
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
                                            <tr class="cart-item-row" data-index="{{ $index }}" data-price="{{ $item['price'] ?? 0 }}" data-quantity="{{ $item['quantity'] ?? 1 }}">
                                                <td>
                                                    <input type="checkbox" 
                                                           name="selected_items[]" 
                                                           value="{{ $index }}" 
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
                                                            @if(isset($item['variant_id']) && $item['variant_id'])
                                                                <br><small class="text-muted">Biến thể #{{ $item['variant_id'] }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ number_format($item['price'] ?? 0, 0, ',', '.') }} VNĐ</td>
                                                <td>
                                                    <form method="POST" action="{{ route('cart.update') }}" class="d-inline cart-update-form">
                                                        @csrf
                                                        <input type="hidden" name="index" value="{{ $index }}">
                                                        <div class="d-flex gap-2 align-items-center">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary quantity-decrease">-</button>
                                                            <input type="number" name="quantity" class="form-control form-control-sm text-center quantity-input" value="{{ $item['quantity'] ?? 1 }}" min="1" max="100" style="width: 70px;">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary quantity-increase">+</button>
                                                        </div>
                                                    </form>
                                                </td>
                                                <td><strong>{{ number_format(($item['quantity'] ?? 1) * ($item['price'] ?? 0), 0, ',', '.') }} VNĐ</strong></td>
                                                <td>
                                                    <form method="POST" action="{{ route('cart.remove') }}" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                                        @csrf
                                                        <input type="hidden" name="index" value="{{ $index }}">
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
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span>
                            <span id="selectedSubtotal">{{ number_format($cart['subtotal'] ?? 0, 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển</span>
                            <span id="selectedShipping">{{ number_format($cart['shipping_fee'] ?? 0, 0, ',', '.') }} VNĐ</span>
                        </div>
                        @if(($cart['discount_total'] ?? 0) > 0)
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
                            <form method="POST" action="{{ route('checkout.index') }}" id="checkoutForm">
                                @csrf
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const shippingFee = {{ $cart['shipping_fee'] ?? 30000 }};
    const discountTotal = {{ $cart['discount_total'] ?? 0 }};

    // Tính tổng tiền các sản phẩm được chọn
    function calculateSelectedTotal() {
        let subtotal = 0;
        const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
        
        selectedCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('.cart-item-row');
            if (row) {
                const price = parseFloat(row.dataset.price) || 0;
                const quantity = parseInt(row.dataset.quantity) || 1;
                subtotal += price * quantity;
            }
        });

        const grandTotal = Math.max((subtotal + shippingFee) - discountTotal, 0);

        // Cập nhật UI
        document.getElementById('selectedSubtotal').textContent = subtotal.toLocaleString('vi-VN') + ' VNĐ';
        document.getElementById('selectedShipping').textContent = shippingFee.toLocaleString('vi-VN') + ' VNĐ';
        document.getElementById('selectedTotal').textContent = grandTotal.toLocaleString('vi-VN') + ' VNĐ';

        // Enable/disable nút thanh toán
        const checkoutBtn = document.getElementById('checkoutBtn');
        if (checkoutBtn) {
            checkoutBtn.disabled = selectedCheckboxes.length === 0;
        }

        // Cập nhật selected items input
        const selectedItems = Array.from(selectedCheckboxes).map(cb => cb.value).join(',');
        const selectedItemsInput = document.getElementById('selectedItemsInput');
        if (selectedItemsInput) {
            selectedItemsInput.value = selectedItems;
        }
    }

    // Chọn tất cả / Bỏ chọn tất cả
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');

    function toggleSelectAll(checked) {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = checked;
        });
        if (selectAllCheckbox) selectAllCheckbox.checked = checked;
        if (selectAllHeader) selectAllHeader.checked = checked;
        calculateSelectedTotal();
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            toggleSelectAll(this.checked);
        });
    }

    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            toggleSelectAll(this.checked);
        });
    }

    // Xử lý checkbox từng sản phẩm
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            calculateSelectedTotal();
            // Cập nhật trạng thái "Chọn tất cả"
            const allChecked = document.querySelectorAll('.item-checkbox:checked').length === itemCheckboxes.length;
            if (selectAllCheckbox) selectAllCheckbox.checked = allChecked;
            if (selectAllHeader) selectAllHeader.checked = allChecked;
        });
    });

    // Xử lý nút tăng/giảm số lượng
    document.querySelectorAll('.quantity-increase').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const currentValue = parseInt(input.value);
            const max = parseInt(input.getAttribute('max')) || 100;
            if (currentValue < max) {
                input.value = currentValue + 1;
                input.dispatchEvent(new Event('change'));
            }
        });
    });

    document.querySelectorAll('.quantity-decrease').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.nextElementSibling;
            const currentValue = parseInt(input.value);
            const min = parseInt(input.getAttribute('min')) || 1;
            if (currentValue > min) {
                input.value = currentValue - 1;
                input.dispatchEvent(new Event('change'));
            }
        });
    });

    // Tự động submit khi thay đổi số lượng
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const row = this.closest('.cart-item-row');
            if (row) {
                row.dataset.quantity = this.value;
                calculateSelectedTotal();
            }
            const form = this.closest('.cart-update-form');
            if (form) {
                form.submit();
            }
        });
    });

    // Xử lý form checkout
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
            if (selectedCheckboxes.length === 0) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
                return false;
            }
        });
    }

    // Tính tổng ban đầu
    calculateSelectedTotal();
});
</script>
@endsection
