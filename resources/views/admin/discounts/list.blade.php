@extends('admin.layouts.admin')

@section('title', 'Quản lý mã giảm giá')

@section('content')
<div class="page-content">
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

        {{-- Debug info (có thể xóa sau) --}}
        @if(config('app.debug'))
            <div class="alert alert-info">
                <strong>Debug:</strong> Tổng số mã giảm giá: {{ $discounts->total() }}
            </div>
        @endif

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Danh sách mã giảm giá</h4>
                        <a href="{{ route('admin.discounts.create') }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-plus"></i> Thêm mã giảm giá
                        </a>
                    </div>

                    {{-- Bộ lọc --}}
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('admin.discounts.index') }}" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" class="form-control" name="search"
                                       value="{{ request('search') }}" placeholder="Mã giảm giá...">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select" name="status">
                                    <option value="">Tất cả</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Đã hết hạn</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Loại</label>
                                <select class="form-select" name="type">
                                    <option value="">Tất cả</option>
                                    <option value="percent" {{ request('type') == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                                    <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Số tiền cố định</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex gap-2 align-items-end">
                                <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Lọc</button>
                                <a href="{{ route('admin.discounts.index') }}" class="btn btn-outline-secondary"><i class="bx bx-refresh"></i></a>
                            </div>
                        </form>
                    </div>

                    {{-- Bảng danh sách --}}
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Mã</th>
                                        <th>Loại</th>
                                        <th>Giá trị</th>
                                        <th>Đơn tối thiểu</th>
                                        <th>Ngày bắt đầu</th>
                                        <th>Ngày hết hạn</th>
                                        <th>Đã dùng</th>
                                        <th>Giới hạn</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($discounts as $discount)
                                        <tr>
                                            <td><strong>{{ $discount->code }}</strong></td>
                                            <td>
                                                @if($discount->discount_type === 'percent')
                                                    <span class="badge bg-info">Phần trăm</span>
                                                @else
                                                    <span class="badge bg-primary">Cố định</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($discount->discount_type === 'percent')
                                                    {{ $discount->discount_value }}%
                                                @else
                                                    {{ number_format($discount->discount_value, 0, ',', '.') }} VNĐ
                                                @endif
                                            </td>
                                            <td>
                                                {{ $discount->min_order_value ? number_format($discount->min_order_value, 0, ',', '.') . ' VNĐ' : '-' }}
                                            </td>
                                            <td>{{ $discount->start_date ? $discount->start_date->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $discount->expiry_date ? $discount->expiry_date->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $discount->used_count }}</td>
                                            <td>{{ $discount->usage_limit ?? '∞' }}</td>
                                            <td>
                                                @if($discount->active && $discount->isValid())
                                                    <span class="badge bg-success">Hoạt động</span>
                                                @elseif($discount->expiry_date && now()->gt($discount->expiry_date))
                                                    <span class="badge bg-danger">Hết hạn</span>
                                                @elseif($discount->usage_limit && $discount->used_count >= $discount->usage_limit)
                                                    <span class="badge bg-warning">Hết lượt</span>
                                                @else
                                                    <span class="badge bg-secondary">Không hoạt động</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.discounts.edit', $discount) }}" 
                                                       class="btn btn-sm btn-light" title="Sửa">
                                                        <i class="bx bx-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.discounts.destroy', $discount) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Bạn có chắc muốn xóa mã giảm giá này?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <p class="text-muted mb-0">Chưa có mã giảm giá nào</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Phân trang --}}
                        <div class="d-flex justify-content-center mt-3">
                            {{ $discounts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

