@extends('admin.layouts.admin')

@section('content')
<div class="container">
    <h2>Chỉnh sửa thương hiệu</h2>
    <form action="{{ route('brand.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Tên thương hiệu</label>
            <input type="text" name="name" class="form-control" value="{{ $brand->name }}" required>
        </div>

        <div class="mb-3">
            <label>Logo hiện tại</label><br>

            @if ($brand->image)
                <img src="{{ asset('storage/' . $brand->image) }}" width="100" class="mb-2">
            @else
                <p>Chưa có logo</p>
            @endif
        </div>

        <div class="mb-3">
            <label>Chọn logo mới</label>
            <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
            <label>Xuất xứ</label>
            <input type="text" name="origin" class="form-control" value="{{ $brand->origin }}">
        </div>

        <div class="mb-3">
            <label>Mô tả</label>
            <textarea name="description" class="form-control">{{ $brand->description }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('brand.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection