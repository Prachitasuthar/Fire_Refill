<div class="topbar">
    <!-- Navbar -->
    <nav class="navbar-custom" id="navbar-custom">
        <ul class="list-unstyled topbar-nav float-end mb-0">
            {{-- <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
                    role="button" aria-haspopup="false" aria-expanded="false">
                    <img src="{{ asset('hotel/images/flags/us_flag.jpg') }}" alt=""
                        class="thumb-xxs rounded-circle">
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#"><img src="{{ asset('hotel/images/flags/us_flag.jpg') }}"
                            alt="" height="15" class="me-2">English</a>
                    <a class="dropdown-item" href="#"><img src="{{ asset('hotel/images/flags/spain_flag.jpg') }}"
                            alt="" height="15" class="me-2">Spanish</a>
                    <a class="dropdown-item" href="#"><img
                            src="{{ asset('hotel/images/flags/germany_flag.jpg') }}" alt="" height="15"
                            class="me-2">German</a>
                    <a class="dropdown-item" href="#"><img src="{{ asset('hotel/images/flags/french_flag.jpg') }}"
                            alt="" height="15" class="me-2">French</a>
                </div>
            </li><!--end topbar-language--> --}}

            {{-- notification --}}
            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
                    role="button" aria-haspopup="false" aria-expanded="false">
                    @if (auth()->check() && auth()->user()->user_type == 'provider')
                        <i class="ti ti-bell"></i> {{-- Provider Notifications --}}
                    @elseif(auth()->check() && auth()->user()->user_type == 'admin')
                        <i class="fas fa-envelope"></i> {{-- Admin Messages --}}
                    @endif

                    @if (auth()->user()->unreadNotifications->count() > 0)
                        <span class="alert-badge"></span> {{-- Notification Indicator --}}
                    @endif
                </a>

                <div class="dropdown-menu dropdown-menu-end dropdown-lg pt-0">
                    <h6
                        class="dropdown-item-text font-15 m-0 py-3 border-bottom d-flex justify-content-between align-items-center">
                        Notifications <span
                            class="badge bg-soft-primary badge-pill">{{ auth()->user()->unreadNotifications->count() }}</span>
                    </h6>

                    <div class="notification-menu" data-simplebar>
                        @forelse (auth()->user()->notifications as $notification)

                            @foreach (auth()->user()->notifications as $notification)
                                @if (auth()->user()->user_type == 'provider' &&
                                        isset($notification->data['provider_id']) &&
                                        $notification->data['provider_id'] == auth()->id())
                                    <a href="javascript:void(0);" class="dropdown-item py-3 view-notification"
                                        data-id="{{ $notification->id }}"
                                        data-message="{{ $notification->data['message'] }}"
                                        data-user="{{ $notification->data['user_name'] }}"
                                        data-service="{{ $notification->data['service_name'] }}"
                                        data-sub-service="{{ $notification->data['sub_service_name'] }}"
                                        data-time="{{ $notification->data['time'] }}">

                                        <small
                                            class="float-end text-muted ps-2">{{ $notification->data['time'] }}</small>
                                        <div class="media">
                                            <div class="avatar-md bg-soft-primary">
                                                <i class="ti ti-chart-arcs"></i>
                                            </div>
                                            <div class="media-body align-self-center ms-2 text-truncate">
                                                <h6 class="my-0 fw-normal text-dark">
                                                    {{ $notification->data['message'] }}</h6>
                                                <small class="text-muted mb-0">
                                                    {{ $notification->data['user_name'] }} requested
                                                    {{ $notification->data['service_name'] }} -
                                                    {{ $notification->data['sub_service_name'] }}
                                                </small>
                                            </div>
                                        </div>
                                    </a>

                                    {{-- Hidden Message Box --}}
                                    <div class="notification-message-box" id="message-{{ $notification->id }}"
                                        style="display: none; background: #f8f9fa; padding: 10px; margin: 5px 0; border-radius: 5px;">
                                        <strong>From:</strong> {{ $notification->data['user_name'] }} <br>
                                        <strong>Service:</strong> {{ $notification->data['service_name'] }} <br>
                                        <strong>Sub-Service:</strong> {{ $notification->data['sub_service_name'] }}
                                        <br>
                                        <strong>Message:</strong> {{ $notification->data['message'] }}
                                    </div>
                                @endif
                            @endforeach


                            {{-- ADMIN NOTIFICATIONS (CONTACT FORM MESSAGES) --}}
                            @foreach (auth()->user()->unreadNotifications->unique('id') as $notification)
                                @if (auth()->user()->user_type == 'admin' && isset($notification->data['contact_message']))
                                    <a href="javascript:void(0);" class="dropdown-item py-3 view-notification"
                                        data-id="{{ $notification->id }}"
                                        data-name="{{ $notification->data['name'] }}"
                                        data-email="{{ $notification->data['email'] }}"
                                        data-message="{{ $notification->data['contact_message'] }}"
                                        data-time="{{ $notification->created_at }}">

                                        <small
                                            class="float-end text-muted ps-2">{{ $notification->created_at->diffForHumans() }}</small>
                                        <div class="media">
                                            <div class="avatar-md bg-soft-danger">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                            <div class="media-body align-self-center ms-2 text-truncate">
                                                <h6 class="my-0 fw-normal text-dark">{{ $notification->data['name'] }}
                                                </h6>
                                                <small class="text-muted mb-0">
                                                    {{ $notification->data['email'] }} sent a message:
                                                    "{{ Str::limit($notification->data['contact_message'], 50) }}"
                                                </small>
                                            </div>
                                        </div>
                                    </a>

                                    {{-- Hidden Message Box --}}
                                    <div class="notification-message-box" id="message-{{ $notification->id }}"
                                        style="display: none; background: #f8f9fa; padding: 10px; margin: 5px 0; border-radius: 5px;">
                                        <strong>From:</strong> {{ $notification->data['name'] }}
                                        ({{ $notification->data['email'] }})<br>
                                        <strong>Message:</strong> {{ $notification->data['contact_message'] }}
                                    </div>
                                @endif
                            @endforeach




                        @empty
                            <a href="#" class="dropdown-item py-3 text-center">No new notifications</a>
                        @endforelse
                    </div>

                    <a href="{{ route('notifications.clear') }}" class="dropdown-item text-center text-primary">
                        Clear all <i class="fi-arrow-right"></i>
                    </a>
                </div>
            </li>


            <style>
                .alert-badge {
                    width: 10px;
                    height: 10px;
                    background-color: red;
                    border-radius: 50%;
                    display: inline-block;
                    position: absolute;
                    top: 5px;
                    right: 5px;
                }

                .hidden {
                    display: none !important;
                }
            </style>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function() {
                    let seenNotifications = new Set();

                    $(".view-notification").each(function() {
                        let notificationId = $(this).data("id");

                        if (seenNotifications.has(notificationId)) {
                            $(this).remove(); // Duplicate notification hatao
                        } else {
                            seenNotifications.add(notificationId);
                        }
                    });

                    $(".view-notification").on("click", function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        var messageBox = $("#message-" + $(this).data("id"));

                        $(".notification-message-box").not(messageBox).slideUp();
                        messageBox.slideToggle();

                        let notificationId = $(this).data("id");

                        // Mark notification as read
                        $.ajax({
                            url: `/notifications/mark-as-read/${notificationId}`,
                            type: "POST",
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                                "Content-Type": "application/json"
                            },
                            success: function(response) {
                                console.log("Notification marked as read", response);
                                $(".alert-badge").addClass("hidden");
                                $(`[data-id='${notificationId}']`).addClass("read-notification");
                            },
                            error: function(error) {
                                console.error("Error:", error);
                            }
                        });
                    });
                });

                $("#clear-all").on("click", function() {
                    $.ajax({
                        url: "/notifications/clear-all",
                        type: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                        },
                        success: function(data) {
                            if (data.success) {
                                console.log("All notifications cleared:", data.remaining_notifications);

                                // ✅ Frontend se bhi remove karo
                                $(".view-notification").remove();
                                $(".notification-message-box").remove();
                                $(".alert-badge").addClass("hidden");
                            }
                        },
                        error: function(error) {
                            console.error("Error clearing notifications", error);
                        }
                    });
                });
            </script>

            <style>
                /* Optional: Style for read notifications */
                .read-notification {
                    background-color: #f0f0f0;
                    /* Light grey to indicate read status */
                }
            </style>

            <style>
                /* Optional: Style for read notifications */
                .read-notification {
                    background-color: #f0f0f0;
                }
            </style>




            <li class="dropdown">
                <a class="nav-link dropdown-toggle nav-user" data-bs-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset(auth()->user()->profile_image ? auth()->user()->profile_image : 'img/profile/Profile-image.png') }}"
                            alt="profile-user" class="rounded-circle me-2 thumb-sm" />

                        <div>
                            <small
                                class="d-none d-md-block font-11">{{ auth()->user()->user_type == 'admin' ? 'Admin' : 'Provider' }}</small>
                            <span class="d-none d-md-block fw-semibold font-12"> {{ auth()->user()->first_name }}
                                {{ auth()->user()->last_name }} <i class="mdi mdi-chevron-down"></i></span>
                        </div>
                    </div>


                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="{{ route('admin.profile.edit') }}"><i
                            class="ti ti-user font-16 me-1 align-text-bottom"></i> Profile</a>
                    <a class="dropdown-item" href="{{ route('change.password.form') }}"><i
                            class="mdi mdi-key-outline font-15 me-1 align-text-bottom"></i> Change Passoword</a>
                    <div class="dropdown-divider mb-0"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item" style="cursor: pointer;"><i
                                class="ti ti-power font-16 me-1 align-text-bottom"></i> Logout</button>
                    </form>
                </div>
            </li><!--end topbar-profile-->
            {{-- <li class="notification-list">
                <a class="nav-link arrow-none nav-icon offcanvas-btn" href="#" data-bs-toggle="offcanvas"
                    data-bs-target="#Appearance" role="button" aria-controls="Rightbar">
                    <i class="ti ti-settings ti-spin"></i>
                </a>
            </li> --}}
        </ul><!--end topbar-nav-->

        @if (
            !request()->routeIs('accessories.create') &&
                !request()->routeIs('fire_extinguishers.create') &&
                !request()->routeIs('fire_suppression.create') &&
                !request()->routeIs('fire_watermist.create') &&
                !request()->routeIs('admin.profile.edit') &&
                 !request()->routeIs('admin.coupons.create') )
            <ul class="list-unstyled topbar-nav mb-0">
                <li>
                    <button class="nav-link button-menu-mobile nav-icon" id="togglemenu">
                        <i class="ti ti-menu-2"></i>
                    </button>
                </li>
            </ul>
        @endif


    </nav>

</div>
