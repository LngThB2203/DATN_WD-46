@extends('client.layouts.app')

@section('title', 'Câu hỏi thường gặp')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">FAQ</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <h2 class="fw-bold mb-4">Câu hỏi thường gặp</h2>
        <div class="accordion" id="faq">
            <div class="accordion-item">
                <h2 class="accordion-header" id="q1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#a1">Vận chuyển mất bao lâu?</button>
                </h2>
                <div id="a1" class="accordion-collapse collapse show" data-bs-parent="#faq">
                    <div class="accordion-body">Thời gian vận chuyển dự kiến 2-5 ngày làm việc.</div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="q2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a2">Chính sách đổi trả như thế nào?</button>
                </h2>
                <div id="a2" class="accordion-collapse collapse" data-bs-parent="#faq">
                    <div class="accordion-body">Đổi trả trong 7 ngày nếu còn nguyên tem và chưa sử dụng.</div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
