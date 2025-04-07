@extends('admin-layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="card shadow p-4">
            <div class="d-flex align-items-center position-relative mb-3">
                <h2 class="mb-0 text-center flex-grow-1">Fire-Watermist List</h2>
                @php
                    $provider = \App\Models\ServiceProvider::where('user_id', auth()->id())->first();
                @endphp

                @can('create watermist')
                    @if (auth()->user()->user_type === 'admin')
                        <a href="{{ route('fire_watermist.create') }}" class="btn btn-primary btn-sm custom-btn">
                            + Add Fire-Watermist/CAFS
                        </a>
                    @elseif(auth()->user()->user_type === 'provider')
                        @if ($provider && ($provider->status === 'rejected' || $provider->status === 'pending'))
                            <span class="btn btn-secondary btn-sm custom-btn disabled" style="cursor: not-allowed; opacity: 0.5;"
                                data-bs-toggle="tooltip"
                                title="Your account is {{ $provider->status }}. Please wait for approval.">
                                + Add Fire-Watermist/CAFS (Not Allowed)
                            </span>
                        @else
                            <a href="{{ route('fire_watermist.create') }}" class="btn btn-primary btn-sm custom-btn">
                                + Add Fire-Watermist/CAFS
                            </a>
                        @endif
                    @endif
                @endcan


            </div>

            <div class="table-responsive">
                <table id="fireWatermistTable" class="table table-bordered display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Provider</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Technology Type</th>
                            <th>Nozzle Type</th>
                            <th>Working Pressure (in bar)</th>
                            <th>Droplet Size (in micros)</th>
                            <th>Flow Rate (in LPM)</th>
                            <th>Application Area</th>
                            <th>Stock</th>
                            <th>Warranty</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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

 

    <!-- Edit Fire Extinguisher Modal -->
    <div class="modal fade" id="editFireWatermistModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Fire Watermist/CAFS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editFireWatermistForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_fire_id" name="id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Fire Watermist/CAFS Name:</label>
                                <input type="text" id="edit_fire_name" name="name" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Price (₹):</label>
                                <input type="number" id="edit_fire_price" name="price" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Description:</label>
                            <textarea id="edit_fire_description" name="description" class="form-control"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Technology Type:</label>
                                <select name="technology_type" id="edit_technology_type" class="form-select">
                                    <option value="">Select Technology Type</option>
                                    <option value="Watermist">Watermist</option>
                                    <option value="CAFS">CAFS</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Nozzle Type:</label>
                                {{-- <input type="text" id="edit_nozzle_type" name="nozzle_type" class="form-control"> --}}
                                <select name="nozzle_type" id="edit_nozzle_type" class="form-select">
                                    <option value="">Select Nozzle Type</option>
                                    <option value="Fixed">Fixed</option>
                                    <option value="Portable">Portable</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Droplet Size (in microns):</label>
                                <input type="text" id="edit_droplet_size" name="droplet_size" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Flow Rate (in LPM):</label>
                                <input type="number" id="edit_flow_rate" name="flow_rate" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Working Pressure (in bar):</label>
                                <input type="text" id="edit_working_pressure" name="working_pressure"
                                    class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Application Area (eg: m² or ft²):</label>
                                <input type="text" id="edit_application_area" name="application_area"
                                    class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Stock:</label>
                                <input type="number" id="edit_fire_stock" name="stock" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Warranty:</label>
                                <input type="number" id="edit_fire_warranty" name="warranty" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6 position-relative">
                            <label>Current Image:</label>
                            <br>
                            <div class="image-container position-relative d-inline-block mt-2">
                                <img id="edit_fire_image_preview" src="" width="100" class="border rounded">
                                <button type="button" id="remove_image_btn" class="position-absolute top-0 end-0"
                                    style="
                                    width: 20px; 
                                    height: 20px; 
                                    background-color: red; 
                                    color: white; 
                                    border: none; 
                                    border-radius: 50%; 
                                    font-weight: bold;
                                    display: flex; 
                                    align-items: center; 
                                    justify-content: center;
                                    font-size: 14px;
                                    cursor: pointer;
                                    transform: translate(50%, -50%);
                                ">
                                    ✖
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Change Image:</label>
                            <input type="file" id="edit_fire_image" name="image" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary">Update Fire Watermist/CAFS</button>
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
        var userPermissions = @json(auth()->user()->getAllPermissions()->pluck('name')->toArray());

        function hasPermission(permission) {
            return userPermissions.includes(permission);
        }

        $(document).ready(function() {
            let table = $('#fireWatermistTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('fire_watermist.data') }}',
                scrollX: true,
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'provider_name',
                        name: 'provider_name'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            if (data) {
                                return `<img src="${data}" width="50" height="50" style="object-fit:cover;">`;
                            }
                            return 'No Image';
                        }
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        render: function(data) {
                            if (!data) return "";
                            let words = data.split(" ");
                            if (words.length > 3) {
                                return words.slice(0, 3).join(" ") + " ...";
                            }
                            return data;
                        }
                    },
                    {
                        data: 'technology_type',
                        name: 'technology_type'
                    },
                    {
                        data: 'nozzle_type',
                        name: 'nozzle_type'
                    },
                    {
                        data: 'working_pressure',
                        name: 'working_pressure'
                    },
                    {
                        data: 'droplet_size',
                        name: 'droplet_size'
                    },
                    {
                        data: 'flow_rate',
                        name: 'flow_rate'
                    },
                    {
                        data: 'application_area',
                        name: 'application_area'
                    },
                    {
                        data: 'stock',
                        name: 'stock'
                    },
                    {
                        data: 'warranty',
                        name: 'warranty'
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

                            if (hasPermission('edit watermist')) {
                                buttons += `
            <button class="btn btn-sm btn-warning edit-fire-btn" data-id="${data}" ${isDisabled ? 'disabled' : ''}>
                <i class="fas fa-edit"></i>
            </button>
        `;
                            }

                            if (hasPermission('delete watermist')) {
                                buttons += `
            <button class="btn btn-sm btn-danger delete-fire-btn" data-id="${data}" ${isDisabled ? 'disabled' : ''}>
                <i class="fas fa-trash"></i>
            </button>
        `;
                            }

                            return buttons || 'No Actions';
                        }

                    }

                ]
            });
            $(document).on('click', '.edit-fire-btn', function() {
                let id = $(this).data('id');

                $.get(`/fire-watermist/${id}/edit`, function(data) {
                    $('#edit_fire_id').val(data.id);
                    $('#edit_fire_name').val(data.name);
                    $('#edit_fire_price').val(data.price);
                    $('#edit_fire_description').val(data.description);
                    $('#edit_technology_type').val(data.technology_type);
                    $('#edit_nozzle_type').val(data.nozzle_type);
                    $('#edit_working_pressure').val(data.working_pressure);
                    $('#edit_droplet_size').val(data.droplet_size);
                    $('#edit_flow_rate').val(data.flow_rate);
                    $('#edit_application_area').val(data.application_area);
                    $('#edit_fire_stock').val(data.stock);
                    $('#edit_fire_warranty').val(data.warranty);


                    $('#edit_fire_image_preview').attr('src', data.image ? (data.image.startsWith(
                            'http') ? data.image :
                        `${window.location.origin}/${data.image}`) : 'No Image');

                    $('#editFireWatermistModal').modal('show');
                });
            });



            $(document).ready(function() {
                let imageRemoved = false; 
                $('#remove_image_btn').click(function() {
                    $('#edit_fire_image_preview').attr('src', '');
                    imageRemoved = true; 
                    $(this).hide();
                });

                $('#edit_fire_image').on('change', function(event) {
                    if (event.target.files.length > 0) {
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            $('#edit_fire_image_preview').attr('src', e.target.result);
                            $('#remove_image_btn').show(); 
                            imageRemoved = false; 
                        };
                        reader.readAsDataURL(event.target.files[0]);
                    }
                });


                $('#editFireWatermistForm').on('submit', function(e) {
                    e.preventDefault();
                    let id = $('#edit_fire_id').val();
                    let formData = new FormData(this);
                    formData.append('_method', 'PUT');

                    if (imageRemoved) {
                        formData.append('remove_image', '1'); 
                    }

                    $.ajax({
                        url: `/fire-watermist/${id}`,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                'content') 
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Updated!',
                                text: response.success,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            $('#editFireWatermistModal').modal(
                            'hide'); 
                            $('#fireWatermistTable').DataTable().ajax.reload(null,
                                false);
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                            console.log(xhr.responseText);
                        }
                    });
                });
            });


            $(document).on('click', '.delete-fire-btn', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/fire-watermist/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', response.success, 'success');
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });

        });



        document.addEventListener("DOMContentLoaded", function() {
            const editForm = document.getElementById("editFireExtinguisherForm");

            const requiredFields = [
                "edit_fire_name",
                "edit_fire_price",
                "edit_fire_description",

            ];

            function showValidationMessage(input, message) {
                let errorSpan = input.nextElementSibling;
                if (!errorSpan || !errorSpan.classList.contains("error-message")) {
                    errorSpan = document.createElement("span");
                    errorSpan.classList.add("error-message", "text-danger");
                    input.parentNode.appendChild(errorSpan);
                }
                errorSpan.innerText = message;
            }

            function removeValidationMessage(input) {
                let errorSpan = input.nextElementSibling;
                if (errorSpan && errorSpan.classList.contains("error-message")) {
                    errorSpan.remove();
                }
            }

            requiredFields.forEach((id) => {
                const input = document.getElementById(id);
                input.addEventListener("input", function() {
                    if (this.value.trim() === "") {
                        showValidationMessage(this, "This field is required.");
                    } else {
                        removeValidationMessage(this);
                    }
                });
            });

            document.getElementById("edit_fire_price").addEventListener("input", function() {
                if (this.value < 0) {
                    showValidationMessage(this, "Price cannot be negative.");
                } else {
                    removeValidationMessage(this);
                }
            });

            document.getElementById("edit_fire_stock").addEventListener("input", function() {
                if (this.value < 0 || !Number.isInteger(Number(this.value))) {
                    showValidationMessage(this, "Stock must be a non-negative whole number.");
                } else {
                    removeValidationMessage(this);
                }
            });

            document.getElementById("edit_fire_warranty").addEventListener("input", function() {
                const warrantyRegex = /^[a-zA-Z0-9\s]+$/;
                if (this.value.trim() === "") {
                    showValidationMessage(this, "Warranty is required.");
                } else if (!warrantyRegex.test(this.value)) {
                    showValidationMessage(this, "Warranty must be alphanumeric.");
                } else {
                    removeValidationMessage(this);
                }
            });

            ["edit_flow_rate", "edit_fire_stock", "edit_fire_price", "edit_fire_warranty"].forEach((id) => {
                document.getElementById(id).addEventListener("input", function() {
                    if (this.value !== "" && isNaN(this.value)) {
                        showValidationMessage(this, "This field must be a valid number.");
                    } else {
                        removeValidationMessage(this);
                    }
                });
            });

            document.getElementById("edit_fire_image").addEventListener("change", function() {
                const file = this.files[0];
                if (file) {
                    const validExtensions = ["image/jpeg", "image/png", "image/jpg", "image/avif"];
                    if (!validExtensions.includes(file.type)) {
                        showValidationMessage(this, "Only JPG, JPEG, AVIF or PNG files are allowed.");
                        this.value = "";
                    } else {
                        removeValidationMessage(this);
                    }
                }
            });

            editForm.addEventListener("submit", function(event) {
                let isValid = true;

                requiredFields.forEach((id) => {
                    const input = document.getElementById(id);
                    if (input.value.trim() === "") {
                        showValidationMessage(input, "This field is required.");
                        isValid = false;
                    }
                });

                const priceInput = document.getElementById("edit_fire_price");
                if (priceInput.value < 0) {
                    showValidationMessage(priceInput, "Price cannot be negative.");
                    isValid = false;
                }

                const stockInput = document.getElementById("edit_fire_stock");
                if (stockInput.value < 0 || !Number.isInteger(Number(stockInput.value))) {
                    showValidationMessage(stockInput, "Stock must be a non-negative whole number.");
                    isValid = false;
                }

                const warrantyInput = document.getElementById("edit_fire_warranty");
                const warrantyRegex = /^[a-zA-Z0-9\s]+$/;
                if (!warrantyRegex.test(warrantyInput.value)) {
                    showValidationMessage(warrantyInput, "Warranty must be alphanumeric.");
                    isValid = false;
                }

                ["edit_flow_rate", "edit_fire_stock", "edit_fire_price", "edit_fire_warranty"].forEach((
                    id) => {
                        const input = document.getElementById(id);
                        if (input.value !== "" && isNaN(input.value)) {
                            showValidationMessage(input, "This field must be a valid number.");
                            isValid = false;
                        }
                    });

                const imageInput = document.getElementById("edit_fire_image");
                if (imageInput.files.length > 0) {
                    const file = imageInput.files[0];
                    const validExtensions = ["image/jpeg", "image/png", "image/jpg", "image/avif"];
                    if (!validExtensions.includes(file.type)) {
                        showValidationMessage(imageInput, "Only JPG, JPEG, AVIF or PNG files are allowed.");
                        isValid = false;
                    }
                }

                if (!isValid) {
                    event.preventDefault();
                }
            });
        });
    </script>

@endsection
