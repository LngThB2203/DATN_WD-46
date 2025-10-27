@extends('admin.layouts.admin')

@section('title', 'Sửa Banner')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0">✏️ Sửa Banner</h5>
        <a href="{{ route('admin.banner.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.banner.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Hình ảnh</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                @if($banner->image)
                    <img src="{{ asset('storage/'.$banner->image) }}" class="mt-2 img-thumbnail" width="250">
                @endif
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Liên kết</label>
                <input type="url" name="link" class="form-control" value="{{ old('link', $banner->link) }}">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Ngày bắt đầu</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $banner->start_date) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Ngày kết thúc</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $banner->end_date) }}">
                </div>
            </div>

            

            <div class="text-end">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
</div>
@endsection
