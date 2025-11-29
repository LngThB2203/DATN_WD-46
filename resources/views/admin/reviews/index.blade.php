<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Danh sách đánh giá</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-3">
<div class="container-fluid">
    <h3 class="mb-3">Danh sách đánh giá</h3>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">
            <input type="text" name="product" value="{{ request('product') }}" class="form-control" placeholder="Tìm theo tên sản phẩm">
        </div>
        <div class="col-auto">
            <select name="status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                <option value="1" @selected(request('status')==='1')>Đã duyệt</option>
                <option value="0" @selected(request('status')==='0')>Ẩn</option>
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" type="submit">Lọc</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.reviews.index') }}">Xoá lọc</a>
        </div>
        <div class="col-auto ms-auto">
            <a class="btn btn-success" href="{{ route('admin.reviews.create') }}">Thêm đánh giá</a>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Sản phẩm</th>
                <th>Người dùng</th>
                <th>Điểm</th>
                <th>Nội dung</th>
                <th>Trạng thái</th>
                <th>Thời gian</th>
                <th style="width:180px">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            @forelse($reviews as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->product->name ?? '-' }}</td>
                    <td>{{ $row->user->name ?? 'Khách' }}</td>
                    <td>{{ $row->rating }}/5</td>
                    <td>{{ \Illuminate\Support\Str::limit($row->comment, 80) }}</td>
                    <td>
                        <span class="badge {{ $row->status ? 'bg-success' : 'bg-secondary' }}">{{ $row->status ? 'Đã duyệt' : 'Ẩn' }}</span>
                    </td>
                    <td>{{ $row->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <form action="{{ route('admin.reviews.toggle', $row) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-warning" type="submit">{{ $row->status ? 'Ẩn' : 'Duyệt' }}</button>
                        </form>
                        <a class="btn btn-sm btn-primary" href="{{ route('admin.reviews.edit', $row) }}">Sửa</a>
                        <form action="{{ route('admin.reviews.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Xoá đánh giá này?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">Xoá</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">Không có dữ liệu</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $reviews->withQueryString()->links() }}
    </div>
</div>
</body>
</html>
