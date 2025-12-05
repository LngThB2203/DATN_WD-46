@extends('client.layouts.app')

@section('title', 'Tài khoản')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tài khoản</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <div class="row g-4">
            <aside class="col-lg-3">
                <div class="list-group">
                    <a class="list-group-item list-group-item-action active">Thông tin</a>
                    <a class="list-group-item list-group-item-action" href="{{ route('orders.index') }}">Đơn hàng</a>
                    <a class="list-group-item list-group-item-action">Địa chỉ</a>
                </div>
            </aside>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header fw-semibold">Thông tin tài khoản</div>
                    <div class="card-body">
                        <p>Trang tài khoản (sẽ hiển thị dữ liệu người dùng sau khi tích hợp backend).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
