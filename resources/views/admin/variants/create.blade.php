@extends('admin.layouts.admin')
@section('content')
<div class="container py-4">
    <h2>Thêm biến thể sản phẩm</h2>
    <form method="POST" action="{{ route('variants.store') }}">
        @csrf
        <div class="mb-3">
            <label>Sản phẩm</label>
            <select name="product_id" class="form-control" required>
                @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Kích thước</label>
                <select name="size_id" class="form-control">
                    <option value="">Không</option>
                    @foreach($sizes as $s)
                        <option value="{{ $s->id }}">{{ $s->size_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Mùi hương</label>
                <select name="scent_id" class="form-control">
                    <option value="">Không</option>
                    @foreach($scents as $s)
                        <option value="{{ $s->id }}">{{ $s->scent_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Nồng độ</label>
                <select name="concentration_id" class="form-control">
                    <option value="">Không</option>
                    @foreach($concentrations as $c)
                        <option value="{{ $c->id }}">{{ $c->concentration_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label>SKU</label>
            <input name="sku" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Tồn kho ban đầu</label>
            <input type="number" name="stock" class="form-control" min="0" value="0">
        </div>
        <div class="mb-3">
            <label>Giá điều chỉnh (+/-)</label>
            <input type="number" step="0.01" name="price_adjustment" class="form-control" value="0">
        </div>
        <div class="mb-3">
            <label>Giới tính</label>
            <select name="gender" class="form-control">
                <option value="male">Nam</option>
                <option value="female">Nữ</option>
                <option value="unisex">Unisex</option>
            </select>
        </div>
        <button class="btn btn-success">Lưu</button>
    </form>
</div>
@endsection
