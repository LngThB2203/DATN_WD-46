@extends('admin.layouts.admin')

@section('title', 'Category List')

@section('content')
<div class="page-content">
    <div class="container-xxl">
        <div class="row">
            @foreach($categories->take(4) as $cat)
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="rounded bg-secondary-subtle d-flex align-items-center justify-content-center mx-auto" style="width:100px; height:100px;">
                                <img
                                    src="{{ $cat->image ? asset($cat->image) : asset('assets/admin/images/default.png') }}"
                                    alt="{{ $cat->category_name }}"
                                    class="avatar-xl object-fit-contain"
                                    style="max-width: 100%; max-height: 100%;">
                            </div>
                            <h4 class="mt-3 mb-0">{{ $cat->category_name }}</h4>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-1">
                        <h4 class="card-title flex-grow-1">All Categories List</h4>

                        <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary">
                            Add Category
                        </a>

                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle btn btn-sm btn-outline-light" data-bs-toggle="dropdown">
                                This Month
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="#" class="dropdown-item">Download</a>
                                <a href="#" class="dropdown-item">Export</a>
                                <a href="#" class="dropdown-item">Import</a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover table-centered">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th style="width: 20px;">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="checkAll">
                                        </div>
                                    </th>
                                    <th>Category Name</th>
                                    <th>Parent</th>
                                    <th>Description</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $cat)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input">
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <img
                                                    src="{{ $cat->image ? asset($cat->image) : asset('assets/admin/images/default.png') }}"
                                                    alt="{{ $cat->category_name }}"
                                                    class="avatar-md object-fit-contain"
                                                    style="max-width: 100%; max-height: 100%;">
                                            </div>
                                            <div>
                                                <p class="text-dark fw-medium fs-15 mb-0">{{ $cat->category_name }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td>{{ $cat->parent?->category_name ?? '—' }}</td>
                                    <td>{{ Str::limit($cat->description, 40) }}</td>
                                    <td>{{ $cat->created_at->format('d/m/Y') }}</td>

                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-soft-primary btn-sm">
                                                <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                            </a>
                                            <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Xóa danh mục này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-soft-danger btn-sm">
                                                    <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Không có danh mục nào.</td>
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
