@extends('admin.layouts.admin')

@section('title', 'List Product')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        {{-- Hiển thị thông báo --}}
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
                        <h4 class="card-title flex-grow-1">Danh sách sản phẩm</h4>

                        <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
                            + Thêm sản phẩm
                        </a>
                    </div>

                    {{-- Bộ lọc --}}
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('products.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" class="form-control" name="search"
                                       value="{{ request('search') }}" placeholder="Tên sản phẩm hoặc SKU...">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Danh mục</label>
                                <select class="form-select" name="category_id">
                                    <option value="">Tất cả</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select" name="status">
                                    <option value="">Tất cả</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hoạt động</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Không hoạt động</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Giá từ</label>
                                <input type="number" class="form-control" name="price_min"
                                       value="{{ request('price_min') }}" min="0">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Giá đến</label>
                                <input type="number" class="form-control" name="price_max"
                                       value="{{ request('price_max') }}" min="0">
                            </div>

                            <div class="col-md-1">
                                <label class="form-label">Sắp xếp</label>
                                <select class="form-select" name="sort_by">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Mới nhất</option>
                                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Tên A-Z</option>
                                    <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Giá</option>
                                </select>
                            </div>

                            <div class="col-md-12 d-flex gap-2 align-items-end">
                                <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Lọc</button>
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary"><i class="bx bx-refresh"></i> Reset</a>
                            </div>
                        </form>
                    </div>

                    {{-- Export --}}
                    <div class="card-body border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('products.export-excel') }}" class="btn btn-sm btn-success me-2" onclick="showExportMessage('Excel')">
                                <i class="bx bx-file"></i> Xuất Excel
                            </a>
                            <a href="{{ route('products.export-pdf') }}" class="btn btn-sm btn-danger" onclick="showExportMessage('PDF')">
                                <i class="bx bx-file-pdf"></i> Xuất PDF
                            </a>
                        </div>
                        <small class="text-muted">
                            Hiển thị {{ $products->count() }} / {{ $products->total() }} sản phẩm
                        </small>
                    </div>

                    {{-- Danh sách --}}
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle table-hover">
                                <thead class="bg-light-subtle">
                                    <tr>
                                        <th>#</th>
                                        <th>Tên sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Danh mục</th>
                                        <th>Thương hiệu</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $product)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($product->primaryImage())
                                                        <img src="{{ asset('storage/' . $product->primaryImage()->image_path) }}" width="40" height="40" class="rounded">
                                                    @else
                                                        <i class="bx bx-image fs-24 text-muted"></i>
                                                    @endif
                                                    <div>
                                                        <a href="{{ route('products.show', $product) }}" class="fw-medium">{{ $product->name }}</a>
                                                        <div class="text-muted small">SKU: {{ $product->sku ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($product->sale_price && $product->sale_price < $product->price)
                                                    <span class="text-decoration-line-through text-muted">{{ number_format($product->price, 0, ',', '.') }}</span><br>
                                                    <span class="fw-bold text-danger">{{ number_format($product->sale_price, 0, ',', '.') }} VNĐ</span>
                                                @else
                                                    <span class="fw-bold">{{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                                                @endif
                                            </td>
                                            <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                                            <td>{{ $product->brand ?? 'N/A' }}</td>
                                            <td>
                                                @if($product->status)
                                                    <span class="badge bg-success">Hoạt động</span>
                                                @else
                                                    <span class="badge bg-danger">Ẩn</span>
                                                @endif
                                            </td>
                                            <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('products.show', $product) }}" class="btn btn-light btn-sm" title="Xem"><i class="bx bx-show"></i></a>
                                                <a href="{{ route('products.edit', $product) }}" class="btn btn-soft-primary btn-sm" title="Sửa"><i class="bx bx-edit"></i></a>
                                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa sản phẩm này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-soft-danger btn-sm" title="Xóa"><i class="bx bx-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">
                                                <i class="bx bx-package fs-48 mb-3 d-block"></i>
                                                <p>Không có sản phẩm nào</p>
                                                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">+ Thêm sản phẩm</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if($products->hasPages())
                            <div class="mt-3">
                                {{ $products->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showExportMessage(type) {
    const message = document.createElement('div');
    message.className = 'alert alert-info position-fixed';
    message.style.cssText = 'top:20px;right:20px;z-index:9999;';
    message.innerHTML = `<i class="bx bx-loader-alt bx-spin"></i> Đang xuất file ${type}...`;
    document.body.appendChild(message);
    setTimeout(() => message.remove(), 3000);
}
</script>

@endsection
