<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Message;
use App\Models\Accessory;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\File;
use App\Models\FireExtinguisher;
use App\Models\FireSuppressionSystem;
use App\Models\WatermistSystem;
use App\Notifications\ContactMessageNotification;
use Illuminate\Notifications\DatabaseNotification;
use App\Notifications\ServiceRequestNotification;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use App\Models\Coupon;
use Illuminate\Support\Facades\Session;
use App\Models\Checkout;
use App\Models\CheckoutItem;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Mail\OrderInvoiceMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class ProfileController extends Controller
{

    //home page

    public function index()
    {
        $providers = User::where('user_type', 'provider')
            ->whereHas('serviceProvider', function ($query) {
                $query->where('status', 'approved');
            })
            ->with('serviceProvider')
            ->get();

        return view('dashboard.index', compact('providers'));
    }

    //about

    public function indexAbout()
    {
        $providers = User::where('user_type', 'provider')
            ->whereHas('serviceProvider', function ($query) {
                $query->where('status', 'approved');
            })
            ->with('serviceProvider')
            ->get();

        return view('dashboard.about', compact('providers'));
    }


    //profile 
    public function showProfile()
    {
        $user = Auth::user();
        return view('users.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile_no' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            $destinationPath = public_path('img/profile');
            File::ensureDirectoryExists($destinationPath, 0755, true);

            if ($user->profile_image && file_exists(public_path($user->profile_image))) {
                unlink(public_path($user->profile_image));
            }
            $file = $request->file('profile_image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $fileName);

            $user->profile_image = 'img/profile/' . $fileName;
        }
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->mobile_no = $request->mobile_no;
        $user->address = $request->address;
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }


    //services page

    public function indexService()
    {
        $services = Service::where('status', 1)->select('id', 'service_name')->get();
        return view('dashboard.services', compact('services'));
    }

    public function getServices()
    {
        $services = Service::select('service_name')->distinct()->get();
        return response()->json($services);
    }

    public function getSubServices($service_name)
    {
        $subServices = Service::where('service_name', $service_name)
            ->select('sub_service_name')
            ->where('status', 1)
            ->distinct()
            ->get();
        return response()->json($subServices);
    }

    public function getProviders($sub_service_name)
    {
        $providers = Service::where('sub_service_name', $sub_service_name)
            ->with('provider')
            ->where('status', 1)
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->provider->id,
                    'name' => $service->provider->first_name . ' ' . $service->provider->last_name
                ];
            });

        return response()->json($providers);
    }


    public function storeServiceRequest(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You need to login first to submit a service request!'
            ], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:500',
            'contact' => 'required|numeric|digits_between:10,15',
            'service_id' => 'required|exists:services,service_name',
            'sub_service_id' => 'nullable|exists:services,sub_service_name',
            'provider_id' => 'required|exists:users,id',
        ]);

        $serviceId = \App\Models\Service::where('service_name', $request->service_id)->value('id');

        $subServiceId = \App\Models\Service::where('sub_service_name', $request->sub_service_id)->value('id');

        $serviceRequest = ServiceRequest::create([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'contact' => $request->contact,
            'service_id' => $serviceId,
            'sub_service_id' => $subServiceId,
            'provider_id' => $request->provider_id,
            'created_at' => now(),
        ]);

        $provider = User::find($request->provider_id);
        $user = $request->input('name');
        $service = $request->input('service_id') ?? null;
        $subService = $request->input('sub_service_id') ?? null;
        $submissionTime = $serviceRequest->created_at->format('Y-m-d H:i:s');

        if ($provider) {
            $provider->notify(new ServiceRequestNotification($user, $service, $subService, $submissionTime));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Service request submitted successfully!'
        ]);
    }



    // contact page 
    function indexContact()
    {
        return view('dashboard.contact');
    }

    // public function storeMessage(Request $request)
    // {
    //     // Authenticated user ko fetch karo jiska user_type "user" hai
    //     $user = Auth::user();

    //     if (!$user || $user->user_type !== 'user') {
    //         // ✅ AJAX Request → JSON Error Response
    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Only users can send messages.',
    //             ], 403);
    //         }

    //         // ✅ Normal Request → Redirect with Error Message
    //         return redirect()->route('contact')->with('error', 'Only users can send messages.');
    //     }

    //     // ✅ Validation Rules
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|max:255',
    //         'subject' => 'required|string|max:255',
    //         'message' => 'required|string',
    //     ]);

    //     $message = Message::create([
    //         'user_id' => $user->id,
    //         'name'    => $request->name,
    //         'email'   => $request->email,
    //         'subject' => $request->subject,
    //         'message' => $request->message,
    //     ]);

    //     $admin = User::where('user_type', 'admin')->first();
    //  if ($admin) {
    //     Notification::send($admin, new ContactMessageNotification($message));
    //  }

    //     if ($request->ajax()) {
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Message sent successfully!',
    //         ]);
    //     }

    //     return redirect()->route('contact')->with('success', 'Message sent successfully!');
    // }


    public function storeMessage(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'user_id' => $user ? $user->id : null,
            'name'    => $request->name,
            'email'   => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        $admin = User::where('user_type', 'admin')->first();
        if ($admin) {
            Notification::send($admin, new ContactMessageNotification($message));
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Message sent successfully!',
            ]);
        }

        return redirect()->route('contact')->with('success', 'Message sent successfully!');
    }

    public function markAsRead($id)
    {
        $notification = DatabaseNotification::find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }


    public function indexProduct(Request $request)
    {
        $category = $request->input('category', 'all');

        $fetchProducts = function ($model) {
            return $model::select(
                'id',
                'category_id',
                'provider_id',
                'coupon_id',
                'name',
                'image',
                'price',
                'description',
                'stock',
                'warranty'
            )->get()->map(function ($product) {
                if ($product->coupon_id) {
                    $coupon = DB::table('coupons')->where('id', $product->coupon_id)->where('status', 'active')->first();

                    if ($coupon) {
                        $product->price = $coupon->final_price;
                    }
                }
                return $product;
            });
        };

        if ($category === 'accessories') {
            $products = $fetchProducts(Accessory::class);
        } elseif ($category === 'fire-extinguishers') {
            $products = $fetchProducts(FireExtinguisher::class);
        } elseif ($category === 'fire-suppression') {
            $products = $fetchProducts(FireSuppressionSystem::class);
        } elseif ($category === 'watermist') {
            $products = $fetchProducts(WatermistSystem::class);
        } else {
            $products = collect()
                ->merge($fetchProducts(Accessory::class))
                ->merge($fetchProducts(FireExtinguisher::class))
                ->merge($fetchProducts(FireSuppressionSystem::class))
                ->merge($fetchProducts(WatermistSystem::class));
        }

        return view('dashboard.product', compact('products', 'category'));
    }


    public function filterProducts(Request $request)
    {
        $category = $request->input('category', 'all');

        if ($category === 'accessories') {

            $products = Accessory::select('*')->get();
        } elseif ($category === 'fire-extinguishers') {
            $products = FireExtinguisher::select('*')->get();
        } elseif ($category === 'fire-suppression') {
            $products = FireSuppressionSystem::select('*')->get();
        } elseif ($category === 'watermist') {
            $products = WatermistSystem::select('*')->get();
        } else {
            $products = collect()
                ->merge(Accessory::select('*')->get())
                ->merge(FireExtinguisher::select('*')->get())
                ->merge(FireSuppressionSystem::select('*')->get())
                ->merge(WatermistSystem::select('*')->get());
        }


        return view('partials.product-list', compact('products'));
    }

    public function fetchProductDetails(Request $request)
    {
        $productId = $request->input('product_id');
        $categoryId = $request->input('category_id');

        if ($categoryId == 1) {
            $product = Accessory::select('*')->find($productId);
        } elseif ($categoryId == 2) {
            $product = FireExtinguisher::select(
                'id',
                'coupon_id',
                'category_id',
                'provider_id',
                'name',
                'image',
                'price',
                'description',
                'fire_class',
                'suitability',
                'capacity',
                'extinguishing_agent',
                'discharge_time',
                'working_pressure',
                'cylinder_material',
                'operating_temprature',
                'weight',
                'stock',
                'warranty',
            )->find($productId);
        } elseif ($categoryId == 3) {
            $product = FireSuppressionSystem::select(
                'id',
                'category_id',
                'provider_id',
                'name',
                'image',
                'price',
                'description',
                'suppression_type',
                'installation_type',
                'application_area',
                'cylinder_capacity',
                'activation_method',
                'response_time',
                'working_temprature_range',
                'stock',
                'warranty',
                'coupon_id'
            )->find($productId);
        } elseif ($categoryId == 4) {
            $product = WatermistSystem::select(
                'id',
                'category_id',
                'provider_id',
                'name',
                'image',
                'price',
                'description',
                'technology_type',
                'nozzle_type',
                'working_pressure',
                'droplet_size',
                'flow_rate',
                'application_area',
                'stock',
                'warranty',
                'coupon_id'
            )->find($productId);
        } else {
            return response()->json(['error' => 'Invalid category'], 400);
        }

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $coupon = null;
        if (!empty($product->coupon_id)) {
            $coupon = DB::table('coupons')
                ->where('id', $product->coupon_id)
                ->where('category_id', $categoryId)
                ->where('product_id', $productId)
                ->where('provider_id', $product->provider_id)
                ->where('status', 'active')
                ->where('expiry_date', '>=', now())
                ->whereColumn('used_count', '<', 'max_usage')
                ->first();
        }

        return response()->json([
            'product' => $product,
            'coupon' => $coupon,
        ]);
    }



    // CART
    public function viewCart()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        $user_id = Auth::id();

        $cartItems = Cart::where('user_id', $user_id)->get();

        $grandTotal = $cartItems->sum(function ($item) {
            return $item->quantity * ($item->product->price ?? 0);
        });

        return view('dashboard.cart', compact('cartItems', 'grandTotal'));
    }


    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Please log in first'], 401);
        }

        $user_id = Auth::id();
        $provider_id = $request->provider_id;
        $category_id = $request->category_id;
        $product_id = $request->product_id;

        $price = $this->getProductPrice($category_id, $product_id);
        if (!$price) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $coupon = DB::table('coupons')
            ->where('provider_id', $provider_id)
            ->where('category_id', $category_id)
            ->where('product_id', $product_id)
            ->where('status', 'active')
            ->where('expiry_date', '>=', now())
            ->first();

        $cartItem = Cart::where('user_id', $user_id)
            ->where('provider_id', $provider_id)
            ->where('category_id', $category_id)
            ->where('product_id', $product_id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += 1;
            $cartItem->save();
        } else {
            $cartData = [
                'user_id' => $user_id,
                'provider_id' => $provider_id,
                'category_id' => $category_id,
                'product_id' => $product_id,
                'quantity' => 1,
                'price' => $price,
                'coupon_id' => $coupon ? $coupon->id : null,
            ];

            Cart::create($cartData);
        }

        return response()->json(['success' => 'Product added to cart!']);
    }

    private function getProductPrice($category_id, $product_id)
    {
        if ($category_id == 1) {
            return \App\Models\Accessory::where('id', $product_id)->value('price');
        } elseif ($category_id == 2) {
            return \App\Models\FireExtinguisher::where('id', $product_id)->value('price');
        } elseif ($category_id == 3) {
            return \App\Models\FireSuppressionSystem::where('id', $product_id)->value('price');
        } elseif ($category_id == 4) {
            return \App\Models\WaterMistSystem::where('id', $product_id)->value('price');
        }

        return null;
    }

    public function applyCoupon(Request $request)
    {
        $coupon = Coupon::where('coupon_code', $request->coupon_code)->first();
        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Invalid coupon']);
        }

        $cartItems = Cart::where('user_id', Auth::id())->get();
        foreach ($cartItems as $item) {
            $item->final_price = null;
            $item->save();
        }

        $newGrandTotal = 0;
        $updatedCartItems = [];
        $discountApplied = false;

        foreach ($cartItems as $item) {
            if (
                $item->product_id == $coupon->product_id &&
                $item->product->provider_id == $coupon->provider_id &&
                $item->product->category_id == $coupon->category_id &&
                !$discountApplied
            ) {
                $discountApplied = true;
                $finalPrice = $coupon->final_price;
                $item->final_price = $finalPrice;
                $item->save();
                $totalPrice = $item->quantity * $finalPrice;
            } else {
                $totalPrice = $item->quantity * ($item->final_price ?? $item->product->price);
            }

            $newGrandTotal += $totalPrice;
            $updatedCartItems[] = [
                'item_id' => $item->id,
                'product_id' => $item->product_id,
                'final_price' => number_format($item->final_price ?? $item->product->price, 2),
                'total' => number_format($totalPrice, 2)
            ];
        }

        // ✅ Coupon discount को session में store करें
        session()->put('coupon_applied', $discountApplied);
        session()->put('grand_total', $newGrandTotal);

        return response()->json([
            'success' => true,
            'new_grand_total' => number_format($newGrandTotal, 2),
            'updated_cart_items' => $updatedCartItems,
            'coupon_applied' => $discountApplied
        ]);
    }



    public function getTotal()
    {
        $cartItems = Cart::where('user_id', Auth::id())->get();
        $grandTotal = 0;

        foreach ($cartItems as $item) {
            $totalPrice = $item->quantity * ($item->final_price ?? $item->product->price);
            $grandTotal += $totalPrice;
        }
        $couponApplied = session()->get('coupon_applied', false);

        return response()->json([
            'success' => true,
            'new_grand_total' => number_format($grandTotal, 2),
            'coupon_applied' => $couponApplied
        ]);
    }


    public function checkStock(Request $request)
{
    $product = null;

    if ($request->category_id == 1) {
        $product = DB::table('accessories')->where('id', $request->product_id)->first();
    } elseif ($request->category_id == 2) {
        $product = DB::table('fire_extinguishers')->where('id', $request->product_id)->first();
    } elseif ($request->category_id == 3) {
        $product = DB::table('fire_suppression_systems')->where('id', $request->product_id)->first();
    } elseif ($request->category_id == 4) {
        $product = DB::table('watermist_systems')->where('id', $request->product_id)->first();
    }

    if ($product) {
        return response()->json(['stock' => $product->stock]); 
    } else {
        return response()->json(['stock' => 0]);
    }
}
 

