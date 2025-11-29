@extends('admin.layouts.admin')

@section('title', 'Thêm khách hàng')

@section('content')
<div class="page-content">
    <div class="container-xxl">
        <form action="{{ route('admin.customers.store') }}" method="POST">
            @csrf

            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="card-title mb-0">Thêm khách hàng mới</h4>
                </div>
                <div class="card-body">
                    <div class="row">

                        {{-- Tên --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Tên khách hàng</label>
                            <input type="text" name="name" class="form-control" placeholder="Nhập tên khách hàng" value="{{ old('name') }}">
                        </div>

                        {{-- Email --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Nhập email" value="{{ old('email') }}">
                        </div>

                        {{-- SĐT --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại" value="{{ old('phone') }}">
                        </div>

                        {{-- Địa chỉ --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" name="address" class="form-control" placeholder="Nhập địa chỉ" value="{{ old('address') }}">
                        </div>

                        {{-- Giới tính --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Giới tính</label>
                            <select name="gender" class="form-control">
                                <option value="">-- Chọn giới tính --</option>
                                <option value="Nam" {{ old('gender') == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('gender') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                <option value="Khác" {{ old('gender') == 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>

                        {{-- Cấp độ thành viên --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Cấp độ thành viên</label>
                            <select name="membership_level" class="form-control">
                                <option value="Silver" {{ old('membership_level') == 'Silver' ? 'selected' : '' }}>Silver</option>
                                <option value="Gold" {{ old('membership_level') == 'Gold' ? 'selected' : '' }}>Gold</option>
                                <option value="Platinum" {{ old('membership_level') == 'Platinum' ? 'selected' : '' }}>Platinum</option>
                            </select>
                        </div>

                        {{-- Trạng thái --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-control">
                                <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Hoạt động</option>
                                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Ngừng hoạt động</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="card-footer border-top text-end">
                    <button type="submit" class="btn btn-primary me-2">Xác nhận</button>
                    <a href="{{ route('admin.customers.list') }}" class="btn btn-outline-secondary">Hủy</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
