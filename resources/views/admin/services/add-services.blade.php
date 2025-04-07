@extends('admin-layouts.app')

@section('content')
<div class="container mt-5 d-flex justify-content-center">
    <div class="card shadow p-4 w-75">
        <h2 class="mb-4 text-center">Add New Service</h2>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off" novalidate>
            @csrf

            <!-- Provider Dropdown -->
            <div class="mb-3">
                <label for="provider_id" class="form-label">Service Provider</label>
                <select name="provider_id" id="provider_id" class="form-select">
                    <option value="">Select Provider</option>
                    @foreach ($providers as $provider)
                        <option value="{{ $provider['id'] }}" 
                                data-business="{{ $provider['business_name'] }}" 
                                data-status="{{ $provider['status'] }}">
                            {{ $provider['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Business Name (Auto-fill) -->
            <div class="mb-3">
                <label for="business_name" class="form-label">Business Name</label>
                <input type="text" name="business_name" id="business_name" class="form-control" readonly>
            </div>

            <!-- Service Name (Dropdown) -->
            <div class="mb-3">
                <label for="service_name" class="form-label">Service Name</label>
                <select name="service_name" id="service_name" class="form-select">
                    <option value="" disabled selected>Select Services</option>
                    <option value="Fire Extinguisher Refilling">Fire Extinguisher Refilling</option>
                    <option value="Fire Safety Upgrade">Fire Safety Upgrade</option>
                </select>
            </div>

            <!-- Sub Service Name (Dynamic Dropdown) -->
            <div class="mb-3" id="sub_service_container" style="display: none;">
                <label for="sub_service_name" class="form-label">Sub Service Name</label>
                <select name="sub_service_name" id="sub_service_name" class="form-select">
                    <option value="" disabled selected>Select Sub Service</option>
                </select>
            </div>

            <!-- Image Upload -->
            {{-- <div class="mb-3">
                <label for="service_image" class="form-label">Service Image</label>
                <input type="file" name="service_image" id="service_image" class="form-control">
            </div> --}}

            <!-- Description -->
            {{-- <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4"></textarea>
            </div> --}}

            <!-- Status -->
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <!-- Bootstrap Alert (Hidden Initially) -->
            <div class="alert alert-danger d-none" id="provider-alert">Selected provider is inactive. Cannot add service.</div>

            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-primary px-4">Add Service</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let providerSelect = document.getElementById("provider_id");
    let businessNameInput = document.getElementById("business_name");
    let providerAlert = document.getElementById("provider-alert");
    let serviceSelect = document.getElementById("service_name");
    let subServiceSelect = document.getElementById("sub_service_name");
    let subServiceContainer = document.getElementById("sub_service_container");
    let form = document.querySelector("form");

    const subServices = {
        "Fire Extinguisher Refilling": [
            "ABC Powder Refilling",
            "CO₂ Refilling",
            "Water-Based Refilling",
            "Foam-Based Refilling",
            "Dry Chemical Powder Refilling",
            "Halotron / Clean Agent Refilling",
            "Class K (Kitchen Fire) Wet Chemical Refilling",
            "Automatic Modular Refilling",
            "Metal Fire (Class D) Extinguisher Refilling",
            "Lithium Battery Refilling"
        ],
        "Fire Safety Upgrade": [
            "Fire Alarm System Upgrade",
            "Smoke Detector Installation/Upgrade",
            "Sprinkler System Installation & Maintenance",
            "Fire Suppression System Installation (CO₂, FM200, Water Mist, etc.)",
            "Emergency Exit Signage & Lighting Upgrade",
            "Fire Hydrant System Installation & Testing",
            "Fire Door & Fireproof Window Installation",
            "Electrical Wiring & Circuit Fire Safety Compliance",
            "Flame Retardant Coating for Walls & Ceilings",
            "Gas Leak Detection System Installation",
            "Building Fireproofing & Fire Barrier Installation"
        ]
    };

    providerSelect.addEventListener("change", function () {
        let selectedOption = providerSelect.options[providerSelect.selectedIndex];
        let businessName = selectedOption.getAttribute("data-business");
        let providerStatus = selectedOption.getAttribute("data-status");

        businessNameInput.value = businessName || "";

        if (providerStatus === "pending" || providerStatus === "rejected") {
            providerAlert.classList.remove("d-none");
        } else {
            providerAlert.classList.add("d-none");
        }
    });

    serviceSelect.addEventListener("change", function () {
        let selectedService = serviceSelect.value;
        subServiceSelect.innerHTML = '<option value="" disabled selected>Select Sub Service</option>';

        if (subServices[selectedService]) {
            subServices[selectedService].forEach(subService => {
                let option = document.createElement("option");
                option.value = subService;
                option.textContent = subService;
                subServiceSelect.appendChild(option);
            });
            subServiceContainer.style.display = "block";
        } else {
            subServiceContainer.style.display = "none";
        }
    });

    // Form validation before submit
    form.addEventListener("submit", function (event) {
        let selectedOption = providerSelect.options[providerSelect.selectedIndex];
        let providerStatus = selectedOption.getAttribute("data-status");

        if (providerStatus === "pending" || providerStatus === "rejected") {
            event.preventDefault();
            providerAlert.classList.remove("d-none");
        }
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("serviceForm");
    const providerSelect = document.getElementById("provider_id");
    const serviceNameSelect = document.getElementById("service_name");
    // const descriptionTextarea = document.getElementById("description");
    const statusSelect = document.getElementById("status");
    // const serviceImageInput = document.getElementById("service_image");

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

    providerSelect.addEventListener("blur", () => validateField(providerSelect, "Please select a provider."));
    serviceNameSelect.addEventListener("blur", () => validateField(serviceNameSelect, "Please select a service name."));
    // descriptionTextarea.addEventListener("blur", () => validateField(descriptionTextarea, "Please provide a description."));
    statusSelect.addEventListener("blur", () => validateField(statusSelect, "Please select a status."));

    // serviceImageInput.addEventListener("change", function () {
    //     if (serviceImageInput.files.length === 0) {
    //         showError(serviceImageInput, "Please upload a service image.");
    //     } else {
    //         const file = serviceImageInput.files[0];
    //         const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
    //         if (!allowedTypes.includes(file.type)) {
    //             showError(serviceImageInput, "Only JPG, JPEG, and PNG images are allowed.");
    //         } else {
    //             clearError(serviceImageInput);
    //         }
    //     }
    // });
    form.addEventListener("submit", function (event) {
        let isValid = true;

        if (!validateField(providerSelect, "Please select a provider.")) isValid = false;
        if (!validateField(serviceNameSelect, "Please select a service name.")) isValid = false;
        // if (!validateField(descriptionTextarea, "Please provide a description.")) isValid = false;
        if (!validateField(statusSelect, "Please select a status.")) isValid = false;

        // if (serviceImageInput.files.length === 0) {
        //     showError(serviceImageInput, "Please upload a service image.");
        //     isValid = false;
        // }

        if (!isValid) {
            event.preventDefault(); 
            return false;
        }
    });

    window.addEventListener("load", function () {
        validateField(providerSelect, "Please select a provider.");
        validateField(serviceNameSelect, "Please select a service name.");
        // validateField(descriptionTextarea, "Please provide a description.");
        validateField(statusSelect, "Please select a status.");
    });
});


</script>

@endsection
