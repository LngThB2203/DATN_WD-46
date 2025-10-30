@extends('admin.layouts.admin')

@section('title', 'Inventory Management')

@section('content')
<div class="page-content">
    <div class="container-xxl">

        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3">Danh sách tồn kho</h4>
            </div>
        </div>

        {{-- Cảnh báo tồn kho thấp --}}
        @if(isset($lowStockItems) && $lowStockItems->count() > 0)
            <div class="alert alert-danger">
                <h5><i class="bx bx-error-circle"></i> Cảnh báo tồn kho thấp!</h5>
                <ul class="mb-0">
                    @foreach($lowStockItems as $item)
                        <li>
                            <strong>{{ $item->product->name ?? 'Sản phẩm không xác định' }}</strong> tại
                            <em>{{ $item->warehouse->warehouse_name ?? 'Kho không xác định' }}</em>
                            chỉ còn <strong>{{ $item->quantity }}</strong> (ngưỡng tối thiểu: {{ $item->min_stock_threshold }})
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Tồn kho hiện tại</h5>
                <a href="#" class="btn btn-sm btn-primary">Thêm mới</a>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>ID</th>
                                <th>Sản phẩm</th>
                                <th>Kho</th>
                                <th>Số lượng</th>
                                <th>Ngưỡng tối thiểu</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventories as $inventory)
                                <tr>
                                    <td>{{ $inventory->id }}</td>
                                    <td>{{ $inventory->product->name ?? 'N/A' }}</td>
                                    <td>{{ $inventory->warehouse->warehouse_name ?? 'N/A' }}</td>
                                    <td>{{ $inventory->quantity }}</td>
                                    <td>{{ $inventory->min_stock_threshold }}</td>
                                    <td>
                                        @if($inventory->quantity <= $inventory->min_stock_threshold)
                                            <span class="badge bg-danger">Sắp hết hàng</span>
                                        @else
                                            <span class="badge bg-success">Đủ hàng</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="#" class="btn btn-light btn-sm"><iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon></a>
                                            <a href="#" class="btn btn-soft-primary btn-sm"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                            <a href="#" class="btn btn-soft-danger btn-sm"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Phân trang --}}
                <div class="card-footer border-top">
                    {{ $inventories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
