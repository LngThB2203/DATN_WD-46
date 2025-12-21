@extends('admin.layouts.admin')

@section('title', 'Thêm khách hàng')

@section('content')
<div class="page-content">
    <div class="container-xxl">

        {{-- HIỂN THỊ LỖI --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.customers.store') }}" method="POST">
            @csrf

            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="card-title mb-0">Thêm khách hàng mới</h4>
                </div>

                <div class="card-body">
                    <div class="row">

                        {{-- USER --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Tài khoản người dùng <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-control" required>
                                <option value="">-- Chọn user --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} - {{ $user->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- CẤP ĐỘ THÀNH VIÊN --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Cấp độ thành viên <span class="text-danger">*</span></label>
                            <select name="membership_level" class="form-control" required>
                                <option value="Silver" {{ old('membership_level') == 'Silver' ? 'selected' : '' }}>Silver</option>
                                <option value="Gold" {{ old('membership_level') == 'Gold' ? 'selected' : '' }}>Gold</option>
                                <option value="Platinum" {{ old('membership_level') == 'Platinum' ? 'selected' : '' }}>Platinum</option>
                            </select>
                        </div>

                        {{-- GIỚI TÍNH --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Giới tính</label>
                            <select name="gender" class="form-control">
                                <option value="">-- Chọn giới tính --</option>
                                <option value="Nam" {{ old('gender') == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('gender') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                <option value="Khác" {{ old('gender') == 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>

                        {{-- ĐỊA CHỈ --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text"
                                   name="address"
                                   class="form-control"
                                   placeholder="Nhập địa chỉ"
                                   value="{{ old('address') }}">
                        </div>

                    </div>
                </div>

                <div class="card-footer border-top text-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="ri-save-line"></i> Xác nhận
                    </button>
                    <a href="{{ route('admin.customers.list') }}" class="btn btn-outline-secondary">
                        Hủy
                    </a>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection
