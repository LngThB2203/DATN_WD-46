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
                <form class="search-form desktop-search-form">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search for products">
                        <button class="btn" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                <div class="header-actions d-flex align-items-center justify-content-end">
                   <!-- Account -->
            <div class="dropdown account-dropdown">
              <button class="header-action-btn" data-bs-toggle="dropdown">
                <i class="bi bi-person"></i>
              </button>
              <div class="dropdown-menu">
                @guest
                    {{-- ==================== GIAO DIỆN KHI CHƯA ĐĂNG NHẬP ==================== --}}
                    <div class="dropdown-header text-center">
                        <h6>Chào mừng bạn tới <b class="sitename">46 Perfume</b></h6>
                        <p class="mb-0">Truy cập tài khoản &amp; Quản lý đơn hàng</p>
                    </div>

                    <div class="dropdown-body">
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('account.show') }}">
                            <i class="bi bi-person-circle me-2"></i>
                            <span>Account</span>
                        </a>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bi bi-bag-check me-2"></i>
                            <span>My Orders</span>
                        </a>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bi bi-heart me-2"></i>
                            <span>My Wishlist</span>
                        </a>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bi bi-gear me-2"></i>
                            <span>Settings</span>
                        </a>
                    </div>

                    <div class="dropdown-footer">
                        <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">Sign In</a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">Register</a>
                    </div>
                @else
                    {{-- ==================== GIAO DIỆN KHI ĐÃ ĐĂNG NHẬP ==================== --}}
                    <div class="dropdown-header text-center">
                        <h6>Xin chào, <strong>{{ Auth::user()->name }}</strong> 👋</h6>
                        <p class="mb-0">Chúc bạn mua sắm vui vẻ</p>
                    </div>

                    <div class="dropdown-body">
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('account.show') }}">
                            <i class="bi bi-person-circle me-2"></i>
                            <span>Account</span>
                        </a>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bi bi-bag-check me-2"></i>
                            <span>My Orders</span>
                        </a>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bi bi-heart me-2"></i>
                            <span>My Wishlist</span>
                        </a>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <i class="bi bi-gear me-2"></i>
                            <span>Settings</span>
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
                        <!-- End -->
                    <a href="#" class="header-action-btn d-none d-md-block">
                        <i class="bi bi-heart"></i>
                        <span class="badge">0</span>
                    </a>
                    <a href="{{ route('cart.index') }}" class="header-action-btn">
                        <i class="bi bi-cart3"></i>
                        <span class="badge">0</span>
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
                    <li><a href="{{ route('account.show') }}">Account</a></li>
                    <li><a href="{{ route('category.index') }}">Category</a></li>
                    <li><a href="{{ route('cart.index') }}">Cart</a></li>
                    <li><a href="{{ route('checkout.index') }}">Checkout</a></li>
                </ul>
            </nav>
        </div>
    </div>
</header>
