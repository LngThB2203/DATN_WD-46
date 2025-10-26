<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>

    <!-- Larkon CSS -->
    <link rel="shortcut icon" href="{{ asset('assets/admin/images/favicon.icon')}}">

    <!-- Vendor css (Require in all Page) -->
    <link href="{{ asset('assets/admin/css/vendor.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- Icons css (Require in all Page) -->
    <link href="{{ asset('assets/admin/css/icons.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- App css (Require in all Page) -->
    <link href="{{ asset('assets/admin/css/app.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- Theme Config js (Require in all Page) -->
    <script src="{{ asset('assets/admin/js/config.js')}}"></script>


</head>

<body>
    <div class="wrapper">

        {{-- Header --}}


        {{-- Sidebar --}}
        @include('admin.partials.sidebar')

        {{-- Content --}}
        <div>
            <section >
                @include('admin.partials.header')
                @yield('content')
            </section>
            @include('admin.partials.footer')
        </div>

        {{-- Footer --}}


    </div>

    <!-- Larkon JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('assets/admin/js/vendor.js')}}"></script>

    <!-- App Javascript (Require in all Page) -->
    <script src="{{ asset('assets/admin/js/app.js')}}"></script>
</body>

</html>
