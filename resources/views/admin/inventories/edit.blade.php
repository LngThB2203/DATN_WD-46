@extends('admin.layouts.admin')
@section('title','Sửa kho')
@section('content')
<div class="page-content">
    <div class="container-xxl">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Sửa kho: {{ $warehouse->warehouse_name }}</h4>
                <a href="{{ route('inventories.warehouse') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bx bx-arrow-back"></i> Quay lại
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('inventories.warehouse.update', $warehouse->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tên kho <span class="text-danger">*</span></label>
                                <input type="text" name="warehouse_name"
                                       class="form-control @error('warehouse_name') is-invalid @enderror"
                                       value="{{ old('warehouse_name', $warehouse->warehouse_name) }}" 
                                       placeholder="Nhập tên kho" required>
                                @error('warehouse_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ</label>
                                <input type="text" name="address"
                                       class="form-control @error('address') is-invalid @enderror"
                                       value="{{ old('address', $warehouse->address) }}" 
                                       placeholder="Nhập địa chỉ kho">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Quản lý</label>
                                <select name="manager_id" class="form-select @error('manager_id') is-invalid @enderror">
                                    <option value="">-- Chọn quản lý --</option>
                                    @foreach($managers as $m)
                                        <option value="{{ $m->id }}" 
                                                {{ old('manager_id', $warehouse->manager_id) == $m->id ? 'selected' : '' }}>
                                            {{ $m->name }} ({{ $m->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('manager_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $warehouse->phone) }}" 
                                       placeholder="Nhập số điện thoại">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="{{ route('inventories.warehouse') }}" class="btn btn-light">Hủy</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
