@extends('client.layouts.app')

@section('title', 'Về chúng tôi')

@section('content')
    <!-- Breadcrumb -->
    <section class="py-4 border-bottom bg-light">
        <div class="container-fluid container-xl">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-dark">Trang chủ</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Về chúng tôi</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-5">
        <div class="container-fluid container-xl">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="{{ asset('assets/client/img/about/about.jpg') }}" alt="Về eStore"
                        class="img-fluid rounded-3 shadow">
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-3">Về eStore</h2>
                    <p class="text-muted mb-3">
                        <strong>eStore</strong> là cửa hàng nước hoa trực tuyến chuyên cung cấp các dòng nước hoa cao cấp,
                        chính hãng từ những thương hiệu nổi tiếng trên thế giới. Chúng tôi cam kết mang đến cho khách hàng
                        trải nghiệm mua sắm trực tuyến dễ dàng, an toàn và nhanh chóng.
                    </p>
                    <p class="text-muted mb-3">
                        Với đội ngũ tư vấn viên nhiệt tình và am hiểu về nước hoa, chúng tôi giúp bạn tìm ra mùi hương phù
                        hợp nhất với phong cách và cá tính riêng của mình.
                    </p>
                    <p class="text-muted">
                        Sự hài lòng của khách hàng là ưu tiên hàng đầu của chúng tôi — từ chất lượng sản phẩm, dịch vụ giao
                        hàng nhanh chóng, đến chính sách đổi trả minh bạch.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-5 bg-light">
        <div class="container-fluid container-xl">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="p-4 bg-white shadow rounded-3 h-100">
                        <h4 class="fw-bold mb-3">Sứ mệnh</h4>
                        <p class="text-muted">
                            Mang đến cho khách hàng những mùi hương tinh tế và trải nghiệm mua sắm tuyệt vời nhất, giúp tôn
                            lên vẻ đẹp và phong cách riêng.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="p-4 bg-white shadow rounded-3 h-100">
                        <h4 class="fw-bold mb-3">Tầm nhìn</h4>
                        <p class="text-muted">
                            Trở thành thương hiệu nước hoa trực tuyến hàng đầu Việt Nam — nơi mọi người có thể tìm thấy mùi
                            hương yêu thích của mình.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="p-4 bg-white shadow rounded-3 h-100">
                        <h4 class="fw-bold mb-3">Giá trị cốt lõi</h4>
                        <p class="text-muted">
                            Uy tín – Chất lượng – Tận tâm – Sáng tạo. Chúng tôi luôn đặt lợi ích và trải nghiệm của khách
                            hàng lên hàng đầu.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="py-5">
        <div class="container-fluid container-xl text-center">
            <h3 class="fw-bold mb-3">Liên hệ với chúng tôi</h3>
            <p class="text-muted mb-4">Nếu bạn có bất kỳ thắc mắc hoặc cần tư vấn thêm, hãy liên hệ với đội ngũ eStore.</p>
            <a href="{{ route('contact.index') }}" class="btn btn-dark px-4 py-2 rounded-pill">
                Liên hệ ngay
            </a>
        </div>
    </section>
@endsection
