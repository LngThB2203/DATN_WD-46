@extends('admin.layouts.admin')

@section('content')
<div class="container">
    <h2>Thêm thương hiệu mới</h2>
    <form action="{{ route('brand.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Tên thương hiệu</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Xuất xứ</label>
            <input type="text" name="origin" class="form-control">
        </div>

        <div class="mb-3">
            <label>Mô tả</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Lưu</button>
        <a href="{{ route('brand.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
