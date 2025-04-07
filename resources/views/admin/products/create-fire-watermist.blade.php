@extends('admin-layouts.app')

@section('content')
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow p-4 w-75">

            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('fire_watermist.index') }}" class="text-decoration-none text-dark">
                    <i class="bi bi-arrow-left" style="font-size: 1.5rem;"></i>
                </a>
            </div>

            <h2 class="mb-4 text-center">Add New Watermist/CAFS</h2>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('fire_watermist.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
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

                <!-- Fire Watermist/CAFS Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Fire Watermist/CAFS Name</label>
                    <input type="text" name="name" id="name" class="form-control">
                </div>

                <!-- Image Upload -->
                <div class="mb-3">
                    <label for="image" class="form-label">Fire Watermist/CAFS Image</label>
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

                <!-- Technology Type Dropdown -->
                <div class="mb-3">
                    <label for="technology_type" class="form-label">Technology Type</label>
                    <select name="technology_type" id="technology_type" class="form-select">
                        <option value="">Select Technology Type</option>
                        <option value="Watermist">Watermist</option>
                        <option value="CAFS">CAFS</option>
                    </select>
                </div>

                <!-- Nozzle Type Dropdown -->
                <div class="mb-3">
                    <label for="nozzle_type" class="form-label">Nozzle Type</label>
                    <select name="nozzle_type" id="nozzle_type" class="form-select">
                        <option value="">Select Nozzle Type</option>
                        <option value="Fixed">Fixed</option>
                        <option value="Portable">Portable</option>
                    </select>
                </div>

                <!-- Working Pressure -->
                <div class="mb-3">
                    <label for="working_pressure" class="form-label">Working Pressure (in bar)</label>
                    <input type="text" name="working_pressure" id="working_pressure" class="form-control">
                </div>

                <!-- Droplet Size -->
                <div class="mb-3">
                    <label for="droplet_size" class="form-label">Droplet Size (in microns)</label>
                    <input type="number" name="droplet_size" id="droplet_size" class="form-control">
                </div>

                <!-- Flow Rate -->
                <div class="mb-3">
                    <label for="flow_rate" class="form-label">Flow Rate (in LPM)</label>
                    <input type="number" name="flow_rate" id="flow_rate" class="form-control">
                </div>

                <!-- Application Area -->
                <div class="mb-3">
                    <label for="application_area" class="form-label">Application Area (eg: m² or ft²)</label>
                    <input type="text" name="application_area" id="application_area" class="form-control">
                </div>

                <!-- Stock -->
                <div class="mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" name="stock" id="stock" class="form-control">
                </div>

                <!-- Warranty -->
                <div class="mb-3">
                    <label for="warranty" class="form-label">Warranty (Years)</label>
                    <input type="text" name="warranty" id="warranty" class="form-control">
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4">Add Watermist/CAFS</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("fireWatermistForm");

            const fields = [{
                    id: "provider_id",
                    message: "Please select a service provider."
                },
                {
                    id: "name",
                    message: "Fire Watermist/CAFS name is required."
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
                    id: "technology_type",
                    message: "Technology type is required."
                },
                {
                    id: "nozzle_type",
                    message: "Nozzle type is required."
                },
                {
                    id: "working_pressure",
                    message: "Working pressure is required."
                },
                {
                    id: "droplet_size",
                    message: "Droplet size is required and must be a number.",
                    type: "number"
                },
                {
                    id: "flow_rate",
                    message: "Flow rate is required and must be a number.",
                    type: "number"
                },
                {
                    id: "application_area",
                    message: "Application area is required."
                },
                {
                    id: "stock",
                    message: "Stock is required and must be at least 1.",
                    type: "number",
                    min: 1
                },
                {
                    id: "warranty",
                    message: "Warranty is required."
                    type: "number",
                    min: 1
                },
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
