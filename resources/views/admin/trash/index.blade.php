@extends('admin.layouts.admin')

@section('title', 'Thùng rác')

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
                        <h4 class="card-title flex-grow-1">
                            <iconify-icon icon="solar:trash-bin-trash-bold-duotone" class="me-2"></iconify-icon>
                            Thùng rác
                        </h4>
                        <span class="badge bg-danger">{{ $total }} mục đã xóa</span>
                    </div>

                    {{-- Bộ lọc --}}
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('admin.trash.index') }}" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Loại</label>
                                <select class="form-select" name="type" id="typeFilter">
                                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Tất cả ({{ $counts['all'] }})</option>
                                    <option value="products" {{ $type === 'products' ? 'selected' : '' }}>Sản phẩm ({{ $counts['products'] }})</option>
                                    <option value="categories" {{ $type === 'categories' ? 'selected' : '' }}>Danh mục ({{ $counts['categories'] }})</option>
                                    <option value="brands" {{ $type === 'brands' ? 'selected' : '' }}>Thương hiệu ({{ $counts['brands'] }})</option>
                                    <option value="variants" {{ $type === 'variants' ? 'selected' : '' }}>Biến thể ({{ $counts['variants'] }})</option>
                                    <option value="newsletters" {{ $type === 'newsletters' ? 'selected' : '' }}>Email đăng ký ({{ $counts['newsletters'] }})</option>
                                    <option value="contacts" {{ $type === 'contacts' ? 'selected' : '' }}>Liên hệ ({{ $counts['contacts'] }})</option>
                                    <option value="warehouses" {{ $type === 'warehouses' ? 'selected' : '' }}>Kho hàng ({{ $counts['warehouses'] }})</option>
                                    <option value="discounts" {{ $type === 'discounts' ? 'selected' : '' }}>Mã giảm giá ({{ $counts['discounts'] }})</option>
                                    <option value="reviews" {{ $type === 'reviews' ? 'selected' : '' }}>Đánh giá ({{ $counts['reviews'] }})</option>
                                    <option value="posts" {{ $type === 'posts' ? 'selected' : '' }}>Bài viết ({{ $counts['posts'] }})</option>
                                    <option value="banners" {{ $type === 'banners' ? 'selected' : '' }}>Banner ({{ $counts['banners'] }})</option>
                                    <option value="customers" {{ $type === 'customers' ? 'selected' : '' }}>Khách hàng ({{ $counts['customers'] }})</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" class="form-control" name="search"
                                       value="{{ $search }}" placeholder="Tìm kiếm theo tên, email, mã...">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">Lọc</button>
                            </div>
                        </form>
                    </div>

                    {{-- Bảng danh sách --}}
                    <div class="card-body">
                        @if($items->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-nowrap align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px">#</th>
                                            <th>Loại</th>
                                            <th>Tên / Mô tả</th>
                                            <th>Ngày xóa</th>
                                            <th style="width: 150px" class="text-center">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $index => $item)
                                            <tr>
                                                <td>{{ ($currentPage - 1) * $perPage + $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $item['type_label'] }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <strong>{{ $item['name'] }}</strong>
                                                        @if($item['description'])
                                                            <small class="text-muted">{{ $item['description'] }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">
                                                        {{ $item['deleted_at']->format('d/m/Y H:i') }}
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $item['deleted_at']->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <form action="{{ $item['restore_route'] }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('POST')
                                                            <button type="submit" class="btn btn-sm btn-success" 
                                                                    onclick="return confirm('Bạn có chắc muốn khôi phục mục này?')"
                                                                    title="Khôi phục">
                                                                <iconify-icon icon="solar:restart-bold-duotone"></iconify-icon>
                                                            </button>
                                                        </form>
                                                        <form action="{{ $item['force_delete_route'] }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                                    onclick="return confirm('Bạn có chắc muốn XÓA VĨNH VIỄN mục này? Hành động này không thể hoàn tác!')"
                                                                    title="Xóa vĩnh viễn">
                                                                <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone"></iconify-icon>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Phân trang --}}
                            @if($lastPage > 1)
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <p class="text-muted mb-0">
                                            Hiển thị {{ (($currentPage - 1) * $perPage) + 1 }} - 
                                            {{ min($currentPage * $perPage, $total) }} / {{ $total }} mục
                                        </p>
                                    </div>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0">
                                            @if($currentPage > 1)
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ route('admin.trash.index', ['type' => $type, 'search' => $search, 'page' => $currentPage - 1]) }}">
                                                        Trước
                                                    </a>
                                                </li>
                                            @endif

                                            @for($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++)
                                                <li class="page-item {{ $i === $currentPage ? 'active' : '' }}">
                                                    <a class="page-link" href="{{ route('admin.trash.index', ['type' => $type, 'search' => $search, 'page' => $i]) }}">
                                                        {{ $i }}
                                                    </a>
                                                </li>
                                            @endfor

                                            @if($currentPage < $lastPage)
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ route('admin.trash.index', ['type' => $type, 'search' => $search, 'page' => $currentPage + 1]) }}">
                                                        Sau
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <iconify-icon icon="solar:trash-bin-trash-bold-duotone" style="font-size: 64px; color: #ccc;"></iconify-icon>
                                <p class="text-muted mt-3 mb-0">Thùng rác trống</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('typeFilter').addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endsection

