@extends('dashboard.layout.app')

@section('content')
    <!-- Carousel Start -->
    <div class="container-fluid p-0 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div id="header-carousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <!-- <img class="w-100" src="img/carousel-1.jpg" alt="Image"> -->
                    <img class="w-100" src="{{ asset('img/fire_image1.jpg') }}" style="opacity: 1 !important;"
                        alt="Fire-Refill">
                    <div class="carousel-caption d-flex align-items-center justify-content-center text-start">
                        <div class="mx-sm-5 px-5" style="max-width: 900px;">
                            <h2 class="display-2 text-white text-uppercase mb-4 animated slideInDown">Get Your Fire
                                Extinguisher Refilled Today!</h2>
                            <h4 class="text-white text-uppercase mb-4 animated slideInDown"><i
                                    class="fa fa-map-marker-alt text-primary me-3"></i>123 Street, New York, USA</h4>
                            <h4 class="text-white text-uppercase mb-4 animated slideInDown"><i
                                    class="fa fa-phone-alt text-primary me-3"></i>+012 345 67890</h4>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="w-100" src="{{ asset('img/firecarousel2.avif') }}" style="opacity: 1 !important;"
                        alt="Fire-Refill">
                    <div class="carousel-caption d-flex align-items-center justify-content-center text-start">
                        <div class="mx-sm-5 px-5" style="max-width: 900px;">
                            <h1 class="display-2 text-white text-uppercase mb-4 animated slideInDown">Your Safety, Our
                                Priority</h1>
                            <h4 class="text-white text-uppercase mb-4 animated slideInDown"><i
                                    class="fa fa-map-marker-alt text-primary me-3"></i>123 Street, New York, USA</h4>
                            <h4 class="text-white text-uppercase mb-4 animated slideInDown"><i
                                    class="fa fa-phone-alt text-primary me-3"></i>+012 345 67890</h4>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#header-carousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#header-carousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
    <!-- Carousel End -->


    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <div class="d-flex flex-column">
                        <img class="img-fluid w-85 align-self-end" src="img/firecarousel2.avif" alt="">
                        <div class="w-50 bg-secondary p-4" style="margin-top: -19%;">
                            <h1 class="text-uppercase text-primary mb-3">Fire Extinguisher</h1>
                            <h2 class="text-uppercase mb-0">Refilling</h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                    <p class="d-inline-block bg-secondary text-primary py-1 px-4">About Us</p>
                    <h1 class="text-uppercase mb-4">Your Fire Safety Experts. Learn More About Us!</h1>
                    <p>Welcome to REFILLEASE, your trusted partner in fire safety solutions. We specialize in fire
                        extinguisher refilling, maintenance, and sales of high-quality fire safety products. </p>
                    <p class="mb-4">Our mission is to ensure the safety of homes, businesses, and industries by providing
                        certified, reliable, and affordable fire protection services.</p>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h3 class="text-uppercase mb-3">Since 1990</h3>
                            <p class="mb-0">Providing trusted fire safety solutions for over three decades, ensuring
                                homes, businesses, and industries stay protected.</p>
                        </div>
                        <div class="col-md-6">
                            <h3 class="text-uppercase mb-3">1000+ clients</h3>
                            <p class="mb-0">We have successfully served thousands of clients with certified fire
                                extinguisher refilling, maintenance, and top-quality fire safety products, ensuring their
                                safety and compliance.</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('about') }}" class="btn bg-secondary text-white py-2 px-4">Learn More</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- About End -->


    <!-- Service Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <p class="d-inline-block bg-secondary text-primary py-1 px-4">Services</p>
                <h1 class="text-uppercase">What We Provide</h1>
            </div>
            <div class="row g-4">
                <div id="serviceCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="service-item position-relative overflow-hidden bg-secondary d-flex flex-column align-items-center h-200 p-5"
                                style="width: 500px; height: 550px; margin: 0 auto;">
                                <div class="bg-dark d-flex align-items-center justify-content-center mb-3 service-content"
                                    style="width: 100px; height: 100px; margin-top: 40px;">
                                    <img class="img-fluid" src="img/fire-servcie-1.webp" alt="">
                                </div>
                                <div class="text-center service-content" style="margin-top: 40px;">
                                    <h3 class="text-uppercase mb-3">Fire Extinguisher Refilling</h3>
                                    <p>Ensure your fire extinguishers are always ready with our certified refilling
                                        services. We offer quick and reliable services to make sure you are safe.</p>
                                    <span class="text-uppercase text-primary">From ₹500</span>
                                </div>
                                <a class="btn mt-auto service-link" href="{{ route('user.services') }}" target="_blank"
                                    style="background: transparent; padding: 0;">
                                    <i class="fas fa-arrow-up-right-from-square" style="color: red;"></i>

                                </a>

                            </div>
                        </div>

                        <div class="carousel-item">
                            <div class="service-item position-relative overflow-hidden bg-secondary d-flex flex-column align-items-center h-200 p-5"
                                style="width: 500px; height: 550px; margin: 0 auto;">
                                <div class="bg-dark d-flex align-items-center justify-content-center mb-3 service-content"
                                    style="width: 100px; height: 100px; margin-top: 40px;">
                                    <img class="img-fluid" src="img/fire-servcie-1.webp" alt="">
                                </div>
                                <div class="text-center service-content" style="margin-top: 40px;">
                                    <h3 class="text-uppercase mb-3">Fire Extinguisher Upgrade</h3>
                                    <p>Upgrade your existing fire extinguishers to the latest models and technology to
                                        ensure maximum safety and compliance.</p>
                                    <span class="text-uppercase text-primary">From ₹1500</span>
                                </div>
                                <a class="btn mt-auto service-link" href="{{ route('user.services') }}" target="_blank"
                                    style="background: transparent; padding: 0;">
                                    <i class="fas fa-arrow-up-right-from-square" style="color: red;"></i>

                                </a>

                            </div>
                        </div>
                        <!-- Add more carousel-items for additional services -->
                    </div>
                    <a class="carousel-control-prev" href="#serviceCarousel" role="button" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#serviceCarousel" role="button" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->

    <style>
        .service-item {
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease-out;
        }

        .service-item:hover {
            transform: scale(1.05);
        }

        .service-item .service-link {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
            font-size: 2rem;
        }

        .service-item .service-content {
            transition: filter 0.3s ease-out;
        }

        .service-item:hover .service-link {
            display: block;
        }

        .service-item.clicked .service-content {
            filter: blur(4px);
        }

        .service-item.clicked .service-link {
            display: block;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const serviceItems = document.querySelectorAll(".service-item");

            serviceItems.forEach(item => {
                item.addEventListener("click", function() {
                    serviceItems.forEach(sibling => sibling.classList.remove("clicked"));
                    this.classList.add("clicked");
                });
            });
        });
    </script>





    <!-- Team Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <p class="d-inline-block bg-secondary text-primary py-1 px-4">Our Service Providers</p>
                <h1 class="text-uppercase">Meet Our Approved Service Providers</h1>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach ($providers as $index => $provider)
                    <div class="col-lg-3 col-md-6 wow fadeInUp provider-group" data-group="{{ floor($index / 4) }}"
                        data-wow-delay="0.{{ $loop->index + 1 }}s">
                        <div class="team-item">
                            <div class="team-img position-relative overflow-hidden">
                                <img class="img-fluid"
                                    src="{{ asset($provider->profile_image ? $provider->profile_image : 'img/profile/Profile-image.png') }}"
                                    alt="{{ $provider->first_name }}"
                                    style="width: 100%; height: 320px; object-fit: cover;">


                                <div class="team-social">
                                    @if ($provider->mobile_no)
                                        <a class="btn btn-square"
                                            href="tel:+91{{ str_replace(' ', '', $provider->mobile_no) }}">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                    @endif

                                    @if ($provider->twitter)
                                        <a class="btn btn-square" href="{{ $provider->twitter }}"><i
                                                class="fab fa-twitter"></i></a>
                                    @endif
                                    @if ($provider->instagram)
                                        <a class="btn btn-square" href="{{ $provider->instagram }}"><i
                                                class="fab fa-instagram"></i></a>
                                    @endif
                                </div>
                            </div>
                            <div class="bg-secondary text-center p-4">
                                <h5 class="text-uppercase">{{ $provider->first_name }} {{ $provider->last_name }}</h5>
                                <span class="text-primary">{{ $provider->designation ?? 'Service Provider' }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if ($providers->count() > 4)
                <div class="d-flex justify-content-between mt-4">
                    <button class="btn btn-secondary" id="prev-btn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="btn btn-secondary" id="next-btn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const groups = document.querySelectorAll(".provider-group");
            const row = document.querySelector(".row.g-4");
            let currentGroup = 0;

            function showGroup(group) {
                groups.forEach(item => {
                    item.style.display = item.dataset.group == group ? "block" : "none";
                });
                // Center providers if 1 to 3 are displayed
                const visibleGroups = [...groups].filter(item => item.style.display === "block");
                row.classList.add("justify-content-center");
            }

            document.getElementById("prev-btn").addEventListener("click", function() {
                if (currentGroup > 0) {
                    currentGroup--;
                    showGroup(currentGroup);
                }
            });

            document.getElementById("next-btn").addEventListener("click", function() {
                if (currentGroup < Math.floor(groups.length / 4)) {
                    currentGroup++;
                    showGroup(currentGroup);
                }
            });

            showGroup(currentGroup);
        });
    </script>


    <!-- Team End -->


    <!-- Working Hours Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-0">
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                    <div class="h-100">
                        <img class="img-fluid h-100" src="img/fire.png" alt="">
                    </div>
                </div>
                <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                    <div class="bg-secondary h-100 d-flex flex-column justify-content-center p-5">
                        <p class="d-inline-flex bg-dark text-primary py-1 px-4 me-auto">Working Hours</p>
                        <h1 class="text-uppercase mb-4">YOUR FIRE SAFETY IS OUR PRIORITY</h1>
                        <div>
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <h6 class="text-uppercase mb-0">Monday</h6>
                                <span class="text-uppercase">09 AM - 09 PM</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <h6 class="text-uppercase mb-0">Tuesday</h6>
                                <span class="text-uppercase">09 AM - 09 PM</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <h6 class="text-uppercase mb-0">Wednesday</h6>
                                <span class="text-uppercase">09 AM - 09 PM</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <h6 class="text-uppercase mb-0">Thursday</h6>
                                <span class="text-uppercase">09 AM - 09 PM</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <h6 class="text-uppercase mb-0">Friday</h6>
                                <span class="text-uppercase">09 AM - 09 PM</span>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <h6 class="text-uppercase mb-0">Sat / Sun</h6>
                                <span class="text-uppercase text-primary">Closed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Working Hours End -->


    <!-- Testimonial Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                <p class="d-inline-block bg-secondary text-primary py-1 px-4">Testimonial</p>
                <h1 class="text-uppercase">What Our Clients Say!</h1>
            </div>
            <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.1s">
                <div class="testimonial-item text-center"
                    data-dot="<img class='img-fluid' src='img/testimonial-1.jpg' alt=''>">
                    <h4 class="text-uppercase">Client Name</h4>
                    <p class="text-primary">Profession</p>
                    <span class="fs-5">Clita clita tempor justo dolor ipsum amet kasd amet duo justo duo duo labore sed
                        sed. Magna ut diam sit et amet stet eos sed clita erat magna elitr erat sit sit erat at rebum justo
                        sea clita.</span>
                </div>
                <div class="testimonial-item text-center"
                    data-dot="<img class='img-fluid' src='img/testimonial-2.jpg' alt=''>">
                    <h4 class="text-uppercase">Client Name</h4>
                    <p class="text-primary">Profession</p>
                    <span class="fs-5">Clita clita tempor justo dolor ipsum amet kasd amet duo justo duo duo labore sed
                        sed. Magna ut diam sit et amet stet eos sed clita erat magna elitr erat sit sit erat at rebum justo
                        sea clita.</span>
                </div>
                <div class="testimonial-item text-center"
                    data-dot="<img class='img-fluid' src='img/testimonial-3.jpg' alt=''>">
                    <h4 class="text-uppercase">Client Name</h4>
                    <p class="text-primary">Profession</p>
                    <span class="fs-5">Clita clita tempor justo dolor ipsum amet kasd amet duo justo duo duo labore sed
                        sed. Magna ut diam sit et amet stet eos sed clita erat magna elitr erat sit sit erat at rebum justo
                        sea clita.</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->
@endsection
