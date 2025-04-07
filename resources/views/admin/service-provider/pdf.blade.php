<!DOCTYPE html>
<html>
<head>
    <title>Providers List</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { display: flex; justify-content: space-between; align-items: center; }
        .header img { max-width: 150px; }
        .header h2, .header .company-name { margin: 0; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('img/Fire_Refill_logo_with_black_and_red_colors-removebg-preview.png') }}" alt="Company Logo">
        <div style="flex: 1; text-align: center;">
            <h2>Service Providers List</h2>
            <div class="company-name">
                RefillEase
            </div>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Business Name</th>
                <th>License</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($providers as $provider)
            <tr>
                <td>{{ $provider->id }}</td>
                <td>{{ $provider->first_name }}</td>
                <td>{{ $provider->last_name }}</td>
                <td>{{ $provider->email }}</td>
                <td>{{ $provider->mobile_no }}</td>
                <td>{{ $provider->address }}</td>
                <td>{{ $provider->business_name }}</td>
                <td>
                    @if($provider->license)
                        <a href="{{ asset($provider->license) }}" target="_blank">View License</a>
                    @else
                        No License
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
