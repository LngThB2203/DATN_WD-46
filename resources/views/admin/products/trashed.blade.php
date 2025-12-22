@extends('admin.layouts.admin')

@section('title', 'Sản phẩm đã xóa')

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
                            Sản phẩm đã xóa
                        </h4>
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary">
                            <iconify-icon icon="solar:arrow-left-bold-duotone"></iconify-icon>
                            Quay lại danh sách
                        </a>
                    </div>

                    {{-- Bộ lọc --}}
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('products.trashed') }}" class="row g-3">
                            <div class="col-md-10">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" class="form-control" name="search"
                                       value="{{ request('search') }}" placeholder="Tên sản phẩm hoặc SKU...">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                            </div>
                        </form>
                    </div>

                    {{-- Bảng danh sách --}}
                    <div class="card-body">
                        @if($products->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-nowrap align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px">#</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Danh mục</th>
                                            <th>Thương hiệu</th>
                                            <th>Giá</th>
                                            <th>Ngày xóa</th>
                                            <th style="width: 150px" class="text-center">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $product)
                                            <tr>
                                                <td>{{ $products->firstItem() + $loop->index }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @php
                                                            $primaryImage = $product->galleries->where('is_primary', true)->first();
                                                            $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->image_path) : asset('assets/admin/images/default-product.png');
                                                        @endphp
                                                        <img src="{{ $imageUrl }}" alt="{{ $product->name }}" 
                                                             class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                        <div>
                                                            <strong>{{ $product->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">SKU: {{ $product->sku }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $product->category->category_name ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span>{{ $product->brand->name ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    @if($product->sale_price)
                                                        <del class="text-muted">{{ number_format($product->price, 0, ',', '.') }} VNĐ</del>
                                                        <br>
                                                        <strong class="text-danger">{{ number_format($product->sale_price, 0, ',', '.') }} VNĐ</strong>
                                                    @else
                                                        <strong>{{ number_format($product->price, 0, ',', '.') }} VNĐ</strong>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="text-muted">
                                                        {{ $product->deleted_at->format('d/m/Y H:i') }}
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $product->deleted_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <form action="{{ route('products.restore', $product->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('POST')
                                                            <button type="submit" class="btn btn-sm btn-success" 
                                                                    onclick="return confirm('Bạn có chắc muốn khôi phục sản phẩm này?')"
                                                                    title="Khôi phục">
                                                                <iconify-icon icon="solar:restart-bold-duotone"></iconify-icon>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('products.force-delete', $product->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                                    onclick="return confirm('Bạn có chắc muốn XÓA VĨNH VIỄN sản phẩm này? Hành động này không thể hoàn tác!')"
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
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <p class="text-muted mb-0">
                                        Hiển thị {{ $products->firstItem() }} - {{ $products->lastItem() }} / {{ $products->total() }} sản phẩm
                                    </p>
                                </div>
                                <div>
                                    {{ $products->links() }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <iconify-icon icon="solar:trash-bin-trash-bold-duotone" style="font-size: 64px; color: #ccc;"></iconify-icon>
                                <p class="text-muted mt-3 mb-0">Không có sản phẩm nào đã bị xóa</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

