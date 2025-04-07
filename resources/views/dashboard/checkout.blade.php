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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Oswald:wght@600&display=swap"
        rel="stylesheet">

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

    <style>
        body {
            background-color: #000;
            /* Dark Theme */
            color: #fff;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
        }

        .form-control {
            background: #000;
            color: #fff;
            border: 1px solid #555;
            padding: 10px;
        }

        .form-control::placeholder {
            color: #bbb;
        }

        .form-label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .btn-primary {
            background-color: red;
            border: none;
            padding: 12px 20px;
            font-size: 18px;
            text-transform: uppercase;
        }

        .btn-primary:hover {
            background-color: darkred;
        }
    </style>
</head>

<body>




    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-secondary navbar-dark sticky-top py-2 px-lg-5 wow fadeIn"
        data-wow-delay="0.1s">
        <!-- logo -->
        <a href="index.html" class="navbar-brand ms-4 ms-lg-0">
            <h2 class="mb-0 text-primary text-uppercase">

                <img src="{{ asset('img/Fire_Refill_logo_with_black_and_red_colors-removebg-preview.png') }}"
                    alt="HairCut Logo" style="height: 1.8em;">
                RefillEase
                </h1>
        </a>


        <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="{{ route('dashboard.index') }}" class="nav-item nav-link py-2">Home</a>
                <a href="{{ route('about') }}" class="nav-item nav-link py-2">About</a>
                <a href="{{ route('user.services') }}" class="nav-item nav-link py-2">Service</a>
                {{-- <div class="nav-item dropdown"> --}}
                <a href="{{ route('product') }}" class="nav-item nav-link py-2">Product</a>

                <a href="{{ route('contact') }}" class="nav-item nav-link py-2">Contact</a>

                @guest
                    <a href="{{ url('/login') }}" class="btn btn-primary rounded-0 py-2 px-lg-2 d-none d-lg-block">Login<i
                            class="fa fa-arrow-right ms-3"></i></a>
                @endguest

                @auth
                <!-- Cart Icon -->
                <a href="{{ route('cart.view') }}"
                    class="nav-link py-2 {{ request()->routeIs('cart.view') ? 'active' : '' }} px-lg-2">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge bg-danger"></span> <!-- Cart Item Count -->
                </a>
                <div class="nav-item dropdown">
                    <a href="{{ route('profile') }}"
                        class="nav-link dropdown-toggle {{ request()->routeIs('profile') ? 'active' : '' }} py-2 px-lg-2"
                        data-bs-toggle="dropdown">Profile</a>
                    <div class="dropdown-menu m-0">
                        <a class="dropdown-item" href="{{ route('profile') }}">View Profile</a>
                        <a class="dropdown-item" href="{{ route('order.history') }}">Your Order</a>
                        <a class="dropdown-item" href="{{ route('change.password.form') }}"> Change Passoword</a>
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


    <!-- Contact Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn text-center" data-wow-delay="0.1s">
        <div class="container py-5">
            <h1 class="display-3 text-white text-uppercase fw-bold animated slideInDown"
                style="letter-spacing: 2px; text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);">
                Proceed to Checkout
            </h1>
            <p class="text-white lead animated fadeInUp" data-wow-delay="0.3s">
                We’d love to hear from you! Reach out to us anytime.
            </p>
        </div>
    </div>
    <!-- Contact Page Header End -->





    <div class="container">
        {{-- <h2>Checkout</h2> --}}
        @if (isset($cartItems) && $cartItems->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Final Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartItems as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>${{ number_format($item->final_price ?? $item->price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <h4>Grand Total: ${{ number_format($grandTotal, 2) }}</h4>
        @else
            <p class="alert alert-warning">Your cart is empty.</p>
        @endif

        {{-- FORM --}}

        <div class="container my-5">
            <h2 style="text-align: center; margin-bottom: 10px;">Place Your Order</h2>

            <form action="{{ route('checkout.order.store') }}" method="POST" autocomplete="off">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter your full name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mobile No</label>
                        <input type="number" name="mobile" class="form-control"
                            placeholder="Enter your mobile no.">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-control"
                            style="background:#000; color:#fff; border:1px solid #555; padding:10px;">
                            <option value="" disabled selected>-- Select Payment Method --</option>
                            <option value="cod">Cash on Delivery</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address Line 1</label>
                        <input type="text" name="address_line1" class="form-control" placeholder="e.g: plot no.">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address Line 2</label>
                        <input type="text" name="address_line2" class="form-control" placeholder="e.g: street">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" placeholder="Enter your city">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" placeholder="Enter your state">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" placeholder="Enter your country">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pin Code</label>
                        <input type="number" name="pincode" class="form-control" placeholder="Enter your pin code">
                    </div>
                </div>


                <div>
                    <button type="submit" id="normal_button" class="btn btn-lg btn-primary">
                        Proceed to Payment
                    </button>
                </div>


            </form>

        </div>




    </div>

    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            function validateField(field, errorMessage, condition) {
                if (condition) {
                    field.next('.error-message').remove();
                    field.after(`<small class="error-message text-danger">${errorMessage}</small>`);
                    field.addClass("is-invalid");
                } else {
                    field.next('.error-message').remove();
                    field.removeClass("is-invalid");
                }
            }

            $("input, select").on("input change", function() {
                let field = $(this);
                let value = field.val().trim();
                let name = field.attr("name");

                switch (name) {
                    case "name":
                        validateField(field, "Full Name is required (Min 3 characters)", value.length < 3);
                        break;
                    case "mobile":
                        validateField(field, "Mobile No must be of 10 digits", !/^\d{10,15}$/.test(value));
                        break;
                    case "email":
                        validateField(field, "Enter a valid email", !/^\S+@\S+\.\S+$/.test(value));
                        break;
                    case "payment_method":
                        validateField(field, "Select a Payment Method", value === "");
                        break;
                    case "address_line1":
                        validateField(field, "Address Line 1 is required", value.length < 5);
                        break;
                    case "city":
                        validateField(field, "City is required", value === "");
                        break;
                    case "state":
                        validateField(field, "State is required", value === "");
                        break;
                    case "country":
                        validateField(field, "Country is required", value === "");
                        break;
                    case "pincode":
                        validateField(field, "Pincode must be 4-10 digits", !/^\d{4,10}$/.test(value));
                        break;
                }
            });

            $("form").on("submit", function(e) {
                let hasErrors = $(".is-invalid").length > 0;
                if (hasErrors) {
                    e.preventDefault();
                    alert("Please fix the errors before submitting.");
                }
            });
        });
    </script>
