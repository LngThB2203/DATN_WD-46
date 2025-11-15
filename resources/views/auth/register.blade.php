@extends('client.layouts.client')

@section('title', 'Đăng ký tài khoản')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <h2 class="text-center mb-4">Đăng ký tài khoản mới</h2>

            <div class="card shadow-sm p-4">

                {{-- Hiển thị thông báo --}}
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form register --}}
                <form method="POST" action="{{ route('register.post') }}">
                    @csrf

                    {{-- Tên --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Họ và tên</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="Nhập họ tên" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" placeholder="Nhập email" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Mật khẩu --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Nhập mật khẩu" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Xác nhận mật khẩu --}}
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-control" placeholder="Nhập lại mật khẩu" required>
                    </div>

                    {{-- Submit --}}
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">Đăng ký</button>
                    </div>

                    {{-- Link đăng nhập --}}
                    <p class="text-center mt-3">
                        Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập ngay</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
