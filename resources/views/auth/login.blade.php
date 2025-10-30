@extends('client.layouts.client')

@section('title', 'Đăng nhập')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <h2 class="text-center mb-4">Đăng nhập tài khoản</h2>

            <div class="card shadow-sm p-4">

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

                {{-- Form login --}}
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email đăng nhập</label>
                        <input type="email" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" placeholder="Nhập email của bạn" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Nhập mật khẩu" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Remember me --}}
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label for="remember" class="form-check-label">Ghi nhớ đăng nhập</label>
                    </div>

                    {{-- Submit button --}}
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Đăng nhập</button>
                    </div>

                    {{-- Đường dẫn đến trang đăng ký --}}
                    <p class="text-center mt-3">
                        Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

