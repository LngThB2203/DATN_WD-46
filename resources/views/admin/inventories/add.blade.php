@extends('admin.layouts.admin')
@section('title','Thêm kho mới')
@section('content')
<div class="page-content">
    <div class="container-xxl">
        <div class="card">
            <div class="card-header">
                <h4>Thêm kho mới</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('inventories.warehouse.store') }}" method="POST">@csrf
                    <div class="mb-3"><label class="form-label">Tên kho</label><input name="warehouse_name"
                            class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Địa chỉ</label><input name="address"
                            class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Quản lý</label>
                        <select name="manager_id" class="form-select">
                            <option value="">-- Chưa chọn --</option>
                            @foreach($managers as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Số điện thoại</label><input name="phone"
                            class="form-control"></div>
                    <button class="btn btn-primary">Lưu</button>
                    <a href="{{ route('inventories.warehouse') }}" class="btn btn-light">Hủy</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
