<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pay With Paypal</title>

    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    
        .full-page-center {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
    
        .paypal-btn {
            background-color: #0070ba;
            color: white;
            padding: 10px 30px;
            font-weight: bold;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
    
        .paypal-btn:hover {
            background-color: #005c99;
            color: white;
            text-decoration: none;
        }
    
        .table th, .table td {
            vertical-align: middle;
        }
    </style>



</head>
<body>
   <div class="full-page-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card shadow-lg rounded">
                    <div class="card-body text-center">

                        <h3 class="mb-4 text-uppercase fw-bold">Checkout Details</h3>

                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($checkout->items as $item)
                                    <tr>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->price, 2) }}</td>
                                        <td>${{ number_format($item->final_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <a href="{{ route('dashboard.paypal', ['checkout_id' => $checkout->id]) }}" class="paypal-btn mt-3">
                            Pay with PayPal
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<!-- Bootstrap 5 JS (optional - if you use dropdowns, modals etc.) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoA6DQZ7FjzF1gYh

    
</body>
</html>