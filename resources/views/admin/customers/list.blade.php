@extends('admin.layouts.admin')

@section('title', 'Danh sách khách hàng')

@section('content')
<div class="page-content">
    <div class="container-xxl">

        {{-- Thanh tìm kiếm --}}
        <form method="GET" action="{{ route('admin.customers.list') }}" class="mb-3 d-flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control w-auto" placeholder="Tìm kiếm khách hàng...">
            <button class="btn btn-primary">Tìm</button>
        </form>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Danh sách khách hàng</h4>
                <a href="{{ route('admin.customers.create') }}" class="btn btn-sm btn-primary">
                    + Thêm khách hàng
                </a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover table-centered mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th>ID</th>
                            <th>Tên khách hàng</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Giới tính</th>
                            <th>Cấp độ</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $cus)
                            <tr>
                                <td>{{ $cus->id }}</td>
                                <td>{{ $cus->name }}</td>
                                <td>{{ $cus->email }}</td>
                                <td>{{ $cus->phone ?? '—' }}</td>
                                <td>{{ $cus->gender ?? '—' }}</td>
                                <td>
                                    <span class="badge
                                        @if($cus->membership_level == 'Gold') bg-warning
                                        @elseif($cus->membership_level == 'Platinum') bg-info
                                        @else bg-secondary
                                        @endif">
                                        {{ $cus->membership_level }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $cus->status ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $cus->status ? 'Hoạt động' : 'Ngừng hoạt động' }}
                                    </span>
                                </td>
                                <td>{{ $cus->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.customers.edit', $cus->id) }}" class="btn btn-sm btn-soft-primary">
                                            <i class="bi bi-pencil">Sửa</i>
                                        </a>
                                        <form action="{{ route('admin.customers.destroy', $cus->id) }}" method="POST" onsubmit="return confirm('Xóa khách hàng này?')">

                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger">
                                                <i class="bi bi-trash">Xóa</i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">Không có khách hàng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer border-top">
                {{ $customers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
