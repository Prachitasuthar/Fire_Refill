@extends('admin-layouts.app')

@section('content')
    <div class="container" style="margin-top: 80px">
        <h2 class="mb-3">Permissions</h2>

        @if (session('success'))
            <div class="alert alert-success" id="success-alert">
                {{ session('success') }}
            </div>

            <script>
                setTimeout(function() {
                    document.getElementById('success-alert').style.display = 'none';
                }, 2000);
            </script>

            <style>
                #success-alert {
                    position: fixed;
                    top: 60px;
                    right: 20px;
                    background-color: #28a745;
                    color: white;
                    padding: 10px 15px;
                    border-radius: 5px;
                    font-size: 14px;
                    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
                    white-space: nowrap;
                }
            </style>
        @endif

        @php
            use Illuminate\Support\Str;

            $categories = [
                'services' => 'Services',
                'order' => 'Booking Orders',
                'user' => 'Users',
                'service_providers' => 'Service Providers',
                'accessories' => 'Accessories',
                'extinguisher' => 'Fire Extinguishers',
                'suppression' => 'Fire Suppression',
                'watermist' => 'Watermist System',
                'coupon' => 'Coupons',
            ];

            $groupedPermissions = [];

            foreach ($categories as $key => $category) {
                $groupedPermissions[$key] = [];
            }

            foreach ($permissions as $permission) {
                $permName = Str::lower($permission->name);

                if (
                    Str::contains($permName, 'service provider') ||
                    Str::contains($permName, 'provider request') ||
                    Str::contains($permName, 'view providers') ||
                    Str::contains($permName, 'rejected provider')
                ) {
                    $groupedPermissions['service_providers'][] = $permission;
                } elseif (
                    Str::contains($permName, 'services') ||
                    (Str::contains($permName, 'service') && !Str::contains($permName, 'provider'))
                ) {
                    $groupedPermissions['services'][] = $permission;
                } else {
                    foreach ($categories as $key => $category) {
                        if (Str::contains($permName, $key)) {
                            $groupedPermissions[$key][] = $permission;
                            break;
                        }
                    }
                }
            }
        @endphp

        <ul class="nav nav-tabs" id="permissionsTab" role="tablist">
            @foreach ($categories as $key => $category)
                @php
                    $slug = Str::slug($key);
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $slug }}-tab" data-bs-toggle="tab"
                        href="#{{ $slug }}" role="tab" aria-controls="{{ $slug }}" aria-selected="true">
                        {{ $category }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content mt-3" id="permissionsTabContent">
            @foreach ($categories as $key => $category)
                @php
                    $slug = Str::slug($key);
                @endphp
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $slug }}" role="tabpanel"
                    aria-labelledby="{{ $slug }}-tab">
                    <div class="card">
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Permission</th>
                                        @foreach ($roles as $role)
                                            <th>{{ ucfirst($role->name) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($groupedPermissions[$key]) > 0)
                                        @foreach ($groupedPermissions[$key] as $permission)
                                            <tr>
                                                <td>{{ $permission->name }}</td>
                                                @foreach ($roles as $role)
                                                    <td>
                                                        <form action="{{ route('roles.permissions.update') }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="role_id"
                                                                value="{{ $role->id }}">
                                                            <input type="hidden" name="permission"
                                                                value="{{ $permission->name }}">

                                                            <input type="checkbox" name="has_permission"
                                                                onchange="this.form.submit()"
                                                                {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                        </form>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="{{ count($roles) + 1 }}" class="text-center">No permissions found.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Bootstrap & jQuery Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                localStorage.setItem('activeTab', $(e.target).attr('href'));
            });

            var activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                $('#permissionsTab a[href="' + activeTab + '"]').tab('show');
            }
        });
    </script>
@endsection
