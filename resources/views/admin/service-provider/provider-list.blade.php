@extends('admin-layouts.app')

@section('content')
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow p-4 w-75">
            <h2 class="mb-4 text-center">Service Providers</h2>

            <a href="{{ route('providers.export-pdf') }}" class="btn btn-danger btn-sm custom-btn"
                style="padding: 0.5rem 1rem;">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>



            <table class="table table-bordered" id="provider-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Business Name</th>
                        <th>License</th>
                        @if (auth()->user()->user_type === 'admin')
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
            </table>
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

        <!-- Edit Provider Modal -->
        <div class="modal fade" id="editProviderModal" tabindex="-1" aria-labelledby="editProviderModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="editProviderModalLabel">Edit Provider</h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editProviderForm">
                            @csrf
                            <input type="hidden" id="provider_id">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" id="first_name" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" id="last_name" class="form-control">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="mobile_no" class="form-label">Phone</label>
                                    <input type="text" id="mobile_no" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="business_name" class="form-label">Business Name</label>
                                    <input type="text" id="business_name" class="form-control">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea id="address" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="license" class="form-label">License</label>
                                <input type="file" id="license" class="form-control">
                                <div class="mt-2">
                                    <a id="licenseLink" href="#" style="display: none;">
                                        <img id="licensePreview" src="" alt="License Image" class="img-thumbnail"
                                            style="width: 150px; height: auto; display: none;">
                                    </a>
                                    <iframe id="licenseIframe"
                                        style="width: 100%; height: 400px; display: none; border: 1px solid #ddd;"></iframe>
                                    <p id="current-license-info" class="mt-2"></p>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Update</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>





        <!-- DataTables -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


        <script>
            $(document).ready(function() {
                let userType = "{{ auth()->user()->user_type }}"; 

                
                let columns = [{
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
                    },
                    {
                        data: 'business_name',
                        name: 'business_name'
                    }, 
                    {
                        data: 'license',
                        name: 'license'
                    }
                ];

               
                if (userType === "admin") {
                    columns.push({
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    });
                }

                var table = $('#provider-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('admin.providers.index') }}", 
                    columns: columns
                });

                // Open Edit Modal
                $(document).on('click', '.edit-provider', function() {
                    var providerId = $(this).data('id');
                    $.ajax({
                        url: "/providers/" + providerId + "/edit",
                        method: "GET",
                        success: function(data) {
                            $('#provider_id').val(data.id);
                            $('#first_name').val(data.first_name);
                            $('#last_name').val(data.last_name);
                            $('#mobile_no').val(data.mobile_no);
                            $('#address').val(data.address);
                            $('#business_name').val(data.business_name); 

                            if (data.license) {
                                let fileExtension = data.license.split('.').pop().toLowerCase();
                                let licensePreview = $('#licensePreview');
                                let licenseLink = $('#licenseLink');
                                let licenseIframe = $('#licenseIframe');

                                if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                                    licensePreview.attr('src', data.license).show();
                                    licenseLink.attr('href', data.license).show();
                                    licenseIframe.hide();
                                    $('#current-license-info').hide();
                                } else {
                                    licensePreview.hide();
                                    licenseLink.hide();
                                    $('#current-license-info').hide();
                                    licenseIframe.attr('src', data.license).show();
                                }
                            } else {
                                $('#licensePreview').hide();
                                $('#licenseLink').hide();
                                $('#licenseIframe').hide();
                                $('#current-license-info').html('No license uploaded.').show();
                            }

                            $('#editProviderModal').modal('show');
                        },
                        error: function() {
                            alert("Error fetching provider details!");
                        }
                    });

                });
                // Submit Update For
                $('#editProviderForm').submit(function(e) {
                    e.preventDefault();
                    var providerId = $('#provider_id').val();

                    var formData = new FormData();
                    formData.append('first_name', $('#first_name').val());
                    formData.append('last_name', $('#last_name').val());
                    formData.append('mobile_no', $('#mobile_no').val());
                    formData.append('address', $('#address').val());
                    formData.append('business_name', $('#business_name').val());

                    if ($('#license')[0].files[0]) {
                        formData.append('license', $('#license')[0].files[0]); 
                    }


                    formData.append('_method', 'PUT');
                    formData.append('_token', "{{ csrf_token() }}");

                    $.ajax({
                        url: "/providers/" + providerId + "/update",
                        method: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            Swal.fire({
                                title: "Updating...",
                                text: "Please wait while we update provider details.",
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: "success",
                                title: "Updated Successfully!",
                                text: response.success,
                                showConfirmButton: false,
                                timer: 2000
                            });

                            $('#editProviderModal').modal('hide');
                            table.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: "error",
                                title: "Update Failed!",
                                text: "Something went wrong. Please try again.",
                            });
                        }
                    });
                });


                // Delete Provider
                //     $(document).on('click', '.delete-provider', function() {
                //     var providerId = $(this).data('id');

                //     Swal.fire({
                //         title: "Are you sure?",
                //         text: "You won't be able to revert this!",
                //         icon: "warning",
                //         showCancelButton: true,
                //         confirmButtonColor: "#d33",
                //         cancelButtonColor: "#3085d6",
                //         confirmButtonText: "Yes, delete it!"
                //     }).then((result) => {
                //         if (result.isConfirmed) {
                //             $.ajax({
                //                 url: "/providers/" + providerId + "/delete",
                //                 method: "POST",
                //                 data: {
                //                     _token: "{{ csrf_token() }}",
                //                     _method: "DELETE"
                //                 },
                //                 success: function(response) {
                //                     Swal.fire({
                //                         title: "Deleted!",
                //                         text: response.success,
                //                         icon: "success",
                //                         timer: 2000,
                //                         showConfirmButton: false
                //                     });

                //                     table.ajax.reload(null, false); // Reload table without resetting pagination
                //                 },
                //                 error: function(xhr) {
                //                     Swal.fire({
                //                         title: "Error!",
                //                         text: "Delete failed! " + xhr.responseText,
                //                         icon: "error"
                //                     });
                //                 }
                //             });
                //         }
                //     });
                // });


                // Restore Provider
                $(document).on('click', '.restore-provider', function() {
                    var providerId = $(this).data('id');

                    if (confirm("Are you sure you want to restore this provider?")) {
                        $.ajax({
                            url: "/providers/" + providerId + "/restore",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                alert(response.success);
                                table.ajax.reload(null,
                                    false); 
                            },
                            error: function(xhr) {
                                alert("Restore failed! " + xhr.responseText);
                            }
                        });
                    }
                });
            });
        </script>
    @endsection
