@extends('admin.layouts.admin')
@section('content')
<div class="container">
    <h1>Sửa biến thể #{{ $variant->id }} - {{ $product->name }}</h1>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
    </div>
    @endif

    <form action="{{ route('admin.variants.update', ['product' => $product->id, 'variant' => $variant->id]) }}"
        method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Size</label>
            <select name="size_id" class="form-control">
                <option value="">-- none --</option>
                @foreach($sizes as $s)
                <option value="{{ $s->id }}" @if($variant->size_id == $s->id) selected @endif>{{ $s->size_name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Scent</label>
            <select name="scent_id" class="form-control">
                <option value="">-- none --</option>
                @foreach($scents as $s)
                <option value="{{ $s->id }}" @if($variant->scent_id == $s->id) selected @endif>{{ $s->scent_name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Concentration</label>
            <select name="concentration_id" class="form-control">
                <option value="">-- none --</option>
                @foreach($concentrations as $c)
                <option value="{{ $c->id }}" @if($variant->concentration_id == $c->id) selected @endif>{{
                    $c->concentration_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>SKU</label>
            <input type="text" name="sku" class="form-control" value="{{ old('sku', $variant->sku) }}">
        </div>

        <div class="mb-3">
            <label>Price adjustment</label>
            <input type="number" step="0.01" name="price_adjustment" class="form-control"
                value="{{ old('price_adjustment', $variant->price_adjustment) }}">
        </div>

        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock" class="form-control" value="{{ old('stock', $variant->stock) }}" required>
        </div>

        <button class="btn btn-success">Cập nhật</button>
        <a href="{{ route('admin.variants.index', $product->id) }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
