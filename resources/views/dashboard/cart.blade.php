@extends('dashboard.layout.app')

@section('content')



    <!-- Page Header Start -->
    <div class="container-fluid page-header py-5 mb-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-3 text-white text-uppercase mb-3 animated slideInDown">Your Cart</h1>

            <div class="mt-3 mx-auto p-2 border rounded bg-dark text-light text-center" style="max-width: 400px;">
                <!-- Coupon Text for Redeem -->
                <p id="redeem-coupon-text" class="mb-2 small">
                    If you have a coupon, redeem it now!
                </p>

                <!-- Coupon Text for Remove (Hidden Initially) -->
                <p id="remove-coupon-text" class="mb-2 small d-none text-warning">
                    A coupon is applied! Remove it if needed.
                </p>

                <div class="d-flex justify-content-center gap-2">
                    <!-- Redeem Coupon Button -->
                    <button id="redeem-coupon-btn" class="btn btn-sm btn-info fw-bold" data-bs-toggle="modal"
                        data-bs-target="#couponModal">
                        🎟️ Redeem
                    </button>

                    <!-- Remove Coupon Button (Hidden Initially) -->
                    <button id="remove-coupon-btn" class="btn btn-sm btn-danger fw-bold d-none">
                        ❌ Remove
                    </button>
                </div>
            </div>
        </div>
    </div>



    <div class="container">
        <h2 class="my-4 text-light">🛒 Your Shopping Cart</h2>

        @if ($cartItems->count() > 0)
            <div class="table-responsive">
                <table class="table table-dark table-striped table-hover text-center align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 120px;">Image</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th style="width: 100px;">Quantity</th>
                            <th>Total</th>
                            <th style="width: 80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cartItems as $item)
                            @if (isset($item->product))
                                <tr>
                                    <!-- Product Image -->
                                    <td>
                                        <img src="{{ asset($item->product->image) }}" alt="Product Image"
                                            class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                    </td>

                                    <!-- Product Name -->
                                    <td class="text-light fw-bold">
                                        {{ $item->product->name }}
                                    </td>

                                    <!-- Price -->
                                    <td class="text-success fw-bold">
                                        ₹<span
                                            id="price-{{ $item->id }}">{{ number_format($item->final_price ?? $item->product->price, 2) }}</span>
                                    </td>

                                    <!-- Quantity -->
                                    <td>
                                        <input type="number" class="form-control text-center quantity-update"
                                            data-id="{{ $item->id }}" value="{{ max(1, $item->quantity) }}"
                                            min="1" oninput="validateQuantity(this)" onblur="validateQuantity(this)"
                                            style="max-width: 70px; background: #222; color: white; border: 1px solid #555;">
                                    </td>

                                    <!-- Total Price -->
                                    <td class="text-warning fw-bold product-total" data-id="{{ $item->id }}">
                                        ₹<span
                                            id="total-{{ $item->id }}">{{ number_format($item->quantity * ($item->final_price ?? $item->product->price), 2) }}</span>
                                    </td>

                                    <!-- Remove Button -->
                                    <td>
                                        <button class="btn btn-danger btn-sm remove-from-cart" data-id="{{ $item->id }}"
                                            style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="6" class="text-danger">⚠ Product not found!</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Grand Total -->
            <h4 class="mt-3 text-light">Grand Total: ₹<span id="grand-total"
                    class="text-warning">{{ number_format($grandTotal, 2) }}</span></h4>


            {{-- checkout --}}
            <form action="{{ route('checkout.store') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                <button type="submit" class="btn btn-lg btn-success">Proceed to Checkout</button>
            </form>
        @else
            <p class="text-muted">No items in cart.</p>
            <a href="{{ route('product') }}" class="btn btn-primary">🏠 Continue Shopping</a>
        @endif
    </div>

    <!-- Coupon Modal -->
    <div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark" id="couponModalLabel">Apply Coupon Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="text-dark">Enter Coupon Code:</label>
                    <input type="text" class="form-control text-center" id="coupon-code" placeholder="Enter coupon here">
                    <button class="btn btn-primary mt-3 w-100 apply-coupon-modal">Apply Coupon</button>
                    <p class="text-success mt-2 coupon-success-msg" style="display:none;">✔ Coupon Applied Successfully!</p>
                    <p class="text-danger mt-2 coupon-error-msg" style="display:none;">❌ Invalid Coupon!</p>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- JavaScript to Handle Coupon -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            updateGrandTotalFromServer();
        });
        document.querySelector(".apply-coupon-modal").addEventListener("click", function() {
            let couponCode = document.querySelector("#coupon-code").value.trim();

            if (couponCode === "") {
                document.querySelector(".coupon-error-msg").style.display = "block";
                document.querySelector(".coupon-success-msg").style.display = "none";
                return;
            }

            fetch("{{ route('apply.coupon') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        coupon_code: couponCode
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(".coupon-success-msg").style.display = "block";
                        document.querySelector(".coupon-error-msg").style.display = "none";

                        document.getElementById("redeem-coupon-btn").classList.add("d-none");
                        document.getElementById("remove-coupon-btn").classList.remove("d-none");

                        document.getElementById("redeem-coupon-text").classList.add("d-none");
                        document.getElementById("remove-coupon-text").classList.remove("d-none");

                        document.getElementById("grand-total").innerText = data.new_grand_total;

                        data.updated_cart_items.forEach(item => {
                            document.querySelector(`#price-${item.item_id}`).innerText = item
                                .final_price;
                            document.querySelector(`#total-${item.item_id}`).innerText = item.total;
                        });

                        sessionStorage.setItem("coupon_applied", "true");
                    } else {
                        document.querySelector(".coupon-error-msg").style.display = "block";
                        document.querySelector(".coupon-success-msg").style.display = "none";
                    }
                })
                .catch(error => console.error("Error:", error));
        });

        document.getElementById("remove-coupon-btn").addEventListener("click", function() {
            fetch("{{ route('remove.coupon') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("redeem-coupon-btn").classList.remove("d-none");
                        document.getElementById("remove-coupon-btn").classList.add("d-none");

                        document.getElementById("redeem-coupon-text").classList.remove("d-none");
                        document.getElementById("remove-coupon-text").classList.add("d-none");

                        data.updated_cart_items.forEach(item => {
                            document.querySelector(`#price-${item.item_id}`).innerText = item
                                .original_price;
                            document.querySelector(`#total-${item.item_id}`).innerText = item.total;
                        });

                        document.getElementById("grand-total").innerText = data.new_grand_total;

                        sessionStorage.removeItem("coupon_applied");
                    }
                })
                .catch(error => console.error("Error:", error));
        });


        function updateGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll(".product-total span").forEach(item => {
                let total = parseFloat(item.innerText.replace("₹", "").replace(",", ""));
                if (!isNaN(total)) {
                    grandTotal += total;
                }
            });

            document.getElementById("grand-total").innerText = grandTotal.toFixed(2);
        }

        function updateGrandTotalFromServer() {
            fetch("{{ route('cart.get-total') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("grand-total").innerText = data.new_grand_total;
                        if (data.coupon_applied) {
                            document.getElementById("redeem-coupon-btn").classList.add("d-none");
                            document.getElementById("remove-coupon-btn").classList.remove("d-none");
                        }
                    }
                })
                .catch(error => console.error("Error fetching total:", error));
        }

        $(document).on("click", ".remove-from-cart", function() {
            let cartId = $(this).data("id");

            $.ajax({
                url: "{{ route('cart.remove') }}",
                type: "POST",
                data: {
                    id: cartId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $(`tr [data-id='${cartId}']`).closest("tr").remove();
                    updateGrandTotal();
                    if ($(".product-total").length === 0) {
                        location.reload();
                    }
                }
            });
        });
        // $(document).on("change", ".quantity-update", function() {
        //     let cartId = $(this).data("id");
        //     let quantity = parseInt($(this).val());
        //     if (isNaN(quantity) || quantity < 1) {
        //         quantity = 1;
        //         $(this).val(1); // Reset invalid input
        //     }

        //     let priceElement = $(`#price-${cartId}`);
        //     let totalElement = $(`#total-${cartId}`);

        //     if (!priceElement.length || !totalElement.length) {
        //         console.error(`Price or total element not found for cart ID: ${cartId}`);
        //         return;
        //     }

        //     let priceText = priceElement.text().replace("", "").replace(",", "").trim();
        //     let price = parseFloat(priceText);

        //     if (isNaN(price) || price <= 0) {
        //         console.error(`Invalid price for cart item ID: ${cartId}, falling back to product price.`);
        //         price = parseFloat(priceElement.attr("data-original-price")) || 0;
        //     }

        //     if (price <= 0) {
        //         console.error(`Still invalid price for cart ID: ${cartId}.`);
        //         return;
        //     }
        //     let newTotal = price * quantity;
        //     totalElement.text(`${newTotal.toFixed(2)}`);

        //     updateGrandTotal(); 

        //     $.ajax({
        //         url: "{{ route('cart.update') }}",
        //         type: "POST",
        //         data: {
        //             id: cartId,
        //             quantity: quantity,
        //             _token: "{{ csrf_token() }}"
        //         },
        //         success: function(response) {
        //             updateGrandTotal(); 
        //         }
        //     });
        // });

        // Quantity validation function
        function validateQuantity(input) {
            let quantity = parseInt(input.value);
            if (isNaN(quantity) || quantity < 1) {
                input.value = 1; 
            }
        }

        $(document).on("change", ".quantity-update", function() {
            let input = $(this);
            let cartId = input.data("id");
            let quantity = parseInt(input.val());

            if (isNaN(quantity) || quantity < 1) {
                quantity = 1;
                input.val(1); 
            }

            $.ajax({
                url: "{{ route('cart.checkStock') }}",
                type: "GET",
                data: {
                    id: cartId,
                    quantity: quantity,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (quantity > response.stock) {
                        input.val(response.stock); 

                        Swal.fire({
                            title: "⚠️ Out of Stock!",
                            text: `Only ${response.stock} items available in stock.`,
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

                        return;
                    }

                    let priceElement = $(`#price-${cartId}`);
                    let totalElement = $(`#total-${cartId}`);

                    if (!priceElement.length || !totalElement.length) {
                        console.error(`Price or total element not found for cart ID: ${cartId}`);
                        return;
                    }

                    let priceText = priceElement.text().replace(",", "").trim();
                    let price = parseFloat(priceText);

                    if (isNaN(price) || price <= 0) {
                        console.error(
                            `Invalid price for cart item ID: ${cartId}, falling back to product price.`
                            );
                        price = parseFloat(priceElement.attr("data-original-price")) || 0;
                    }

                    if (price <= 0) {
                        console.error(`Still invalid price for cart ID: ${cartId}.`);
                        return;
                    }

                    let newTotal = price * quantity;
                    totalElement.text(`${newTotal.toFixed(2)}`);

                    updateGrandTotal();

                    $.ajax({
                        url: "{{ route('cart.update') }}",
                        type: "POST",
                        data: {
                            id: cartId,
                            quantity: quantity,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            updateGrandTotal();
                        }
                    });

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
