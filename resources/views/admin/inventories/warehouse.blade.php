@extends('admin.layouts.admin')
@section('title', 'Warehouse')
@section('content')
<div class="page-content">
<<<<<<< Updated upstream
    <div class="container-xxl">

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="d-flex card-header justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">All Warehouse List</h4>
                        </div>
                        <a href="{{ route('inventories.warehouse.add') }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-plus"></i> Add Warehouse
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover table-centered">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>#</th>
                                    <th>Warehouse Name</th>
                                    <th>Address</th>
                                    <th>Manager</th>
                                    <th>Phone</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($warehouses as $warehouse)
                                <tr>
                                    <td>{{ $warehouse->id }}</td>
                                    <td>{{ $warehouse->warehouse_name }}</td>
                                    <td>{{ $warehouse->address }}</td>
                                    <td>
                                        @if($warehouse->manager)
                                            {{ $warehouse->manager->name }}
                                        @else
                                            <span class="text-muted fst-italic">No Manager</span>
                                        @endif
                                    </td>
                                    <td>{{ $warehouse->phone ?? '—' }}</td>
                                    <td>{{ $warehouse->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="#" class="btn btn-light btn-sm" title="View">
                                                <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                            </a>
                                            <a href="#" class="btn btn-soft-primary btn-sm" title="Edit">
                                                <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                            </a>
                                            <form action="{{ route('inventories.warehouse.destroy', $warehouse->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xoá?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-soft-danger btn-sm" title="Delete">
                                                    <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No warehouses found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer border-top">
                        {{ $warehouses->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>

    </div>
=======
<div class="container-xxl">

    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Danh sách kho</h4>

            <div class="d-flex gap-2">
                <!-- Nút Nhập hàng -->
                <a href="{{ route('inventories.import.create') }}" class="btn btn-success btn-sm">
                    <i class="bx bx-import"></i> Nhập hàng
                </a>

                <!-- Nút Xuất hàng -->
                <a href="{{ route('inventories.export.create') }}" class="btn btn-danger btn-sm">
                    <i class="bx bx-export"></i> Xuất hàng
                </a>

                <!-- Nút Thêm kho -->
                <a href="{{ route('inventories.warehouse.add') }}" class="btn btn-primary btn-sm">
                    <i class="bx bx-plus"></i> Thêm kho
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover table-centered">
                <thead class="bg-light-subtle">
                    <tr>
                        <th>ID</th>
                        <th>Tên kho</th>
                        <th>Địa chỉ</th>
                        <th>Quản lý</th>
                        <th>Điện thoại</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $w)
                        <tr>
                            <td>{{ $w->id }}</td>
                            <td>{{ $w->warehouse_name }}</td>
                            <td>{{ $w->address }}</td>
                            <td>{{ $w->manager->name ?? '—' }}</td>
                            <td>{{ $w->phone ?? '—' }}</td>
                            <td>{{ $w->created_at?->format('d/m/Y') }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('inventories.warehouse.edit', $w->id) }}" class="btn btn-soft-primary btn-sm">
                                        <iconify-icon icon="solar:pen-2-broken"></iconify-icon>
                                    </a>
                                    <form action="{{ route('inventories.warehouse.destroy', $w->id) }}" method="POST" onsubmit="return confirm('Xóa kho này?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-soft-danger btn-sm">
                                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Chưa có kho nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer border-top">
            {{ $warehouses->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>
>>>>>>> Stashed changes
</div>
@endsection
