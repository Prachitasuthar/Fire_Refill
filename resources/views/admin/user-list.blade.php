@extends('admin-layouts.app')

@section('content')
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow p-4 w-75">
            <h2 class="mb-4 text-center">User List</h2>

            <a href="{{ route('users.export-pdf') }}" class="btn btn-danger btn-sm custom-btn" style="padding: 0.5rem 1rem;">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>

            <table class="table table-bordered" id="user-table" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        @if (auth()->user()->user_type === 'admin')
                            <th>Email Verified</th>
                        @endif
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
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


    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="user_id">

                        <div class="mb-3">
                            <label>First Name:</label>
                            <input type="text" id="first_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Last Name:</label>
                            <input type="text" id="last_name" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Phone:</label>
                            <input type="text" id="mobile_no" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Address:</label>
                            <textarea id="address" class="form-control"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery & DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



    <script>
        $(document).ready(function() {
            var userType = "{{ auth()->user()->user_type }}"; 

            var columns = [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'first_name',
                    name: 'first_name'
                },
                {
                    data: 'last_name',
                    name: 'last_name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'mobile_no',
                    name: 'mobile_no'
                },
                {
                    data: 'address',
                    name: 'address'
                }
            ];

            if (userType === 'admin') {
                columns.push({

                    data: 'email_verified',
                    name: 'email_verified',
                    orderable: false,
                    searchable: false
                });
            }

            columns.push({
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false
            });

            var table = $('#user-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.users.index') }}",
                columns: columns
            });

            if (userType === 'admin') {
                $(document).on('change', '.update-verification-status', function() {
                    var $this = $(this); 
                    var userId = $this.data('id');
                    var newStatus = $this.val();

                    $this.prop('disabled', true);

                    $.ajax({
                        url: "/admin/users/" + userId + "/update-email-verification",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            is_email_verified: newStatus
                        },
                        success: function(response) {
                            alert(response.success);
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            alert("Failed to update verification status! " + xhr.responseText);
                        },
                        complete: function() {
                            $this.prop('disabled', false); 
                        }
                    });
                });
            }

            $(document).on('click', '.edit-user', function() {
                var userId = $(this).data('id');

                $('#editUserForm')[0].reset(); 
                $('#user_id').val(userId);

                $.ajax({
                    url: "/users/" + userId + "/edit",
                    method: "GET",
                    beforeSend: function() {
                        $('#editUserModal').modal('show');
                    },
                    success: function(data) {
                        $('#first_name').val(data.first_name);
                        $('#last_name').val(data.last_name);
                        $('#mobile_no').val(data.mobile_no);
                        $('#address').val(data.address);
                    },
                    error: function() {
                        alert("Error fetching user details!");
                    }
                });
            });

            $('#editUserForm').on('submit', function(e) {
                e.preventDefault();
                var userId = $('#user_id').val();

                $.ajax({
                    url: "/users/" + userId + "/update",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: "PUT",
                        first_name: $('#first_name').val(),
                        last_name: $('#last_name').val(),
                        mobile_no: $('#mobile_no').val(),
                        address: $('#address').val()
                    },
                    beforeSend: function() {
                        $('#editUserForm button[type="submit"]').prop('disabled', true).text(
                            'Updating...');
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Great!",
                            text: response.success,
                            icon: "success",
                            confirmButtonText: "OK"
                        });

                        $('#editUserModal').modal('hide');
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: "Error!",
                            text: "Update failed! " + xhr.responseText,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    },
                    complete: function() {
                        $('#editUserForm button[type="submit"]').prop('disabled', false).text(
                            'Update');
                    }
                });
            });

            $(document).on('click', '.delete-user', function() {
                var userId = $(this).data('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You can be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/users/" + userId + "/delete",
                            method: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response.success,
                                    icon: "success",
                                    confirmButtonText: "OK"
                                });

                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Delete failed! " + xhr.responseText,
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            }
                        });
                    }
                });
            });


            $(document).on('click', '.restore-user', function() {
                var userId = $(this).data('id');

                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to restore this user?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, restore it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/users/" + userId + "/restore",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Restored!",
                                    text: response.success,
                                    icon: "success",
                                    confirmButtonText: "OK"
                                });

                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Restore failed! " + xhr.responseText,
                                    icon: "error",
                                    confirmButtonText: "OK"
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