public function checkCartStock(Request $request)
{
    $cartItem = Cart::find($request->id);
    if (!$cartItem) {
        return response()->json(['stock' => 0]);
    }

    $product = null;

    switch ($cartItem->category_id) {
        case 1:
            $product = DB::table('accessories')->where('id', $cartItem->product_id)->first();
            break;
        case 2:
            $product = DB::table('fire_extinguishers')->where('id', $cartItem->product_id)->first();
            break;
        case 3:
            $product = DB::table('fire_suppression_systems')->where('id', $cartItem->product_id)->first();
            break;
        case 4:
            $product = DB::table('watermist_systems')->where('id', $cartItem->product_id)->first();
            break;
    }

    if ($product) {
        return response()->json(['stock' => $product->stock]);
    } else {
        return response()->json(['stock' => 0]);
    }
}



    public function removeFromCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Please log in first'], 401);
        }

        $cartItem = Cart::where('id', $request->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($cartItem) {
            $cartItem->delete();
            return response()->json(['success' => 'Item removed from cart']);
        }

        return response()->json(['error' => 'Item not found'], 404);
    }



    public function updateCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Please log in first'], 401);
        }

        $cartItem = Cart::where('id', $request->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($cartItem) {
            $cartItem->quantity = $request->quantity;
            $cartItem->save();
            return response()->json(['success' => 'Cart updated successfully']);
        }

        return response()->json(['error' => 'Item not found'], 404);
    }

    public function removeCoupon()
    {
        $cartItems = Cart::where('user_id', Auth::id())->get();
        $newGrandTotal = 0;
        $updatedCartItems = [];

        foreach ($cartItems as $item) {
            $originalPrice = $item->product->price;
            $item->final_price = null;
            $item->save();

            // Calculate total
            $totalPrice = $item->quantity * $originalPrice;
            $newGrandTotal += $totalPrice;

            $updatedCartItems[] = [
                'item_id' => $item->id,
                'original_price' => number_format($originalPrice, 2),
                'total' => number_format($totalPrice, 2)
            ];
        }

        return response()->json([
            'success' => true,
            'new_grand_total' => number_format($newGrandTotal, 2),
            'updated_cart_items' => $updatedCartItems
        ]);
    }


    // CHECKOUT

    public function storeCheckout(Request $request)
    {
        if (!Session::has('user_id')) {
            Session::put('user_id', $request->user_id);
        }

        return redirect()->route('checkout');
    }

    private function getProductName($categoryId, $productId)
    {
        switch ($categoryId) {
            case 1:
                return Accessory::where('id', $productId)->value('name');
            case 2:
                return FireExtinguisher::where('id', $productId)->value('name');
            case 3:
                return FireSuppressionSystem::where('id', $productId)->value('name');
            case 4:
                return WatermistSystem::where('id', $productId)->value('name');
            default:
                return 'Unknown Product';
        }
    }


    public function showCheckout()
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return redirect()->route('cart')->with('error', 'Your cart is empty!');
        }

        $cartItems = Cart::where('user_id', $userId)->get();

        $grandTotal = 0;

        foreach ($cartItems as $item) {
            $itemTotal = ($item->final_price ? $item->final_price * $item->quantity : $item->price * $item->quantity);
            $grandTotal += $itemTotal;

            $item->product_name = $this->getProductName($item->category_id, $item->product_id);
        }

        return view('dashboard.checkout', compact('cartItems', 'grandTotal'));
    }


    public function store(Request $request)
{
    $user = Auth::user();
    if (!$user) {
        return back()->with('error', 'User not authenticated!');
    }

    $cartItems = Cart::where('user_id', $user->id)->get();
    if ($cartItems->isEmpty()) {
        return back()->with('error', 'Your cart is empty!');
    }

    // ✅ Grand total using final_price (if applied), else fallback to regular price
    $grandTotal = $cartItems->sum(function ($item) {
        return $item->quantity * ($item->final_price ?? $item->price);
    });

    // ✅ Stock check for each product
    foreach ($cartItems as $item) {
        $table = $this->getCategoryTable($item->category_id); 
        $product = DB::table($table)->where('id', $item->product_id)->first();

        if (!$product) {
            return back()->with('error', "Product not found!");
        }

        if ($product->stock < $item->quantity) {
            return back()->with('error', "Only $product->stock quantity available for {$product->name}. Out of stock!");
        }
    }

    // ✅ Save checkout details
    $checkout = Checkout::create([
        'user_id' => $user->id,
        'name' => $request->name,
        'mobile' => $request->mobile,
        'email' => $request->email,
        'address_line1' => $request->address_line1,
        'address_line2' => $request->address_line2,
        'city' => $request->city,
        'state' => $request->state,
        'country' => $request->country,
        'pincode' => $request->pincode,
        'payment_method' => $request->payment_method,
        'grand_total' => $grandTotal,
        'status' => 0,
    ]);

    // ✅ Create checkout_items and update stock
    foreach ($cartItems as $item) {
        CheckoutItem::create([
            'checkout_id' => $checkout->id,
            'product_id' => $item->product_id,
            'provider_id' => $item->provider_id,
            'category_id' => $item->category_id,
            'quantity' => $item->quantity,
            'price' => $item->price,
            'final_price' => $item->final_price 
                ? $item->quantity * $item->final_price 
                : $item->quantity * $item->price,
        ]);

        $table = $this->getCategoryTable($item->category_id);
        DB::table($table)->where('id', $item->product_id)->decrement('stock', $item->quantity);
    }

    // ✅ Clear cart
    Cart::where('user_id', $user->id)->delete();

    // ✅ Redirect based on payment method
    if ($request->payment_method == 'cod') {
        $checkoutItems = CheckoutItem::where('checkout_id', $checkout->id)->get();

        return view('emails.invoice', [
            'checkout' => $checkout,
            'items' => $checkoutItems
        ]);
    }

    return redirect()->route('dashboard.paypal.pay', ['checkout' => $checkout->id]);
}



    // public function store(Request $request)
    // {
    //     $user = Auth::user();
    //     if (!$user) {
    //         return back()->with('error', 'User not authenticated!');
    //     }

    //     $cartItems = Cart::where('user_id', $user->id)->get();
    //     if ($cartItems->isEmpty()) {
    //         return back()->with('error', 'Your cart is empty!');
    //     }

    //     $grandTotal = $cartItems->sum(fn($item) => $item->quantity * $item->price);

    //     foreach ($cartItems as $item) {
    //         $table = $this->getCategoryTable($item->category_id);
    //         $product = DB::table($table)->where('id', $item->product_id)->first();

    //         if (!$product) {
    //             return back()->with('error', "Product not found!");
    //         }

    //         if ($product->stock < $item->quantity) {
    //             return back()->with('error', "Only $product->stock quantity available for {$product->name}. Out of stock!");
    //         }
    //     }

    //     // Create Checkout Entry
    //     $checkout = Checkout::create([
    //         'user_id' => $user->id,
    //         'name' => $request->name,
    //         'mobile' => $request->mobile,
    //         'email' => $request->email,
    //         'address_line1' => $request->address_line1,
    //         'address_line2' => $request->address_line2,
    //         'city' => $request->city,
    //         'state' => $request->state,
    //         'country' => $request->country,
    //         'pincode' => $request->pincode,
    //         'payment_method' => $request->payment_method,
    //         'grand_total' => $grandTotal,
    //         'status' => 0,
    //     ]);

    //     $checkoutItems = [];

    //     foreach ($cartItems as $item) {
    //         $checkoutItems[] = CheckoutItem::create([
    //             'checkout_id' => $checkout->id,
    //             'product_id' => $item->product_id,
    //             'provider_id' => $item->provider_id,
    //             'category_id' => $item->category_id,
    //             'quantity' => $item->quantity,
    //             'price' => $item->price,
    //             'final_price' => $item->quantity * $item->price,
    //         ]);

    //         $table = $this->getCategoryTable($item->category_id);
    //         DB::table($table)->where('id', $item->product_id)->decrement('stock', $item->quantity);
    //     }
       

    //     Cart::where('user_id', $user->id)->delete();
       
    //     if ($request->payment_method == 'cod') {
    //         return view('emails.invoice', [
    //             'checkout' => $checkout,
    //             'items' => $checkoutItems
    //         ]);
    //     }

    //     return redirect()->route('dashboard.paypal.pay', ['checkout' => $checkout->id]);
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

    public function paypalView($checkout_id)
    {

        $checkout = Checkout::with('items.product')->findOrFail($checkout_id);

        return view('dashboard.paypal', compact('checkout'));
    }


    public function cancelOrder(Request $request)
    {
        $order = CheckoutItem::find($request->order_id);

        if (!$order) {
            return Response::json(['success' => false, 'message' => 'Order not found.']);
        }

        if (now()->diffInHours($order->created_at) > 24) {
            return Response::json(['success' => false, 'message' => 'Order cancellation time exceeded.']);
        }

        if (!in_array($order->tracking_status, ['confirmed', 'processed'])) {
            return Response::json(['success' => false, 'message' => 'Order cannot be canceled at this stage.']);
        }

        $table = $this->getCategoryTable($order->category_id);
        DB::table($table)->where('id', $order->product_id)->increment('stock', $order->quantity);

        $order->delete();

        return Response::json(['success' => true, 'message' => 'Order canceled successfully']);
    }


    public function orderHistory()
    {
        $user_id = auth()->id();

        $latestOrder = CheckoutItem::whereHas('checkout', function ($query) use ($user_id) {
            $query->where('user_id', $user_id)
                ->whereDate('created_at', today());
        })->where('tracking_status', '!=', 'arrived')
            ->latest('created_at')
            ->get();

        $pastOrders = CheckoutItem::whereHas('checkout', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->where('tracking_status', 'arrived')
            ->whereDate('arrival_date', '>=', now()->subDays(2))
            ->latest('created_at')
            ->get();

        foreach ($latestOrder as $item) {
            $categoryTable = $this->getCategoryTable($item->category_id);
            $product = DB::table($categoryTable)->where('id', $item->product_id)->first();
            $provider = User::find($item->provider_id);

            $item->product_name = $product->name ?? 'Unknown';
            $item->provider_name = $provider ? $provider->first_name . ' ' . $provider->last_name : 'Unknown';
        }

        foreach ($pastOrders as $item) {
            $categoryTable = $this->getCategoryTable($item->category_id);
            $product = DB::table($categoryTable)->where('id', $item->product_id)->first();
            $provider = User::find($item->provider_id);

            $item->product_name = $product->name ?? 'Unknown';
            $item->provider_name = $provider ? $provider->first_name . ' ' . $provider->last_name : 'Unknown';
        }

        return view('dashboard.checkout-detail', compact('latestOrder', 'pastOrders'));
    }


    public function trackOrder($orderId)
    {
        $order = CheckoutItem::where('id', $orderId)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found!']);
        }

        $trackingSteps = [
            'confirmed' => 'Order Placed',
            'processed' => 'Order Processing',
            'shipped' => 'Shipped',
            'en_route' => 'Out for Delivery',
            'arrived' => 'Arrived',
        ];

        $orderStatus = $order->tracking_status;
        $progressStep = array_search($orderStatus, array_keys($trackingSteps));

        $arrivalDate = $order->arrival_date ? Carbon::parse($order->arrival_date)->format('d M Y') : 'Not Available';

        return response()->json([
            'success' => true,
            'trackingSteps' => $trackingSteps,
            'currentStep' => $progressStep,
            'arrivalDate' => $arrivalDate,
        ]);
    }
}
