<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .invoice-container {
            max-width: 700px;
            background: #ffffff;
            margin: auto;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            position: relative;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }
        .header img {
            max-width: 100px;
            display: block;
            margin: 0 auto 10px;
        }
        .header h2 {
            margin: 0;
            color: #333;
        }
        .invoice-id { text-align: right; font-size: 12px; color: #777; }
        .invoice-details p { margin: 5px 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; font-size: 16px; color: #d9534f; }
    </style>
</head>
<body>

<div class="invoice-container">
    <div class="header">
        <img src="{{ public_path('img/Fire_Refill_logo_with_black_and_red_colors-removebg-preview.png') }}" alt="RefillEase Logo">
        <h2>RefillEase</h2>
    </div>

    <div class="invoice-id">
        Invoice #{{ $checkout->id }}
    </div>

    <div class="invoice-details">
        <p><strong>Name:</strong> {{ $checkout->name }}</p>
        <p><strong>Email:</strong> {{ $checkout->email }}</p>
        <p><strong>Phone:</strong> {{ $checkout->mobile }}</p>
        <p><strong>Address:</strong> {{ $checkout->address_line1 }}, {{ $checkout->address_line2 }}, {{ $checkout->city }}, {{ $checkout->state }}</p>
        <p><strong>Payment Method:</strong> 
            {{ $checkout->payment_method == 'cod' ? 'Cash on Delivery' : ucfirst($checkout->payment_method) }}
        </p>
        
    </div>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
            <tr>
                <td>{{ $product->name ?? 'N/A' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->price, 2) }}</td>
                <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="total">Grand Total</td>
                <td class="total">${{ number_format($checkout->grand_total, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

</body>
</html>
