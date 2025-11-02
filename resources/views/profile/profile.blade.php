@extends('client.layouts.client')

@section('title', 'My Profile')

@section('content')
<div class="container my-5">

    {{-- Thông tin tài khoản --}}
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="border p-3 rounded">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-person"></i> THÔNG TIN TÀI KHOẢN</h5>
                    <a href="{{ route('profile.edit') }}" class="text-decoration-none">Chỉnh sửa</a>
                </div>
                <div>
                    <p><strong>Họ và tên:</strong> {{ Auth::user()->name ?? 'Chưa có' }}</p>
                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                    <p><strong>Giới tính:</strong>
    {{ Auth::user()->gender === 1 ? 'Nam' : (Auth::user()->gender === 0 ? 'Nữ' : 'Chưa có') }}
</p>
                    <p><strong>Số điện thoại:</strong> {{ Auth::user()->phone ?? 'Chưa có' }}</p>
                    <p><strong>Địa chỉ:</strong> {{ Auth::user()->address ?? 'Chưa có' }}</p>
                </div>
            </div>
        </div>

        {{-- Lịch sử mua hàng --}}
        <div class="col-md-6 mb-4">
            <div class="border p-3 rounded">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> LỊCH SỬ MUA HÀNG</h5>
                    <a href="#" class="text-decoration-none">Xem tất cả</a>
                </div>
                <p>Bạn chưa có đơn hàng nào. Tiếp tục mua hàng!</p>
            </div>
        </div>
    </div>

    {{-- Chương trình thành viên & Địa chỉ --}}
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="border p-3 rounded">
                <h5><i class="bi bi-diagram-3"></i> CHƯƠNG TRÌNH THÀNH VIÊN</h5>
                <hr>
                <p>Hạng thành viên của bạn là: <strong>BRONZE</strong></p>
            </div>
        </div>
    </div>

    {{-- Sản phẩm yêu thích --}}
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="border p-3 rounded">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-heart"></i> SẢN PHẨM YÊU THÍCH</h5>
                    <a href="#" class="text-decoration-none">Xem tất cả</a>
                </div>
                <p>Bạn chưa có sản phẩm yêu thích nào trong danh sách!</p>
            </div>
        </div>
    </div>

    {{-- Đăng xuất --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-dark">ĐĂNG XUẤT</button>
    </form>

</div>
@endsection
