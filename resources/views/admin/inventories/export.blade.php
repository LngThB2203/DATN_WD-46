@extends('admin.layouts.admin')

@section('title', 'Xuất kho')

@section('content')
<div class="page-content">
    <div class="container-xxl">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h4>Xuất kho</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('inventories.export.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Sản phẩm *</label>
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
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kho *</label>
                            <select name="warehouse_id" class="form-select" required>
                                <option value="">-- Chọn kho --</option>
                                @foreach($warehouses as $w)
                                    <option value="{{ $w->id }}">{{ $w->warehouse_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Số lượng *</label>
                            <input type="number" name="quantity" value="1" min="1" class="form-control" required>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-warning">Xuất kho</button>
                            <a href="{{ route('inventories.received-orders') }}" class="btn btn-light">Quay về</a>
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

    variantSelect.innerHTML = '<option>Đang tải...</option>';
    variantSelect.disabled = true;

    if (productId) {
        fetch(`/admin/inventories/get-variants/${productId}`)
            .then(res => res.json())
            .then(data => {
                variantSelect.innerHTML = '<option value="">-- Không có biến thể --</option>';
                data.forEach(v => {
                    variantSelect.innerHTML += `<option value="${v.id}">${v.name}</option>`;
                });
                variantSelect.disabled = false;
            });
    }
});
</script>
@endsection
