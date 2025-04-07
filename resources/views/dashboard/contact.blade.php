@extends('dashboard.layout.app')

@section('content')
    <!-- Contact Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn text-center" data-wow-delay="0.1s">
        <div class="container py-5">
            <h1 class="display-3 text-white text-uppercase fw-bold animated slideInDown"
                style="letter-spacing: 2px; text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);">
                Get in Touch
            </h1>
            <p class="text-white lead animated fadeInUp" data-wow-delay="0.3s">
                We’d love to hear from you! Reach out to us anytime.
            </p>
        </div>
    </div>
    <!-- Contact Page Header End -->



    <!-- Contact Start -->
    @if (session('success'))
        <div id="successToast" class="toast align-items-center text-bg-success border-0 position-fixed bottom-0 end-0 m-3"
            role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('success') }}
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div id="errorToast" class="toast align-items-center text-bg-danger border-0 position-fixed bottom-0 end-0 m-3"
            role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('error') }}
                </div>
            </div>
        </div>
    @endif



    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-0">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <div class="bg-secondary p-5">
                        <p class="d-inline-block bg-dark text-primary py-1 px-4">Contact Us</p>
                        <h1 class="text-uppercase mb-4">Have Any Query? Please Contact Us!</h1>

                        <!-- Contact Form Start -->
                        <form id="contactForm" method="POST" action="{{ route('messages.store') }}" autocomplete="off">
                            @csrf <!-- CSRF Protection -->

                            <!-- User ID (Hidden Field) -->
                            @auth
                                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            @endauth

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control bg-transparent" id="name"
                                            name="name" placeholder="Your Name">
                                        <label for="name">Your Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control bg-transparent" id="email"
                                            name="email" placeholder="Your Email">
                                        <label for="email">Your Email</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control bg-transparent" id="subject"
                                            name="subject" placeholder="Subject">
                                        <label for="subject">Subject</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control bg-transparent" placeholder="Leave a message here" id="message" name="message"
                                            style="height: 100px"></textarea>
                                        <label for="message">Message</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3" type="submit">Send Message</button>
                                </div>
                            </div>
                        </form>
                        <!-- Contact Form End -->
                    </div>
                </div>

                <!-- Map Section -->
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                    <div class="h-100" style="min-height: 400px;">
                        <img src="{{ asset('img/contact.png') }}" 
                             alt="Contact Image" 
                             class="w-100 h-100" 
                             style="object-fit: cover; border: 0;" />
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <!-- Contact End -->
    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // var successToast = document.getElementById("successToast");
            // var errorToast = document.getElementById("errorToast");

            // if (successToast) {
            //     var toast = new bootstrap.Toast(successToast);
            //     toast.show();
            //     setTimeout(() => {
            //         successToast.remove();
            //     }, 4000);
            // }

            // if (errorToast) {
            //     var toast = new bootstrap.Toast(errorToast);
            //     toast.show();
            //     setTimeout(() => {
            //         errorToast.remove();
            //     }, 4000);
            // }

            $("#contactForm").submit(function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('messages.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "🎉 Great!",
                            text: "Message sent successfully!",
                            icon: "success",
                            timer: 2500,
                            showConfirmButton: false,
                            toast: true,
                            position: "top-end",
                            background: "#f0f9ff",
                            color: "#0c5460",
                            iconColor: "#28a745",
                            customClass: {
                                popup: 'swal2-popup-custom',
                            },
                            showClass: {
                                popup: 'animate__animated animate__fadeInDown'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOutUp'
                            }
                        });
                        $("#contactForm")[0].reset();

                        var toastDiv = document.createElement("div");
                        toastDiv.classList.add("toast", "align-items-center", "text-bg-success",
                            "border-0", "position-fixed", "bottom-0", "end-0", "m-3");
                        toastDiv.setAttribute("role", "alert");
                        toastDiv.setAttribute("aria-live", "assertive");
                        toastDiv.setAttribute("aria-atomic", "true");
                        toastDiv.innerHTML =
                            `<div class="d-flex"><div class="toast-body">${response.message}</div></div>`;

                        document.body.appendChild(toastDiv);
                        var toast = new bootstrap.Toast(toastDiv);
                        toast.show();

                        setTimeout(() => {
                            toastDiv.remove();
                        }, 4000);
                    },
                    error: function(response) {
                        if (response.status === 403) {
                            Swal.fire({
                                icon: "warning",
                                title: "Login Required",
                                text: "Only logged-in users can send messages!",
                                confirmButtonText: "Login Now"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href =
                                    "{{ route('login') }}"; // Redirect to login page
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Oops!",
                                text: "Error submitting message.",
                                showConfirmButton: true
                            });
                        }
                    },
                });
            });
        });

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

            $("#contactForm input, #contactForm textarea").on("input", function() {
                let field = $(this);
                let value = field.val().trim();
                let name = field.attr("name");

                switch (name) {
                    case "name":
                        validateField(field, "Name must be at least 3 characters", value.length < 3);
                        break;
                    case "email":
                        validateField(field, "Enter a valid email", !/^\S+@\S+\.\S+$/.test(value));
                        break;
                    case "subject":
                        validateField(field, "Subject must be at least 5 characters", value.length < 5);
                        break;
                    case "message":
                        validateField(field, "Message must be at least 10 characters", value.length < 10);
                        break;
                }
            });
            // $("#contactForm").on("submit", function(e) {
            //     let hasErrors = $(".is-invalid").length > 0;
            //     if (hasErrors) {
            //         e.preventDefault();
            //         Swal.fire({
            //             icon: 'error',
            //             title: 'Form Validation Failed!',
            //             text: 'Please fix the errors before submitting.',
            //             confirmButtonColor: '#d33'
            //         });
            //     }
            // });
        });
    </script>
@endsection
