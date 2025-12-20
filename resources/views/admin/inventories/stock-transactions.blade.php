@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid">

    <h4 class="mb-4">Lịch sử nhập / xuất kho</h4>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="warehouse_id" class="form-control">
                <option value="">-- Tất cả kho --</option>
                @foreach($warehouses as $w)
                    <option value="{{ $w->id }}" @selected(request('warehouse_id') == $w->id)>
                        {{ $w->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select name="type" class="form-control">
                <option value="">-- Tất cả loại --</option>
                <option value="import">Nhập kho</option>
                <option value="export">Xuất kho</option>
            </select>
        </div>

        <div class="col-md-2">
            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
        </div>

        <div class="col-md-2">
            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Lọc</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Thời gian</th>
                <th>Kho</th>
                <th>Sản phẩm</th>
                <th>Biến thể</th>
                <th>Lô</th>
                <th>Loại</th>
                <th>Số lượng</th>
                <th>Tồn trước</th>
                <th>Tồn sau</th>
                <th>Tham chiếu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $t)
            <tr>
                <td>{{ $t->created_at }}</td>
                <td>{{ $t->warehouse->name ?? '-' }}</td>
                <td>{{ $t->product->name ?? '-' }}</td>
                <td>{{ $t->variant->sku ?? '-' }}</td>
                <td>{{ $t->batch_code }}</td>
                <td>
                    @if($t->type === 'import')
                        <span class="badge bg-success">Nhập</span>
                    @else
                        <span class="badge bg-danger">Xuất</span>
                    @endif
                </td>
                <td>{{ $t->quantity }}</td>
                <td>{{ $t->before_quantity }}</td>
                <td>{{ $t->after_quantity }}</td>
                <td>{{ $t->reference_type }} #{{ $t->reference_id }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $transactions->links() }}
</div>
@endsection
