@extends('admin-layouts.app')

@section('content')
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow p-4 w-75">
            <div class="d-flex align-items-center position-relative mb-3">
                <h2 class="mb-0 text-center flex-grow-1">Coupon List</h2>
                @php
                    $provider = \App\Models\ServiceProvider::where('user_id', auth()->id())->first();
                @endphp

                @can('create coupon')
                    @if (auth()->user()->user_type === 'admin')
                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm custom-btn">
                            + Add New Coupons
                        </a>
                    @elseif(auth()->user()->user_type === 'provider')
                        @if ($provider && ($provider->status === 'rejected' || $provider->status === 'pending'))
                            <span class="btn btn-secondary btn-sm custom-btn disabled" style="cursor: not-allowed; opacity: 0.5;"
                                data-bs-toggle="tooltip"
                                title="Your account is {{ $provider->status }}. Please wait for approval.">
                                + Add New Coupons (Not Allowed)
                            </span>
                        @else
                            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm custom-btn">
                                + Add New Coupons
                            </a>
                        @endif
                    @endif
                @endcan


            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered yajra-datatable text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Coupon</th>
                                <th>Provider</th>
                                <th>Category</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Discount</th>
                                <th>Final Price</th>
                                {{-- <th>Max Usage</th> --}}
                                {{-- <th>Used</th> --}}
                                <th>Expiry</th>
                                <th>Countdown</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <style>
        .custom-btn {
            padding: 5px 10px;
            font-size: 14px;
            position: absolute;
            right: 0;
        }

        .table td .btn-group {
            display: flex;
            justify-content: center;
            align-items: center;
            white-space: nowrap;
        }

        th:nth-child(15),
        td:nth-child(15) {
            min-width: 150px;
        }
    </style>

    <!-- Edit Coupon Modal -->
    <div class="modal fade" id="editCouponModal" tabindex="-1" aria-labelledby="editCouponModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCouponModalLabel">Edit Coupon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCouponForm">
                        @csrf
                        <input type="hidden" id="coupon_id" name="coupon_id">

                        <div class="mb-3">
                            <label class="form-label">Coupon Code</label>
                            <input type="text" class="form-control" id="coupon_code" name="coupon_code">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Discount (%)</label>
                            <input type="number" class="form-control" id="discount" name="discount">
                        </div>
                        {{-- <div class="mb-3">
                            <label class="form-label">Max Usage</label>
                            <input type="number" class="form-control" id="max_usage" name="max_usage">
                        </div> --}}
                        <div class="mb-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="datetime-local" class="form-control" id="expiry_date" name="expiry_date">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Update Coupon</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTables & SweetAlert -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.coupons.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'coupon_code',
                        name: 'coupon_code'
                    },
                    {
                        data: 'provider',
                        name: 'provider'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'product',
                        name: 'product'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'discount',
                        name: 'discount'
                    },
                    {
                        data: 'final_price',
                        name: 'final_price'
                    },
                    // {
                    //     data: 'max_usage',
                    //     name: 'max_usage'
                    // },
                    // {
                    //     data: 'used_count',
                    //     name: 'used_count'
                    // },
                    // { data: 'status', name: 'status' },
                    {
                        data: 'expiry_date',
                        name: 'expiry_date'
                    },
                    {
                        data: 'countdown',
                        name: 'countdown',
                        render: function(data, type, row) {
                            return `<span class="countdown-timer" data-expiry="${row.expiry_timestamp}">${data}</span>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'id',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let buttons = '';
                            let isDisabled = row.user_type === 'provider' && row.provider_status !==
                                'approved';

                            if (row.user_type === 'admin') {
                                isDisabled = false;
                            }
                            return `
                        <button class="btn btn-sm btn-warning editCoupon" data-id="${data}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger deleteCoupon" data-id="${data}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                drawCallback: function() {
                    startCountdown();
                }
            });

            function startCountdown() {
                $(".countdown-timer").each(function() {
                    let $this = $(this);
                    let expiryTimestamp = parseInt($this.attr("data-expiry")) * 1000;

                    function updateCountdown() {
                        let now = new Date().getTime();
                        let timeLeft = expiryTimestamp - now;

                        if (timeLeft <= 0) {
                            $this.html("<span class='text-danger'>Expired</span>");
                        } else {
                            let days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                            let hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            let minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                            let seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                            $this.html(`${days}d ${hours}h ${minutes}m ${seconds}s`);
                        }
                    }

                    updateCountdown();
                    setInterval(updateCountdown, 1000);
                });
            }

            $(document).on('click', '.editCoupon', function() {
                let couponId = $(this).data('id');

                $.ajax({
                    url: `/coupons/${couponId}/edit`, 
                    type: "GET",
                    success: function(data) {
                        console.log("Coupon Data:", data);
                        $('#coupon_id').val(data.id);
                        $('#coupon_code').val(data.coupon_code);
                        $('#discount').val(data.discount);
                        // $('#max_usage').val(data.max_usage);
                        $('#expiry_date').val(data.expiry_date);
                        $('#editCouponModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", xhr.responseText);
                        Swal.fire("Error", "Failed to fetch coupon data! " + xhr.responseText,
                            "error");
                    }
                });
            });


            $('#editCouponForm').submit(function(e) {
                e.preventDefault();

                let formData = {
                    _token: "{{ csrf_token() }}",
                    coupon_id: $('#coupon_id').val(),
                    coupon_code: $('#coupon_code').val(),
                    discount: $('#discount').val(),
                    // max_usage: $('#max_usage').val(),
                    expiry_date: $('#expiry_date').val(),
                };

                $.post("{{ route('coupons.update') }}", formData, function(response) {
                    $('#editCouponModal').modal('hide');
                    Swal.fire("Success", response.success, "success");
                    $('.yajra-datatable').DataTable().ajax.reload();
                }).fail(function(xhr) {
                    Swal.fire("Error", "Something went wrong!", "error");
                });
            });

            $(document).on('click', '.deleteCoupon', function() {
                let couponId = $(this).data('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "This action cannot be undone!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/coupons/${couponId}`,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire("Deleted!", response.success, "success");
                                $('.yajra-datatable').DataTable().ajax.reload();
                            },
                            error: function() {
                                Swal.fire("Error", "Failed to delete!", "error");
                            }
                        });
                    }
                });
            });
        });


        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("editCouponForm");

            const couponCode = document.getElementById("coupon_code");
            const discount = document.getElementById("discount");
            // const maxUsage = document.getElementById("max_usage");
            const expiryDate = document.getElementById("expiry_date");

            function showError(input, message) {
                input.classList.add("is-invalid");

                let error = input.nextElementSibling;
                if (!error || !error.classList.contains("invalid-feedback")) {
                    error = document.createElement("div");
                    error.className = "invalid-feedback";
                    input.parentNode.appendChild(error);
                }
                error.innerText = message;
            }

            function removeError(input) {
                input.classList.remove("is-invalid");

                let error = input.nextElementSibling;
                if (error && error.classList.contains("invalid-feedback")) {
                    error.remove();
                }
            }

            function validateField(input, condition, message) {
                if (condition) {
                    showError(input, message);
                    return false;
                } else {
                    removeError(input);
                    return true;
                }
            }

            function validateForm() {
                let isValid = true;

                if (!validateField(couponCode, couponCode.value.trim() === "", "Coupon Code is required")) {
                    isValid = false;
                }
                if (!validateField(discount, discount.value === "" || discount.value < 0 || discount.value > 100,
                        "Enter a valid discount (0-100%)")) {
                    isValid = false;
                }
                // if (!validateField(maxUsage, maxUsage.value.trim() === "", "Max Usage is required")) {
                //     isValid = false;
                // }

                const selectedDate = new Date(expiryDate.value);
                const currentDate = new Date();
                if (!validateField(expiryDate, expiryDate.value === "" || selectedDate <= currentDate,
                        "Expiry date must be in the future")) {
                    isValid = false;
                }

                return isValid;
            }

            form.addEventListener("submit", function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                }
            });

            [couponCode, discount, maxUsage, expiryDate].forEach(input => {
                input.addEventListener("input", () => validateForm());
                input.addEventListener("blur", () => validateForm());
            });
        });
    </script>

@endsection
