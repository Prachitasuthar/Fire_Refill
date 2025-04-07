@extends('admin-layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="card shadow p-4">
            <div class="d-flex align-items-center position-relative mb-3">
                <h2 class="mb-0 text-center flex-grow-1">Accessories List</h2>
                @php
                    $provider = \App\Models\ServiceProvider::where('user_id', auth()->id())->first();
                @endphp

                @can('create accessories')
                    @if (auth()->user()->user_type === 'admin')
                        <a href="{{ route('accessories.create') }}" class="btn btn-primary btn-sm custom-btn">
                            + Add Accessories
                        </a>
                    @elseif(auth()->user()->user_type === 'provider')
                        @if ($provider && ($provider->status === 'rejected' || $provider->status === 'pending'))
                            <span class="btn btn-secondary btn-sm custom-btn disabled" style="cursor: not-allowed; opacity: 0.5;"
                                data-bs-toggle="tooltip"
                                title="Your account is {{ $provider->status }}. Please wait for approval.">
                                + Add Accessories (Not Allowed)
                            </span>
                        @else
                            <a href="{{ route('accessories.create') }}" class="btn btn-primary btn-sm custom-btn">
                                + Add Accessories
                            </a>
                        @endif
                    @endif
                @endcan


            </div>

            <div class="table-responsive">
                <table id="accessoriesTable" class="table table-bordered display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Provider</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Weight</th>
                            <th>Power Source</th>
                            <th>Operating Voltage</th>
                            <th>Material</th>
                            <th>Working Temperature</th>
                            <th>IP Routing</th>
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


    <!-- Edit Accessory Modal -->
    <div class="modal fade" id="editAccessoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Accessory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editAccessoryForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Accessory Name:</label>
                                <input type="text" id="edit_name" name="name" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Price:</label>
                                <input type="number" id="edit_price" name="price" class="form-control">
                            </div>
                        </div>
                        <div class="row">

                            <div>
                                <label>Description:</label>
                                <textarea id="edit_description" name="description" class="form-control"></textarea>
                            </div>
                        </div>

                        <br>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Power Source (e.g., Battery, AC/DC):</label>
                                <input type="text" id="edit_power_source" name="power_source" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Operating Voltage (v):</label>
                                <input type="text" id="edit_operating_voltage" name="operating_voltage"
                                    class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Material (e.g., Stainless Steel, Aluminum):</label>
                                <input type="text" id="edit_material" name="material" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Working Temperature (°C):</label>
                                <input type="text" id="edit_working_temperature" name="working_temprature"
                                    class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>IP Routing (e.g., IP67, IP54):</label>
                                <input type="text" id="edit_ip_routing" name="ip_routing" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Weight (kg):</label>
                                <input type="text" id="edit_weight" name="weight" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Warranty:</label>
                                <input type="text" id="edit_warranty" name="warranty" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Stock (units):</label>
                                <input type="number" id="edit_stock" name="stock" class="form-control">
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6 position-relative">
                                <label>Current Image:</label>
                                <br>
                                <div class="image-container position-relative d-inline-block mt-2">
                                    <img id="edit_image_preview" src="" width="100" class="border rounded">
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
                                <input type="file" id="edit_image" name="image" class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Accessory</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


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
            let table = $('#accessoriesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('accessories.data') }}',
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
                        data: 'weight',
                        name: 'weight'
                    },
                    {
                        data: 'power_source',
                        name: 'power_source'
                    },
                    {
                        data: 'operating_voltage',
                        name: 'operating_voltage'
                    },
                    {
                        data: 'material',
                        name: 'material'
                    },
                    {
                        data: 'working_temprature',
                        name: 'working_temprature'
                    },
                    {
                        data: 'IP_routing',
                        name: 'IP_routing'
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
                            let disabledAttr = isDisabled ? 'disabled' : '';

                            if (hasPermission('edit accessories')) {
                                buttons += `
            <button class="btn btn-sm btn-warning edit-btn mx-1" data-id="${data}" ${disabledAttr}>
                <i class="fas fa-edit"></i>
            </button>
        `;
                            }

                            if (hasPermission('delete accessories')) {
                                buttons += `
            <button class="btn btn-sm btn-danger delete-btn mx-1" data-id="${data}" ${disabledAttr}>
                <i class="fas fa-trash"></i>
            </button>
        `;
                            }

                            return buttons || 'No Actions';
                        }

                    }

                ]
            });

            // Edit Accessory
            $(document).on('click', '.edit-btn', function() {
                let id = $(this).data('id');

                $.get(`/accessories/${id}/edit`, function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_name').val(data.name);
                    $('#edit_price').val(data.price);
                    $('#edit_description').val(data.description);
                    $('#edit_weight').val(data.weight);
                    $('#edit_power_source').val(data.power_source);
                    $('#edit_operating_voltage').val(data.operating_voltage);
                    $('#edit_material').val(data.material);
                    $('#edit_working_temperature').val(data.working_temprature);
                    $('#edit_ip_routing').val(data.IP_routing);
                    $('#edit_stock').val(data.stock);
                    $('#edit_warranty').val(data.warranty);

                    $('#edit_image_preview').attr('src', data.image ? (data.image.startsWith(
                            'http') ? data.image : `${window.location.origin}/${data.image}`) :
                        'No Image');

                    $('#editAccessoryModal').modal('show');
                });
            });
            $(document).ready(function() {
                let imageRemoved = false; 


                $('#remove_image_btn').click(function() {
                    $('#edit_image_preview').attr('src', ''); 
                    imageRemoved = true; 
                    $(this).hide(); 
                });

                $('#edit_image').on('change', function(event) {
                    if (event.target.files.length > 0) {
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            $('#edit_image_preview').attr('src', e.target.result);
                            $('#remove_image_btn').show(); 
                            imageRemoved = false; 
                        };
                        reader.readAsDataURL(event.target.files[0]);
                    }
                });

                $('#editAccessoryForm').on('submit', function(e) {
                    e.preventDefault();
                    let id = $('#edit_id').val();
                    let formData = new FormData(this);
                    formData.append('_method', 'PUT'); 

                    if (imageRemoved) {
                        formData.append('remove_image', '1'); 
                    }

                    $.ajax({
                        url: `/accessories/${id}`,
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

                            $('#editAccessoryModal').modal(
                            'hide'); 
                            $('#accessoriesTable').DataTable().ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                            console.log(xhr.responseText);
                        }
                    });
                });
            });


            // Delete Accessory
            $(document).on('click', '.delete-btn', function() {
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
                            url: `/accessories/${id}`,
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
            const editAccessoryForm = document.getElementById("editAccessoryForm");

            const requiredFields = [
                "edit_name",
                "edit_price",
                "edit_description",
                "edit_stock",
                "edit_warranty"
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

            document.getElementById("edit_price").addEventListener("input", function() {
                if (this.value < 0) {
                    showValidationMessage(this, "Price cannot be negative.");
                } else {
                    removeValidationMessage(this);
                }
            });

            document.getElementById("edit_stock").addEventListener("input", function() {
                if (this.value < 0) {
                    showValidationMessage(this, "Stock cannot be negative.");
                } else {
                    removeValidationMessage(this);
                }
            });

            document.getElementById("edit_warranty").addEventListener("input", function() {
                const warrantyRegex = /^[a-zA-Z0-9\s]+$/;
                if (this.value.trim() === "") {
                    showValidationMessage(this, "Warranty is required.");
                } else if (!warrantyRegex.test(this.value)) {
                    showValidationMessage(this, "Warranty must be alphanumeric.");
                } else {
                    removeValidationMessage(this);
                }
            });

            editAccessoryForm.addEventListener("submit", function(event) {
                let isValid = true;

                requiredFields.forEach((id) => {
                    const input = document.getElementById(id);
                    if (input.value.trim() === "") {
                        showValidationMessage(input, "This field is required.");
                        isValid = false;
                    }
                });

                const priceInput = document.getElementById("edit_price");
                if (priceInput.value < 0) {
                    showValidationMessage(priceInput, "Price cannot be negative.");
                    isValid = false;
                }

                const stockInput = document.getElementById("edit_stock");
                if (stockInput.value < 0) {
                    showValidationMessage(stockInput, "Stock cannot be negative.");
                    isValid = false;
                }

                const warrantyInput = document.getElementById("edit_warranty");
                const warrantyRegex = /^[a-zA-Z0-9\s]+$/;
                if (!warrantyRegex.test(warrantyInput.value)) {
                    showValidationMessage(warrantyInput, "Warranty must be alphanumeric.");
                    isValid = false;
                }

                if (!isValid) {
                    event.preventDefault();
                }
            });
        });
    </script>
@endsection
