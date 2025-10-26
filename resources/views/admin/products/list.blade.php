@extends('admin.layouts.admin')

@section('title', 'List Product')

@section('content')
<div class="page-content">

    <!-- Start Container Fluid -->
    <div class="container-fluid">

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

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-1">
                        <h4 class="card-title flex-grow-1">All Product List</h4>

                        <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
                            Add Product
                        </a>

<<<<<<< Updated upstream
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle btn btn-sm btn-outline-light"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                This Month
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="#" class="dropdown-item">Download</a>
                                <!-- item-->
                                <a href="#" class="dropdown-item">Export</a>
                                <!-- item-->
                                <a href="#" class="dropdown-item">Import</a>
                            </div>
                        </div>
                    </div>

                    <!-- Filter and Search Form -->
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('products.index') }}" class="row g-3">
                            <!-- Search -->
                            <div class="col-md-3">
                                <label for="search" class="form-label">Tìm kiếm</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Tên sản phẩm hoặc SKU...">
                            </div>

                            <!-- Category Filter -->
                            <div class="col-md-2">
                                <label for="category_id" class="form-label">Danh mục</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Tất cả danh mục</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-2">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Không hoạt động</option>
                                </select>
                            </div>

                            <!-- Price Range -->
                            <div class="col-md-2">
                                <label for="price_min" class="form-label">Giá từ</label>
                                <input type="number" class="form-control" id="price_min" name="price_min" 
                                       value="{{ request('price_min') }}" placeholder="0" min="0">
                            </div>

                            <div class="col-md-2">
                                <label for="price_max" class="form-label">Giá đến</label>
                                <input type="number" class="form-control" id="price_max" name="price_max" 
                                       value="{{ request('price_max') }}" placeholder="1000000" min="0">
                            </div>

                            <!-- Sort -->
                            <div class="col-md-1">
                                <label for="sort_by" class="form-label">Sắp xếp</label>
                                <select class="form-select" id="sort_by" name="sort_by">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Mới nhất</option>
                                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Tên A-Z</option>
                                    <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Giá</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-12 d-flex gap-2 align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-search"></i> Lọc
                                </button>
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-refresh"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                    <div>
<<<<<<< Updated upstream
                        <!-- Results Summary -->
                        @if(request()->hasAny(['search', 'category_id', 'status', 'price_min', 'price_max']))
=======
=======
                        <!-- Export Buttons -->
                        <div class="btn-group" role="group">
                            <a href="{{ route('products.export-excel') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
                               class="btn btn-sm btn-success" 
                               title="Xuất Excel"
                               onclick="showExportMessage('Excel')">
                                <i class="bx bx-file"></i> Excel
                            </a>
                            <a href="{{ route('products.export-pdf') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" 
                               class="btn btn-sm btn-danger" 
                               title="Xuất PDF"
                               onclick="showExportMessage('PDF')">
                                <i class="bx bx-file-pdf"></i> PDF
                            </a>
                        </div>
                    </div>

                    <!-- Filter and Search Form -->
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('products.index') }}" class="row g-3">
                            <!-- Search -->
                            <div class="col-md-3">
                                <label for="search" class="form-label">Tìm kiếm</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Tên sản phẩm hoặc SKU...">
                            </div>

                            <!-- Category Filter -->
                            <div class="col-md-2">
                                <label for="category_id" class="form-label">Danh mục</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Tất cả danh mục</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-2">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Không hoạt động</option>
                                </select>
                            </div>

                            <!-- Brand Filter -->
                            <div class="col-md-2">
                                <label for="brand" class="form-label">Thương hiệu</label>
                                <input type="text" class="form-control" id="brand" name="brand" 
                                       value="{{ request('brand') }}" placeholder="Tên thương hiệu...">
                            </div>

                            <!-- Price Range -->
                            <div class="col-md-2">
                                <label for="price_min" class="form-label">Giá từ</label>
                                <input type="number" class="form-control" id="price_min" name="price_min" 
                                       value="{{ request('price_min') }}" placeholder="0" min="0">
                            </div>

                            <div class="col-md-2">
                                <label for="price_max" class="form-label">Giá đến</label>
                                <input type="number" class="form-control" id="price_max" name="price_max" 
                                       value="{{ request('price_max') }}" placeholder="1000000" min="0">
                            </div>

                            <!-- Sort -->
                            <div class="col-md-1">
                                <label for="sort_by" class="form-label">Sắp xếp</label>
                                <select class="form-select" id="sort_by" name="sort_by">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Mới nhất</option>
                                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Tên A-Z</option>
                                    <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Giá</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-12 d-flex gap-2 align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-search"></i> Lọc
                                </button>
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-refresh"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                    <div>
                        <!-- Results Summary -->
                        @if(request()->hasAny(['search', 'category_id', 'status', 'brand', 'price_min', 'price_max']))
