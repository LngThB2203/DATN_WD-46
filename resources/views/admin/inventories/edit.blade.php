@extends('admin.layouts.admin')
@section('title','Sửa kho')
@section('content')
<div class="page-content">
    <div class="container-xxl">
        <div class="card">
            <div class="card-header">
                <h4>Sửa kho: {{ $warehouse->warehouse_name }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('inventories.warehouse.update',$warehouse->id) }}" method="POST">@csrf
                    @method('PUT')
                    <div class="mb-3"><label class="form-label">Tên kho</label><input name="warehouse_name"
                            value="{{ $warehouse->warehouse_name }}" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Địa chỉ</label><input name="address"
                            value="{{ $warehouse->address }}" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Quản lý</label>
                        <select name="manager_id" class="form-select">
                            <option value="">-- Không chọn --</option>
                            @foreach($managers as $m)<option value="{{ $m->id }}" {{ $warehouse->
                                manager_id==$m->id?'selected':'' }}>{{ $m->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Số điện thoại</label><input name="phone"
                            value="{{ $warehouse->phone }}" class="form-control"></div>
                    <button class="btn btn-primary">Cập nhật</button>
                    <a href="{{ route('inventories.warehouse') }}" class="btn btn-light">Hủy</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
