@extends('admin.layouts.admin')

@section('content')
<div class="page-content">
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 text-success">➕ <strong>Thêm thương hiệu mới</strong></h5>
        </div>
        <div class="card-body">
            <form action="{{ route('brand.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tên thương hiệu <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control shadow-sm" placeholder="Nhập tên thương hiệu..." required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Xuất xứ</label>
                        <input type="text" name="origin" class="form-control shadow-sm" placeholder="Nhập quốc gia (VD: Ý, Pháp...)">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Logo thương hiệu</label>
                    <input type="file" name="image" class="form-control shadow-sm" accept="image/*">
                    <small class="text-muted italic">Định dạng hỗ trợ: jpg, png, webp. Kích thước tối đa 2MB.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả</label>
                    <textarea name="description" class="form-control shadow-sm" rows="5" placeholder="Nhập tóm tắt về thương hiệu..."></textarea>
                </div>

                <div class="mt-4 border-top pt-3 text-end">
                    <button type="submit" class="btn btn-success px-4">Lưu lại</button>
                    <a href="{{ route('brand.index') }}" class="btn btn-secondary px-4">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection