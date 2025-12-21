<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Index - eStore Bootstrap Template</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="{{asset('assets/client/img/favicon.png')}}" rel="icon">
    <link href="{{asset('assets/client/img/apple-touch-icon.png')}}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{asset ('assets/client/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset ('assets/client/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
    <link href="{{asset ('assets/client/vendor/swiper/swiper-bundle.min.css')}}" rel="stylesheet">
    <link href="{{asset ('assets/client/vendor/aos/aos.css')}}" rel="stylesheet">
    <link href="{{asset ('assets/client/vendor/glightbox/css/glightbox.min.css')}}" rel="stylesheet">
    <link href="{{asset ('assets/client/vendor/drift-zoom/drift-basic.css')}}" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Main CSS File -->
    <link href="{{asset ('assets/client/css/main.css')}}" rel="stylesheet">
</head>

<body class="pt-5 mt-4">

        @include('client.partials.header')

       <main style="margin-top: 150px;">
        @yield('content')

    </main>
        @include('client.partials.chat_ai')
        @include('client.partials.footer')

    <script src="{{asset ('assets/client/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset ('assets/client/vendor/php-email-form/validate.js')}}"></script>
  <script src="{{asset ('assets/client/vendor/swiper/swiper-bundle.min.js')}}"></script>
  <script src="{{asset ('assets/client/vendor/aos/aos.js')}}"></script>
  <script src="{{asset ('assets/client/vendor/imagesloaded/imagesloaded.pkgd.min.js')}}"></script>
  <script src="{{asset ('assets/client/vendor/isotope-layout/isotope.pkgd.min.js')}}"></script>
  <script src="{{asset ('assets/client/vendor/glightbox/js/glightbox.min.js')}}"></script>
  <script src="{{asset ('assets/client/vendor/drift-zoom/Drift.min.js')}}"></script>
  <script src="{{asset ('assets/client/vendor/purecounter/purecounter_vanilla.js')}}"></script>

  <script src="{{asset ('assets/client/js/main.js')}}"></script>
  
  <script>
    // Load cart count on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadCartCount();
        // Auto-handle all cart add forms
        initCartForms();
    });
    
    // Function to load cart count
    function loadCartCount() {
        fetch('{{ route("cart.count") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartBadge(data.count || 0);
            }
        })
        .catch(error => {
            console.error('Error loading cart count:', error);
        });
    }
    
    // Function to update cart badge
    function updateCartBadge(count) {
        const badge = document.getElementById('cartBadge');
        if (badge) {
            badge.textContent = count;
            // Animation effect
            badge.style.transition = 'transform 0.2s';
            badge.style.transform = 'scale(1.2)';
            setTimeout(() => {
                badge.style.transform = 'scale(1)';
            }, 200);
        }
    }
    
    // Function to show notification
    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
    
    // Initialize all cart add forms to use AJAX
    function initCartForms() {
        const cartForms = document.querySelectorAll('form[action*="cart.add"]');
        
        cartForms.forEach(form => {
            // Skip if already has custom handler (like product detail page)
            if (form.id === 'addToCartForm') {
                return;
            }
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                }
                
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateCartBadge(data.cart_count || 0);
                        showNotification(data.message || 'Đã thêm sản phẩm vào giỏ hàng!', 'success');
                    } else {
                        showNotification(data.message || 'Có lỗi xảy ra!', 'error');
                    }
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng!', 'error');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                });
            });
        });
    }
    
    // Make functions available globally
    window.loadCartCount = loadCartCount;
    window.updateCartBadge = updateCartBadge;
    window.showNotification = showNotification;
  </script>
  
  @yield('scripts')
</body>

</html>
<style>
  body {
    padding-top: 0;
  }
</style>