>>>>>>> Stashed changes
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Hiển thị {{ $products->count() }} / {{ $products->total() }} sản phẩm
                                    @if(request('search'))
                                        | Tìm kiếm: "{{ request('search') }}"
                                    @endif
                                    @if(request('category_id'))
                                        | Danh mục: {{ $categories->where('id', request('category_id'))->first()->category_name ?? 'N/A' }}
                                    @endif
                                    @if(request('status') !== null)
                                        | Trạng thái: {{ request('status') ? 'Hoạt động' : 'Không hoạt động' }}
                                    @endif
<<<<<<< Updated upstream
=======
                                    @if(request('brand'))
                                        | Thương hiệu: "{{ request('brand') }}"
                                    @endif
>>>>>>> Stashed changes
                                </small>
                                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bx bx-x"></i> Xóa bộ lọc
                                </a>
                            </div>
                        </div>
                        @endif

<<<<<<< Updated upstream
=======
>>>>>>> Stashed changes
>>>>>>> Stashed changes
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 table-hover table-centered">
                                <thead class="bg-light-subtle">
                                    <tr>
                                        <th style="width: 20px;">
                                            <div class="form-check ms-1">
                                                <input type="checkbox" class="form-check-input" id="customCheck1">
                                                <label class="form-check-label" for="customCheck1"></label>
                                            </div>
                                        </th>
<<<<<<< Updated upstream
                                        <th>Tên sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Danh mục</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
=======
<<<<<<< Updated upstream
                                        <th>Product Name & Size</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Category</th>
                                        <th>Rating</th>
                                        <th>Action</th>
=======
                                        <th>Tên sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Danh mục</th>
                                        <th>Thương hiệu</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
>>>>>>> Stashed changes
>>>>>>> Stashed changes
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="form-check ms-1">
                                                <input type="checkbox" class="form-check-input" id="customCheck{{ $product->id }}">
                                                <label class="form-check-label" for="customCheck{{ $product->id }}">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center">
                                                    @if($product->primaryImage())
                                                        <img src="{{ asset('storage/' . $product->primaryImage()->image_path) }}" alt="{{ $product->name }}" class="avatar-md">
                                                    @else
                                                        <i class="bx bx-image fs-24 text-muted"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <a href="{{ route('products.show', $product) }}" class="text-dark fw-medium fs-15">{{ $product->name }}</a>
                                                    <p class="text-muted mb-0 mt-1 fs-13">SKU: {{ $product->sku ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
<<<<<<< Updated upstream
=======
<<<<<<< Updated upstream
                                            <p class="mb-1 text-muted"><span class="text-dark fw-medium">486 Item</span>
                                                Left</p>
                                            <p class="mb-0 text-muted">155 Sold</p>
=======
>>>>>>> Stashed changes
                                            <div>
                                                @if($product->sale_price && $product->sale_price < $product->price)
                                                    <span class="text-decoration-line-through text-muted">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span><br>
                                                    <span class="text-danger fw-bold">{{ number_format($product->sale_price, 0, ',', '.') }} VNĐ</span>
                                                @else
                                                    <span class="fw-bold">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $product->category->category_name ?? 'N/A' }}</td>
<<<<<<< Updated upstream
=======
                                        <td>{{ $product->brand ?? 'N/A' }}</td>
>>>>>>> Stashed changes
                                        <td>
                                            @if($product->status)
                                                <span class="badge bg-success">Hoạt động</span>
                                            @else
                                                <span class="badge bg-danger">Không hoạt động</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $product->created_at->format('d/m/Y H:i') }}
                                            </small>
<<<<<<< Updated upstream
=======
>>>>>>> Stashed changes
>>>>>>> Stashed changes
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('products.show', $product) }}" class="btn btn-light btn-sm" title="View">
                                                    <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                                </a>
                                                <a href="{{ route('products.edit', $product) }}" class="btn btn-soft-primary btn-sm" title="Edit">
                                                    <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                                </a>
                                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-soft-danger btn-sm" title="Delete">
                                                        <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
