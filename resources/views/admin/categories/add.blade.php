@extends('admin.layouts.admin')

@section('title', 'Thêm danh mục')

@section('content')
<div class="page-content">
    <div class="container-xxl">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="bg-light rounded p-3">
                                <img src="{{ asset('assets/admin/images/categories/cate_1.jpg') }}" alt="Preview"
                                     class="avatar-xxl" id="preview-img">
                            </div>
                            <div class="mt-3">
                                <h4>Thêm danh mục mới</h4>
                            </div>
                        </div>
                        <div class="card-footer border-top">
                            <div class="row g-2">
                                <div class="col-lg-6">
                                    <button type="submit" class="btn btn-primary w-100">Lưu</button>
                                </div>
                                <div class="col-lg-6">
                                    <a href="{{ route('admin.categories.list') }}" class="btn btn-outline-secondary w-100">Hủy</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-9 col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4 class="card-title">Thông tin danh mục</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label for="category_name" class="form-label">Tên danh mục</label>
                                    <input type="text" name="category_name" id="category_name"
                                           class="form-control @error('category_name') is-invalid @enderror"
                                           placeholder="Nhập tên danh mục"
                                           value="{{ old('category_name') }}">
                                    @error('category_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <label for="parent_id" class="form-label">Danh mục cha</label>
                                    <select name="parent_id" id="parent_id" class="form-control">
                                        <option value="">-- Không có --</option>
                                        @foreach($parents as $parent)
                                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                                {{ $parent->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label for="description" class="form-label">Mô tả</label>
                                    <textarea name="description" id="description" class="form-control"
                                              rows="4" placeholder="Nhập mô tả">{{ old('description') }}</textarea>
                                </div>

                                <div class="col-lg-12 mb-3">
                                    <label for="image" class="form-label">Ảnh danh mục (nếu có)</label>
                                    <input type="file" name="image" id="image" class="form-control" accept="image/*"
                                           onchange="document.getElementById('preview-img').src = window.URL.createObjectURL(this.files[0])">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
