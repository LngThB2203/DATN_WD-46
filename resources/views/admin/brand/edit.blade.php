@extends('admin.layouts.admin')

@section('content')
<div class="page-content"> {{-- Dùng class này để đồng bộ lề với trang Banner --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">🏢 <strong>Chỉnh sửa thương hiệu</strong></h5>
            <a href="{{ route('brand.index') }}" class="btn btn-sm btn-secondary">Quay lại</a>
        </div>
        
        <div class="card-body">
            {{-- Form bắt buộc phải có @method('PUT') và enctype --}}
            <form action="{{ route('brand.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tên thương hiệu</label>
                        <input type="text" name="name" class="form-control" value="{{ $brand->name }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Xuất xứ</label>
                        <input type="text" name="origin" class="form-control" value="{{ $brand->origin }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold d-block">Logo hiện tại</label>
                    <div class="p-2 border rounded bg-light d-inline-block mb-2">
                        @if ($brand->image)
                            <img src="{{ asset('storage/' . $brand->image) }}" width="150" class="img-fluid rounded shadow-sm">
                        @else
                            <span class="text-muted">Chưa có logo</span>
                        @endif
                    </div>
                    <label class="form-label fw-bold d-block">Tải lên logo mới (nếu muốn thay đổi)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả thương hiệu</label>
                    {{-- Dùng class form-control và cho rows cao hơn để dễ nhìn --}}
                    <textarea name="description" class="form-control" rows="6">{{ old('description', $brand->description) }}</textarea>
                </div>

                <div class="border-top pt-3">
                    <button type="submit" class="btn btn-primary px-4">Cập nhật dữ liệu</button>
                    <a href="{{ route('brand.index') }}" class="btn btn-outline-secondary px-4">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection