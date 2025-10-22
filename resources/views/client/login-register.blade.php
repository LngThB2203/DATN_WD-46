@extends('client.layouts.app')

@section('title', 'Đăng nhập / Đăng ký')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Đăng nhập / Đăng ký</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header fw-semibold">Đăng nhập</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control"/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control"/>
                        </div>
                        <button class="btn btn-primary w-100">Đăng nhập</button>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header fw-semibold">Đăng ký</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Họ tên</label>
                            <input class="form-control"/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control"/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control"/>
                        </div>
                        <button class="btn btn-outline-primary w-100">Tạo tài khoản</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
