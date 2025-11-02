@extends('client.layouts.app')

@section('title', 'Chính sách bảo mật')

@section('content')
<section class="py-4 border-bottom">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chính sách bảo mật</li>
            </ol>
        </nav>
    </div>
</section>
<section class="py-5">
    <div class="container-fluid container-xl">
        <h2 class="fw-bold mb-3">Chính sách bảo mật</h2>
        <p class="text-muted">Nội dung chính sách bảo mật sẽ được cập nhật sau.</p>
    </div>
</section>
@endsection
