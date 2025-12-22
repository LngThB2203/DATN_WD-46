@extends('admin.layouts.admin')

@section('title', 'Add Product')

@section('content')
<div class="page-content">
    <div class="container-xxl">

        {{-- Hiển thị thông báo --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Form thêm sản phẩm --}}
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                {{-- Cột trái - Hình ảnh --}}
                <div class="col-xl-3 col-lg-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div id="image-preview" class="mb-3">
                                <i class="bx bx-image fs-48 text-muted mb-3"></i>
                                <p class="text-muted">Chưa có ảnh nào được chọn</p>
                            </div>
                            <div>
                                <h5 class="fw-medium">Upload Images</h5>
                                <input type="file" name="images[]" id="images" class="form-control" multiple
                                    accept="image/*">
                                <small class="text-muted">Chọn nhiều ảnh (JPG, PNG, GIF)</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cột phải - Thông tin sản phẩm --}}
                <div class="col-xl-9 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Thông tin sản phẩm</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                {{-- Tên sản phẩm --}}
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tên sản phẩm <span
                                                class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            placeholder="Nhập tên sản phẩm" value="{{ old('name') }}" required>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- SKU --}}
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="sku" class="form-label">SKU</label>
                                        <input type="text" id="sku" name="sku"
                                            class="form-control @error('sku') is-invalid @enderror" placeholder="Mã SKU"
                                            value="{{ old('sku') }}">
                                        @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Danh mục --}}
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Danh mục</label>
                                        <select id="category_id" name="category_id"
                                            class="form-select @error('category_id') is-invalid @enderror">
                                            <option value="">-- Chọn danh mục --</option>
                                            @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id')==$category->id ?
                                                'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Thương hiệu --}}
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="brand" class="form-label">Thương hiệu</label>
                                        <input type="text" id="brand" name="brand"
                                            class="form-control @error('brand') is-invalid @enderror"
                                            placeholder="Nhập thương hiệu" value="{{ old('brand') }}">
                                        @error('brand')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Trạng thái --}}
                                <div class="col-lg-6">
                                    <div class="form-check form-switch mt-4">
                                        <input type="hidden" name="status" value="0">

                                        <input class="form-check-input" type="checkbox" id="status" name="status"
                                            value="1" {{ old('status') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="status">Kích hoạt sản phẩm</label>
                                    </div>
                                </div>
                            </div>


                            {{-- Mô tả --}}
                            <div class="mb-3 mt-4">
                                <label for="description" class="form-label">Mô tả sản phẩm</label>
                                <textarea id="description" name="description" rows="5"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Nhập mô tả chi tiết">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    

                    {{-- Quản lý tồn kho --}}
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Quản lý tồn kho</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                {{-- Kho hàng --}}
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="warehouse_id" class="form-label">Kho hàng</label>
                                        <select id="warehouse_id" name="warehouse_id"
                                            class="form-select @error('warehouse_id') is-invalid @enderror">
                                            <option value="">-- Chọn kho hàng --</option>
                                            @if(isset($warehouses))
                                                @foreach($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id')==$warehouse->id ? 'selected' : '' }}>
                                                        {{ $warehouse->warehouse_name ?? $warehouse->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('warehouse_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Chọn kho để thêm số lượng tồn kho cho sản phẩm</small>
                                    </div>
                                </div>

                                {{-- Số lượng tồn kho --}}
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="stock_quantity" class="form-label">Số lượng tồn kho <span id="stockRequired" class="text-danger d-none">*</span></label>
                                        <input type="number" id="stock_quantity" name="stock_quantity"
                                            class="form-control @error('stock_quantity') is-invalid @enderror"
                                            placeholder="Nhập số lượng" value="{{ old('stock_quantity') }}" min="0" step="1">
                                        @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Nhập số lượng sản phẩm có trong kho (bắt buộc nếu chọn kho)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <small><i class="bi bi-info-circle"></i> <strong>Lưu ý:</strong> Nếu không chọn kho hàng, bạn có thể thêm tồn kho sau trong phần Quản lý kho.</small>
                            </div>
                        </div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="p-3 bg-light mb-3 rounded">
                        <div class="row justify-content-end g-2">
                            <div class="col-lg-2">
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">Hủy</a>
                            </div>
                            <div class="col-lg-2">
                                <button type="submit" class="btn btn-primary w-100">Tạo sản phẩm</button>
                            </div>
                        </div>
                    </div>
                </div> {{-- end col 9 --}}
            </div> {{-- end row --}}
        </form>
    </div>
</div>

{{-- Preview ảnh --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Xử lý warehouse selection
    const warehouseSelect = document.getElementById('warehouse_id');
    const stockQuantityInput = document.getElementById('stock_quantity');
    const stockRequired = document.getElementById('stockRequired');

    if (warehouseSelect && stockQuantityInput) {
        warehouseSelect.addEventListener('change', function() {
            if (this.value) {
                stockQuantityInput.setAttribute('required', 'required');
                if (stockRequired) stockRequired.classList.remove('d-none');
            } else {
                stockQuantityInput.removeAttribute('required');
                if (stockRequired) stockRequired.classList.add('d-none');
                stockQuantityInput.value = '';
            }
        });

        // Trigger on load
        if (warehouseSelect.value) {
            warehouseSelect.dispatchEvent(new Event('change'));
        }
    }

    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('image-preview');

    imageInput.addEventListener('change', function(e) {
        const files = e.target.files;
        imagePreview.innerHTML = '';

        if (files.length > 0) {
            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-thumbnail m-1';
                        img.style.maxHeight = '100px';
                        imagePreview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            });
        } else {
            imagePreview.innerHTML = '<i class="bx bx-image fs-48 text-muted mb-3"></i><p class="text-muted">Chưa có ảnh nào được chọn</p>';
        }
    });
});
</script>
@endsection
