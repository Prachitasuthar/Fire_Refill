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
        .card img {
            transition: transform 0.3s ease-in-out;
        }

        .card:hover img {
            transform: scale(1.05);
        }


        /* Category Dropdown Styling */
        .category-container {
            display: flex;
            justify-content: flex-start;
            /* Move dropdown to the right */
            margin-bottom: 20px;
        }

        .category-dropdown {
            padding: 10px;
            font-size: 1rem;
            border: 2px solid #333;
            border-radius: 8px;
            background-color: #f8f9fa;
            color: #333;
            transition: all 0.3s ease;
            cursor: pointer;
            width: 250px;
            /* Fixed Width */
        }

        .category-dropdown:hover {
            background-color: #e9ecef;
            border-color: #555;
        }

        .modal-content {
            border-radius: 10px;
            overflow: hidden;
        }

        .modal-header {
            border-bottom: 2px solid #007bff;
        }

        .modal-body img {
            border: 1px solid #ddd;
            padding: 5px;
            background: #fff;
        }

        .table {
            margin-top: 10px;
        }


        #productImage:hover {
            transform: scale(1.08);
            filter: drop-shadow(0px 0px 10px rgba(0, 0, 0, 0.2));
        }

        /* Gradient Button */
        .btn-gradient {
            background: linear-gradient(135deg, #FF6F00, #FFA000);
            color: white;
            border: none;
            transition: 0.3s;
        }

        .btn-gradient:hover {
            background: linear-gradient(135deg, #FFA000, #FF6F00);
            transform: scale(1.05);
        }

        /* Updated Warranty Badge */
        .warranty-badge {
            background: linear-gradient(135deg, #00C9FF, #92FE9D);
            /* Bright Cyan to Green */
            color: white;
            font-weight: bold;
            box-shadow: 0px 0px 5px rgba(0, 201, 255, 0.5);
            transition: all 0.3s ease-in-out;
        }

        .warranty-badge:hover {
            transform: scale(1.1);
            box-shadow: 0px 0px 10px rgba(0, 201, 255, 0.8);
        }

        /* Responsive text sizing */
        @media (max-width: 576px) {
            .modal-title {
                font-size: 1.2rem;
            }

            .btn {
                font-size: 0.9rem;
            }
        }
    </style>

</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner"
        class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->

    @include('dashboard.partials.header')

    @yield('content')


    @include('dashboard.partials.footer')
    <!-- Back to Top -->
    <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


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
