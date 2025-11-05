@extends('admin.layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-3">Danh sách thương hiệu</h2>
    <a href="{{ route('brand.create') }}" class="btn btn-primary mb-3">+ Thêm thương hiệu</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Tên thương hiệu</th>
                <th>Xuất xứ</th>
                <th>Mô tả</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($brands as $brand)
            <tr>
                <td>{{ $brand->id }}</td>
                <td>{{ $brand->name }}</td>
                <td>{{ $brand->origin }}</td>
                <td>{{ $brand->description }}</td>
                <td>
                    <a href="{{ route('brand.edit', $brand->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                    <a href="{{ route('brand.delete', $brand->id) }}" class="btn btn-sm btn-danger"
                       onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
