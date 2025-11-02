@extends('client.layouts.client')

@section('title', 'Chỉnh sửa thông tin cá nhân')

@section('content')
<div class="container my-5" style="max-width: 600px;">
    <h3 class="mb-4">Chỉnh sửa thông tin cá nhân</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" value="{{ $user->email }}" class="form-control" disabled>
        </div>

        <div class="form-group">
    <label for="gender">Giới tính</label>
    <select name="gender" class="form-control">
        <option value="1" {{ old('gender', $user->gender) == 1 ? 'selected' : '' }}>Nam</option>
        <option value="0" {{ old('gender', $user->gender) == 0 ? 'selected' : '' }}>Nữ</option>
    </select>
</div>

        <div class="mb-3">
            <label class="form-label">Số điện thoại</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Địa chỉ</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}" class="form-control">
        </div>

        <button type="submit" class="btn btn-dark">Cập nhật</button>
        <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">Hủy</a>
    </form>
</div>
@endsection
