@extends('admin-layouts.app')

@section('content')
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow p-4 w-75">

            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('fire_suppression.index') }}" class="text-decoration-none text-dark">
                    <i class="bi bi-arrow-left" style="font-size: 1.5rem;"></i>
                </a>
            </div>

            <h2 class="mb-4 text-center">Add New fire-suppression</h2>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('fire_suppression.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                @csrf

                <!-- Provider Dropdown -->
                <div class="mb-3">
                    <label for="provider_id" class="form-label">Service Provider</label>
                    <select name="provider_id" id="provider_id" class="form-select">
                        <option value="">Select Provider</option>
                        @foreach ($providers as $provider)
                            <option value="{{ $provider->id }}">{{ $provider->first_name }} {{ $provider->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Accessory Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Fire Supression Name</label>
                    <input type="text" name="name" id="name" class="form-control">
                </div>

                <!-- Image Upload -->
                <div class="mb-3">
                    <label for="image" class="form-label">Fire Supression Image</label>
                    <input type="file" name="image" id="image" class="form-control">
                </div>

                <!-- Price -->
                <div class="mb-3">
                    <label for="price" class="form-label">Price (₹)</label>
                    <input type="number" name="price" id="price" class="form-control">
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4"></textarea>
                </div>

                <!-- Suppression Type Dropdown -->
                <div class="mb-3">
                    <label for="suppression_type" class="form-label">Suppression Type</label>
                    <select name="suppression_type" id="suppression_type" class="form-select">
                        <option value="">Select Type</option>
                        <option value="Clean Agent">Clean Agent</option>
                        <option value="CO2">CO2</option>
                        <option value="Water Mist">Water Mist</option>
                        <option value="Foam">Foam</option>
                    </select>
                </div>

                <!-- Installation Type Dropdown -->
                <div class="mb-3">
                    <label for="installation_type" class="form-label">Installation Type</label>
                    <select name="installation_type" id="installation_type" class="form-select">
                        <option value="">Select Installation Type</option>
                        <option value="Ceiling Mounted">Ceiling Mounted</option>
                        <option value="Wall Mounted">Wall Mounted</option>
                        <option value="Standalone">Standalone</option>
                    </select>
                </div>

                <!-- Activation Method Dropdown -->
                <div class="mb-3">
                    <label for="activation_method" class="form-label">Activation Method</label>
                    <select name="activation_method" id="activation_method" class="form-select">
                        <option value="">Select Activation Method</option>
                        <option value="Manual">Manual</option>
                        <option value="Automatic">Automatic</option>
                        <option value="Hybrid">Hybrid (Both Manual & Automatic)</option>
                    </select>
                </div>


                <div class="mb-3">
                    <label for="application_area" class="form-label">Application Area (eg: m² or ft²) </label>
                    <input type="number" name="application_area" id="application_area" class="form-control">

                </div>

                <div class="mb-3">
                    <label for="cylinder_capacity" class="form-label">Cylinder Capacity (L/kg)</label>
                    <input type="number" name="cylinder_capacity" id="cylinder_capacity" class="form-control">
                </div>


                <div class="mb-3">
                    <label for="response_time" class="form-label">Response Time (ms/s)</label>
                    <input type="text" name="response_time" id="response_time" class="form-control">
                </div>

               
                <div class="mb-3">
                    <label for="working_temprature_range" class="form-label">Working Temprature Range (°C)</label>
                    <input type="text" name="working_temprature_range" id="working_temprature_range"
                        class="form-control">
                </div>



                <div class="mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" name="stock" id="stock" class="form-control">
                </div>


                <div class="mb-3">
                    <label for="warranty" class="form-label">Warranty (Year)</label>
                    <input type="text" name="warranty" id="warranty" class="form-control">

                </div>


                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4">Add Fire Extinguisher</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("fireSuppressionForm");

            const fields = [{
                    id: "provider_id",
                    message: "Please select a service provider."
                },
                {
                    id: "name",
                    message: "Fire suppression name is required."
                },
                {
                    id: "image",
                    message: "Image is required."
                },
                {
                    id: "price",
                    message: "Price is required and must be greater than 0.",
                    type: "number"
                },
                {
                    id: "description",
                    message: "Description is required."
                },
                {
                    id: "suppression_type",
                    message: "Suppression type is required."
                },
                {
                    id: "installation_type",
                    message: "Installation type is required."
                },
                {
                    id: "application_area",
                    message: "Application area is required.",
                    type: "number"
                },
                {
                    id: "cylinder_capacity",
                    message: "Cylinder capacity is required.",
                    type: "number"
                },
                {
                    id: "activation_method",
                    message: "Activation method is required."
                },
                {
                    id: "response_time",
                    message: "Response time is required."
                },
                {
                    id: "working_temprature_range",
                    message: "Working temperature range is required."
                },
                {
                    id: "stock",
                    message: "Stock is required and must be at least 1.",
                    type: "number",
                    min: 1
                },
                {
                    id: "warranty",
                    message: "Warranty is required.",
                    type: "number",
                    min: 1
                }
            ];

            function validateField(field) {
                let input = document.getElementById(field.id);
                let value = input.value.trim();
                let errorElement = input.parentNode.querySelector(".error-message");

                if (errorElement) {
                    errorElement.remove();
                }

                let hasError = false;

                if (value === "") {
                    hasError = true;
                } else if (field.type === "number" && (isNaN(value) || Number(value) <= 0)) {
                    hasError = true;
                } else if (field.min && Number(value) < field.min) {
                    hasError = true;
                }

                if (hasError) {
                    let errorMsg = document.createElement("small");
                    errorMsg.classList.add("text-danger", "error-message");
                    errorMsg.innerText = field.message;
                    input.parentNode.appendChild(errorMsg);
                }
            }

            fields.forEach(field => {
                let input = document.getElementById(field.id);

                input.addEventListener("input", () => validateField(field));
                input.addEventListener("blur", () => validateField(field));
            });
            
            form.addEventListener("submit", function(event) {
                event.preventDefault();
                let isValid = true;

                fields.forEach(field => {
                    validateField(field);
                    if (document.getElementById(field.id).parentNode.querySelector(
                        ".error-message")) {
                        isValid = false;
                    }
                });

                if (isValid) {
                    form.submit();
                }
            });
        });
    </script>
@endsection
