@extends('admin.layouts.admin')
@section('content')
<div class="container py-4">
    <h2>Danh sách biến thể</h2>
    <a href="{{ route('variants.create') }}" class="btn btn-primary mb-3">+ Thêm biến thể</a>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th><th>Sản phẩm</th><th>Size</th><th>Mùi</th><th>Nồng độ</th>
                <th>SKU</th><th>Tồn</th><th>Giá+</th><th>Giới tính</th><th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($variants as $v)
            <tr>
                <td>{{ $v->id }}</td>
                <td>{{ $v->product->name ?? '' }}</td>
                <td>{{ $v->size->size_name ?? '-' }}</td>
                <td>{{ $v->scent->scent_name ?? '-' }}</td>
                <td>{{ $v->concentration->concentration_name ?? '-' }}</td>
                <td>{{ $v->sku }}</td>
                <td>{{ $v->stock }}</td>
                <td>{{ $v->price_adjustment }}</td>
                <td>{{ ucfirst($v->gender) }}</td>
                <td>
                    <a href="{{ route('variants.edit',$v->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                    <form method="POST" action="{{ route('variants.destroy',$v->id) }}" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $variants->links() }}
</div>
@endsection
