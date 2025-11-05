@extends('admin.layouts.admin')
@section('title','Xuất kho')
@section('content')
<div class="page-content">
<div class="container-xxl">
    <div class="card">
        <div class="card-header"><h4>Xuất hàng</h4></div>
        <div class="card-body">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

            <form action="{{ route('inventories.export.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Kho</label>
                    <select name="warehouse_id" class="form-select" required>
                        <option value="">-- Chọn kho --</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->warehouse_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Sản phẩm</label>
                    <select name="product_id" class="form-select" required>
                        <option value="">-- Chọn sản phẩm --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label>Số lượng</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label>Ghi chú</label>
                    <input type="text" name="note" class="form-control">
                </div>

                <button class="btn btn-danger">Xuất hàng</button>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
