@extends('admin.layouts.admin')

@section('title', 'Xuất kho')

@section('content')
<div class="page-content">
    <div class="container-xxl">

        {{-- Thông báo --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Xuất kho</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('inventories.export.store') }}" method="POST" id="exportForm">
                    @csrf
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Sản phẩm <span class="text-danger">*</span></label>
                            <select id="productSelect" name="product_id" class="form-select" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Biến thể</label>
                            <select id="variantSelect" name="variant_id" class="form-select" disabled>
                                <option value="">-- Chọn sản phẩm trước --</option>
                            </select>
                            <small class="text-muted">Để trống nếu sản phẩm không có biến thể</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kho <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                <option value="">-- Chọn kho --</option>
                                @foreach($warehouses as $w)
                                    <option value="{{ $w->id }}">{{ $w->warehouse_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" value="1" min="1" class="form-control" required>
                            <small class="text-muted">Số lượng sản phẩm cần xuất khỏi kho</small>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-dash-circle me-1"></i> Xuất kho
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Hủy
                            </button>
                            <a href="{{ route('inventories.received-orders') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Quay về danh sách
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
document.getElementById('productSelect').addEventListener('change', function () {
    let productId = this.value;
    let variantSelect = document.getElementById('variantSelect');

    variantSelect.innerHTML = '<option value="">Đang tải...</option>';
    variantSelect.disabled = true;

    if(productId) {
        fetch(`/admin/inventories/get-variants/${productId}`)
            .then(res => res.json())
            .then(data => {
                variantSelect.innerHTML = '';

                if(data.length === 0){
                    variantSelect.innerHTML = '<option value="">Không có biến thể (xuất kho sản phẩm chính)</option>';
                    variantSelect.disabled = false;
                } else {
                    variantSelect.innerHTML = '<option value="">-- Chọn biến thể (hoặc để trống) --</option>';
                    data.forEach(v => {
                        // Hiển thị thông tin chi tiết biến thể
                        let variantText = v.sku || 'SKU: ' + v.id;
                        if(v.size_name) variantText += ' - Size: ' + v.size_name;
                        if(v.scent_name) variantText += ' - Mùi: ' + v.scent_name;
                        if(v.concentration_name) variantText += ' - Nồng độ: ' + v.concentration_name;
                        variantSelect.innerHTML += `<option value="${v.id}">${variantText}</option>`;
                    });
                    variantSelect.disabled = false;
                }
            })
            .catch(() => {
                variantSelect.innerHTML = '<option value="">Lỗi tải biến thể</option>';
                variantSelect.disabled = true;
            });
    } else {
        variantSelect.innerHTML = '<option value="">-- Chọn sản phẩm trước --</option>';
        variantSelect.disabled = true;
    }
});
</script>
@endsection
