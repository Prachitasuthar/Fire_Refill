@extends('dashboard.layout.app')

@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-3 text-white text-uppercase mb-3 animated slideInDown fw-bold">About Us</h1>
            <p class="text-white lead animated fadeInUp" style="max-width: 600px; margin: auto;">
                Discover our journey, values, and commitment to excellence in fire safety and protection.
            </p>
        </div>
    </div>

    <!-- Page Header End -->


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
                    <h1 class="text-uppercase mb-4">Your Fire Safety Experts</h1>
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
                </div>

            </div>
        </div>
    </div>
    <!-- About End -->

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
                                        <a class="btn btn-square" href="tel:{{ $provider->mobile_no }}">
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
@endsection
