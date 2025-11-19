@extends('admin.layouts.admin')
@section('title','Inventory Management')
@section('content')
<div class="page-content">
    <div class="container-xxl">

        @if($lowStockItems->count()>0)
        <div class="alert alert-danger">
            <h5>Cảnh báo tồn kho thấp!</h5>
            <ul>
                @foreach($lowStockItems as $i)
                <li>{{ $i->product->name ?? 'SP?' }} - {{ $i->warehouse->warehouse_name ?? 'Kho?' }}: {{ $i->quantity
                    }}/{{ $i->min_stock_threshold }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Tồn kho hiện tại</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Sản phẩm</th>
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
                            <td>{{ $inv->warehouse->warehouse_name ?? 'N/A' }}</td>
                            <td>{{ $inv->quantity }}</td>
                            <td>{{ $inv->min_stock_threshold }}</td>
                            <td>
                                @if($inv->quantity <= $inv->min_stock_threshold)
                                    <span class="badge bg-danger">Sắp hết</span>
                                    @else
                                    <span class="badge bg-success">Đủ</span>
                                    @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('inventories.updateQuantity',$inv->id) }}">
                                    @csrf @method('PUT')
                                    <input type="number" name="quantity" value="{{ $inv->quantity }}" min="0"
                                        style="width:80px;">
                                    <button class="btn btn-sm btn-primary">Lưu</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="card-footer">{{ $inventories->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
