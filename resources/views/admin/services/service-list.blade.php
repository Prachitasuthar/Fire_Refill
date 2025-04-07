@extends('admin-layouts.app')

@section('content')
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow p-4 w-75">
            <h2 class="mb-4 text-center">Service List</h2>
            <table id="services-table" class="table table-striped table-bordered" width="100%">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Provider Name</th>
                        <th>Business Name</th>
                        <th>Service Name</th>
                        <th>Sub Service Name</th>
                        {{-- <th>Image</th>
                        <th>Description</th> --}}
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>


    </div>
    <!-- View Modal -->
    <div class="modal fade" id="viewServiceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Service Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>ID:</strong> <span id="view_id"></span></p>
                    <p><strong>Provider Name:</strong> <span id="view_provider"></span></p>
                    <p><strong>Business Name:</strong> <span id="view_business"></span></p>
                    <p><strong>Service Name:</strong> <span id="view_service"></span></p>
                    <p><strong>Sub Service:</strong> <span id="view_sub_service"></span></p>
                    {{-- <p><strong>Description:</strong> <span id="view_description"></span></p>
                    <p><strong>Image:</strong> <br> <img id="view_image" src="" width="150"></p> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editServiceForm">
                        @csrf
                        <input type="hidden" id="edit_id">
                        <div class="mb-3">
                            <label>Service Name:</label>
                            <input type="text" id="edit_service_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Sub Service Name:</label>
                            <input type="text" id="edit_sub_service" class="form-control">
                        </div>
                        {{-- <div class="mb-3">
                            <label>Description:</label>
                            <textarea id="edit_description" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Image:</label>
                            <input type="file" id="edit_image" class="form-control">
                            <img id="preview_image" src="" width="100" class="mt-2">
                        </div> --}}
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        #services-table_wrapper {
            overflow-x: auto;
            width: 100%;
        }


        .dataTables_wrapper {
            width: 100%;
            overflow-x: auto;
        }

        #services-table {
            width: 100% !important;
            table-layout: fixed;
        }
    </style>




    {{-- <script> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        $(document).ready(function() {
            let table = $('#services-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                scrollY: "500px",
                scrollCollapse: true,
                fixedHeader: true,
                autoWidth: false,

                ajax: "{{ route('services.data') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'provider_name',
                        name: 'provider_name'
                    },
                    {
                        data: 'business_name',
                        name: 'business_name'
                    },
                    {
                        data: 'service_name',
                        name: 'service_name'
                    },
                    {
                        data: 'sub_service_name',
                        name: 'sub_service_name'
                    },
                    // {
                    //     data: 'service_image',
                    //     name: 'service_image',
                    //     orderable: false,
                    //     searchable: false,
                    //     render: function(data) {
                    //         return data ?
                    //             `<img src="{{ asset('') }}${data}" width="80" height="80" class="img-thumbnail">` :
                    //             'No Image';
                    //     }
                    // },
                    // {
                    //     data: 'description',
                    //     name: 'description',
                    //     render: function(data) {
                    //         let words = data.split(' ');
                    //         if (words.length > 5) {
                    //             words = words.slice(0, 5).join(' ') + '...';
                    //         } else {
                    //             words = data;
                    //         }
                    //         return words;
                    //     }
                    // },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let statusOptions = `<select class="status-dropdown" data-id="${row.id}">
                                                <option value="1" ${data == 1 ? 'selected' : ''}>Active</option>
                                                <option value="0" ${data == 0 ? 'selected' : ''}>Inactive</option>
                                              </select>`;
                            return statusOptions;
                        }
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $(document).on('change', '.status-dropdown', function() {
                let serviceId = $(this).data('id');
                let newStatus = $(this).val();

                $.ajax({
                    url: "{{ route('services.updateStatus') }}",
                    type: "POST",

                    data: {
                        id: serviceId,
                        status: newStatus,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire({
                            title: "Success!",
                            text: response.success,
                            icon: "success",
                            confirmButtonText: "OK"
                        });

                    },
                    error: function(xhr) {
                        alert("Error updating status!");
                    }
                });
            });

            // View Service
            $(document).on('click', '.view-service', function() {
                let data = $(this).data();
                $('#view_id').text(data.id);
                $('#view_provider').text(data.provider);
                $('#view_business').text(data.business);
                $('#view_service').text(data.name);
                $('#view_sub_service').text(data.subService);
                // $('#view_description').text(data.description);
                // $('#view_image').attr('src', data.image ? (data.image.startsWith('http') ? data.image :
                //     `{{ asset('') }}${data.image}`) : 'No Image');
                $('#viewServiceModal').modal('show');
            });

            $(document).on('click', '.edit-service', function() {
                let serviceId = $(this).data('id');

                $.ajax({
                    url: "/services/" + serviceId + "/edit",
                    type: "GET",
                    success: function(response) {
                        $('#edit_id').val(response.id);
                        $('#edit_service_name').val(response.service_name);
                        $('#edit_sub_service').val(response.sub_service_name);
                        // $('#edit_description').val(response.description);
                        // $('#view_image').attr('src', response.image ? `{{ asset('') }}` +
                        //     response.image : 'No Image');
                        $('#editServiceModal').modal('show');
                    }
                });
            });



            // Update Service via AJAX
            $('#editServiceForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData();
                formData.append('id', $('#edit_id').val());
                formData.append('service_name', $('#edit_service_name').val());
                formData.append('sub_service_name', $('#edit_sub_service').val());
                // formData.append('description', $('#edit_description').val());

                // if ($('#edit_image')[0].files[0]) {
                //     formData.append('image', $('#edit_image')[0].files[0]);
                // }

                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    url: "{{ route('services.update') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                $('#editServiceModal').modal('hide');
                                table.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                title: "Warning!",
                                text: response.message,
                                icon: "warning",
                                confirmButtonText: "OK"
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = "Error updating service!";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            title: "Error!",
                            text: errorMessage,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                });
            });

            $(document).on('click', '.delete-service', function() {
                let serviceId = $(this).data('id');

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
                        $.ajax({
                            url: `/services/delete/${serviceId}`,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire("Deleted!", response.message, "success");
                                $('#services-table').DataTable().ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire("Error!", "Failed to delete the service.",
                                    "error");
                            }
                        });
                    }
                });
            });

        });


        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("editServiceForm");

            const serviceName = document.getElementById("edit_service_name");
            const subService = document.getElementById("edit_sub_service");
            // const description = document.getElementById("edit_description");
            // const imageInput = document.getElementById("edit_image");
            const previewImage = document.getElementById("preview_image");

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

                if (!validateField(serviceName, serviceName.value.trim() === "", "Service Name is required")) {
                    isValid = false;
                }
                if (!validateField(subService, subService.value.trim() === "", "Sub Service Name is required")) {
                    isValid = false;
                }
                // if (!validateField(description, description.value.trim() === "", "Description is required")) {
                //     isValid = false;
                // }

                // // Image Validation
                // if (imageInput.files.length > 0) {
                //     const file = imageInput.files[0];
                //     const allowedTypes = ["image/png", "image/jpeg", "image/jpg"];
                //     if (!allowedTypes.includes(file.type)) {
                //         showError(imageInput, "Only PNG, JPG, JPEG images are allowed");
                //         isValid = false;
                //     } else {
                //         removeError(imageInput);
                //     }
                // }

                return isValid;
            }

            form.addEventListener("submit", function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                }
            });

            [serviceName, subService, description, imageInput].forEach(input => {
                input.addEventListener("input", () => validateForm());
                input.addEventListener("blur", () => validateForm());
            });

            imageInput.addEventListener("change", function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewImage.classList.remove("d-none");
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>
@endsection
