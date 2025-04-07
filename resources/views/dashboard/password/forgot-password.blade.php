<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Fire Extinguisher Refilling </title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Oswald:wght@600&display=swap" rel="stylesheet">

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->



    <!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-secondary navbar-dark sticky-top py-2 px-lg-5 wow fadeIn" data-wow-delay="0.1s">
  <!-- logo -->
    <a href="index.html" class="navbar-brand ms-4 ms-lg-0">
    <h2 class="mb-0 text-primary text-uppercase">
       
        <img src="{{ asset('img/Fire_Refill_logo_with_black_and_red_colors-removebg-preview.png') }}" alt="HairCut Logo" style="height: 1.8em;">
        RefillEase
    </h1>
</a>


    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="#" class="nav-item nav-link active py-2">Home</a>
            <a href="{{route('about')}}" class="nav-item nav-link py-2">About</a>
            <a href="{{route('user.services')}}" class="nav-item nav-link py-2">Service</a>
                <a href="{{route('product')}}" class="nav-item nav-link py-2">Product</a>
                
            <a href="{{route('contact')}}" class="nav-item nav-link py-2">Contact</a>

            @guest
            <a href="{{ url('/login') }}"  class="btn btn-primary rounded-0 py-2 px-lg-2 d-none d-lg-block">Login<i class="fa fa-arrow-right ms-3"></i></a>
            @endguest

            @auth

                   <!-- Cart Icon -->
    <a href="{{route('cart.view')}}" class="nav-link py-2 px-lg-2">
        <i class="fas fa-shopping-cart"></i> 
        <span class="badge bg-danger"></span> <!-- Cart Item Count -->
    </a>
            <div class="nav-item dropdown">
                <a href="{{ route('profile') }}" class="nav-link dropdown-toggle py-2 py-2 px-lg-2" data-bs-toggle="dropdown">Profile</a>
                <div class="dropdown-menu m-0">
                    <a class="dropdown-item" href="{{ route('profile') }}" >View Profile</a>
                    <a class="dropdown-item" href="{{route('change.password.form')}}"> Change Passoword</a>
                    <form action="{{ route('logout') }}" method="POST">
                      @csrf
                      <button type="submit" class="dropdown-item" style="cursor: pointer;">Logout</button>
                    </form>
                </div>
            </div>

       
            @endauth
        </div>
    </div>
</nav>
<!-- Navbar End -->



<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card w-50 shadow-sm p-4">
        <h2 class="mb-4" style="color: #666666">Forgot Password</h2>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('admin.forgot.password') }}" method="POST">
            @csrf

            <div class="form-group mb-3">
                <label for="email">{{ __('Email Address') }}</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">{{ __('Send Reset Link') }}</button>
        </form>
    </div>
</div>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>