@extends('client.layouts.app')

@section('title', 'Liên hệ')

@section('content')
{{-- Breadcrumb Section --}}
<section class="py-3 border-bottom bg-light">
    <div class="container-fluid container-xl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Liên hệ</li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container-fluid container-xl">
        <div class="row g-5">
            {{-- Form Column --}}
            <div class="col-lg-6">
                <div class="pe-lg-4">
                    <h2 class="fw-bold mb-3">Liên hệ với chúng tôi</h2>
                    <p class="text-muted mb-4">Gửi câu hỏi, yêu cầu hỗ trợ hoặc góp ý cho chúng tôi theo biểu mẫu dưới đây.</p>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}" class="row g-3 shadow-sm p-4 rounded bg-white border" id="contactForm">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="Nguyễn Văn A" value="{{ old('name') }}" required />
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   placeholder="example@gmail.com" value="{{ old('email') }}" required />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Điện thoại</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                   placeholder="09xxx..." value="{{ old('phone') }}" />
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chủ đề</label>
                            <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" 
                                   placeholder="Hỗ trợ đơn hàng, tư vấn..." value="{{ old('subject') }}" />
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nội dung <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control @error('message') is-invalid @enderror" 
                                      rows="4" placeholder="Nhập nội dung tin nhắn..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm" id="submitBtn">
                                <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                                Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Map Column --}}
            <div class="col-lg-6">
                <div class="h-100 min-vh-400">
                    <h4 class="fw-bold mb-3">Vị trí của chúng tôi</h4>
                    <div class="ratio ratio-1x1 rounded-3 overflow-hidden shadow-sm border" style="min-height: 450px;">
                        {{-- Thay link nhúng Google Map thực tế của bạn vào đây --}}
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.863981044384!2d105.79155707503147!3d21.03812778061353!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab354920c233%3A0x5d0607751395400d!2zQ8O0bmcgdmnDqm4gQ-G6p3UgR2nhuql5!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s" 
                            style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts') {{-- Dùng push thay vì section để tránh xung đột layout chính --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    if(contactForm) {
        contactForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const spinner = submitBtn.querySelector('.spinner-border');
            
            submitBtn.disabled = true;
            spinner.classList.remove('d-none');
        });
    }
});
</script>
@endpush