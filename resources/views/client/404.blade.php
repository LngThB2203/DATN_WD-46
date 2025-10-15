@extends('client.layouts.app')

@section('title', 'Không tìm thấy trang')

@section('content')
<section class="py-5 text-center">
    <div class="container-fluid container-xl">
        <h1 class="display-4 fw-bold mb-3">404</h1>
        <p class="lead mb-4">Trang bạn yêu cầu không tồn tại hoặc đã bị di chuyển.</p>
        <a class="btn btn-primary" href="{{ route('home') }}">Về trang chủ</a>
    </div>
</section>
@endsection
