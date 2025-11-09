@extends('admin.layouts.admin')

@section('content')
<div class="container mt-4">
    <h2>Sửa bài viết</h2>

    <form method="POST" action="{{ route('post.update', $post->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

        <div class="mb-3">
            <label>Tiêu đề</label>
            <input type="text" name="title" class="form-control" value="{{ $post->title }}" required>
        </div>

        <div class="mb-3">
            <label>Danh mục</label>
            <select name="category_id" class="form-select">
                <option value="">-- Chọn danh mục --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $cat->id == $post->category_id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Ảnh đại diện</label><br>
            @if ($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" width="100" class="mb-2"><br>
            @endif
            <input type="file" name="image" class="form-control">
        </div>

        <div class="mb-3">
            <label>Nội dung</label>
            <textarea name="content" rows="5" class="form-control">{{ $post->content }}</textarea>
        </div>

        <button class="btn btn-success">Cập nhật</button>
        <a href="{{ route('post.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
