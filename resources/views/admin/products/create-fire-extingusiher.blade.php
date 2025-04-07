@extends('admin-layouts.app')

@section('content')
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow p-4 w-75">

            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('fire_extinguishers.index') }}" class="text-decoration-none text-dark">
                    <i class="bi bi-arrow-left" style="font-size: 1.5rem;"></i>
                </a>
            </div>

            <h2 class="mb-4 text-center">Add New fire-extinguisher</h2>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form id="fireExtinguisherForm" action="{{ route('fire_extinguishers.store') }}" method="POST"
                enctype="multipart/form-data" autocomplete="off">
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
                    <label for="name" class="form-label">Fire Extinguisher Name</label>
                    <input type="text" name="name" id="name" class="form-control">
                </div>

                <!-- Image Upload -->
                <div class="mb-3">
                    <label for="image" class="form-label">Fire Extinguisher Image</label>
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

                <!-- fire_class -->
                <div class="mb-3">
                    <label for="fire_class" class="form-label">Fire Class (e.g., A, B, C, D, E, F/K)</label>
                    <input type="text" name="fire_class" id="fire_class" class="form-control">
                </div>

                <!-- Power Source -->
                <div class="mb-3">
                    <label for="suitability" class="form-label">Suitability (e.g., Home, Industrial, Marine,
                        Electrical)</label>
                    <input type="text" name="suitability" id="suitability" class="form-control">
                </div>

                <!-- Operating Voltage -->
                <div class="mb-3">
                    <label for="capacity" class="form-label">Capacity (kg or L)</label>
                    <input type="number" name="capacity" id="capacity" class="form-control">

                </div>

                <!-- Material -->
                <div class="mb-3">
                    <label for="extinguishing_agent" class="form-label">Extinguishing Agent (e.g., Water, Foam, CO₂,
                        DCP)</label>
                    <input type="text" name="extinguishing_agent" id="extinguishing_agent" class="form-control">
                </div>

                <!-- Working Temperature -->
                <div class="mb-3">
                    <label for="discharge_time" class="form-label">Discharge Time (in seconds)</label>
                    <input type="text" name="discharge_time" id="discharge_time" class="form-control">
                </div>

                <!-- IP Routing -->
                <div class="mb-3">
                    <label for="working_pressure" class="form-label">Working Pressure (bar)</label>
                    <input type="text" name="working_pressure" id="working_pressure" class="form-control">
                </div>

                <!-- Stock -->
                <div class="mb-3">
                    <label for="cylinder_material" class="form-label">Cylinder Material (e.g., Mild Steel, Stainless
                        Steel)</label>
                    <input type="text" name="cylinder_material" id="cylinder_material" class="form-control">
                </div>

                <!-- Warranty -->
                <div class="mb-3">
                    <label for="operating_temprature" class="form-label">Operating Temprature (°C) </label>
                    <input type="text" name="operating_temprature" id="operating_temprature" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="weight" class="form-label">Weight (kg)</label>
                    <input type="number" name="weight" id="weight" class="form-control">
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
            const form = document.getElementById("fireExtinguisherForm");

            const fields = [{
                    id: "provider_id",
                    message: "Please select a service provider."
                },
                {
                    id: "name",
                    message: "Fire extinguisher name is required."
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
                    id: "fire_class",
                    message: "Fire class must be a combination of A, B, C, D, E, or F/K (e.g., A, B, C).",
                    pattern: /^(A|B|C|D|E|F\/K)(,\s*(A|B|C|D|E|F\/K))*$/
                }

                {
                    id: "cylinder_material",
                    message: "Cylinder material is required."
                },
                {
                    id: "operating_temprature",
                    message: "Operating temperature is required.",
                    type: "number"
                },
                {
                    id: "weight",
                    message: "Weight is required.",
                    type: "number"
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
                } else if (field.pattern && !field.pattern.test(value)) {
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
