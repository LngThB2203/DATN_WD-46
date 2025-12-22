@extends('admin.layouts.admin')

@section('content')
<div class="page-content">
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📝 <strong>Quản lý bài viết</strong></h5>
            <a href="{{ route('post.create') }}" class="btn btn-success btn-sm">+ Thêm bài viết</a>
        </div>
        
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Tiêu đề</th>
                        <th style="width: 120px;">Ảnh</th>
                        <th style="width: 160px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($posts as $post)
                    <tr>
                        <td>{{ $post->id }}</td>
                        <td class="text-start fw-bold">
                            <div style="max-width: 400px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $post->title }}
                            </div>
                        </td>
                        <td>
                            @if ($post->image)
                                <img src="{{ asset('storage/' . $post->image) }}" width="80" class="img-thumbnail">
                            @else
                                <span class="text-muted small">Không ảnh</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('post.edit', $post->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                                
                                {{-- FIX: Đổi sang POST và DELETE để đúng bảo mật Laravel --}}
                                <form action="{{ route('post.delete', $post->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xóa bài viết này?')">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="mt-3 d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection