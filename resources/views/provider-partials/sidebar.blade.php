<!-- Leftbar Tab Menu -->
<div class="leftbar-tab-menu">
    <div class="main-icon-menu">
        <!-- Logo -->
        <a class='logo logo-metrica d-block text-center' href='/metrica/default/'>
            <span>
                <img src="{{asset('hotel/images/logo-sm.png')}}" alt="logo-small" class="logo-sm">
            </span>
        </a>
        <!-- Main Icon Menu Body -->
        <div class="main-icon-menu-body">
            <div class="position-relative h-100" data-simplebar style="overflow-x: hidden;">
                <ul class="nav nav-tabs" role="tablist" id="tab-menu">
                    <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard" data-bs-trigger="hover">
                        <a href="#MetricaDashboard" id="dashboard-tab" class="nav-link">
                            <i class="ti ti-smart-home menu-icon"></i>
                        </a><!--end nav-link-->
                    </li><!--end nav-item-->
                    <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Apps" data-bs-trigger="hover">
                        <a href="#MetricaApps" id="apps-tab" class="nav-link">
                            <i class="ti ti-apps menu-icon"></i>
                        </a><!--end nav-link-->
                    </li><!--end nav-item-->
                    <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Uikit" data-bs-trigger="hover">
                        <a href="#MetricaUikit" id="uikit-tab" class="nav-link">
                            <i class="ti ti-planet menu-icon"></i>
                        </a><!--end nav-link-->
                    </li><!--end nav-item-->
                    <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Pages" data-bs-trigger="hover">
                        <a href="#MetricaPages" id="pages-tab" class="nav-link">
                            <i class="ti ti-files menu-icon"></i>
                        </a><!--end nav-link-->
                    </li><!--end nav-item-->
                    <li class="nav-item" data-bs-toggle="tooltip" data-bs-placement="right" title="Authentication" data-bs-trigger="hover">
                        <a href="#MetricaAuthentication" id="authentication-tab" class="nav-link">
                            <i class="ti ti-shield-lock menu-icon"></i>
                        </a><!--end nav-link-->
                    </li><!--end nav-item-->
                </ul><!--end nav-->
            </div><!--end /div-->
        </div><!--end main-icon-menu-body-->
        <!-- Profile -->
        <div class="pro-metrica-end">
            <a href="" class="profile">
                <img src="{{asset('hotel/images/users/user-4.jpg')}}" alt="profile-user" class="rounded-circle thumb-sm">
            </a>
        </div><!--end pro-metrica-end-->
    </div><!--end main-icon-menu-->

    <!-- Main Menu Inner -->
    <div class="main-menu-inner">
        <!-- Logo -->
        <div class="topbar-left">
            <a class='logo' href='/metrica/default/'>
                <span>
                    <img src="{{asset('hotel/images/logo-dark.png')}}" alt="logo-large" class="logo-lg logo-dark">
                    <img src="{{asset('hotel/images/logo.png')}}" alt="logo-large" class="logo-lg logo-light">
                </span>
            </a><!--end logo-->
        </div><!--end topbar-left-->

        <!-- Menu Body -->
        <div class="menu-body navbar-vertical tab-content" data-simplebar>
            <!-- Dashboard Tab -->
            <div id="MetricaDashboard" class="main-icon-menu-pane tab-pane" role="tabpanel" aria-labelledby="dashboard-tab">
                <div class="title-box">
                    <h6 class="menu-title">Dashboard</h6>
                </div>
                <ul class="nav flex-column bg-light p-3 rounded">
                    <li class="nav-item mb-2">
                        <a class="nav-link text-dark" href="/metrica/default/">
                            <i class="bi bi-grid-fill me-2"></i> Dashboard
                        </a>
                    </li><!--end nav-item-->
                    <li class="nav-item mb-2">
                        <a class="nav-link text-dark" href="/metrica/default/crypto-index">
                            <i class="bi bi-house-door-fill me-2"></i> Rooms
                        </a>
                    </li><!--end nav-item-->
                    <li class="nav-item mb-2">
                        <a class="nav-link text-dark" href="/metrica/default/crm-index">
                            <i class="bi bi-person-lines-fill me-2"></i> Users
                        </a>
                    </li><!--end nav-item-->
                    <li class="nav-item mb-2">
                        <a class="nav-link text-dark" href="/metrica/default/projects-index">
                            <i class="bi bi-credit-card-2-back-fill me-2"></i>Payments
                        </a>
                    </li><!--end nav-item-->
                    <li class="nav-item mb-2">
                        <a class="nav-link text-dark" href="/metrica/default/ecommerce-index">
                            <i class="bi bi-calendar-event-fill me-2"></i> Booking
                        </a>
                    </li><!--end nav-item-->
                    <li class="nav-item mb-2">
                        <a class="nav-link text-dark" href="/metrica/default/helpdesk-index">
                            <i class="bi bi-star-fill me-2"></i> Rating
                        </a>
                    </li><!--end nav-item-->
                    <li class="nav-item mb-2">
                        <a class="nav-link text-dark" href="/metrica/default/hospital-index">
                            {{-- <i class="bi bi-hospital me-2"></i>  --}}
                        </a>
                    </li><!--end nav-item-->
                </ul><!--end nav-->
                
            </div>

            <!-- Apps Tab -->
            <div id="MetricaApps" class="main-icon-menu-pane tab-pane" role="tabpanel" aria-labelledby="apps-tab">
                <div class="title-box">
                    <h6 class="menu-title">Apps</h6>
                </div>
                <!-- Add apps content here -->
            </div><!--end MetricaApps-->

            <!-- UI Kit Tab -->
            <div id="MetricaUikit" class="main-icon-menu-pane tab-pane" role="tabpanel" aria-labelledby="uikit-tab">
                <div class="title-box">
                    <h6 class="menu-title">UI Kit</h6>
                </div>
                <!-- Add UI Kit content here -->
            </div><!--end MetricaUikit-->

            <!-- Pages Tab -->
            <div id="MetricaPages" class="main-icon-menu-pane tab-pane" role="tabpanel" aria-labelledby="pages-tab">
                <div class="title-box">
                    <h6 class="menu-title">Pages</h6>
                </div>
                <!-- Add pages content here -->
            </div><!--end MetricaPages-->

            <!-- Authentication Tab -->
            <div id="MetricaAuthentication" class="main-icon-menu-pane tab-pane" role="tabpanel" aria-labelledby="authentication-tab">
                <div class="title-box">
                    <h6 class="menu-title">Authentication</h6>
                </div>
                <!-- Add authentication content here -->
            </div><!--end MetricaAuthentication-->
        </div><!--end menu-body-->
    </div><!--end main-menu-inner-->
</div><!--end leftbar-tab-menu-->
