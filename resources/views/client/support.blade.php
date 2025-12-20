@extends('client.layouts.app')

@section('title', 'Hỗ trợ')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Hỗ trợ</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <h2 class="fw-bold mb-3">Trung tâm hỗ trợ</h2>
        <p class="text-muted">Vui lòng chọn chủ đề bạn cần hỗ trợ hoặc liên hệ trực tiếp.</p>
        <ul>
            <li><a href="{{ route('faq.index') }}">Câu hỏi thường gặp</a></li>
            <li><a href="{{ route('contact.index') }}">Liên hệ</a></li>
            <li><a href="{{ route('return.policy') }}">Chính sách đổi trả</a></li>
        </ul>
    </div>
</section>
@endsection
