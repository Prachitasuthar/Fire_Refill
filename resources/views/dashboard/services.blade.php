<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Fire Extinguisher Refilling </title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Oswald:wght@600&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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



    <!-- Navbar Start -->
    <nav class="navbar navbar-expand-lg bg-secondary navbar-dark sticky-top py-2 px-lg-5 wow fadeIn"
        data-wow-delay="0.1s">
        <!-- logo -->
        <a href="index.html" class="navbar-brand ms-4 ms-lg-0">
            <h2 class="mb-0 text-primary text-uppercase">

                <img src="{{ asset('img/Fire_Refill_logo_with_black_and_red_colors-removebg-preview.png') }}"
                    alt="FireRefill Logo" style="height: 1.8em;">
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
                <a href="{{ route('user.services') }}" class="nav-item nav-link active py-2">Service</a>
                <a href="{{ route('product') }}" class="nav-item nav-link py-2">Product</a>
                <a href="{{ route('contact') }}" class="nav-item nav-link py-2">Contact</a>

                @guest
                    <a href="{{ url('/login') }}" class="btn btn-primary rounded-0 py-2 px-lg-2 d-none d-lg-block">Login<i
                            class="fa fa-arrow-right ms-3"></i></a>
                @endguest

                @auth

                    <!-- Cart Icon -->
                    <a href="{{ route('cart.view') }}" class="nav-link py-2 px-lg-2">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="badge bg-danger"></span> <!-- Cart Item Count -->
                    </a>
                    <div class="nav-item dropdown">
                        <a href="{{ route('profile') }}" class="nav-link dropdown-toggle py-2 py-2 px-lg-2"
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

    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-3 text-white text-uppercase mb-3 animated slideInDown fw-bold">Our Services</h1>
            <p class="text-white lead animated fadeInUp" style="max-width: 600px; margin: auto;">
                Providing top-notch fire safety solutions, from refilling to advanced protection systems.
            </p>
        </div>
    </div>
    <!-- Page Header End -->

    <!-- Services Section Start -->
    <div class="container-xxl py-5 bg-dark text-light">
        <div class="container">
            <div class="row g-5">
                <!-- Services Section -->
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <h1 class="text-uppercase mb-4 text-danger">Our Services</h1>

                    <div class="card mb-4 bg-dark text-light shadow-lg border-0"
                        style="transition: transform 0.3s; border-radius: 15px;">
                        <div class="row g-0">
                            <div class="col-md-6 d-flex align-items-center justify-content-center">
                                <img src="{{ asset('img/fire_image1.jpg') }}" class="img-fluid rounded"
                                    alt="Fire Refilling" style="max-height: 250px; width: 100%;"
                                    onmouseover="this.parentElement.parentElement.style.transform='scale(1.05)'"
                                    onmouseout="this.parentElement.parentElement.style.transform='scale(1)'">
                            </div>
                            <div class="col-md-6 d-flex flex-column justify-content-center">
                                <div class="card-body">
                                    <h5 class="card-title text-danger">Fire Extinguisher Refilling</h5>
                                    <p class="card-text">We provide professional fire extinguisher refilling services
                                        to keep your safety equipment in top condition. Our team ensures timely and
                                        efficient service, maintaining the highest safety standards. Contact us today
                                        for reliable and thorough refilling. Your safety is our priority!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4 bg-dark text-light shadow-lg border-0"
                        style="transition: transform 0.3s; border-radius: 15px;">
                        <div class="row g-0">
                            <div class="col-md-6 d-flex align-items-center justify-content-center">
                                <img src="{{ asset('img/fire-upgrade-refilling.png') }}" class="img-fluid rounded"
                                    alt="Fire Safety Upgrade" style="max-height: 190px; width: 100%;"
                                    onmouseover="this.parentElement.parentElement.style.transform='scale(1.05)'"
                                    onmouseout="this.parentElement.parentElement.style.transform='scale(1)'">
                            </div>
                            <div class="col-md-6 d-flex flex-column justify-content-center">
                                <div class="card-body">
                                    <h5 class="card-title text-danger">Fire Safety Upgrade</h5>
                                    <p class="card-text">Upgrade your fire safety system with advanced protection
                                        measures for homes and businesses. We offer the latest technology and
                                        comprehensive solutions tailored to your specific needs. Ensure your safety with
                                        our expert upgrades. We're here to protect you and your property!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Inquiry Form Section -->
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                    <h2 class="text-uppercase mb-4 text-danger">Request For Service</h2>
                    <form method="POST" action="{{ route('service-requests.store') }}" id="requestForm" autocomplete="off">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control bg-dark text-light" id="name"
                                name="name" placeholder="Your Name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control bg-dark text-light" id="email"
                                name="email" placeholder="Your Email">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control bg-dark text-light" name="address"
                                id="address" placeholder="Your Address">
                        </div>
                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact Number</label>
                            <input type="text" class="form-control bg-dark text-light" id="contact"
                                name="contact" placeholder="Your Contact Number">
                        </div>
                        <div class="mb-3">
                            <label for="service" class="form-label">Select Service</label>
                            <select class="form-select bg-dark text-light" name="service_id" id="service">
                                <option selected disabled>Select a Service</option>
                            </select>
                        </div>

                        <div class="mb-3" id="product-field" style="display: none;">
                            <label for="product" class="form-label">Select Sub-Service</label>
                            <select class="form-select bg-dark text-light" id="product" name="sub_service_id">
                                <option selected disabled>Select a Sub-Service</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="provider" class="form-label">Select Provider</label>
                            <select class="form-select bg-dark text-light" id="provider" name="provider_id">
                                <option selected disabled>Select a Provider</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 text-uppercase">Send Request</button>
                    </form>
                </div>

                <script>
                    $(document).ready(function() {

                        $('#service').change(function() {
                            let service_id = $(this).val();
                            $('#sub_service').empty().append('<option selected disabled>Select a Sub-Service</option>');
                            $('#provider').empty().append('<option selected disabled>Select a Provider</option>');
                            $.get(`/get-sub-services/${service_id}`, function(data) {
                                $('#subServiceField').show();
                                $('#sub_service').append(data.map(sub =>
                                    `<option value="${sub.id}">${sub.sub_service_name}</option>`));
                            });
                        });

                        // ✅ Load Providers on Sub-Service Selection
                        $('#sub_service').change(function() {
                            let sub_service_id = $(this).val();
                            $('#provider').empty().append('<option selected disabled>Select a Provider</option>');
                            $.get(`/get-providers/${sub_service_id}`, function(data) {
                                $('#provider').append(data.map(provider =>
                                    `<option value="${provider.id}">${provider.name}</option>`));
                            });
                        });

                        // ✅ Submit Form via AJAX
                        $("#requestForm").submit(function(e) {
                            e.preventDefault();
                            let formData = new FormData(this);

                            $.ajax({
                                url: "{{ route('service-requests.store') }}",
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                                },

                                success: function(response) {
                                    $('#loader').fadeOut(); // Hide loader

                                    Swal.fire({
                                        title: 'Great!',
                                        text: 'Your request has been successfully submitted!',
                                        icon: 'success',
                                        background: '#fff', // White background
                                        color: '#333', // Dark text
                                        timer: 3000,
                                        timerProgressBar: true,
                                        showConfirmButton: false,
                                        position: 'top-end', // Show at top-right
                                        toast: true, // Smaller size
                                        showClass: {
                                            popup: 'animate__animated animate__fadeInDown'
                                        },
                                        hideClass: {
                                            popup: 'animate__animated animate__fadeOutUp'
                                        }
                                    });


                                    $("#requestForm")[0].reset(); // Reset form
                                },
                                error: function(xhr) {


                                    if (xhr.status === 401) {

                                        Swal.fire({
                                            title: 'Login Required!',
                                            text: 'Please login first before submitting the request.',
                                            icon: 'warning',
                                            background: '#fff', // White background
                                            color: '#333', // Dark text
                                            // timer: 3000,
                                            timerProgressBar: true,
                                            showConfirmButton: true, // Show confirm button for user action
                                            position: 'top-end', // Show at top-right
                                            toast: true, // Compact alert
                                            showClass: {
                                                popup: 'animate__animated animate__fadeInDown'
                                            },
                                            hideClass: {
                                                popup: 'animate__animated animate__fadeOutUp'
                                            }
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href =
                                                "{{ route('login') }}"; // Redirect to login page
                                            }
                                        });

                                    } else {
                                        // General Error Message
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error!',
                                            text: 'Error submitting request. Please try again.',
                                        });
                                    }
                                }
                            });
                        });
                    });


                    document.addEventListener("DOMContentLoaded", function() {
                        let serviceSelect = document.getElementById("service");
                        let subServiceSelect = document.getElementById("product");
                        let providerSelect = document.getElementById("provider");
                        let productField = document.getElementById("product-field");

                        // ✅ Fetch Services on Page Load (Vanilla JS)
                        fetch("{{ route('get.services') }}")
                            .then(response => response.json())
                            .then(data => {
                                serviceSelect.innerHTML = '<option selected disabled>Select a Service</option>';
                                data.forEach(service => {
                                    serviceSelect.innerHTML +=
                                        `<option value="${service.service_name}">${service.service_name}</option>`;
                                });
                            })
                            .catch(error => console.error("Error fetching services:", error));

                        // ✅ Event Listener: When Service is Selected
                        serviceSelect.addEventListener("change", function() {
                            let selectedService = this.value;
                            fetchSubServices(selectedService);
                            fetchProviders(selectedService);
                        });

                        // ✅ Event Listener: When Sub-Service is Selected
                        subServiceSelect.addEventListener("change", function() {
                            let selectedSubService = this.value;
                            fetchProviders(selectedSubService);
                        });

                        // ✅ Function to Fetch Sub-Services
                        function fetchSubServices(serviceName) {
                            fetch(`/get-sub-services/${encodeURIComponent(serviceName)}`)
                                .then(response => response.json())
                                .then(data => {
                                    subServiceSelect.innerHTML = '<option selected disabled>Select a Sub-Service</option>';

                                    if (data.length > 0) {
                                        productField.style.display = "block"; // Show sub-service field
                                        data.forEach(subService => {
                                            subServiceSelect.innerHTML +=
                                                `<option value="${subService.sub_service_name}">${subService.sub_service_name}</option>`;
                                        });
                                    } else {
                                        productField.style.display = "none"; // Hide if no sub-services
                                    }
                                })
                                .catch(error => console.error("Error fetching sub-services:", error));
                        }

                        // ✅ Function to Fetch Providers
                        function fetchProviders(serviceOrSubService) {
                            fetch(`/get-providers/${encodeURIComponent(serviceOrSubService)}`)
                                .then(response => response.json())
                                .then(data => {
                                    providerSelect.innerHTML = '<option selected disabled>Select a Provider</option>';

                                    if (data.length > 0) {
                                        data.forEach(provider => {
                                            providerSelect.innerHTML +=
                                                `<option value="${provider.id}">${provider.name}</option>`;
                                        });
                                    } else {
                                        providerSelect.innerHTML += `<option disabled>No Providers Available</option>`;
                                    }
                                })
                                .catch(error => console.error("Error fetching providers:", error));
                        }
                    });

                    document.addEventListener("DOMContentLoaded", function() {
                        const form = document.getElementById("requestForm");

                        function showError(input, message) {
                            clearError(input);
                            const errorDiv = document.createElement("div");
                            errorDiv.className = "text-danger small mt-1";
                            errorDiv.innerText = message;
                            input.classList.add("is-invalid");
                            input.parentElement.appendChild(errorDiv);
                        }

                        function clearError(input) {
                            input.classList.remove("is-invalid");
                            const existingError = input.parentElement.querySelector(".text-danger");
                            if (existingError) {
                                existingError.remove();
                            }
                        }

                        function validateName() {
                            const nameInput = document.getElementById("name");
                            if (nameInput.value.trim() === "") {
                                showError(nameInput, "Name is required.");
                                return false;
                            } else {
                                clearError(nameInput);
                                return true;
                            }
                        }

                        function validateEmail() {
                            const emailInput = document.getElementById("email");
                            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                            if (emailInput.value.trim() === "") {
                                showError(emailInput, "Email is required.");
                                return false;
                            } else if (!emailPattern.test(emailInput.value)) {
                                showError(emailInput, "Enter a valid email address.");
                                return false;
                            } else {
                                clearError(emailInput);
                                return true;
                            }
                        }

                        function validateAddress() {
                            const addressInput = document.getElementById("address");
                            if (addressInput.value.trim() === "") {
                                showError(addressInput, "Address is required.");
                                return false;
                            } else {
                                clearError(addressInput);
                                return true;
                            }
                        }

                        function validateContact() {
                            const contactInput = document.getElementById("contact");
                            const contactPattern = /^[0-9]{10,15}$/;
                            if (contactInput.value.trim() === "") {
                                showError(contactInput, "Contact number is required.");
                                return false;
                            } else if (!contactPattern.test(contactInput.value)) {
                                showError(contactInput, "Enter a valid contact number.");
                                return false;
                            } else {
                                clearError(contactInput);
                                return true;
                            }
                        }

                        function validateService() {
                            const serviceInput = document.getElementById("service");
                            if (serviceInput.value === "" || serviceInput.value === "Select a Service") {
                                showError(serviceInput, "Please select a service.");
                                return false;
                            } else {
                                clearError(serviceInput);
                                return true;
                            }
                        }

                        function validateProvider() {
                            const providerInput = document.getElementById("provider");
                            if (providerInput.value === "" || providerInput.value === "Select a Provider") {
                                showError(providerInput, "Please select a provider.");
                                return false;
                            } else {
                                clearError(providerInput);
                                return true;
                            }
                        }

                        // Real-time validation
                        document.getElementById("name").addEventListener("keyup", validateName);
                        document.getElementById("email").addEventListener("keyup", validateEmail);
                        document.getElementById("address").addEventListener("keyup", validateAddress);
                        document.getElementById("contact").addEventListener("keyup", validateContact);
                        document.getElementById("service").addEventListener("change", validateService);
                        document.getElementById("provider").addEventListener("change", validateProvider);

                        // Form submit validation
                        form.addEventListener("submit", function(event) {
                            let isValid =
                                validateName() &
                                validateEmail() &
                                validateAddress() &
                                validateContact() &
                                validateService() &
                                validateProvider();

                            if (!isValid) {
                                event.preventDefault();
                            }
                        });
                    });
                </script>








                <style>
                    .input-placeholder::placeholder {
                        color: #6C7293 !important
                    }


                    .input-placeholder:focus,
                    .input-placeholder:active {
                        color: white !important;
                        background-color: black !important;
                        border-color: #dc3545 !important;
                    }

                    .form-select:focus,
                    .form-select:active,
                    .form-select option {
                        color: white !important;
                        background-color: black !important;
                    }

                    .input-placeholder:-webkit-autofill,
                    .input-placeholder:-webkit-autofill:hover,
                    .input-placeholder:-webkit-autofill:focus {
                        background-color: black !important;
                        color: white !important;
                        -webkit-box-shadow: 0 0 0px 1000px black inset !important;
                        transition: background-color 5000s ease-in-out 0s;
                        /* Prevent autofill override */
                    }
                </style>





                <!-- Footer Start -->
                <div class="container-fluid bg-secondary text-light footer mt-5 pt-5 wow fadeIn"
                    data-wow-delay="0.1s">
                    <div class="container py-5">
                        <div class="row g-5">
                            <div class="col-lg-4 col-md-6">
                                <h4 class="text-uppercase mb-4">Get In Touch</h4>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="btn-square bg-dark flex-shrink-0 me-3">
                                        <span class="fa fa-map-marker-alt text-primary"></span>
                                    </div>
                                    <span>123 Street, New York, USA</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="btn-square bg-dark flex-shrink-0 me-3">
                                        <span class="fa fa-phone-alt text-primary"></span>
                                    </div>
                                    <span>+012 345 67890</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="btn-square bg-dark flex-shrink-0 me-3">
                                        <span class="fa fa-envelope-open text-primary"></span>
                                    </div>
                                    <span>info@example.com</span>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <h4 class="text-uppercase mb-4">Quick Links</h4>
                                <a class="btn btn-link" href="">About Us</a>
                                <a class="btn btn-link" href="">Contact Us</a>
                                <a class="btn btn-link" href="">Our Services</a>
                                <a class="btn btn-link" href="">Terms & Condition</a>
                                <a class="btn btn-link" href="">Support</a>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <h4 class="text-uppercase mb-4">Our Motto</h4>
                                <p class="mb-0">"Protecting lives and assets with passion and precision."</p>
                                
                                <div class="d-flex pt-1 m-n1">
                                    <a class="btn btn-lg-square btn-dark text-primary m-1" href=""><i
                                            class="fab fa-twitter"></i></a>
                                    <a class="btn btn-lg-square btn-dark text-primary m-1" href=""><i
                                            class="fab fa-facebook-f"></i></a>
                                    <a class="btn btn-lg-square btn-dark text-primary m-1" href=""><i
                                            class="fab fa-youtube"></i></a>
                                    <a class="btn btn-lg-square btn-dark text-primary m-1" href=""><i
                                            class="fab fa-linkedin-in"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="copyright">
                            <div class="row">
                                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                                    &copy; <a class="border-bottom" href="#">Your Site Name</a>, All Right
                                    Reserved.
                                </div>
                                <div class="col-md-6 text-center text-md-end">
                                    <!--/*** This template is free as long as you keep the footer author’s credit link/attribution link/backlink. If you'd like to use the template without the footer author’s credit link/attribution link/backlink, you can purchase the Credit Removal License from "https://htmlcodex.com/credit-removal". Thank you for your support. ***/-->
                                    Designed By <a class="border-bottom" href="https://htmlcodex.com">HTML Codex</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Footer End -->


                <!-- Back to Top -->
                <a href="#" class="btn btn-primary btn-lg-square back-to-top"><i
                        class="bi bi-arrow-up"></i></a>



                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
                <script src="lib/wow/wow.min.js"></script>
                <script src="lib/easing/easing.min.js"></script>
                <script src="lib/waypoints/waypoints.min.js"></script>
                <script src="lib/owlcarousel/owl.carousel.min.js"></script>
                <!-- jQuery version of SweetAlert -->
                <!-- SweetAlert2 CSS -->
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

                <!-- SweetAlert2 JS -->
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



                <!-- Template Javascript -->
                <script src="js/main.js"></script>
</body>

</html>
