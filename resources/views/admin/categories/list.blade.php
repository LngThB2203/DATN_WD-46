@extends('admin.layouts.admin')

@section('title', 'Category List')

@section('content')
<div class="page-content">
    <div class="container-xxl">
        <form method="GET" action="{{ route('admin.categories.list') }}" class="mb-3 d-flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control w-auto" placeholder="Tìm kiếm danh mục...">
            <button class="btn btn-primary">Tìm</button>
        </form>

        <div class="row">
            @foreach($categories->take(4) as $cat)
                <div class="col-md-6 col-xl-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="rounded bg-secondary-subtle d-flex align-items-center justify-content-center mx-auto" style="width:100px; height:100px;">
                                <img src="{{ $cat->image ? asset($cat->image) : asset('assets/admin/images/default.png') }}"
                                    alt="{{ $cat->category_name }}"
                                    class="object-fit-contain"
                                    style="max-width: 100%; max-height: 100%;">
                            </div>
                            <h4 class="mt-3 mb-1">{{ $cat->category_name }}</h4>
                            <p class="text-muted mb-0">
                                {{ $cat->parent?->category_name ? 'Thuộc: '.$cat->parent->category_name : 'Danh mục gốc' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-1">
                        <h4 class="card-title flex-grow-1">Danh sách danh mục</h4>

                        <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary">
                            + Thêm danh mục
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-hover table-centered mb-0">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên danh mục</th>
                                    <th>Danh mục cha</th>
                                    <th>Mô tả</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $cat)
                                    <tr>
                                        <td>{{ $cat->id }}</td>
                                        <td>{{ $cat->category_name }}</td>
                                        <td>{{ $cat->parent?->category_name ?? '—' }}</td>
                                        <td>{{ Str::limit($cat->description, 50) }}</td>
                                        <td>
                                            <a href="{{ route('admin.categories.toggle', $cat->id) }}"
                                               class="badge {{ $cat->status ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $cat->status ? 'Hiển thị' : 'Ẩn' }}
                                            </a>
                                        </td>
                                        <td>{{ $cat->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-sm btn-soft-primary">
                                                    <i class="bi bi-pencil">Sửa</i>
                                                </a>
                                                <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Xóa danh mục này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-soft-danger">
                                                        <i class="bi bi-trash">Xóa</i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Không có danh mục nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer border-top">
                        {{ $categories->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
