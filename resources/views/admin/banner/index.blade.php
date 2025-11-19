@extends('admin.layouts.admin')

@section('title', 'Danh sách Banner')

@section('content')
<div class="page-content">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0">🖼️ Danh sách Banner</h5>
        <a href="{{ route('banner.create') }}" class="btn btn-success btn-sm">+ Thêm Banner</a>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Hình ảnh</th>
                    <th>Liên kết</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Trạng thái</th>
                    <th width="150">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($banners as $key => $banner)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td><img src="{{ asset('storage/'.$banner->image) }}" alt="" width="120" class="rounded"></td>
                        <td>{{ $banner->link }}</td>
                        <td>{{ $banner->start_date }}</td>
                        <td>{{ $banner->end_date }}</td>
                        <td>
    @if($banner->status)
        <form action="{{ route('banner.toggleStatus', $banner->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success btn-sm">Hiển thị</button>
        </form>
    @else
        <form action="{{ route('banner.toggleStatus', $banner->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm">Ẩn</button>
        </form>
    @endif
</td>

                        <td>
                         <a href="{{ route('banner.edit', $banner->id) }}">Sửa</a>


<form action="{{ route('banner.delete', $banner->id) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit">Xóa</button>
</form>

                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">Chưa có banner nào</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $banners->links() }}
        </div>
    </div>
</div>
@endsection
