@extends('admin.layouts.admin')

@section('content')
<div class="page-content">
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 text-success">➕ <strong>Thêm bài viết mới</strong></h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Tiêu đề bài viết <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Nhập tiêu đề..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Ảnh đại diện</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nội dung chi tiết</label>
                    <textarea name="content" id="content_summernote" class="form-control">{{ old('content') }}</textarea>
                </div>

                <div class="border-top pt-3 text-end">
                    <a href="{{ route('post.index') }}" class="btn btn-secondary px-4">Hủy</a>
                    <button type="submit" class="btn btn-success px-4">Lưu bài viết</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- Giữ nguyên phần @push('styles') và @push('scripts') như bạn đã viết --}}
@push('styles')
{{-- 1. Thêm CSS của Summernote --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    /* Tuỳ chỉnh nếu cần */
    .note-editor.note-frame {
        border-radius: 0.25rem; /* Giúp editor nhìn đồng bộ với form-control */
    }
</style>
@endpush

@push('scripts')
{{-- 2. Thêm jQuery (Summernote yêu cầu) và JS của Summernote --}}
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        // 3. Khởi tạo Summernote trên ID đã đặt
        $('#content_summernote').summernote({
            placeholder: 'Nhập nội dung bài viết...',
            tabsize: 2,
            height: 300, // Chiều cao mặc định của editor
            // Tuỳ chọn ngôn ngữ Tiếng Việt (nếu bạn đã thêm file ngôn ngữ)
            // lang: 'vi-VN', 
            toolbar: [
                // Cấu hình toolbar đơn giản hơn (sử dụng summernote-lite)
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
</script>
@endpush
