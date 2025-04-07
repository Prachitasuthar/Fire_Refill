@extends('admin-layouts.app')

@section('content')
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow p-4 w-75">

            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('admin.coupons.index') }}" class="text-decoration-none text-dark">
                    <i class="bi bi-arrow-left" style="font-size: 1.5rem;"></i>
                </a>
            </div>

            <h2 class="mb-4 text-center">Add New Coupon</h2>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.coupons.store') }}" method="POST" autocomplete="off">
                @csrf
                <!-- Provider Dropdown -->
                <div class="mb-3">
                    <label for="provider" class="form-label">Select Provider</label>
                    <select id="provider" name="provider_id" class="form-select">
                        <option value="">-- Select Provider --</option>
                        @foreach ($providers as $provider)
                            <option value="{{ $provider->id }}">{{ $provider->first_name }} {{ $provider->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>


                <!-- Category Dropdown -->
                <div class="mb-3">
                    <label for="category" class="form-label">Select Category</label>
                    <select id="category" name="category_id" class="form-select">
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>


                <!-- Product Dropdown -->
                <div class="mb-3">
                    <label for="product" class="form-label">Select Product</label>
                    <select id="product" name="product_id" class="form-select">
                        <option value="">-- Select Product --</option>
                    </select>
                </div>

                <!-- Product Price -->
                <div class="mb-3">
                    <label for="price" class="form-label">Product Price</label>
                    <input type="number" id="price" name="price" class="form-control" readonly>
                </div>

                <!-- Discount Percentage -->
                <div class="mb-3">
                    <label for="discount" class="form-label">Discount %</label>
                    <input type="number" id="discount" name="discount" class="form-control" min="1" max="100">
                </div>

                <!-- Final Price -->
                <div class="mb-3">
                    <label for="final_price" class="form-label">Final Price</label>
                    <input type="number" id="final_price" name="final_price" class="form-control" readonly>
                </div>

                <!-- Coupon Code -->
                <div class="mb-3">
                    <label for="coupon_code" class="form-label">Coupon Code</label>
                    <div class="input-group">
                        <input type="text" id="coupon_code" name="coupon_code" class="form-control" readonly>
                        <button type="button" id="generate_coupon" class="btn btn-primary">Generate</button>
                    </div>
                </div>

                <!-- Expiry Date -->
                <div class="mb-3">
                    <label for="expiry_date" class="form-label">Expiry Date & Time</label>
                    <input type="datetime-local" id="expiry_date" name="expiry_date" class="form-control">
                </div>

                <input type="hidden" id="max_limit" name="max_usage" value="10">

                <div class="text-center">
                    <button type="submit" class="btn btn-success px-4">Save Coupon</button>
                </div>
            </form>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#provider, #category').change(function() {
                let providerId = $('#provider').val();
                let categoryId = $('#category').val();

                if (providerId && categoryId) {
                    $.ajax({
                        url: "{{ route('admin.getProducts') }}",
                        type: "GET",
                        data: {
                            provider_id: providerId,
                            category_id: categoryId
                        },
                        success: function(response) {
                            $('#product').empty().append(
                                '<option value="">-- Select Product --</option>');
                            $.each(response.products, function(index, product) {
                                $('#product').append('<option value="' + product.id +
                                    '" data-price="' + product.price + '">' +
                                    product.name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#product').empty().append('<option value="">-- Select Product --</option>');
                    $('#price').val('');
                }
            });

            $('#product').change(function() {
                let selectedProduct = $('#product option:selected');
                let productPrice = selectedProduct.data('price') ||
                '';
                $('#price').val(productPrice);
            });
        });

        document.getElementById('generate_coupon').addEventListener('click', function() {
            let coupon = Math.random().toString(36).substring(2, 8).toUpperCase();
            document.getElementById('coupon_code').value = coupon;
        });

        document.getElementById('discount').addEventListener('input', function() {
            let price = parseFloat(document.getElementById('price').value) || 0;
            let discount = parseFloat(this.value) || 0;
            let finalPrice = price - (price * (discount / 100));
            document.getElementById('final_price').value = finalPrice.toFixed(2);
        });

        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");

            const fields = [{
                    id: "provider",
                    message: "Please select a provider."
                },
                {
                    id: "category",
                    message: "Please select a category."
                },
                {
                    id: "product",
                    message: "Please select a product."
                },
                {
                    id: "discount",
                    message: "Discount is required and must be between 1-50%.",
                    type: "number",
                    min: 1,
                    max: 50
                },
                {
                    id: "expiry_date",
                    message: "Expiry date & time is required."
                },
                {
                    id: "max_limit",
                    message: "Max usage limit is required and must be at least 10.",
                    type: "number",
                    min: 10
                }
            ];

            // **Field Validation**
            function validateField(field) {
                let input = document.getElementById(field.id);
                let value = input.value.trim();
                let errorElement = input.parentNode.querySelector(".error-message");

                if (errorElement) {
                    errorElement.remove();
                }

                let hasError = false;

                if (value === "") {
                    hasError = true;
                } else if (field.type === "number") {
                    let numValue = Number(value);
                    if (isNaN(numValue) || (field.min && numValue < field.min) || (field.max && numValue > field
                            .max)) {
                        hasError = true;
                    }
                }

                if (hasError) {
                    let errorMsg = document.createElement("small");
                    errorMsg.classList.add("text-danger", "error-message");
                    errorMsg.innerText = field.message;
                    input.parentNode.appendChild(errorMsg);
                }
            }

            fields.forEach(field => {
                let input = document.getElementById(field.id);
                if (input) {
                    input.addEventListener("input", () => validateField(field));
                    input.addEventListener("blur", () => validateField(field));
                }
            });

            form.addEventListener("submit", function(event) {
                event.preventDefault();
                let isValid = true;

                fields.forEach(field => {
                    validateField(field);
                    if (document.getElementById(field.id).parentNode.querySelector(
                        ".error-message")) {
                        isValid = false;
                    }
                });

                if (isValid) {
                    form.submit();
                }
            });

            document.getElementById("discount").addEventListener("input", function() {
                let price = parseFloat(document.getElementById("price").value) || 0;
                let discount = parseFloat(this.value) || 0;

                if (discount >= 1 && discount <= 100) {
                    let finalPrice = price - (price * (discount / 100));
                    document.getElementById("final_price").value = finalPrice.toFixed(2);
                } else {
                    document.getElementById("final_price").value = price.toFixed(2);
                }
            });
        });
    </script>
@endsection
