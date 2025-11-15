<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Trang chủ')</title>
    <link href="{{ asset('assets/client/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/client/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/client/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/client/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/client/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/client/vendor/drift-zoom/drift-basic.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/client/css/main.css') }}" rel="stylesheet">
</head>
<body class="index-page">

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show position-fixed"
     style="top: 20px; right: 20px; z-index: 9999;">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@include('client.partials.header')

<main style="margin-top: 150px;">
    @yield('content')
</main>

@include('client.partials.footer')

<script src="{{ asset('assets/client/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/client/vendor/swiper/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/client/vendor/aos/aos.js') }}"></script>
<script src="{{ asset('assets/client/vendor/glightbox/js/glightbox.min.js') }}"></script>
<script src="{{ asset('assets/client/vendor/drift-zoom/Drift.min.js') }}"></script>
<script src="{{ asset('assets/client/js/main.js') }}"></script>

<script>
function loadCartCount() {
    fetch("{{ route('cart.count') }}")
        .then(res => res.json())
        .then(data => {
            let badge = document.getElementById("cart-count");
            if (badge) {
                badge.innerText = data.count;
                badge.style.display = data.count > 0 ? 'inline-block' : 'none';
            }
        });
}

document.addEventListener("DOMContentLoaded", loadCartCount);

</script>


</body>
</html>
