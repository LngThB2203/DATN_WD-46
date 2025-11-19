@extends('admin.layouts.admin')
@section('title', 'Xuất hàng')
@section('content')
<div class="page-content">
    <div class="container-xxl">
        <h4 class="mb-4">Form xuất hàng</h4>
        <form action="{{ route('inventories.export.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Chọn kho</label>
                <select name="warehouse_id" class="form-select" required>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Chọn sản phẩm</label>
                <select name="product_id" class="form-select" required>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Số lượng xuất</label>
                <input type="number" name="quantity" class="form-control" min="1" required>
            </div>
            <button class="btn btn-danger">Xuất hàng</button>
        </form>
    </div>
</div>
@endsection
