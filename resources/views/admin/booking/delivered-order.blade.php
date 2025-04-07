@extends('admin-layouts.app')

@section('content')
<div class="container mt-5 d-flex justify-content-center">
    <div class="card shadow p-4 w-100">
        <div class="d-flex align-items-center position-relative mb-3">
            <h2 class="mb-0 text-center flex-grow-1">Delivered Order List</h2>
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
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>





<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">


<!-- DataTables & SweetAlert -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>
   $(document).ready(function() {
    $('#bookings-table').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true, 
        autoWidth: false, 
        ajax: '{{ route("admin.deliveredOrder") }}',
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
                render: function(data) {
                        if (!data) return ''; 
                        let parts = data.split('-');  
                        return `${parts[2]}-${parts[1]}-${parts[0]}`; 
                 }
            },

        ],
             columnDefs: [
                 { targets: "_all", className: "text-center" } 
            ]
});



    });

    
</script>

@endsection
