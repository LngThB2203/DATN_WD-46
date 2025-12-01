@extends('admin.layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="page-content">
    <div class="container-xxl">
        <div class="row">
            {{-- Cột trái: Ảnh danh mục --}}
            <div class="col-xl-3 col-lg-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="mb-3">Ảnh hiện tại</h5>
                        <div class="rounded bg-light p-2">
                            <img src="{{ $category->image ? asset($category->image) : asset('assets/admin/images/default.png') }}"
                                 alt="{{ $category->category_name }}"
                                 class="img-fluid rounded border"
                                 style="max-height: 200px; object-fit: contain;">
                        </div>
                        <p class="text-muted mt-2 mb-0">
                            {{ $category->category_name }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Cột phải: Form chỉnh sửa --}}
            <div class="col-xl-9 col-lg-8">
                <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Ảnh mới --}}
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Cập nhật ảnh danh mục</h4>
                        </div>
                        <div class="card-body">
                            <input type="file" name="image" class="form-control mb-2">
                            <small class="text-muted">Nếu không chọn ảnh mới thì sẽ giữ nguyên ảnh cũ.</small>
                        </div>
                    </div>

                    {{-- Thông tin chung --}}
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Thông tin danh mục</h4>
                        </div>
                        <div class="card-body row">
                            <div class="col-lg-6 mb-3">
                                <label for="category_name" class="form-label">Tên danh mục</label>
                                <input type="text" name="category_name" id="category_name" class="form-control"
                                       value="{{ old('category_name', $category->category_name) }}" required>
                            </div>

                            <div class="col-lg-6 mb-3">
                                <label for="parent_id" class="form-label">Danh mục cha</label>
                                <select name="parent_id" id="parent_id" class="form-select">
                                    <option value="">-- Không có --</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ $category->parent_id == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-12 mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea name="description" id="description" rows="5"
                                          class="form-control bg-light-subtle">{{ old('description', $category->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Nút lưu --}}
                    <div class="p-3 bg-light rounded mt-3">
                        <div class="row justify-content-end g-2">
                            <div class="col-lg-2">
                                <button type="submit" class="btn btn-primary w-100">Lưu thay đổi</button>
                            </div>
                            <div class="col-lg-2">
                                <a href="{{ route('admin.categories.list') }}" class="btn btn-outline-secondary w-100">Hủy</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
