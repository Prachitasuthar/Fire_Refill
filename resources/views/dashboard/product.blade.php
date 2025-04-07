@extends('dashboard.layout.app')

@section('content')
    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-3 text-white text-uppercase mb-3 animated slideInDown">Our Products</h1>
            <p class="text-white fs-5 animated fadeInUp">
                Explore our wide range of high-quality fire safety products.
            </p>
        </div>
    </div>
    <!-- Page Header End -->


    <div class="container py-5">
        <!-- Dynamic Heading -->
        <h2 id="categoryHeading" class="text-center mb-5 font-weight-bold">All Products</h2>

        <!-- Category Dropdown (Fixed Layout) -->
        <div class="category-container">
            <select id="categoryFilter" class="category-dropdown">
                <option value="all">All Products</option>
                <option value="accessories">Accessories</option>
                <option value="fire-extinguishers">Fire Extinguishers</option>
                <option value="fire-suppression">Fire Suppression Systems</option>
                <option value="watermist">Watermist/CAFS Systems</option>
            </select>
        </div>

        <!-- Product Grid -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" id="productList">
            @include('partials.product-list', ['products' => $products])
        </div>
    </div>




    <!-- Ultra-Stylish Product Modal -->
    <div class="modal fade" id="productDetailsModal" tabindex="-1" aria-labelledby="productDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg border-0">

                <!-- Neon Gradient Header -->
                <div class="modal-header text-white border-0"
                    style="background: linear-gradient(135deg, #8E2DE2, #4A00E0); box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);">
                    <h5 class="modal-title fw-bold" id="productModalTitle">Product Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4">

                    <div class="row">
                        <!-- Product Image -->
                        <div class="col-md-5 text-center">
                            <img id="productImage" src="default.jpg" class="img-fluid rounded-3 shadow-lg"
                                style="max-height: 350px; object-fit: contain; transition: transform 0.3s ease;">
                        </div>
                        <!-- Price, Stock, and Warranty -->
                        <div class="col-md-7">
                            <h4 id="productName" class="fw-bold text-dark mb-2"></h4>

                            <!-- Price Badge -->
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge px-3 py-2 fw-bold"
                                    style="font-size: 1rem; background: linear-gradient(135deg, #FF6F00, #FFA000); color: white;">
                                    <span id="productPrice"></span>
                                </span>
                            </div>

                            <div id="productCoupon" class="mt-2"></div>

                            <div class="d-flex flex-wrap gap-3">
                                <span class="badge bg-danger px-3 py-2">Stock: <span id="productStock"></span></span>
                                <span class="badge warranty-badge px-3 py-2">Warranty: <span
                                        id="productWarranty"></span></span>
                            </div>
                        </div>

                    </div>

                    <div class="mt-4">
                        <h5 class="text-primary fw-bold">Description</h5>
                        <p id="productDescription" class="text-muted mb-3" style="font-size: 1rem;"></p>
                    </div>

                    <hr class="my-4">

                    <!-- Technical Specifications -->
                    <h5 class="text-primary fw-bold">Technical Specifications</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered shadow-sm rounded-3">
                            <tbody id="productSpecifications">
                                <!-- Dynamic Specifications Will Be Loaded Here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-0 d-flex justify-content-center bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        Close
                    </button>
                    {{-- <button type="button" class="btn btn-gradient px-4">
                    Add to Cart <i class="fas fa-shopping-cart ms-2"></i>
                </button> --}}
                </div>
            </div>
        </div>
    </div>

