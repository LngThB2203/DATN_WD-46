<div class="main-nav">
    <!-- Sidebar Logo -->
    <div class="logo-box">
        <a href="{{ url('admin') }}" class="logo-dark">
            <img src="{{asset('assets/admin/images/logo-sm.png') }}" class="logo-sm" alt="logo sm">
            <img src="{{asset('assets/admin/images/logo-dark.png') }}" class="logo-lg" alt="logo dark">
        </a>

        <a href="{{ url('admin') }}" class="logo-light">
            <img src="{{asset('assets/admin/images/logo-sm.png') }}" class="logo-sm" alt="logo sm">
            <img src="{{asset('assets/admin/images/logo-light.png') }}" class="logo-lg" alt="logo light">
        </a>
    </div>

    <!-- Menu Toggle Button (sm-hover) -->
    <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
        <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone" class="button-sm-hover-icon"></iconify-icon>
    </button>

    <div class="scrollbar" data-simplebar>
        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title">General</li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:widget-5-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Dashboard </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="category-list.html#sidebarProducts" data-bs-toggle="collapse"
                    role="button" aria-expanded="false" aria-controls="sidebarProducts">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:t-shirt-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Products </span>
                </a>
                <div class="collapse" id="sidebarProducts">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('products.index') }}">List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('products.show') }}">Details</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('products.edit') }}">Edit</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('products.add') }}">Create</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="category-list.html#sidebarCategory" data-bs-toggle="collapse"
                    role="button" aria-expanded="false" aria-controls="sidebarCategory">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:clipboard-list-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Category </span>
                </a>
                <div class="collapse" id="sidebarCategory">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('categories.list') }}">List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('categories.edit') }}">Edit</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('categories.add') }}">Create</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="category-list.html#sidebarInventory" data-bs-toggle="collapse"
                    role="button" aria-expanded="false" aria-controls="sidebarInventory">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:box-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Inventory </span>
                </a>
                <div class="collapse" id="sidebarInventory">
                    <ul class="nav sub-navbar-nav">

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('inventories.warehouse') }}">Warehouse</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('inventories.received-orders') }}">Received Orders</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="category-list.html#sidebarOrders" data-bs-toggle="collapse"
                    role="button" aria-expanded="false" aria-controls="sidebarOrders">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:bag-smile-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Orders </span>
                </a>
                <div class="collapse" id="sidebarOrders">
                    <ul class="nav sub-navbar-nav">

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('orders.list') }}">List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('orders.show') }}">Details</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('orders.cart') }}">Cart</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('orders.checkout') }}">Check Out</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="category-list.html#sidebarPurchases" data-bs-toggle="collapse"
                    role="button" aria-expanded="false" aria-controls="sidebarPurchases">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:card-send-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Purchases </span>
                </a>
                <div class="collapse" id="sidebarPurchases">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('purchases.list') }}">List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('purchases.order') }}">Order</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="#">Return</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="category-list.html#sidebarAttributes" data-bs-toggle="collapse"
                    role="button" aria-expanded="false" aria-controls="sidebarAttributes">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:confetti-minimalistic-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Attributes </span>
                </a>
                <div class="collapse" id="sidebarAttributes">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('attributes.list') }}">List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('attributes.edit') }}">Edit</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('attributes.add') }}">Create</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="category-list.html#sidebarInvoice" data-bs-toggle="collapse"
                    role="button" aria-expanded="false" aria-controls="sidebarInvoice">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:bill-list-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Invoices </span>
                </a>
                <div class="collapse" id="sidebarInvoice">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('invoices.list') }}">List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('invoices.show') }}">Details</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('invoices.create') }}">Create</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="settings.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:settings-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Settings </span>
                </a>
            </li>

            <li class="menu-title mt-2">Users</li>

            <li class="nav-item">
                <a class="nav-link" href="pages-profile.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:chat-square-like-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Profile </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="category-list.html#sidebarRoles" data-bs-toggle="collapse"
                    role="button" aria-expanded="false" aria-controls="sidebarRoles">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:user-speak-rounded-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Roles </span>
                </a>
                <div class="collapse" id="sidebarRoles">
                    <ul class="nav sub-navbar-nav">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="{{ route('roles.list') }}">List</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="{{ route('roles.edit') }}">Edit</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link" href="{{ route('roles.create') }}">Create</a>
                            </li>
                        </ul>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="pages-permissions.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:checklist-minimalistic-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Permissions </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="category-list.html#sidebarCustomers" data-bs-toggle="collapse"
                    role="button" aria-expanded="false" aria-controls="sidebarCustomers">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:users-group-two-rounded-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Customers </span>
                </a>
                <div class="collapse" id="sidebarCustomers">
                    <ul class="nav sub-navbar-nav">

                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('customers.list') }}">List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('customers.show') }}">Details</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="category-list.html#sidebarSellers" data-bs-toggle="collapse"
                    role="button" aria-expanded="false" aria-controls="sidebarSellers">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:shop-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Sellers </span>
                </a>
                <div class="collapse" id="sidebarSellers">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sellers.list') }}">List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sellers.show') }}">Details</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sellers.edit') }}">Edit</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('sellers.add') }}">Create</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="menu-title mt-2">Other</li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="category-list.html#sidebarCoupons" data-bs-toggle="collapse"
                    role="button" aria-expanded="false" aria-controls="sidebarCoupons">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:leaf-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Coupons </span>
                </a>
                <div class="collapse" id="sidebarCoupons">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('coupons.list') }}">List</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('coupons.add') }}">Add</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="pages-review.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:chat-square-like-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Reviews </span>
                </a>
            </li>

            <li class="menu-title mt-2">Other Apps</li>

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
                    <span class="nav-text"> Calendar </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="apps-todo.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:checklist-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Todo </span>
                </a>
            </li>

            <li class="menu-title mt-2">Support</li>

            <li class="nav-item">
                <a class="nav-link" href="help-center.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:help-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Help Center </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="pages-faqs.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:question-circle-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> FAQs </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="privacy-policy.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:document-text-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Privacy Policy </span>
                </a>
            </li>

            <li class="menu-title mt-2">Custom</li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="category-list.html#sidebarPages" data-bs-toggle="collapse"
                    role="button" aria-expanded="false" aria-controls="sidebarPages">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:gift-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Pages </span>
                </a>
                <div class="collapse" id="sidebarPages">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="pages-starter.html">Welcome</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="pages-comingsoon.html">Coming Soon</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="pages-timeline.html">Timeline</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="pages-pricing.html">Pricing</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="pages-maintenance.html">Maintenance</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="pages-404.html">404 Error</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="pages-404-alt.html">404 Error (alt)</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link menu-arrow" href="category-list.html#sidebarAuthentication" data-bs-toggle="collapse"
                    role="button" aria-expanded="false" aria-controls="sidebarAuthentication">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:lock-keyhole-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text"> Authentication </span>
                </a>
                <div class="collapse" id="sidebarAuthentication">
                    <ul class="nav sub-navbar-nav">
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="auth-signin.html">Sign In</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="auth-signup.html">Sign Up</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="auth-password.html">Reset Password</a>
                        </li>
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="auth-lock-screen.html">Lock Screen</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="widgets.html">
                    <span class="nav-icon">
                        <iconify-icon icon="solar:atom-bold-duotone"></iconify-icon>
                    </span>
                    <span class="nav-text">Widgets</span>
                    <span class="badge bg-info badge-pill text-end">9+</span>
                </a>
            </li>
        </ul>
    </div>
</div>
