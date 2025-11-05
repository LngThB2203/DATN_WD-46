@extends('admin.layouts.admin')
@section('title', 'Nhập hàng')
@section('content')
<div class="page-content">
    <div class="container-xxl">
        <h4 class="mb-4">Form nhập hàng</h4>
        <form action="{{ route('inventories.import.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="warehouse" class="form-label">Chọn kho</label>
                <select name="warehouse_id" class="form-select" required>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->warehouse_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="product" class="form-label">Chọn sản phẩm</label>
                <select name="product_id" class="form-select" required>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Số lượng nhập</label>
                <input type="number" name="quantity" class="form-control" min="1" required>
            </div>
            <button class="btn btn-success">Nhập hàng</button>
        </form>
    </div>
</div>
@endsection
