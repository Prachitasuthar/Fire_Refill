<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>


    <meta charset="utf-8" />
    <title>Admin-Fire Extinguisher</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">



    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('hotel/images/favicon.ico') }}">

    <!-- FontAwesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <link href="{{ asset('hotel/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('hotel/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('hotel/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
   
    <style>
        .nav-item .nav-link {
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .nav-item .nav-link:hover {
            background-color: #dfa974;
            color: #fff;
            border-radius: 5px;
        }
    </style>

</head>

<body id="body">
    <!-- Javascript  -->

    @include('admin-partials.header')

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer Section -->
    @include('admin-partials.sidebar')
    <!-- vendor js -->

    <script src="{{ asset('hotel/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('hotel/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('hotel/js/feather.min.js') }}"></script>

    <script src="{{ asset('hotel/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('hotel/js/analytics-index.init.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('hotel/js/app.js') }}"></script>

</body>
<!--end body-->

</html>
