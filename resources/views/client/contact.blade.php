@extends('client.layouts.app')

@section('title', 'Liên hệ')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Liên hệ</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <div class="row g-4">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3">Liên hệ với chúng tôi</h2>
                <p class="text-muted">Gửi câu hỏi, yêu cầu hỗ trợ tại đây.</p>
                <form class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Họ tên</label>
                        <input class="form-control" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" />
                    </div>
                    <div class="col-12">
                        <label class="form-label">Nội dung</label>
                        <textarea class="form-control" rows="5"></textarea>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary">Gửi</button>
                    </div>
                </form>
            </div>
            <div class="col-lg-6">
                <div class="ratio ratio-16x9 rounded overflow-hidden">
                    <iframe src="https://maps.google.com/maps?q=Ho%20Chi%20Minh&t=&z=13&ie=UTF8&iwloc=&output=embed"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
