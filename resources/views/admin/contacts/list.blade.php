@extends('admin.layouts.admin')

@section('title', 'Quản lý liên hệ')

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
                        <h4 class="card-title flex-grow-1">Danh sách liên hệ</h4>
                    </div>

                    {{-- Thống kê --}}
                    <div class="card-body border-bottom">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Tổng số</h5>
                                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Mới</h5>
                                        <h3 class="mb-0">{{ $stats['new'] }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Đã đọc</h5>
                                        <h3 class="mb-0">{{ $stats['read'] }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Đã phản hồi</h5>
                                        <h3 class="mb-0">{{ $stats['replied'] }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bộ lọc --}}
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('admin.contacts.index') }}" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" class="form-control" name="search"
                                       value="{{ request('search') }}" placeholder="Tên, email, chủ đề...">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select" name="status">
                                    <option value="">Tất cả</option>
                                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>Mới</option>
                                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Đã đọc</option>
                                    <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Đã phản hồi</option>
                                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Đã lưu trữ</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Sắp xếp</label>
                                <select class="form-select" name="sort_by">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Mới nhất</option>
                                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Tên A-Z</option>
                                    <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                                </select>
                            </div>

                            <div class="col-md-1">
                                <label class="form-label">Thứ tự</label>
                                <select class="form-select" name="sort_order">
                                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Giảm</option>
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Tăng</option>
                                </select>
                            </div>

                            <div class="col-md-12 d-flex gap-2 align-items-end">
                                <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Lọc</button>
                                <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary"><i class="bx bx-refresh"></i> Reset</a>
                            </div>
                        </form>
                    </div>

                    {{-- Bảng danh sách --}}
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Điện thoại</th>
                                        <th>Chủ đề</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày gửi</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($contacts as $contact)
                                        <tr class="{{ $contact->status === 'new' ? 'table-warning' : '' }}">
                                            <td>{{ $contact->id }}</td>
                                            <td>
                                                <strong>{{ $contact->name }}</strong>
                                                @if($contact->status === 'new')
                                                    <span class="badge bg-danger ms-1">Mới</span>
                                                @endif
                                            </td>
                                            <td>{{ $contact->email }}</td>
                                            <td>{{ $contact->phone ?? '-' }}</td>
                                            <td>{{ $contact->subject ?? '-' }}</td>
                                            <td>
                                                @if($contact->status === 'new')
                                                    <span class="badge bg-warning">Mới</span>
                                                @elseif($contact->status === 'read')
                                                    <span class="badge bg-info">Đã đọc</span>
                                                @elseif($contact->status === 'replied')
                                                    <span class="badge bg-success">Đã phản hồi</span>
                                                @else
                                                    <span class="badge bg-secondary">Đã lưu trữ</span>
                                                @endif
                                            </td>
                                            <td>{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.contacts.show', $contact) }}" 
                                                       class="btn btn-sm btn-light" title="Xem chi tiết">
                                                        <i class="bx bx-eye"></i>
                                                    </a>
                                                    <form action="{{ route('admin.contacts.destroy', $contact) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Bạn có chắc muốn xóa liên hệ này?');">
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
                                            <td colspan="8" class="text-center py-4">
                                                <p class="text-muted mb-0">Không có liên hệ nào</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Phân trang --}}
                        <div class="d-flex justify-content-center mt-3">
                            {{ $contacts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

