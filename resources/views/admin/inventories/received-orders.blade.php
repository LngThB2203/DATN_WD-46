@extends('admin.layouts.admin')
@section('title', 'Inventory Management')
@section('content')
<div class="page-content"><div class="container-xxl">

@if($lowStockItems->count() > 0)
<div class="alert alert-danger">
    <h5><i class="bx bx-error"></i> Cảnh báo tồn kho thấp</h5>
    <ul>
        @foreach($lowStockItems as $item)
            <li>{{ $item->product->name ?? 'Sản phẩm?' }}
                @if($item->variant)
                    ({{ $item->variant->size->size_name ?? '' }}{{ $item->variant->scent ? ' | '.$item->variant->scent->scent_name : '' }}{{ $item->variant->concentration ? ' | '.$item->variant->concentration->concentration_name : '' }})
                @endif
                tại <strong>{{ $item->warehouse->warehouse_name ?? 'Kho?' }}</strong> còn {{ $item->quantity }}/{{ $item->min_stock_threshold }}
            </li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Tồn kho hiện tại</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover table-centered">
                <thead class="bg-light-subtle">
                    <tr>
                        <th>ID</th>
                        <th>Sản phẩm</th>
                        <th>Biến thể</th> <!-- Thêm cột biến thể -->
                        <th>Kho</th>
                        <th>Số lượng</th>
                        <th>Ngưỡng</th>
                        <th>Trạng thái</th>
                        <th>Cập nhật</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inventories as $inv)
                    <tr>
                        <td>{{ $inv->id }}</td>
                        <td>{{ $inv->product->name ?? 'N/A' }}</td>
                        <td>
                            @if($inv->variant)
                                {{ $inv->variant->size->size_name ?? '' }}
                                {{ $inv->variant->scent ? ' | '.$inv->variant->scent->scent_name : '' }}
                                {{ $inv->variant->concentration ? ' | '.$inv->variant->concentration->concentration_name : '' }}
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $inv->warehouse->warehouse_name ?? 'N/A' }}</td>
                        <td>{{ $inv->quantity }}</td>
                        <td>{{ $inv->min_stock_threshold }}</td>
                        <td>
                            @if($inv->quantity <= $inv->min_stock_threshold)
                                <span class="badge bg-danger">Sắp hết hàng</span>
                            @else
                                <span class="badge bg-success">Đủ hàng</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('inventories.updateQuantity', $inv->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="input-group input-group-sm">
                                    <input type="number" name="quantity" value="{{ $inv->quantity }}" min="0" class="form-control" style="max-width:80px;">
                                    <button class="btn btn-primary btn-sm">Lưu</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer border-top">{{ $inventories->links() }}</div>
    </div>
</div>

</div></div>
@endsection
