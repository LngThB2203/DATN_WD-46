<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Thêm đánh giá</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="p-3">
<div class="container">
    <h3 class="mb-3">Thêm đánh giá</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.reviews.store') }}" method="POST" class="card p-3">
        @csrf
        <div class="mb-3">
            <label class="form-label">Sản phẩm</label>
            <select name="product_id" class="form-select" required>
                <option value="">Chọn sản phẩm</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" @selected(old('product_id')==$p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Người dùng</label>
            <select name="user_id" class="form-select">
                <option value="">Khách</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" @selected(old('user_id')==$u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Điểm (1-5)</label>
            <select name="rating" class="form-select" required>
                <option value="">Chọn</option>
                @for($i=1;$i<=5;$i++)
                    <option value="{{ $i }}" @selected(old('rating')==$i)>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Nội dung</label>
            <textarea name="comment" rows="3" class="form-control" placeholder="Nhận xét (tuỳ chọn)">{{ old('comment') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select" required>
                <option value="1" @selected(old('status')==='1')>Đã duyệt</option>
                <option value="0" @selected(old('status')==='0')>Ẩn</option>
            </select>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Lưu</button>
            <a class="btn btn-secondary" href="{{ route('admin.reviews.index') }}">Quay lại</a>
        </div>
    </form>
</div>
</body>
</html>
