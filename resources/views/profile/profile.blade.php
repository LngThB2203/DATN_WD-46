@extends('client.layouts.client')

@section('title', 'My Profile')

@section('content')
<style>
    .profile-container {
        max-width: 1000px;
        margin: 50px auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .profile-box {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .profile-header h5 {
        font-weight: 600;
        text-transform: uppercase;
    }

    .profile-header button {
        background: none;
        border: none;
        color: #333;
        text-decoration: underline;
        cursor: pointer;
        font-size: 14px;
    }

    .profile-row {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
    }

    .profile-row label {
        font-weight: 500;
        color: #555;
    }

    .profile-row span {
        color: #222;
    }

    input.form-control {
        display: none;
        margin-top: 5px;
    }

    .btn-save, .btn-cancel {
        display: none;
        margin-top: 15px;
    }

    .btn-save {
        background-color: #000;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-cancel {
        background-color: #fff;
        color: #000;
        border: 1px solid #000;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        margin-left: 10px;
    }
</style>

<div class="container mt-5">
    <h3 class="text-center mb-4">Tài Khoản Của Tôi</h3>

<script>
    const editBtn = document.getElementById('editBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const saveBtn = document.querySelector('.btn-save');
    const formControls = document.querySelectorAll('.form-control');
    const spans = document.querySelectorAll('.profile-row span');

    editBtn.addEventListener('click', () => {
        formControls.forEach(el => el.style.display = 'block');
        spans.forEach(el => el.style.display = 'none');
        saveBtn.style.display = 'inline-block';
        cancelBtn.style.display = 'inline-block';
        editBtn.style.display = 'none';
    });

    cancelBtn.addEventListener('click', () => {
        formControls.forEach(el => el.style.display = 'none');
        spans.forEach(el => el.style.display = 'inline');
        saveBtn.style.display = 'none';
        cancelBtn.style.display = 'none';
        editBtn.style.display = 'inline';
    });
</script>
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
