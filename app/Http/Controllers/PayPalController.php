<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checkout;
use Illuminate\Support\Facades\Http;
use App\Models\Cart;
use App\Models\CheckoutItem;
use Illuminate\Support\Facades\DB;

class PayPalController extends Controller
{
    private function getAccessToken()
    {
        $clientId = config('paypal.client_id');
        $secret = config('paypal.secret');

        $response = Http::asForm()->withBasicAuth($clientId, $secret)
            ->post('https://api-m.sandbox.paypal.com/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);
        // dd($response->json()['access_token']);
        return $response->json()['access_token'] ?? null;
    }
    public function payment($checkout_id)
    {


        $checkout = Checkout::findOrFail($checkout_id);

        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)->post('https://api-m.sandbox.paypal.com/v2/checkout/orders', [
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => number_format($checkout->grand_total, 2, '.', '')
                    ]
                ]
            ],
            "application_context" => [
                "brand_name" => "Fire Refill Checkout",
                "landing_page" => "LOGIN",
                "user_action" => "PAY_NOW",
                "return_url" => route('paypal.success', ['checkout_id' => $checkout->id]),
                "cancel_url" => route('paypal.cancel', ['checkout_id' => $checkout->id]),
            ]
        ]);

        // dd($response);

        $data = $response->json();
        // dd($data);
        if (isset($data['id'])) {

            foreach ($data['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->away($link['href']);
                }
            }
        }

        return redirect()->route('checkout')->with('error', 'Something went wrong with PayPal.');
    }


   
// public function success(Request $request, $checkout_id)
// {
//     $accessToken = $this->getAccessToken();

//     $url = "https://api-m.sandbox.paypal.com/v2/checkout/orders/{$request->token}/capture";

//     // Send the request with NO BODY using `send()`
//     $response = Http::withToken($accessToken)
//         ->withHeaders([
//             'Content-Type' => 'application/json',
//             'Accept' => 'application/json',
//         ])
//         ->send('POST', $url);

//     $data = $response->json();

//     if (isset($data['status']) && $data['status'] === 'COMPLETED') {
//         // dd(isset($data['status']) && $data['status'] === 'COMPLETED');
//         $checkout = Checkout::findOrFail($checkout_id);
//         // dd($checkout);
//         $checkout->status = 1;
//         // dd($checkout->status);
//         $checkout->save();
      

//         return redirect()->route('order.history')->with('success', 'Payment successful.');
//     }

//     return redirect()->route('checkout')->with('error', 'Payment failed.');
// }
    

private function getCategoryTable($category_id)
{
    $tables = [
        1 => 'accessories',
        2 => 'fire_extinguishers',
        3 => 'fire_suppression_systems',
        4 => 'watermist_systems',
    ];

    return $tables[$category_id] ?? null;
}



public function success(Request $request, $checkout_id)
{
    $accessToken = $this->getAccessToken();

    $url = "https://api-m.sandbox.paypal.com/v2/checkout/orders/{$request->token}/capture";

    $response = Http::withToken($accessToken)
        ->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
        ->send('POST', $url);

    $data = $response->json();

    if (isset($data['status']) && $data['status'] === 'COMPLETED') {
        $checkout = Checkout::findOrFail($checkout_id);
        $checkout->status = 1;
        $checkout->save();
    
        $checkoutItems = CheckoutItem::where('checkout_id', $checkout->id)->get();
    
        foreach ($checkoutItems as $item) {
            $table = $this->getCategoryTable($item->category_id);
            $product = DB::table($table)->where('id', $item->product_id)->first();
            $item->product_name = $product->name ?? 'N/A';
        }
    
        return view('emails.invoice', [
            'checkout' => $checkout,
            'items' => $checkoutItems
        ]);
    }
    

    return redirect()->route('checkout')->with('error', 'Payment failed.');
}

    public function cancel($checkout_id)
    {
        return redirect()->route('checkout')->with('error', 'Payment cancelled.');
    }
}
