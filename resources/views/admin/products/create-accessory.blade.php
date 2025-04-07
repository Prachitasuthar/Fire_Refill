@extends('admin-layouts.app')

@section('content')
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow p-4 w-75">

            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('accessories.index') }}" class="text-decoration-none text-dark">
                    <i class="bi bi-arrow-left" style="font-size: 1.5rem;"></i>
                </a>
            </div>

            <h2 class="mb-4 text-center">Add New Accessory</h2>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('accessories.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
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
                    <label for="name" class="form-label">Accessory Name</label>
                    <input type="text" name="name" id="name" class="form-control">
                </div>

                <!-- Image Upload -->
                <div class="mb-3">
                    <label for="image" class="form-label">Accessory Image</label>
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

                <!-- Weight -->
                <div class="mb-3">
                    <label for="weight" class="form-label">Weight (kg)</label>
                    <input type="number" name="weight" id="weight" class="form-control">
                </div>


                <div class="mb-3">
                    <label for="power_source" class="form-label">Power Source (e.g., Battery, AC/DC)</label>
                    <input type="text" name="power_source" id="power_source" class="form-control">
                </div>

                <!-- Operating Voltage -->
                <div class="mb-3">
                    <label for="operating_voltage" class="form-label">Operating Voltage (v)</label>
                    <input type="text" name="operating_voltage" id="operating_voltage" class="form-control">
                </div>

                {{-- <!-- Material --> --}}
                <div class="mb-3">
                    <label for="material" class="form-label">Material (e.g., Stainless Steel, Aluminum)</label>
                    <input type="text" name="material" id="material" class="form-control">
                </div>

                <!-- Working Temperature -->
                <div class="mb-3">
                    <label for="working_temprature" class="form-label">Working Temperature (°C)</label>
                    <input type="text" name="working_temprature" id="working_temprature" class="form-control">
                </div>

                <!-- IP Routing -->
                <div class="mb-3">
                    <label for="IP_routing" class="form-label">IP Routing (e.g., IP67, IP54)</label>
                    <input type="text" name="IP_routing" id="IP_routing" class="form-control">
                </div>

                <!-- Stock -->
                <div class="mb-3">
                    <label for="stock" class="form-label">Stock Quantity (units)</label>
                    <input type="number" name="stock" id="stock" class="form-control">
                </div>

                <!-- Warranty -->
                <div class="mb-3">
                    <label for="warranty" class="form-label">Warranty (Year)</label>
                    <input type="number" name="warranty" id="warranty" class="form-control">
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4">Add Accessory</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("accessoryForm"); 
            const providerSelect = document.getElementById("provider_id");
            const nameInput = document.getElementById("name");
            const imageInput = document.getElementById("image");
            const priceInput = document.getElementById("price");
            const descriptionTextarea = document.getElementById("description");
            const weightInput = document.getElementById("weight");
            const stockInput = document.getElementById("stock");
            const warrantyInput = document.getElementById("warranty");

         
            function showError(input, message) {
                let errorDiv = input.parentNode.querySelector(".error-message");
                if (!errorDiv) {
                    errorDiv = document.createElement("div");
                    errorDiv.className = "error-message text-danger mt-1";
                    input.parentNode.appendChild(errorDiv);
                }
                errorDiv.textContent = message;
                input.classList.add("is-invalid");
            }

          
            function clearError(input) {
                let errorDiv = input.parentNode.querySelector(".error-message");
                if (errorDiv) {
                    errorDiv.remove();
                }
                input.classList.remove("is-invalid");
            }

           
            function validateField(input, message) {
                if (!input.value.trim()) {
                    showError(input, message);
                    return false;
                } else {
                    clearError(input);
                    return true;
                }
            }

           
            function validateStock() {
                let stockValue = parseInt(stockInput.value);
                if (isNaN(stockValue) || stockValue <= 0) {
                    showError(stockInput, "Stock quantity must be a positive number.");
                    return false;
                } else {
                    clearError(stockInput);
                    return true;
                }
            }

          
            function validateWarranty() {
                let warrantyValue = parseFloat(warrantyInput.value);
                if (isNaN(warrantyValue) || warrantyValue <= 0) {
                    showError(warrantyInput, "Warranty must be a positive number (e.g., 1.5, 2).");
                    return false;
                } else {
                    clearError(warrantyInput);
                    return true;
                }
            }

           
            providerSelect.addEventListener("blur", () => validateField(providerSelect,
                "Please select a provider."));
            nameInput.addEventListener("blur", () => validateField(nameInput, "Please enter the accessory name."));
            imageInput.addEventListener("blur", () => validateField(imageInput, "Please select an image"));
            priceInput.addEventListener("blur", () => validateField(priceInput, "Please enter the price."));
            descriptionTextarea.addEventListener("blur", () => validateField(descriptionTextarea,
                "Please enter a description."));
            // weightInput.addEventListener("blur", () => validateField(weightInput, "Please enter the weight."));
            stockInput.addEventListener("blur", validateStock);
            warrantyInput.addEventListener("blur", validateWarranty);

           
            form.addEventListener("submit", function(event) {
                let isValid = true;

                if (!validateField(providerSelect, "Please select a provider.")) isValid = false;
                if (!validateField(nameInput, "Please enter the accessory name.")) isValid = false;
                if (!validateField(imageInput, "Please select an image.")) isValid = false;
                if (!validateField(priceInput, "Please enter the price.")) isValid = false;
                if (!validateField(descriptionTextarea, "Please enter a description.")) isValid = false;
                if (!validateField(weightInput, "Please enter the weight.")) isValid = false;
                if (!validateStock()) isValid = false;
                if (!validateWarranty()) isValid = false;

             
                if (!isValid) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>
@endsection
