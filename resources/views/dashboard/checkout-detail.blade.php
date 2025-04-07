@extends('dashboard.layout.app')

@section('content')


   <!-- Orders Page Header Start -->
<div class="container-fluid page-header py-5 mb-5 wow fadeIn text-center" data-wow-delay="0.1s">
    <div class="container py-5">
        <h1 class="display-3 text-white text-uppercase fw-bold animated slideInDown"
            style="letter-spacing: 2px; text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);">
            Your Orders
        </h1>
        <p class="text-white lead animated fadeInUp" data-wow-delay="0.3s">
            Track your recent purchases and stay updated on delivery status.
        </p>
    </div>
</div>


    <!-- Contact Page Header End -->
    {{-- order history and detail --}}
    <div class="container">
        <h2 class="mb-4">Your Orders</h2>

        @if ($latestOrder->isEmpty() && $pastOrders->isEmpty())
            <div class="alert alert-warning">No order is placed by your account.</div>
        @else
            @if (!$latestOrder->isEmpty())
                <h3 class="text-success">Latest Order</h3>
                <table class="table table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Provider</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($latestOrder as $item)
                            <tr id="order-{{ $item->id }}">
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->category->name }}</td>
                                <td>{{ $item->provider_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>${{ number_format($item->final_price, 2) }}</td>
                                <td>
                                    @if (now()->diffInHours($item->created_at) <= 24)
                                        <button class="btn btn-danger btn-sm cancel-order" data-id="{{ $item->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">Cannot Cancel</span>
                                    @endif
                                    <button class="btn btn-info btn-sm track-order" data-id="{{ $item->id }}">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </button>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if (!$pastOrders->isEmpty())
                <h3 class="text-muted mt-4">Order History</h3>
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Provider</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pastOrders as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->category->name }}</td>
                                <td>{{ $item->provider_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>${{ number_format($item->final_price, 2) }}</td>
                                <td>{{ $item->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endif
    </div>


    <!-- Order Tracking Modal -->
    <div class="modal fade" id="orderTrackingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Order Tracking</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body text-center">
                    <div class="tracking-container">
                        <!-- Progress Bar -->
                        <div class="progress-container">
                            <div class="progress-bar">
                                <div class="progress" id="progress"></div>
                            </div>

                            <!-- Steps -->
                            <div class="step active" data-step="confirmed">
                                <i class="fas fa-check-circle"></i>
                                <span>Order Placed</span>
                            </div>
                            <div class="step" data-step="processed">
                                <i class="fas fa-cogs"></i>
                                <span>Order Processing</span>
                            </div>
                            <div class="step" data-step="shipped">
                                <i class="fas fa-truck"></i>
                                <span>Shipped</span>
                            </div>
                            <div class="step" data-step="en_route">
                                <i class="fas fa-shipping-fast"></i>
                                <span>Out for Delivery</span>
                            </div>
                            <div class="step" data-step="arrived">
                                <i class="fas fa-home"></i>
                                <span>Arrived</span>
                            </div>
                        </div>
                    </div>

                    <!-- Estimated Arrival Date -->
                    <p class="mt-3 arrival-date text-dark">
                        Estimated Arrival: <span id="arrivalDate"></span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- CSS -->
    <style>
        .tracking-container {
            text-align: center;
            padding: 20px;
        }

        .progress-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            width: 100%;
            margin: 30px 0;
        }

        .progress-bar {
            position: absolute;
            top: 50%;
            left: 10%;
            width: 80%;
            height: 6px;
            background: #e0e0e0;
            border-radius: 5px;
            z-index: 1;
        }

        .progress {
            position: absolute;
            height: 6px;
            width: 0;
            background: #007bff;
            border-radius: 5px;
            transition: width 0.5s ease-in-out;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 14px;
            color: #999;
            cursor: pointer;
            transition: color 0.3s ease-in-out;
            z-index: 2;
            flex: 1;
        }

        .step i {
            font-size: 24px;
            margin-bottom: 5px;
            color: #007bff;
        }

        .step.active i {
            color: #007bff;
            font-weight: bold;
        }

        .step.active {
            color: #007bff;
            font-weight: bold;
        }
    </style>
    {{-- SweetAlert & AJAX --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



    <script>
        $(document).on('click', '.cancel-order', function() {
            var orderId = $(this).data('id');

            Swal.fire({
                title: "Are you sure?",
                text: "You can cancel your order within 24 hours.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, I am sure!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('order.cancel') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            order_id: orderId
                        },
                        success: function(response) {
                            if (response.success) {
                                $("#order-" + orderId).fadeOut();
                                Swal.fire("Canceled!", response.message, "success");
                            } else {
                                Swal.fire("Error!", response.message, "error");
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.track-order', function() {
            var orderId = $(this).data('id');

            $.ajax({
                url: "{{ url('/checkout/track') }}/" + orderId,
                type: "GET",
                success: function(response) {
                    if (response.success) {
                        $("#progress").css("width", (response.currentStep * 25) + "%");

                        $(".step").each(function(index) {
                            if (index <= response.currentStep) {
                                $(this).addClass("active");
                            } else {
                                $(this).removeClass("active");
                            }
                        });

                        $("#orderTrackingModal").modal("show");
                        $("#arrivalDate").text(response.arrivalDate);

                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                }
            });
        });
    </script>


@endsection
