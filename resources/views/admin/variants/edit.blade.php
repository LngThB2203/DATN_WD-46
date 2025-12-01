@extends('admin.layouts.admin')
@section('content')
<div class="container">
    <h1>Sửa biến thể #{{ $variant->id }} - {{ $product->name }}</h1>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
    </div>
    @endif

    <form action="{{ route('variants.update', $variant->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <input type="hidden" name="product_id" value="{{ $product->id }}">

        <div class="mb-3">
            <label>Kích thước</label>
            <select name="size_id" class="form-control">
                <option value="">-- none --</option>
                @foreach($sizes as $s)
                <option value="{{ $s->id }}" @selected($variant->size_id == $s->id)>
                    {{ $s->size_name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Mùi hương</label>
            <select name="scent_id" class="form-control">
                <option value="">-- none --</option>
                @foreach($scents as $s)
                <option value="{{ $s->id }}" @selected($variant->scent_id == $s->id)>
                    {{ $s->scent_name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Nồng độ</label>
            <select name="concentration_id" class="form-control">
                <option value="">-- none --</option>
                @foreach($concentrations as $c)
                <option value="{{ $c->id }}" @selected($variant->concentration_id == $c->id)>
                    {{ $c->concentration_name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>SKU</label>
            <input type="text" name="sku" class="form-control" value="{{ $variant->sku }}">
        </div>

        <div class="mb-3">
            <label>Giá điều chỉnh</label>
            <input type="number" step="0.01" name="price_adjustment" class="form-control"
                value="{{ $variant->price_adjustment }}">
        </div>

        <div class="mb-3">
            <label>Tồn kho</label>
            <input type="text" class="form-control" value="{{ $variant->stock }}" disabled>
        </div>

        <div class="mb-3">
            <label>Giới tính</label>
            <select name="gender" class="form-control">
                <option value="unisex" @selected($variant->gender == 'unisex')>Unisex</option>
                <option value="male" @selected($variant->gender == 'male')>Male</option>
                <option value="female" @selected($variant->gender == 'female')>Female</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Ảnh hiện tại</label><br>
            @if($variant->image)
                <img src="{{ asset('storage/'.$variant->image) }}" width="90" class="rounded mb-2">
            @else
                <span class="text-muted">Không có ảnh</span>
            @endif
        </div>

        <div class="mb-3">
            <label>Ảnh mới</label>
            <input type="file" name="image" class="form-control">
        </div>

        <button class="btn btn-success">Cập nhật</button>
        <a href="{{ route('variants.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
