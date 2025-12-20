<div class="main-nav">
    <!-- Sidebar Logo -->
    <div class="logo-box">
        <a href="{{ url('admin') }}" class="logo-dark">
            <img src="{{ asset('assets/admin/images/logo-sm.png') }}" class="logo-sm" alt="logo sm">
            <img src="{{ asset('assets/admin/images/logo-dark.png') }}" class="logo-lg" alt="logo dark">
        </a>
        <a href="{{ url('admin') }}" class="logo-light">
            <img src="{{ asset('assets/admin/images/logo-sm.png') }}" class="logo-sm" alt="logo sm">
            <img src="{{ asset('assets/admin/images/logo-light.png') }}" class="logo-lg" alt="logo light">
        </a>
    </div>

    <!-- Menu Toggle Button -->
    <button type="button" class="button-sm-hover" aria-label="Hiển thị Sidebar đầy đủ">
        <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone" class="button-sm-hover-icon"></iconify-icon>
    </button>

    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title">Tổng quan</li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Bảng điều khiển </span>
                </a>
            </li>

            <!-- Sản phẩm -->
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarProducts" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarProducts">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:t-shirt-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Sản phẩm </span>
                </a>
                <div class="collapse" id="sidebarProducts">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('products.index') }}">Danh sách</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('products.create') }}">Thêm mới</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Danh mục -->
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarCategory" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarCategory">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:clipboard-list-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Danh mục </span>
                </a>
                <div class="collapse" id="sidebarCategory">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.categories.list') }}">Danh sách</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Biến thể sản phẩm -->
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarVariants" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarVariants">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:flask-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Biến thể sản phẩm </span>
                </a>
                <div class="collapse" id="sidebarVariants">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('variants.index') }}">Danh sách</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('variants.create') }}">Thêm mới</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Khách hàng -->
            {{-- <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarCustomer" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarCustomer">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:user-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Khách hàng </span>
                </a>
                <div class="collapse" id="sidebarCustomer">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.customers.list') }}">Danh sách khách hàng</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.customers.create') }}">Thêm khách hàng</a>
                        </li>
                    </ul>
                </div>
            </li> --}}

            <!-- Newsletter -->
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarNewsletter" data-bs-toggle="collapse"
                   role="button" aria-expanded="false" aria-controls="sidebarNewsletter">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:envelope-bold-duotone" style="color:black;"></iconify-icon>
                    </span>
                    <span class="nav-text"> Bản tin </span>
                </a>
                <div class="collapse" id="sidebarNewsletter">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('post.index') }}">Danh sách tin</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Banner -->
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarBanner" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarBanner">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:box-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Quảng cáo </span>
                </a>
                <div class="collapse" id="sidebarBanner">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('banner.index') }}">Danh sách Banner</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('banner.create') }}">Thêm Banner mới</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Brand -->
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarBrand" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarBrand">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:box-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Thương hiệu </span>
                </a>
                <div class="collapse" id="sidebarBrand">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('brand.index') }}">Danh sách Brand</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('brand.create') }}">Thêm Brand mới</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Kho hàng -->
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarInventory" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarInventory">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:box-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Kho hàng </span>
                </a>
                <div class="collapse" id="sidebarInventory">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('inventories.warehouse') }}">Kho hàng</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('inventories.received-orders') }}">Tồn kho</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('inventories.stock-transactions.index') }}">Lịch sử nhập/xuất</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Orders -->
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarOrders" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarOrders">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:bag-smile-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Đơn hàng </span>
                </a>
                <div class="collapse" id="sidebarOrders">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.orders.list') }}">Danh sách</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Mã giảm giá -->
            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarDiscounts" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarDiscounts">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:leaf-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Mã giảm giá </span>
                </a>
                <div class="collapse" id="sidebarDiscounts">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.discounts.index') }}">Danh sách</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.discounts.create') }}">Thêm mới</a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Liên hệ -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.contacts.index') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:letter-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Liên hệ </span>
                </a>
            </li>

            <!-- Cài đặt -->
            <li class="nav-item">
                <a class="nav-link" href="settings.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:settings-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Cài đặt </span>
                </a>
            </li>

            <!-- Người dùng -->
            <li class="menu-title mt-2">Người dùng</li>

            <li class="nav-item">
                <a class="nav-link" href="pages-profile.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:chat-square-like-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Hồ sơ </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="#sidebarRoles" data-bs-toggle="collapse" role="button"
                   aria-expanded="false" aria-controls="sidebarRoles">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:user-speak-rounded-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Vai trò </span>
                </a>
                <div class="collapse" id="sidebarRoles">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('roles.list') }}">Danh sách</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('roles.edit') }}">Sửa</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('roles.create') }}">Thêm mới</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="pages-permissions.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:checklist-minimalistic-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Quyền </span>
                </a>
            </li>

            <!-- Các ứng dụng khác -->
            <li class="menu-title mt-2">Ứng dụng khác</li>
            <li class="nav-item">
                <a class="nav-link" href="apps-chat.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:chat-round-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Chat </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="apps-email.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:mailbox-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Email </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="apps-calendar.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:calendar-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Lịch </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="apps-todo.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:checklist-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Việc cần làm </span>
                </a>
            </li>

        </ul>
    </div>
</div>