@extends('admin-layouts.app')

@section('content')
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow p-4 w-75"> 
            <h2 class="mb-4 text-center">Request Service</h2>
            <table class="table table-bordered" id="serviceRequestsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Service</th>
                        <th>Sub-Service</th>
                        <th>Provider</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th style="width: 100px;">Actions</th> 
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Deny Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Deny Service Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to deny this service request?</p>
                    <input type="hidden" id="deleteRequestId">
                    <input type="hidden" id="deleteUserEmail">
                    <textarea id="deleteReason" class="form-control" placeholder="Enter reason for denial..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Send Email & Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loader  -->
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            let table = $('#serviceRequestsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.service-requests.data') }}",
                    type: "GET"
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
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
                        data: 'contact'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });

            $(document).on('click', '.acceptRequest', function() {
                let requestId = $(this).data('id');
                let userEmail = $(this).data('email');

                $('#loader').show();

                $.ajax({
                    url: "{{ route('admin.service-requests.accept') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: requestId,
                        email: userEmail
                    },
                    success: function(response) {
                        $('#loader').hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        table.ajax.reload();
                    },
                    error: function(error) {
                        $('#loader').hide();

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error occurred while processing request.'
                        });
                    }
                });
            });


            $(document).on('click', '.deleteRequest', function() {
                let requestId = $(this).data('id');
                let userEmail = $(this).data('email');
                $('#deleteRequestId').val(requestId);
                $('#deleteUserEmail').val(userEmail);
                $('#deleteModal').modal('show');
            });

            $('#confirmDelete').click(function() {
                let requestId = $('#deleteRequestId').val();
                let userEmail = $('#deleteUserEmail').val();
                let reason = $('#deleteReason').val();

                if (!reason) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Please provide a reason for denial.'
                    });
                    return;
                }
                $('#loader').show();
                $.ajax({
                    url: "{{ route('admin.service-requests.delete') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: requestId,
                        email: userEmail,
                        reason: reason
                    },
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        table.ajax.reload();
                        $('#loader').hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(error) {
                        $('#loader').hide();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error occurred while deleting request.'
                        });
                    }
                });
            });
        });
    </script>


    <style>
        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .acceptRequest,
        .deleteRequest {
            padding: 5px 10px;
        }
    </style>
@endsection
