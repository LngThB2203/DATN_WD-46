@extends('client.layouts.client')

@section('title', 'Đặt lại mật khẩu')

@section('content')
<div class="container mt-5" style="max-width: 400px;">
    <h3 class="text-center mb-4">Đặt lại mật khẩu</h3>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <div class="mb-3">
            <label>Mật khẩu mới</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Nhập lại mật khẩu</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <button type="submit" class="btn btn-dark w-100">Cập nhật mật khẩu</button>
    </form>
</div>
@endsection
