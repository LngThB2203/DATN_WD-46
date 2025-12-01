@extends('admin.layouts.admin')
@section('title', 'Nhập kho')
@section('content')
<div class="page-content">
    <div class="container-xxl">

        <div class="card mb-4">
            <div class="card-header"><h5>Nhập kho</h5></div>
            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('inventories.import.store') }}" method="POST" id="importForm">
                    @csrf
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label class="form-label">Sản phẩm</label>
                            <select id="productSelect" class="form-select" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Biến thể</label>
                            <select id="variantSelect" name="variant_id" class="form-select" required disabled>
                                <option value="">-- Chọn sản phẩm trước --</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kho</label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $w)
                                    <option value="{{ $w->id }}">{{ $w->warehouse_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Số lượng</label>
                            <input type="number" name="quantity" value="1" min="1" class="form-control" required>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">Nhập kho</button>
                            <button type="reset" class="btn btn-secondary">Hủy</button>
                            <a href="{{ route('inventories.received-orders') }}" class="btn btn-light">Quay về danh sách</a>
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

    if(productId) {
        fetch(`/admin/inventories/get-variants/${productId}`)
            .then(res => res.json())
            .then(data => {
                variantSelect.innerHTML = '';

                if(data.length === 0){
                    variantSelect.innerHTML = '<option value="">Không có biến thể</option>';
                } else {
                    variantSelect.innerHTML = '<option value="">-- Chọn biến thể --</option>';
                    data.forEach(v => {
                        variantSelect.innerHTML += `<option value="${v.id}">${v.name}</option>`;
                    });
                }
                variantSelect.disabled = false;
            })
            .catch(() => {
                variantSelect.innerHTML = '<option value="">Lỗi tải biến thể</option>';
                variantSelect.disabled = true;
            });
    }
});
</script>
@endsection
