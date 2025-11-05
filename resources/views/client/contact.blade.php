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
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="row g-3" id="contactForm">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required />
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" required />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Điện thoại</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                               value="{{ old('phone') }}" />
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Chủ đề</label>
                        <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" 
                               value="{{ old('subject') }}" />
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control @error('message') is-invalid @enderror" 
                                  rows="5" required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Gửi
                        </button>
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

@section('scripts')
<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const spinner = submitBtn.querySelector('.spinner-border');
    
    submitBtn.disabled = true;
    spinner.classList.remove('d-none');
});
</script>
@endsection
