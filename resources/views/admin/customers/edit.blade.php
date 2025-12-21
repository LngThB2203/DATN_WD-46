@extends('admin.layouts.admin')

@section('title', 'Cập nhật khách hàng')

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

        <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="card-title mb-0">Cập nhật khách hàng</h4>
                </div>

                <div class="card-body">
                    <div class="row">

                        {{-- USER INFO --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Tài khoản người dùng</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ $customer->user->name }} - {{ $customer->user->email }}"
                                   disabled>
                        </div>

                        {{-- MEMBERSHIP --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Cấp độ thành viên <span class="text-danger">*</span></label>
                            <select name="membership_level" class="form-control" required>
                                <option value="Silver" {{ $customer->membership_level == 'Silver' ? 'selected' : '' }}>Silver</option>
                                <option value="Gold" {{ $customer->membership_level == 'Gold' ? 'selected' : '' }}>Gold</option>
                                <option value="Platinum" {{ $customer->membership_level == 'Platinum' ? 'selected' : '' }}>Platinum</option>
                            </select>
                        </div>

                        {{-- GIỚI TÍNH --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Giới tính</label>
                            <select name="gender" class="form-control">
                                <option value="">-- Chọn giới tính --</option>
                                <option value="Nam" {{ $customer->gender == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ $customer->gender == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                <option value="Khác" {{ $customer->gender == 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>

                        {{-- ĐỊA CHỈ --}}
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text"
                                   name="address"
                                   class="form-control"
                                   value="{{ old('address', $customer->address) }}">
                        </div>

                    </div>
                </div>

                <div class="card-footer border-top text-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="ri-save-line"></i> Cập nhật
                    </button>
                    <a href="{{ route('admin.customers.list') }}" class="btn btn-outline-secondary">
                        Quay lại
                    </a>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection
