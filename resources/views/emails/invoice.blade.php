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
            max-width: 150px;
            display: block;
            margin: 0 auto 10px;
        }
        .header h2 {
            margin: 0;
            color: #333;
        }
        .invoice-id {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 12px;
            color: #777;
        }
        .invoice-details {
            margin: 20px 0;
        }
        .invoice-details p {
            margin: 5px 0;
            font-size: 16px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .invoice-table th, .invoice-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .invoice-table th {
            background-color: #f2f2f2;
            color: #333;
        }
        .total {
            font-weight: bold;
            font-size: 18px;
            color: #d9534f;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
        .actions {
            margin-top: 20px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn-back {
            background-color: #6c757d;
            color: white;
        }
        .btn-download {
            background-color: #007bff;
            color: white;
        }
        @media print {
            .btn-back, .btn-download {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="invoice-container">
    <div class="header">
        <img src="{{ asset('img/Fire_Refill_logo_with_black_and_red_colors-removebg-preview.png') }}" alt="RefillEase Logo">

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

    <table class="invoice-table" border="1" cellspacing="0" cellpadding="10" width="100%">
        <thead style="background-color: #f0f0f0;">
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price (Each)</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = 0;
            @endphp
    
            @foreach ($items as $item)
                @php
                    // Calculate price and total with final_price logic
                    $unitPrice = $item->quantity > 0 
                        ? (($item->final_price ?? ($item->price * $item->quantity)) / $item->quantity) 
                        : $item->price;
    
                    $total = $item->final_price ?? ($item->price * $item->quantity);
                    $grandTotal += $total;
                @endphp
                <tr>
                    <td>{{ $item->product_name ?? 'N/A' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($unitPrice, 2) }}</td>
                    <td>${{ number_format($total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Grand Total:</strong></td>
                <td><strong>${{ number_format($grandTotal, 2) }}</strong></td>
            </tr>
        </tfoot>
        
    </table>
    

    <div class="actions">
        <a href="{{ route('order.history') }}" class="btn btn-back">← </a>
        <a href="{{ route('invoice.download', $checkout->id) }}" class="btn btn-download">📄 Download Invoice</a>

    </div>

    <div class="footer">
        <p>For any queries, contact us at <strong>support@example.com</strong></p>
        <p style="color: red; font-weight: bold; margin-top: 10px;">
            ⚠️ Note: Please download and keep a copy of this invoice for future reference.  
            This invoice will be required in case of any service request, warranty claim, or refund inquiry.
        </p>
    </div>
    
</div>

</body>
</html>
