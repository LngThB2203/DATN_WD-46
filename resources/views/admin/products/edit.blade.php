@extends('admin.layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="page-content">

    <!-- Start Container Fluid -->
    <div class="container-xxl">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-dark fw-medium mb-3">Current Images</h5>
                            <div id="current-images" class="mb-3">
                                @forelse($product->galleries as $gallery)
                                    <div class="position-relative d-inline-block me-2 mb-2">
                                        <img src="{{ asset('storage/' . $gallery->image_path) }}" alt="{{ $gallery->alt_text }}"
                                             class="img-fluid rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                        @if($gallery->is_primary)
                                            <span class="badge bg-primary position-absolute top-0 start-0">Primary</span>
                                        @endif
                                        <div class="position-absolute top-0 end-0">
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="deleteImage({{ $gallery->id }})" title="Delete">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        </div>
                                        @if(!$gallery->is_primary)
                                            <div class="position-absolute bottom-0 start-0">
                                                <button type="button" class="btn btn-sm btn-success"
                                                        onclick="setPrimary({{ $gallery->id }})" title="Set as Primary">
                                                    <i class="bx bx-star"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-muted">No images</p>
                                @endforelse
                            </div>

                            <div class="mt-3">
                                <h5 class="text-dark fw-medium">Add New Images</h5>
                                <input type="file" name="images[]" id="images" class="form-control" multiple accept="image/*">
                                <small class="text-muted">Chọn nhiều ảnh (JPG, PNG, GIF)</small>
                            </div>

                            <div id="image-preview" class="mt-3"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-9 col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Product Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                               placeholder="Product Name" value="{{ old('name', $product->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="sku" class="form-label">SKU</label>
                                        <input type="text" id="sku" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                               placeholder="Product SKU" value="{{ old('sku', $product->sku) }}">
                                        @error('sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                            <option value="">Choose a category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>




                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="brand" class="form-label">Brand</label>
                                        <input type="text" id="brand" name="brand" class="form-control @error('brand') is-invalid @enderror"
                                               placeholder="Product Brand" value="{{ old('brand', $product->brand) }}">
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
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                                                  name="description" rows="5" placeholder="Product description">{{ old('description', $product->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Pricing Details</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">VNĐ</span>
                                            <input type="number" id="price" name="price" class="form-control @error('price') is-invalid @enderror"
                                                   placeholder="0" value="{{ old('price', $product->price) }}" required step="0.01" min="0">
                                        </div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="sale_price" class="form-label">Sale Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">VNĐ</span>
                                            <input type="number" id="sale_price" name="sale_price" class="form-control @error('sale_price') is-invalid @enderror"
                                                   placeholder="0" value="{{ old('sale_price', $product->sale_price) }}" step="0.01" min="0">
                                        </div>
                                        @error('sale_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Quản lý tồn kho --}}
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Quản lý tồn kho</h4>
                        </div>
                        <div class="card-body">
                            {{-- Hiển thị tồn kho hiện tại --}}
                            @if($product->warehouseProducts->count() > 0)
                                <div class="mb-4">
                                    <h6 class="fw-semibold mb-3">Tồn kho hiện tại:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Kho hàng</th>
                                                    <th>Số lượng</th>
                                                    <th>Ngưỡng tối thiểu</th>
                                                    <th>Trạng thái</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($product->warehouseProducts as $wp)
                                                    <tr>
                                                        <td>{{ $wp->warehouse->warehouse_name ?? 'N/A' }}</td>
                                                        <td><strong>{{ $wp->quantity }}</strong></td>
                                                        <td>{{ $wp->min_stock_threshold ?? 0 }}</td>
                                                        <td>
                                                            @if($wp->isLowStock())
                                                                <span class="badge bg-warning">Sắp hết</span>
                                                            @else
                                                                <span class="badge bg-success">Đủ</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-2">
                                        <strong>Tổng tồn kho: {{ $product->stock_quantity }} sản phẩm</strong>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info mb-3">
                                    <small><i class="bi bi-info-circle"></i> Sản phẩm chưa có tồn kho trong bất kỳ kho nào.</small>
                                </div>
                            @endif

                            <hr>

                            <h6 class="fw-semibold mb-3">Cập nhật/Thêm tồn kho:</h6>
                            
                            {{-- Debug info (remove in production) --}}
                            @if(config('app.debug'))
                                <div class="alert alert-info mb-3">
                                    <small>Debug: Có {{ isset($warehouses) ? $warehouses->count() : 0 }} kho hàng</small>
                                </div>
                            @endif
                            
                            <div class="row">
                                {{-- Kho hàng --}}
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="warehouse_id" class="form-label">Kho hàng <span class="text-danger">*</span></label>
                                        <select id="warehouse_id" name="warehouse_id"
                                            class="form-select @error('warehouse_id') is-invalid @enderror">
                                            <option value="">-- Chọn kho hàng --</option>
                                            @if(isset($warehouses) && $warehouses->count() > 0)
                                                @foreach($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}" 
                                                        {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}
                                                        data-warehouse-name="{{ $warehouse->warehouse_name ?? $warehouse->name ?? 'Kho #' . $warehouse->id }}">
                                                        {{ $warehouse->warehouse_name ?? $warehouse->name ?? 'Kho #' . $warehouse->id }}
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="" disabled>Chưa có kho nào. Vui lòng tạo kho trước.</option>
                                            @endif
                                        </select>
                                        @error('warehouse_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Chọn kho để cập nhật số lượng tồn kho</small>
                                        @if(!isset($warehouses) || $warehouses->count() == 0)
                                            <div class="alert alert-warning mt-2 mb-0">
                                                <small><i class="bi bi-exclamation-triangle"></i> Chưa có kho nào. <a href="{{ route('inventories.warehouse.add') }}" target="_blank">Tạo kho mới</a></small>
                                            </div>
                                        @endif
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
                            <div class="alert alert-warning">
                                <small><i class="bi bi-exclamation-triangle"></i> <strong>Lưu ý:</strong> Nếu chọn kho và nhập số lượng, hệ thống sẽ cập nhật hoặc tạo mới tồn kho cho kho đó. Để quản lý chi tiết hơn, vui lòng vào phần Quản lý kho.</small>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-light mb-3 rounded">
                        <div class="row justify-content-end g-2">
                            <div class="col-lg-2">
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                            </div>
                            <div class="col-lg-2">
                                <button type="submit" class="btn btn-primary w-100">Update Product</button>




                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý warehouse selection
    const warehouseSelect = document.getElementById('warehouse_id');
    const stockQuantityInput = document.getElementById('stock_quantity');
    const stockRequired = document.getElementById('stockRequired');
    
    console.log('Warehouse select:', warehouseSelect);
    console.log('Stock quantity input:', stockQuantityInput);
    
    if (warehouseSelect && stockQuantityInput) {
        // Đảm bảo cả hai đều required khi có giá trị
        warehouseSelect.addEventListener('change', function() {
            console.log('Warehouse changed:', this.value);
            if (this.value) {
                stockQuantityInput.setAttribute('required', 'required');
                stockQuantityInput.removeAttribute('disabled');
                if (stockRequired) {
                    stockRequired.classList.remove('d-none');
                    stockRequired.textContent = '*';
                }
            } else {
                stockQuantityInput.removeAttribute('required');
                stockQuantityInput.setAttribute('disabled', 'disabled');
                stockQuantityInput.value = '';
                if (stockRequired) {
                    stockRequired.classList.add('d-none');
                }
            }
        });
        
        // Trigger on load để set initial state
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
            const label = document.createElement('p');
            label.className = 'text-muted mb-2';
            label.textContent = 'New Images:';
            imagePreview.appendChild(label);

            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-fluid rounded me-2 mb-2';
                        img.style.width = '80px';
                        img.style.height = '80px';
                        img.style.objectFit = 'cover';
                        img.title = file.name;

                        imagePreview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
});

function deleteImage(galleryId) {
    if (confirm('Bạn có chắc chắn muốn xóa ảnh này?')) {
        fetch(`/admin/products/gallery/${galleryId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa ảnh');
        });
    }
}

function setPrimary(galleryId) {
    fetch(`/admin/products/gallery/${galleryId}/set-primary`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi đặt ảnh chính');
    });
}
</script>
@endsection
