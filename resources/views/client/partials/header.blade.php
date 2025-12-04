<header id="header" class="header position-absolute top-0 start-0 w-100 bg-white shadow-sm" style="z-index: 999;">
    <div class="top-bar py-2 border-bottom">
        <div class="container-fluid container-xl">
            <div class="row align-items-center">
                <div class="col-lg-4 d-none d-lg-flex">
                    <div class="top-bar-item">
                        <i class="bi bi-telephone-fill me-2"></i>
                        <span>Need help? Call us: </span>
                        <a href="tel:+1234567890">+1 (234) 567-890</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 text-center">
                    <div class="announcement-slider swiper init-swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">🚚 Free shipping on orders over $50</div>
                            <div class="swiper-slide">💰 30 days money back guarantee.</div>
                            <div class="swiper-slide">🎁 20% off on your first order</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block">
                    <div class="d-flex justify-content-end">
                        <!-- Language & Currency Dropdowns -->
                        <div class="top-bar-item dropdown me-3">
                            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-translate me-2"></i>EN
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-check2 me-2 selected-icon"></i>English</a></li>
                                <li><a class="dropdown-item" href="#">Español</a></li>
                                <li><a class="dropdown-item" href="#">Français</a></li>
                                <li><a class="dropdown-item" href="#">Deutsch</a></li>
                            </ul>
                        </div>
                        <div class="top-bar-item dropdown">
                            <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-currency-dollar me-2"></i>USD
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-check2 me-2 selected-icon"></i>USD</a></li>
                                <li><a class="dropdown-item" href="#">EUR</a></li>
                                <li><a class="dropdown-item" href="#">GBP</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-header">
        <div class="container-fluid container-xl">
            <div class="d-flex py-3 align-items-center justify-content-between">
                <a href="{{ route('home') }}" class="logo d-flex align-items-center">
                    <h1 class="sitename mb-0">46 Perfume</h1>
                </a>

                <!-- Header Search Form -->
                <div class="position-relative w-50">
                    <input type="text" class="form-control" placeholder="Search for products" id="searchInput">
                    <div id="searchResults" class="position-absolute bg-white shadow-sm w-100 border rounded"
                         style="top:100%; left:0; z-index:1000; display:none; max-height:400px; overflow-y:auto; padding:10px;"></div>
                </div>

                <div class="header-actions d-flex align-items-center justify-content-end">
                    <!-- Account -->
                    <div class="dropdown account-dropdown">
                        <button class="header-action-btn" data-bs-toggle="dropdown">
                            <i class="bi bi-person"></i>
                        </button>
                        <div class="dropdown-menu">
                            @guest
                                <div class="dropdown-header text-center">
                                    <h6>Chào mừng bạn tới <b class="sitename">46 Perfume</b></h6>
                                    <p class="mb-0">Truy cập tài khoản & Quản lý đơn hàng</p>
                                </div>
                                <div class="dropdown-body">
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('account.show') }}">
                                        <i class="bi bi-person-circle me-2"></i>
                                        <span>Account</span>
                                    </a>
                                </div>
                                <div class="dropdown-footer">
                                    <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">Sign In</a>
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">Sign Up</a>
                                </div>
                            @else
                                <div class="dropdown-header text-center">
                                    <h6>Xin chào, {{ Auth::user()->name }}</h6>
                                </div>
                                <div class="dropdown-body">
                                    <a class="dropdown-item d-flex align-items-center" href="{{ route('account.show') }}">
                                        <i class="bi bi-person-circle me-2"></i>
                                        <span>Account</span>
                                    </a>
                                </div>
                                <div class="dropdown-footer">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="bi bi-box-arrow-right me-1"></i> Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            @endguest
                        </div>
                    </div>

                    <a href="{{ route('cart.index') }}" class="header-action-btn position-relative">
                        <i class="bi bi-cart3 fs-4"></i>
                        <span class="badge bg-primary position-absolute top-0 start-100 translate-middle" id="cartBadge">0</span>
                    </a>
                    <i class="mobile-nav-toggle d-xl-none bi bi-list me-0"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="header-nav border-top">
        <div class="container-fluid container-xl">
            <nav id="navmenu" class="navmenu">
                <ul class="d-flex flex-wrap justify-content-center gap-3 py-2 mb-0 list-unstyled">
                    <li><a href="{{ route('home') }}" class="active">Home</a></li>
                    <li><a href="{{ route('about') }}">About</a></li>
                    <li><a href="{{ route('blog.index') }}">Blog</a></li>
                    <li><a href="{{ route('contact.index') }}">Contact</a></li>
                    <li><a href="{{ route('client.products.index') }}">Product</a></li>
                    <li><a href="{{ route('category.index') }}">Category</a></li>
                    <li><a href="{{ route('cart.index') }}">Cart</a></li>
                    <li><a href="{{ route('checkout.index') }}">Checkout</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- AJAX Search Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const resultsDiv = document.getElementById('searchResults');
        let timeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value.trim();

            if(query.length < 2) {
                resultsDiv.style.display = 'none';
                resultsDiv.innerHTML = '';
                return;
            }

            timeout = setTimeout(() => {
                fetch('{{ route("home.search") }}?q=' + encodeURIComponent(query))
                    .then(res => res.json())
                    .then(data => {
                        resultsDiv.innerHTML = data.html || '<div class="text-center text-muted p-2">Không tìm thấy sản phẩm</div>';
                        resultsDiv.style.display = 'block';

                        // Click vào sản phẩm đóng popup
                        resultsDiv.querySelectorAll('a').forEach(link=>{
                            link.addEventListener('click', ()=>{
                                resultsDiv.style.display = 'none';
                            });
                        });

                        // Rebind add-to-cart buttons inside search results
                        resultsDiv.querySelectorAll('.add-to-cart-btn').forEach(btn=>{
                            btn.addEventListener('click', function(e){
                                e.preventDefault();
                                const productId = this.dataset.productId;
                                const select = this.closest('.card-body').querySelector('.variant-select');
                                const variantId = select ? select.value : null;
                                if(select && !variantId){
                                    alert('Vui lòng chọn biến thể trước khi thêm vào giỏ');
                                    return;
                                }

                                fetch('{{ route("cart.add") }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept':'application/json'
                                    },
                                    body: new URLSearchParams({
                                        product_id: productId,
                                        variant_id: variantId,
                                        quantity: 1
                                    })
                                })
                                .then(res=>res.json())
                                .then(data=>{
                                    if(data.success){
                                        alert(data.message);
                                        document.getElementById('cartBadge').textContent = data.cart_count;
                                    } else {
                                        alert(data.message);
                                    }
                                });
                            });
                        });
                    });
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if(!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                resultsDiv.style.display = 'none';
            }
        });
    });
    </script>
</header>