<<<<<<< Updated upstream
                                        <td colspan="7" class="text-center py-4">
=======
<<<<<<< Updated upstream
                                        <td>
                                            <div class="form-check ms-1">
                                                <input type="checkbox" class="form-check-input" id="customCheck2">
                                                <label class="form-check-label" for="customCheck2">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div
                                                    class="rounded bg-light avatar-md d-flex align-items-center justify-content-center">
                                                    <img src="assets/images/product/p-2.png" alt="" class="avatar-md">
                                                </div>
                                                <div>
                                                    <a href="product-list.html#!"
                                                        class="text-dark fw-medium fs-15">Olive Green Leather Bag</a>
                                                    <p class="text-muted mb-0 mt-1 fs-13"><span>Size : </span>S , M </p>
                                                </div>
                                            </div>

                                        </td>
                                        <td>$136.00</td>
                                        <td>
                                            <p class="mb-1 text-muted"><span class="text-dark fw-medium">784 Item</span>
                                                Left</p>
                                            <p class="mb-0 text-muted">674 Sold</p>
                                        </td>
                                        <td> Hand Bag</td>
                                        <td> <span class="badge p-1 bg-light text-dark fs-12 me-1"><i
                                                    class="bx bxs-star align-text-top fs-14 text-warning me-1"></i>
                                                4.1</span> 143 Review</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="product-list.html#!" class="btn btn-light btn-sm">
                                                    <iconify-icon icon="solar:eye-broken" class="align-middle fs-18">
                                                    </iconify-icon>
                                                </a>
                                                <a href="product-list.html#!" class="btn btn-soft-primary btn-sm">
                                                    <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18">
                                                    </iconify-icon>
                                                </a>
                                                <a href="product-list.html#!" class="btn btn-soft-danger btn-sm">
                                                    <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                        class="align-middle fs-18"></iconify-icon>
                                                </a>
=======
                                        <td colspan="8" class="text-center py-4">
>>>>>>> Stashed changes
                                            <div class="text-muted">
                                                <i class="bx bx-package fs-48 mb-3"></i>
                                                @if(request()->hasAny(['search', 'category_id', 'status', 'price_min', 'price_max']))
                                                    <p>Không tìm thấy sản phẩm nào phù hợp với bộ lọc</p>
                                                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary me-2">Xóa bộ lọc</a>
                                                @else
                                                    <p>Không có sản phẩm nào</p>
                                                @endif
                                                <a href="{{ route('products.create') }}" class="btn btn-primary">Thêm sản phẩm</a>
<<<<<<< Updated upstream
=======
>>>>>>> Stashed changes
>>>>>>> Stashed changes
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- end table-responsive -->
                    </div>
                    @if($products->hasPages())
                    <div class="card-footer border-top">
                        <nav aria-label="Page navigation example">
                            {{ $products->links() }}
                        </nav>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<<<<<<< Updated upstream
=======
<<<<<<< Updated upstream
=======
>>>>>>> Stashed changes

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const filterForm = document.querySelector('form[method="GET"]');
    const filterInputs = filterForm.querySelectorAll('select, input[type="number"]');
    
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Add a small delay for search input
            if (input.name === 'search') {
                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(() => {
                    filterForm.submit();
                }, 500);
            } else {
                filterForm.submit();
            }
        });
    });

    // Handle search input with debounce
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(() => {
                filterForm.submit();
            }, 500);
        });
    }

    // Clear filters button
    const clearFiltersBtn = document.querySelector('a[href="{{ route("products.index") }}"]');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '{{ route("products.index") }}';
        });
    }
});
<<<<<<< Updated upstream
</script>
=======

// Export message function
function showExportMessage(type) {
    // Create a temporary message
    const message = document.createElement('div');
    message.className = 'alert alert-info position-fixed';
    message.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    message.innerHTML = `
        <i class="bx bx-loader-alt bx-spin"></i> 
        Đang xuất file ${type}... Vui lòng chờ trong giây lát.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(message);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (message.parentNode) {
            message.parentNode.removeChild(message);
        }
    }, 5000);
}
</script>
>>>>>>> Stashed changes
>>>>>>> Stashed changes
@endsection