<!-- Include jQuery from a CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



    <script>
        document.addEventListener('click', function(event) {
            if (event.target.closest('.view-product')) {
                let button = event.target.closest('.view-product');
                let productId = button.dataset.id;
                let categoryId = button.dataset.categoryId;
                let providerId = button.dataset.providerId;

                console.log("Product ID:", productId);
                console.log("Category ID:", categoryId);
                console.log("Provider ID:", providerId);

                let modalTitle = "";

                // Dynamic modal header titles
                if (categoryId == 1) {
                    modalTitle = "Accessory Details";
                } else if (categoryId == 2) {
                    modalTitle = "Fire Extinguisher Details";
                } else if (categoryId == 3) {
                    modalTitle = "Fire Suppression System Details";
                } else if (categoryId == 4) {
                    modalTitle = "Watermist System Details";
                }

                fetch(
                        `/fetch-product-details?product_id=${productId}&category_id=${categoryId}&provider_id=${providerId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }

                        let product = data.product;
                        let coupon = data.coupon;

                        document.getElementById("productModalTitle").textContent = modalTitle;
                        document.getElementById("productImage").src = product.image || 'default.jpg';
                        document.getElementById("productName").textContent = product.name || 'No Name';
                        document.getElementById("productDescription").textContent = product.description ||
                            'No Description';
                        document.getElementById("productStock").textContent = product.stock || 'N/A';
                        document.getElementById("productWarranty").textContent = product.warranty || 'N/A';

                        const priceElement = document.getElementById("productPrice");
                        const couponElement = document.getElementById(
                        "productCoupon"); 
                        priceElement.innerHTML = `<span class="fw-bold text-success">₹${product.price}</span>`;

                        if (coupon) {
                            let discountPrice = coupon.final_price;
                            couponElement.innerHTML = `<span class="text-primary">Coupon Code: <b>${coupon.coupon_code}</b></span><br>
                               <span class="text-danger">After applying, the price: ₹${discountPrice}</span>`;
                        } else {
                            couponElement.innerHTML = `<span class="text-muted"></span>`;
                        }


                        let specificationsHtml = "";

                        if (categoryId == 1) { // Accessories
                            specificationsHtml += `
                        <tr><th>Weight</th><td>${product.weight || 'N/A'}</td></tr>
                        <tr><th>Power Source</th><td>${product.power_source || 'N/A'}</td></tr>
                        <tr><th>Operating Voltage</th><td>${product.operating_voltage || 'N/A'}</td></tr>
                        <tr><th>Material</th><td>${product.material || 'N/A'}</td></tr>
                        <tr><th>Working Temperature</th><td>${product.working_temprature || 'N/A'}</td></tr>
                        <tr><th>IP Rating</th><td>${product.IP_routing || 'N/A'}</td></tr>
                    `;
                        } else if (categoryId == 2) { // Fire Extinguisher
                            specificationsHtml += `
                        <tr><th>Fire Class</th><td>${product.fire_class || 'N/A'}</td></tr>
                        <tr><th>Suitability</th><td>${product.suitability || 'N/A'}</td></tr>
                        <tr><th>Capacity</th><td>${product.capacity || 'N/A'}</td></tr>
                        <tr><th>Extinguishing Agent</th><td>${product.extinguishing_agent || 'N/A'}</td></tr>
                        <tr><th>Discharge Time</th><td>${product.discharge_time || 'N/A'}</td></tr>
                        <tr><th>Working Pressure</th><td>${product.working_pressure || 'N/A'}</td></tr>
                    `;
                        } else if (categoryId == 3) { // Fire Suppression System
                            specificationsHtml += `
                        <tr><th>Suppression Type</th><td>${product.suppression_type || 'N/A'}</td></tr>
                        <tr><th>Installation Type</th><td>${product.installation_type || 'N/A'}</td></tr>
                        <tr><th>Application Area</th><td>${product.application_area || 'N/A'}</td></tr>
                        <tr><th>Cylinder Capacity</th><td>${product.cylinder_capacity || 'N/A'}</td></tr>
                        <tr><th>Activation Method</th><td>${product.activation_method || 'N/A'}</td></tr>
                    `;
                        } else if (categoryId == 4) { // Watermist System
                            specificationsHtml += `
                        <tr><th>Technology Type</th><td>${product.technology_type || 'N/A'}</td></tr>
                        <tr><th>Nozzle Type</th><td>${product.nozzle_type || 'N/A'}</td></tr>
                        <tr><th>Working Pressure</th><td>${product.working_pressure || 'N/A'}</td></tr>
                        <tr><th>Droplet Size</th><td>${product.droplet_size || 'N/A'}</td></tr>
                        <tr><th>Flow Rate</th><td>${product.flow_rate || 'N/A'}</td></tr>
                    `;
                        }

                        document.getElementById("productSpecifications").innerHTML = specificationsHtml;

                        let modal = new bootstrap.Modal(document.getElementById("productDetailsModal"));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error fetching product details:', error);
                        alert('Failed to fetch product details. Please try again.');
                    });
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const categoryFilter = document.getElementById("categoryFilter");
            const categoryHeading = document.getElementById("categoryHeading");

            const categoryNames = {
                "all": "All Products",
                "accessories": "Accessories",
                "fire-extinguishers": "Fire Extinguishers",
                "fire-suppression": "Fire Suppression Systems",
                "watermist": "Watermist/CAFS Systems"
            };

            categoryFilter.addEventListener("change", function() {
                const selectedCategory = categoryFilter.value;
                categoryHeading.textContent = categoryNames[selectedCategory];
            });
        });


        document.getElementById('categoryFilter').addEventListener('change', function() {
            let category = this.value;

            fetch(`/filter-products?category=${category}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(data => {
                    document.getElementById('productList').innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        });

        // $(document).on('click', '.add-to-cart', function() {
        //     let button = $(this);
        //     let productId = button.data('id');
        //     let categoryId = button.data('category-id');
        //     let providerId = button.data('provider-id');

        //     $.ajax({
        //         url: "{{ route('cart.add') }}",
        //         type: "POST",
        //         data: {
        //             product_id: productId,
        //             category_id: categoryId,
        //             provider_id: providerId,
        //             quantity: 1,
        //             _token: "{{ csrf_token() }}"
        //         },
        //         success: function(response) {

        //             Swal.fire({
        //                 title: "🎉 Great!",
        //                 text: response.success,
        //                 icon: "success",
        //                 timer: 2500,
        //                 showConfirmButton: false,
        //                 toast: true,
        //                 position: "top-end",
        //                 background: "#f0f9ff",
        //                 color: "#0c5460",
        //                 iconColor: "#28a745",
        //                 customClass: {
        //                     popup: 'swal2-popup-custom',
        //                 },
        //                 showClass: {
        //                     popup: 'animate__animated animate__fadeInDown'
        //                 },
        //                 hideClass: {
        //                     popup: 'animate__animated animate__fadeOutUp'
        //                 }
        //             });



        //         },
        //         error: function(xhr) {
        //             Swal.fire({
        //                 title: "Error!",
        //                 text: xhr.responseJSON.error,
        //                 icon: "error",
        //                 confirmButtonText: "OK"
        //             });
        //         }
        //     });
        // });

        $(document).on('click', '.add-to-cart', function() {
    let button = $(this);
    let productId = button.data('id');
    let categoryId = button.data('category-id');
    let providerId = button.data('provider-id');

    $.ajax({
        url: "{{ route('product.stock.check') }}", 
        type: "GET",
        data: {
            product_id: productId,
            category_id: categoryId,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            if (response.stock <= 1) {
               
                Swal.fire({
                    title: "⚠️ Out of Stock!",
                    text: "This product is currently out of stock.",
                    icon: "warning",
                    confirmButtonText: "OK",
                    background: "#fff3cd",
                    color: "#856404",
                    iconColor: "#ff0000",
                    customClass: {
                        popup: 'swal2-popup-custom',
                    },
                    showClass: {
                        popup: 'animate__animated animate__shakeX'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp'
                    }
                });
            } else {
              
                $.ajax({
                    url: "{{ route('cart.add') }}",
                    type: "POST",
                    data: {
                        product_id: productId,
                        category_id: categoryId,
                        provider_id: providerId,
                        quantity: 1,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "🎉 Great!",
                            text: response.success,
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
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: "Error!",
                            text: xhr.responseJSON.error,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                title: "Error!",
                text: "Failed to check stock.",
                icon: "error",
                confirmButtonText: "OK"
            });
        }
    });
});

    </script>
@endsection
