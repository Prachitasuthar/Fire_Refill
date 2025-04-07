@extends('admin-layouts.app')

@section('content')
<div class="container mt-5 d-flex justify-content-center">
    <div class="card shadow p-4 w-100">
        <div class="d-flex align-items-center position-relative mb-3">
            <h2 class="mb-0 text-center flex-grow-1">Order Booking List</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="bookings-table" class="table table-bordered table-striped" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="min-width: 120px;">User Name</th>
                            <th style="min-width: 150px;">Provider Name</th>
                            <th style="min-width: 150px;">Category Name</th>
                            <th style="min-width: 180px;">Product Name</th>
                            <th style="min-width: 150px;">Checkout Name</th>
                            <th style="min-width: 130px;">Mobile</th>
                            <th style="min-width: 200px;">Email</th>
                            <th style="min-width: 250px;">Address</th> 
                            <th style="min-width: 140px;">Payment Method</th>
                            <th style="min-width: 120px;">Final Price</th>
                            <th style="min-width: 140px;">Payment Status</th>
                            <th style="min-width: 140px;">Order Status</th>
                            <th style="min-width: 160px;">Order Date</th>
                            <th style="min-width: 160px;">Arrived Date</th>
                            <th style="min-width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>





<!-- Order Tracking Modal -->
<div class="modal fade" id="orderTrackingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Order Tracking</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
            </div>
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
                            <span>Order Placed </span>
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
                            <span style>Out for Delivery </span>
                        </div>
                        <div class="step" data-step="arrived">
                            <i class="fas fa-home"></i>
                            <span>Arrived</span>
                        </div>
                    </div>
                </div>
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



<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>
var userPermissions = @json(auth()->user()->getAllPermissions()->pluck('name')->toArray());

function hasPermission(permission) {
    return userPermissions.includes(permission);
}
    
 $(document).ready(function() {
    $('#bookings-table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true, 
        autoWidth: false, 
        ajax: '{{ route("admin.getBookingsData") }}',
        columns: [
            { data: 'user_name', name: 'user_name', width: '150px' },
            { data: 'provider_name', name: 'provider_name', width: '180px' },
            { data: 'category_name', name: 'category_name', width: '180px' },
            { data: 'product_name', name: 'product_name', width: '200px' },
            { data: 'checkout_name', name: 'checkout_name', width: '150px' },
            { data: 'mobile', name: 'mobile', width: '130px' },
            { data: 'email', name: 'email', width: '200px' },
            { 
                data: 'address',
                name: 'address',
                width: '250px',
                render: function(data) {
                    return `<span class="text-wrap d-block" style="white-space: normal; word-break: break-word;">${data}</span>`;
                }
            },
            { data: 'payment_method', name: 'payment_method', width: '150px' },
            { data: 'final_price', name: 'final_price', width: '120px' },
            { data: 'payment_status', name: 'payment_status', width: '150px' },
            { data: 'status', name: 'status', width: '150px' },
            { data: 'order_date', name: 'order_date', width: '180px' },
            { 
             data: 'arrival_date', 
             name: 'arrival_date', 
             width: '180px',
             render: function(data, type, row) {
                     let formattedDate = '';
                     if (data) {
                     let parts = data.split('-'); 
                     formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`; 
                    }

                     let loggedInUserType = "{{ auth()->user()->user_type }}"; 
                     let loggedInUserStatus = "{{ auth()->user()->serviceProvider->status ?? '' }}";
                     let isDisabled = (loggedInUserType === 'provider' && loggedInUserStatus !== 'approved');
                     return `<input type="date" class="form-control arrival-date" data-id="${row.id}" value="${formattedDate}" ${isDisabled ? 'disabled' : ''}>`;
              }
            },
            { 
             data: 'id',
             width: '100px',
             render: function(id, type, row) {
                     let buttons = '';
                     if (hasPermission('track order')) {
                     buttons += `
                               <button class="btn btn-info btn-sm track-order" data-id="${id}" >
                               <i class="bi bi-pin-map"></i>
                               </button> `;
                    }

                    if (hasPermission('delete order booking')) {
                     buttons += `
                              <button class="btn btn-danger btn-sm delete-booking" data-id="${id}">
                              <i class="bi bi-trash"></i> 
                              </button> `;
                     }
                     return buttons || 'No Actions'; 
               }

            }
                ],
             columnDefs: [
                 { targets: "_all", className: "text-center" } 
                 ]
});

    // ✅ Arrival Date Update
    $(document).on('change', '.arrival-date', function() {
        let orderId = $(this).data('id');
        let newDate = $(this).val();

        $.ajax({
            url: '{{ route("admin.updateArrivalDate") }}',
            type: 'POST',
            data: { id: orderId, arrival_date: newDate, _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Updated!', 'Arrival Date updated successfully.', 'success');
                } else {
                    Swal.fire('Error!', 'Failed to update Arrival Date.', 'error');
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                Swal.fire('Error!', 'Something went wrong.', 'error');
            }
        });
    });

    // ✅ Order Tracking
    let orderId = null;
    $(document).on('click', '.track-order', function() {
        orderId = $(this).data('id');

        $.get(`/admin/get-order-status/${orderId}`, function(response) {
            $('.step').removeClass('active');

            let stepIndex = -1;
            $('.step').each(function(index) {
                if ($(this).data('step') === response.tracking_status) {
                    stepIndex = index;
                }
                if (index <= stepIndex) {
                    $(this).addClass('active');
                }
            });

            let totalSteps = $('.step').length - 1;
            let progressPercent = (stepIndex / totalSteps) * 100;
            $('#progress').css('width', `${progressPercent}%`);

            $('#orderTrackingModal').modal('show');
        });
    });

    $('.step').on('click', function() {
        let selectedStep = $(this).data('step');

        $.ajax({
            url: '/admin/update-tracking-status',
            type: 'POST',
            data: { id: orderId, status: selectedStep, _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    Swal.fire('Updated!', 'Order status updated successfully.', 'success');
                    updateProgress(selectedStep);

                    setTimeout(function() {
                        $('#bookings-table').DataTable().ajax.reload(null, false);
                    }, 300);
                }
            },
            error: function(xhr) {
                Swal.fire('Error!', 'Failed to update status.', 'error');
            }
        });
    });

    // ✅ Progress Bar Update Function
    function updateProgress(currentStatus) {
        let stepIndex = -1;
        $('.step').removeClass('active');

        $('.step').each(function(index) {
            if ($(this).data('step') === currentStatus) {
                stepIndex = index;
            }
            if (index <= stepIndex) {
                $(this).addClass('active');
            }
        });

        let totalSteps = $('.step').length - 1;
        let progressPercent = (stepIndex / totalSteps) * 100;
        $('#progress').css('width', `${progressPercent}%`);
    }


// Delete booking
$(document).on('click', '.delete-booking', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: "Are you sure?",
            text: "This booking will be removed from the List.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, remove it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.deleteBooking") }}',
                    type: 'POST',
                    data: { id: id, _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire("Deleted!", response.message, "success");
                            $('#bookings-table').DataTable().ajax.reload();
                        }
                    }
                });
            }
        });
    });


});
    
</script>

@endsection
