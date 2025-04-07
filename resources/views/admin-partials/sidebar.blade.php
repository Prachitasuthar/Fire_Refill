<!-- Leftbar Tab Menu -->
<div class="leftbar-tab-menu">
    <div class="main-icon-menu">
        <!-- Logo -->
        <a class='logo logo-metrica d-block text-center' href="{{ route('admin.dashboard') }}">
            <span>
                <img src="{{ asset('hotel/images/logo1.png') }}" alt="logo-small" class="logo-sm">
            </span>
        </a>
        <!-- Main Icon Menu Body -->
        <div class="main-icon-menu-body">
            <div class="position-relative h-100" data-simplebar style="overflow-x: hidden;">
                <ul class="nav nav-tabs" role="tablist" id="tab-menu">
                    <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard"
                        data-bs-trigger="hover">
                        <a href="#MetricaDashboard" id="dashboard-tab" class="nav-link">
                            <i class="ti ti-smart-home menu-icon"></i>
                        </a><!--end nav-link-->
                    </li><!--end nav-item-->
                    <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Booking"
                        data-bs-trigger="hover">
                        <a href="#MetricaApps" id="apps-tab" class="nav-link">
                            <i class="ti ti-notebook menu-icon"></i>

                        </a><!--end nav-link-->
                    </li><!--end nav-item-->
                    <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Service"
                        data-bs-trigger="hover">
                        <a href="#MetricaUikit" id="uikit-tab" class="nav-link">
                            <i class="ti ti-package menu-icon"></i>
                        </a><!--end nav-link-->
                    </li><!--end nav-item-->


                    {{-- @if (auth()->user()->user_type == 'admin') --}}
                    <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Users"
                        data-bs-trigger="hover">
                        <a href="#MetricaUsers" id="uikit-tab" class="nav-link">
                            <i class="ti ti-user menu-icon"></i>
                        </a>
                    </li>
                    {{-- @endif --}}



                    <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Service Provider"
                        data-bs-trigger="hover">
                        <a href="#MetricaProvider" id="pages-tab" class="nav-link">
                            <i class="ti ti-users menu-icon"></i>


                        </a><!--end nav-link-->
                    </li><!--end nav-item-->

                    <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Product"
                        data-bs-trigger="hover">
                        <a href="#MetricaProduct" id="authentication-tab" class="nav-link">
                            <i class="ti ti-tag menu-icon"></i>

                        </a><!--end nav-link-->
                    </li><!--end nav-item-->

                    <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Coupon"
                        data-bs-trigger="hover">
                        <a href="#MetricaCoupan" id="authentication-tab" class="nav-link">
                            <i class="ti ti-discount-2 menu-icon"></i>
                        </a><!--end nav-link-->
                    </li><!--end nav-item-->

                    {{-- <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Rating" data-bs-trigger="hover">
                        <a href="#MetricaRating" id="authentication-tab" class="nav-link">
                            <i class="ti ti-star menu-icon"></i>


                        </a><!--end nav-link-->
                    </li><!--end nav-item--> --}}

                    {{-- <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Payment" data-bs-trigger="hover">
                        <a href="#MetricaPayment" id="authentication-tab" class="nav-link">
                            <i class="ti ti-credit-card menu-icon"></i>

                        </a>
                    </li> --}}
                    @if (auth()->user()->user_type === 'admin')
                        <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Setting"
                            data-bs-trigger="hover">
                            <a href="#MetricaSetting" id="authentication-tab" class="nav-link">
                                <i class="ti ti-settings menu-icon"></i>
                            </a>
                        </li>
                    @endif
                </ul>
            </div><!--end /div-->
        </div><!--end main-icon-menu-body-->
        <!-- Profile -->
        <div class="pro-metrica-end">
            <img
                src="{{ asset(auth()->user()->profile_image ? auth()->user()->profile_image : 'img/profile/Profile-image.png') }}"class="rounded-circle thumb-sm">
            </a>
        </div>
    </div>



    <!-- Main Menu Inner -->
    @if (
        !request()->routeIs('accessories.create') &&
            !request()->routeIs('fire_extinguishers.create') &&
            !request()->routeIs('fire_suppression.create') &&
            !request()->routeIs('fire_watermist.create')&&
            !request()->routeIs('admin.profile.edit') &&
            !request()->routeIs('admin.coupons.create') )

        <div class="main-menu-inner">
            <!-- Logo -->
            <div class="topbar-left">
                <a class='logo' href="{{ route('admin.dashboard') }}">
                    <span style="font-size: 24px; font-weight: bold; color: black;">
                        RefillEase
                    </span>
                </a><!--end logo-->

            </div><!--end topbar-left-->

            <!-- Menu Body -->
            <div class="menu-body navbar-vertical tab-content" data-simplebar>
                <!-- Dashboard Tab -->
                <div id="MetricaDashboard" class="main-icon-menu-pane tab-pane" role="tabpanel"
                    aria-labelledby="dashboard-tab">
                    <div class="title-box">
                        <h6 class="menu-title">Dashboard</h6>
                    </div>
                    <ul class="nav flex-column bg-light p-3 rounded">
                        <li class="nav-item mb-2">
                            <a class="nav-link text-dark" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-grid-fill me-2"></i> Dashboard
                            </a>
                        </li><!--end nav-item-->


                    </ul><!--end nav-->

                </div>

                <!-- Apps Tab -->
                <div id="MetricaApps" class="main-icon-menu-pane tab-pane" role="tabpanel"
                    aria-labelledby="apps-tab">
                    <div class="title-box" href="{{ route('admin.dashboard') }}">

                        <h6 class="menu-title">Booking</h6>
                    </div>
                    <ul class="nav flex-column bg-light p-3 rounded">
                        @can('view order')
                            <li class="nav-item mb-2">
                                <a class="nav-link text-dark" href="{{ route('admin.bookingShow') }}">
                                    <i class="bi bi-grid-fill me-2"></i> Booking list
                                </a>
                            </li>
                        @endcan

                        @can('view delivered order')
                            <li class="nav-item mb-2">
                                <a class="nav-link text-dark" href="{{ route('admin.OrderShow') }}">
                                    <i class="bi bi-grid-fill me-2"></i> Delivered Orders
                                </a>
                            </li>
                        @endcan
                    </ul>


                </div><!--end MetricaApps-->

                <!-- UI Kit Tab -->
                <div id="MetricaUikit" class="main-icon-menu-pane tab-pane" role="tabpanel"
                    aria-labelledby="uikit-tab">
                    <div class="title-box">
                        <h6 class="menu-title" href="{{ route('admin.dashboard') }}">Services</h6>
                    </div>
                    <ul class="nav flex-column bg-light p-3 rounded">

                        @can('services')
                            <li class="nav-item mb-2">
                                <a class="nav-link text-dark" href="{{ route('services') }}">
                                    <i class="bi bi-clipboard-check me-2"></i> Service List
                                </a>
                            </li>
                        @endcan

                        @php
                            $provider = \App\Models\ServiceProvider::where('user_id', auth()->id())->first();
                        @endphp

                        <li class="nav-item mb-2">
                            @if (auth()->user()->user_type === 'admin' && auth()->user()->can('create services'))
                                <a id="add-service-link" class="nav-link text-dark"
                                    href="{{ route('services.create') }}">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Add Services
                                </a>
                            @elseif(auth()->user()->user_type === 'provider' && auth()->user()->can('create services'))
                                @if ($provider && ($provider->status === 'rejected' || $provider->status === 'pending'))
                                    <span class="text-muted" style="cursor: not-allowed; opacity: 0.5;"
                                        data-bs-toggle="tooltip"
                                        title="Your account is {{ $provider->status }}. Please wait for approval.">
                                        <i class="bi bi-plus-circle me-2"></i>
                                        Add Services (Not Allowed)
                                    </span>
                                @else
                                    <a id="add-service-link" class="nav-link text-dark"
                                        href="{{ route('services.create') }}">
                                        <i class="bi bi-plus-circle me-2"></i>
                                        Add Services
                                    </a>
                                @endif
                            @endif
                        </li>

                        @can('show service resquest')
                            <li class="nav-item mb-2">
                                <a class="nav-link text-dark" href="{{ route('admin.service-requests') }}">
                                    <i class="bi bi-clipboard-check me-2"></i>Request List
                                </a>
                            </li>
                        @endcan

                        @can('accept service request list')
                            <li class="nav-item mb-2">
                                <a class="nav-link text-dark" href="{{ route('admin.service-requests.accepted') }}">
                                    <i class="bi bi-clipboard-check me-2"></i>Accept Request
                                </a>
                            </li>
                        @endcan


                        {{-- Tooltip Activation (Include this in your Blade template) --}}
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                                    return new bootstrap.Tooltip(tooltipTriggerEl);
                                });
                            });
                        </script>


                    </ul>
                    <!-- Add UI Kit content here -->
                </div><!--end MetricaUikit-->

                <!-- Pages Tab -->
                {{-- @if (auth()->user()->user_type == 'admin') --}}
                <div id="MetricaUsers" class="main-icon-menu-pane tab-pane" role="tabpanel"
                    aria-labelledby="authentication-tab">
                    <div class="title-box">
                        <h6 class="menu-title" href="{{ route('admin.dashboard') }}">Users</h6>
                    </div>
                    <ul class="nav flex-column bg-light p-3 rounded">
                        @can('user list')
                            <li class="nav-item mb-2">
                                <a class="nav-link text-dark" href="{{ route('admin.users.index') }}">
                                    <i class="bi bi-grid-fill me-2"></i> Users
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
                {{-- @endif --}}


                <!-- Authentication Tab -->
                <div id="MetricaProvider" class="main-icon-menu-pane tab-pane" role="tabpanel"
                    aria-labelledby="authentication-tab">
                    <div class="title-box">
                        <h6 class="menu-title" href="{{ route('admin.dashboard') }}">Service Provider</h6>

                        <ul class="nav flex-column bg-light p-3 rounded">

                            @can('view providers')
                                <li class="nav-item mb-2">
                                    <a class="nav-link text-dark" href="{{ route('admin.providers.index') }}">

                                        <i class="bi bi-clipboard-check me-2"></i> Provider List
                                    </a>
                                </li>
                            @endcan
                            {{-- @if (auth()->user()->user_type === 'admin') --}}
                            @can('provider request')
                                <li class="nav-item mb-2">
                                    <a class="nav-link text-dark" href="{{ route('admin.providers') }}">
                                        <i class="bi bi-person-lines-fill me-2"></i> Request List
                                    </a>
                                </li>
                            @endcan

                            @can('rejected provider')
                                <li class="nav-item mb-2">
                                    <a class="nav-link text-dark" href="{{ route('admin.providers.rejected') }}">
                                        <i class="bi bi-person-x-fill me-2"></i> Rejected List
                                    </a>
                                </li>
                            @endcan

                            {{-- @endif --}}

                    </div>

                </div><!--end MetricaAuthentication-->

                <div id="MetricaProduct" class="main-icon-menu-pane tab-pane" role="tabpanel"
                    aria-labelledby="authentication-tab">
                    <div class="title-box">
                        <h6 class="menu-title" href="{{ route('admin.dashboard') }}">Product</h6>
                    </div>
                    <ul class="nav flex-column bg-light p-3 rounded">


                        @can('view accessories')
                            <li class="nav-item mb-2">
                                <a class="nav-link text-dark" href="{{ route('accessories.index') }}">
                                    <i class="bi bi-plus-circle me-2"></i> Accessories
                                </a>
                            </li>
                        @endcan

                        @can('view fire extinguisher')
                            <li class="nav-item mb-2">
                                <a class="nav-link text-dark" href="{{ route('fire_extinguishers.index') }}">
                                    <i class="bi bi-plus-circle me-2"></i> Fire Extinguishers
                                </a>
                            </li>
                        @endcan

                        @can('view suppression')
                            <li class="nav-item mb-2">
                                <a class="nav-link text-dark" href="{{ route('fire_suppression.index') }}">
                                    <i class="bi bi-plus-circle me-2"></i> Fire Suppression
                                </a>
                            </li>
                        @endcan

                        @can('view watermist')
                            <li class="nav-item mb-2">
                                <a class="nav-link text-dark" href="{{ route('fire_watermist.index') }}">
                                    <i class="bi bi-plus-circle me-2"></i> Watermist/CAFS
                                </a>
                            </li>
                        @endcan
                    </ul>

                </div>


                <div id="MetricaCoupan" class="main-icon-menu-pane tab-pane" role="tabpanel"
                    aria-labelledby="authentication-tab">
                    <div class="title-box">
                        <h6 class="menu-title" href="">Coupon</h6>
                    </div>
                    <ul class="nav flex-column bg-light p-3 rounded">

                        @can('view coupon')
                            <li class="nav-item mb-2">
                                <a class="nav-link text-dark" href="{{ route('admin.coupons.index') }}">
                                    <i class="bi bi-grid-fill me-2"></i> Coupon List
                                </a>
                            </li>
                        @endcan

                    </ul>
                    <!-- Add authentication content here -->
                </div><!--end MetricaAuthentication-->

                {{-- 

            <div id="MetricaPayment" class="main-icon-menu-pane tab-pane" role="tabpanel" aria-labelledby="authentication-tab">
                <div class="title-box">
                    <h6 class="menu-title">payment</h6>
                </div>
                <ul class="nav flex-column bg-light p-3 rounded">
                        <li class="nav-item mb-2">
                            <a class="nav-link text-dark" href="/metrica/default/crypto-index">
                                <i class="bi bi-grid-fill me-2"></i> Payment
                            </a>
                        </li>
                </ul>
      
            </div> --}}


                <div id="MetricaSetting" class="main-icon-menu-pane tab-pane" role="tabpanel"
                    aria-labelledby="authentication-tab">
                    <div class="title-box">
                        <h6 class="menu-title">Settings</h6>
                    </div>
                    <ul class="nav flex-column bg-light p-3 rounded">

                        <li class="nav-item mb-2">
                            <a class="nav-link text-dark" href="{{ route('roles.permissions') }}">
                                <i class="bi bi-grid-fill me-2"></i> Role & Permission
                            </a>
                        </li>
                    </ul>

                </div>
            </div><!--end menu-body-->
        </div><!--end main-menu-inner-->
    @endif

</div><!--end leftbar-tab-menu-->
