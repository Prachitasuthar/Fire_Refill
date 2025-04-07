@extends('admin-layouts.app')

@section('content')
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow p-4 w-75">
            <h2 class="mb-4 text-center">Accepted Service Requests</h2>
            <table class="table table-bordered" id="acceptedRequestsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Service</th>
                        <th>Sub-Service</th>
                        <th>Provider</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div id="loader" class="loader-overlay" style="display: none;">
        <div class="loader"></div>
    </div>

    <style>
        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loader {
            width: 60px;
            height: 60px;
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <!-- jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let table = $('#acceptedRequestsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.service-requests.accepted') }}",
                    type: "GET"
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'service_name'
                    },
                    {
                        data: 'sub_service_name'
                    },
                    {
                        data: 'provider_name'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'contact'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });


            // Handle delete request
            $(document).on('click', '.deleteRequest', function() {
                let requestId = $(this).data('id');
                let email = $(this).data('email');


                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#loader').show();
                        $.ajax({
                            url: "{{ route('admin.service-requests.delete-accepted') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: requestId
                            },
                            success: function(response) {
                                $('#loader').hide();
                                Swal.fire("Deleted!", response.message, "success");
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                $('#loader').hide();
                                Swal.fire("Error!", xhr.responseJSON.message, "error");
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
