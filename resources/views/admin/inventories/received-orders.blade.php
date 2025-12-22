@extends('admin.layouts.admin')

@section('title', 'Quản lý kho hàng')

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

        {{-- Thống kê tổng quan --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card border-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1">Tổng sản phẩm</h6>
                                <h4 class="mb-0">{{ $totalProducts }}</h4>
                            </div>
                            <div class="avatar-sm bg-primary bg-opacity-10 rounded">
                                <iconify-icon icon="solar:box-bold-duotone" class="fs-24 text-primary"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1">Tổng biến thể</h6>
                                <h4 class="mb-0">{{ $totalVariants }}</h4>
                            </div>
                            <div class="avatar-sm bg-success bg-opacity-10 rounded">
                                <iconify-icon icon="solar:widget-4-bold-duotone" class="fs-24 text-success">
                                </iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card border-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-1">Số lượng tồn kho</h6>
                                <h4 class="mb-0">{{ number_format($totalQuantity, 0, ',', '.') }}</h4>
                            </div>
                            <div class="avatar-sm bg-info bg-opacity-10 rounded">
                                <iconify-icon icon="solar:archive-bold-duotone" class="fs-24 text-info"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cảnh báo tồn kho thấp --}}
        @if($lowStockItems->count() > 0)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Cảnh báo tồn kho thấp ({{
                $lowStockItems->count() }} sản phẩm)</h5>
            <ul class="mb-0">
                @foreach($lowStockItems as $item)
                <li>
                    <strong>{{ $item->product->name }}</strong>
                    @if($item->variant)
                    <span class="badge bg-secondary">SKU: {{ $item->variant->sku }}</span>
                    @endif
                    còn tổng cộng
                    <span class="badge bg-danger">{{ $item->total_quantity }}</span>
                </li>
                @endforeach

                @if($lowStockItems->count() > 5)
                <li class="text-muted">... và {{ $lowStockItems->count() - 5 }} sản phẩm khác</li>
                @endif
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        {{-- Bộ lọc và tìm kiếm --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('inventories.received-orders') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="Tên sản phẩm...">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Sản phẩm</label>
                        <select name="product_id" class="form-select">
                            <option value="">Tất cả sản phẩm</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ request('product_id')==$p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Lọc</label>
                        <select name="low_stock" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="1" {{ request('low_stock') ? 'selected' : '' }}>Tồn kho thấp (≤10)</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i> Tìm kiếm
                        </button>
                        <a href="{{ route('inventories.received-orders') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise me-1"></i> Đặt lại
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-box-seam me-2"></i>Tồn kho hiện tại
                    <span class="badge bg-primary ms-2">{{ $totalStockItems }} bản ghi</span>
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('inventories.import.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Nhập kho
                    </a>
                    <a href="{{ route('inventories.export.create') }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-dash-circle me-1"></i> Xuất kho
                    </a>
                    <a href="{{ route('inventories.warehouse') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-building me-1"></i> Quản lý kho
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>#</th>
                                <th>Sản phẩm</th>
                                <th>Biến thể</th>
                                <th>Tổng tồn</th>
                                <th>Trạng thái</th>
                                <th>Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventories as $index => $inv)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    <strong>{{ $inv->product->name ?? 'Sản phẩm đã xóa' }}</strong>

                                </td>

                                <td>
                                    @if($inv->variant)
                                    <div class="small">
                                        <span class="badge bg-secondary">SKU: {{ $inv->variant->sku }}</span><br>

                                        @if($inv->variant->size)
                                        <span class="badge bg-info">{{ $inv->variant->size->size_name ??
                                            $inv->variant->size->name }}</span>
                                        @endif
                                        @if($inv->variant->scent)
                                        <span class="badge bg-success">{{ $inv->variant->scent->scent_name ??
                                            $inv->variant->scent->name }}</span>
                                        @endif
                                        @if($inv->variant->concentration)
                                        <span class="badge bg-warning text-dark">
                                            {{ $inv->variant->concentration->concentration_name ??
                                            $inv->variant->concentration->name }}
                                        </span>
                                        @endif
                                    </div>
                                    @else
                                    <span class="text-muted">Sản phẩm chính</span>
                                    @endif
                                </td>

                                <td>
                                    <strong class="text-primary">
                                        {{ number_format($inv->total_quantity) }}
                                    </strong>
                                </td>

                                <td>
                                    @php
                                    $threshold = 10;
                                    @endphp

                                    @if($inv->total_quantity <= $threshold) <span class="badge bg-danger">Sắp hết
                                        hàng</span>
                                        @elseif($inv->total_quantity <= $threshold * 2) <span
                                            class="badge bg-warning text-dark">Cần bổ sung</span>
                                            @else
                                            <span class="badge bg-success">Đủ hàng</span>
                                            @endif
                                </td>

                                <td>
                                    <a href="{{ route('inventories.stock.show', [
    $inv->product_id,
    $inv->variant_id
]) }}" class="btn btn-sm btn-outline-info">
                                        Xem
                                    </a>

                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <div class="card-footer border-top">
                    {{ $inventories->links() }}
                </div>
            </div>

        </div>
    </div>
    @endsection
