@extends('admin.layouts.admin')

@section('title', 'Thêm Banner mới')

@section('content')
<div class="page-content">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0">🖼️ Thêm Banner mới</h5>
        <a href="{{ route('banner.index') }}" class="btn btn-secondary btn-sm">Quay lại</a>
    </div>

    <div class="card-body">
        <form action="{{ route('banner.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Hình ảnh</label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Liên kết</label>
                <input type="url" name="link" class="form-control" placeholder="https://example.com">
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Ngày bắt đầu</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Ngày kết thúc</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
            </div>



            <div class="text-end">
                <button type="submit" class="btn btn-success">Thêm mới</button>
            </div>
        </form>
    </div>
</div>
@endsection
