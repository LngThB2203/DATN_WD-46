@extends('admin.layouts.admin')

@section('title', 'Gửi Tin Newsletter')

@section('content')
<div class="page-content">
    <div class="container-xxl">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Soạn Tin Newsletter</h4>
                        <a href="{{ route('admin.newsletters.list') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Quay lại
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.newsletters.sendMail') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Chủ đề <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror"
                                    id="subject" name="subject" value="{{ old('subject') }}"
                                    placeholder="VD: Khuyến mãi 20% - Giảm giá đặc biệt cho bạn">
                                @error('subject')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('message') is-invalid @enderror" id="content"
    name="message" rows="12"
    placeholder="Nhập nội dung tin nhắn...">{{ old('message') }}</textarea>
                                @error('content')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-2">Tin sẽ được gửi đến: tất cả khách hàng + những
                                    người đăng ký newsletter</small>
                            </div>

                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-2"></i>Gửi Newsletter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-lightbulb me-2"></i>Mẫu nội dung
                        </h5>
                    </div>
                    <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                        <div class="list-group list-group-sm">
                            <button type="button" class="list-group-item list-group-item-action text-start"
                                onclick="setTemplate('Chào mừng bạn tới 46 Perfume!', 'Xin chào,\n\nCảm ơn bạn đã tạo tài khoản tại 46 Perfume. Chúng tôi rất vui được phục vụ bạn!\n\nChúng tôi chuyên cung cấp các sản phẩm nước hoa chính hãng, chất lượng cao từ các thương hiệu nổi tiếng trên thế giới.\n\nHãy khám phá ngay bộ sưu tập nước hoa độc đáo của chúng tôi tại 46perfume.com\n\nTrân trọng,\nĐội ngũ 46 Perfume')">
                                <i class="bi bi-star me-2"></i>
                                <strong>Chào mừng</strong><br>
                                <small class="text-muted">Cho khách hàng mới</small>
                            </button>

                            <button type="button" class="list-group-item list-group-item-action text-start"
                                onclick="setTemplate('Khuyến mãi 20% - Hôm nay hãy mua sắm!', 'Xin chào khách hàng thân yêu,\n\n46 Perfume tưng bừng khuyến mãi GIẢM 20% toàn bộ sản phẩm!\n\nCác ưu đãi:\n✓ Giảm 20% cho toàn bộ sản phẩm\n✓ Miễn phí vận chuyển cho đơn hàng từ 500,000đ\n✓ Tặng mẫu thử nước hoa cao cấp với mỗi đơn\n✓ Đóng gói quà tặng miễn phí\n\nThời gian: 22-24 tháng 12 (Còn 3 ngày thôi!)\n\nMua ngay tại: 46perfume.com\n\nTrân trọng,\nĐội ngũ 46 Perfume')">
                                <i class="bi bi-percent me-2"></i>
                                <strong>Khuyến mãi</strong><br>
                                <small class="text-muted">Tăng bán hàng</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setTemplate(subject, content) {
    document.getElementById('subject').value = subject;
    document.getElementById('content').value = content;
    document.getElementById('subject').focus();
}
</script>
@endsection
