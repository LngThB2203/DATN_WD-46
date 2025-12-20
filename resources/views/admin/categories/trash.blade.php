@extends('admin.layouts.admin')

@section('title', 'Danh mục đã xóa')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Danh mục đã xóa</h4>
        <a href="{{ route('admin.categories.list') }}" class="btn btn-secondary">← Quay lại danh sách</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tên danh mục</th>
                            <th>Slug</th>
                            <th>Ngày xóa</th>
                            <th width="180">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->category_name }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>{{ $category->deleted_at?->format('d/m/Y H:i') }}</td>
                            <td>
                                {{-- Khôi phục --}}
                                <form action="{{ route('admin.categories.restore', $category->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Khôi phục</button>
                                </form>

                                {{-- Xóa vĩnh viễn --}}
                                <form action="{{ route('admin.categories.forceDelete', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa vĩnh viễn danh mục này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Xóa vĩnh viễn</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Không có danh mục nào bị xóa</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection
