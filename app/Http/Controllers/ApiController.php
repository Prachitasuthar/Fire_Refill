<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\ServiceRequestNotification;
use Carbon\Carbon;
use App\Models\User;
use App\Models\ServiceProvider;
use App\Models\ServiceRequest;
use App\Models\Checkout;
use App\Models\Accessory;
use App\Models\FireExtinguisher;
use App\Models\FireSuppressionSystem;
use App\Models\WatermistSystem;
use App\Models\CheckoutItem;
use App\Models\Service;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'mobile_no' => 'required|string|max:10',
            'address' => 'required|string',
            'user_type' => 'required|in:user,admin,provider',
        ]);

        if ($request->user_type === 'provider') {
            $providerValidation = $request->validate([
                'business_name' => 'required|string|max:255',
                'license' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);
        }

        $verificationToken = Str::random(60);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'mobile_no' => $validated['mobile_no'],
            'address' => $validated['address'],
            'user_type' => $validated['user_type'],
            'verification_token' => $verificationToken,
            'is_email_verified' => 0,
        ]);

        if ($user->user_type === 'provider') {
            $licenseFolder = public_path('img/licenses');
            if (!File::exists($licenseFolder)) {
                File::makeDirectory($licenseFolder, 0775, true);
            }

            $licenseFile = $request->file('license');
            $licensePath = $licenseFile->move($licenseFolder, $licenseFile->getClientOriginalName());

            ServiceProvider::create([
                'user_id' => $user->id,
                'business_name' => $request->business_name,
                'license' => 'img/licenses/' . $licenseFile->getClientOriginalName(),
                'status' => 'pending',
            ]);
        }
        Mail::send('emails.provider_verification', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email)->subject('Email Verification');
        });

        return response()->json([
            'message' => 'User registered successfully. Please check your email for verification.',
            'user' => $user
        ], 201);
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid login credentials'
            ], 401);
        }

        if (!$user->is_email_verified) {
            return response()->json([
                'status' => false,
                'message' => 'Please verify your email before logging in.'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $redirectUrl = match ($user->user_type) {
            'admin' => route('admin.dashboard'),
            'provider' => route('provider.dashboard'),
            default => route('dashboard.index'),
        };

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'token' => $token,
                'redirect_url' => $redirectUrl
            ]
        ], 200);
    }



    // Service Request

    public function getServiceRequests()
    {
        $user = Auth::user();

        if ($user->user_type === 'admin') {
            $requests = ServiceRequest::all();
        } elseif ($user->user_type === 'provider') {
            $requests = ServiceRequest::where('provider_id', $user->id)->get(); 
        } else {
            return response()->json(['message' => 'Unauthorized access!'], 403);
        }

        return response()->json(['service_requests' => $requests]);
    }

    public function acceptServiceRequest(Request $request)
    {
        $user = Auth::user();
        $serviceRequest = ServiceRequest::find($request->id);

        if (!$serviceRequest) {
            return response()->json(['message' => 'Service request not found!'], 404);
        }

        if ($user->user_type === 'admin' || ($user->user_type === 'provider' && $serviceRequest->provider_id == $user->id)) {
            $serviceRequest->status = 1;
            $serviceRequest->save();

            Mail::raw("Your service request has been accepted successfully.", function ($message) use ($serviceRequest) {
                $message->to($serviceRequest->email)
                    ->subject('Service Request Accepted');
            });

            return response()->json(['message' => 'Service request accepted and email sent successfully!']);
        }

        return response()->json(['message' => 'Unauthorized action!'], 403);
    }


    public function deleteServiceRequest(Request $request)
    {
        $user = Auth::user();
        $serviceRequest = ServiceRequest::find($request->id);

        if (!$serviceRequest) {
            return response()->json(['message' => 'Service request not found!'], 404);
        }

        if ($user->user_type === 'admin' || ($user->user_type === 'provider' && $serviceRequest->provider_id == $user->id)) {
            $userEmail = $serviceRequest->email;
            $reason = $request->reason;
            $serviceRequest->delete();

            Mail::raw("Your service request has been denied.\nReason: $reason", function ($message) use ($userEmail) {
                $message->to($userEmail)
                    ->subject('Service Request Denied');
            });

            return response()->json(['message' => 'Service request denied and email sent successfully!']);
        }

        return response()->json(['message' => 'Unauthorized action!'], 403);
    }

    public function updateRequestStatus(Request $request)
    {
        $user = auth()->user();

        
        if (!$user || $user->user_type !== 'admin') {
            return response()->json(['message' => 'Unauthorized! Only admin can update provider status.'], 403);
        }

        
        $request->validate([
            'id' => 'required|exists:service_providers,id',
            'status' => 'required|in:pending,approved,rejected',
            'reason' => 'required_if:status,rejected|max:255'
        ]);

     
        $provider = ServiceProvider::findOrFail($request->id);
        $provider->status = $request->status;

        
        if ($request->status === 'rejected') {
            $provider->rejection_reason = $request->reason;
            $provider->save();

            Mail::to($provider->user->email)->send(new \App\Mail\RejectionMail($provider->rejection_reason));

            return response()->json([
                'message' => 'Status updated to rejected and email sent successfully.'
            ]);
        }

        $provider->save();

        return response()->json([
            'message' => "Status updated successfully to {$request->status}.",
            'provider' => [
                'id' => $provider->id,
                'business_name' => $provider->business_name,
                'status' => $provider->status
            ]
        ]);
    }

    public function getBookingsData(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized!'], 401);
        }

        $query = Checkout::with(['checkoutItems' => function ($q) {
            $q->whereNull('deleted_at')->where('tracking_status', '!=', 'arrived');
        }, 'user']);

        if ($user->user_type === 'provider') {
            $query->whereHas('checkoutItems', function ($q) use ($user) {
                $q->where('provider_id', $user->id)->whereNull('deleted_at')->where('tracking_status', '!=', 'arrived');
            });
        }

        $bookings = $query->select('checkout.*')->get();

        $formattedData = $bookings->flatMap(function ($booking) use ($user) {
            return $booking->checkoutItems->map(function ($item) use ($booking, $user) {
                if ($user->user_type === 'provider' && $item->provider_id !== $user->id) {
                    return null;
                }

                $provider = User::where('id', $item->provider_id)->where('user_type', 'provider')->first();

                return [
                    'user_name' => optional($booking->user)->first_name . ' ' . optional($booking->user)->last_name ?? 'N/A',
                    'provider_name' => $provider ? $provider->first_name . ' ' . $provider->last_name : 'N/A',
                    'category_name' => $this->getCategoryName($item->category_id),
                    'product_name' => $this->getProductName($item),
                    'checkout_name' => $booking->name,
                    'mobile' => $booking->mobile,
                    'email' => $booking->email,
                    'address' => "{$booking->address_line1} {$booking->address_line2}, {$booking->city}, {$booking->state}",
                    'payment_method' => $booking->payment_method,
                    'final_price' => $item->final_price,
                    'payment_status' => $booking->status,
                    'status' => $item->tracking_status,
                    'order_date' => $booking->created_at->format('d-m-Y'),
                    'arrival_date' => $item->arrival_date ? Carbon::parse($item->arrival_date)->format('d-m-Y') : null,
                    'id' => $item->id,
                ];
            })->filter();
        });

        return DataTables::of($formattedData)->make(true);
    }

    private function getCategoryName($categoryId)
    {
        $categories = [
            1 => 'Accessory',
            2 => 'Fire Extinguisher',
            3 => 'Fire Suppression System',
            4 => 'Watermist System',
        ];
        return $categories[$categoryId] ?? 'Unknown';
    }

    private function getProductName($item)
    {
        if (!$item || !$item->category_id || !$item->product_id) {
            return 'Unknown';
        }

        return match ($item->category_id) {
            1 => Accessory::find($item->product_id)?->name,
            2 => FireExtinguisher::find($item->product_id)?->name,
            3 => FireSuppressionSystem::find($item->product_id)?->name,
            4 => WatermistSystem::find($item->product_id)?->name,
            default => 'N/A',
        };
    }

    public function updateArrivalDate(Request $request)
    {


        $request->validate([
            'id' => 'required|exists:checkout_items,id',
            'arrival_date' => 'nullable|date'
        ]);

        $user = Auth::user();
        $item = CheckoutItem::findOrFail($request->id);

        if ($user->user_type === 'provider' && $item->provider_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $item->arrival_date = $request->arrival_date;
        $item->save();



        return response()->json(['success' => true, 'message' => 'Arrival date updated successfully']);
    }

    public function updateTrackingStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:checkout_items,id'
        ]);

        $user = Auth::user();
        $order = CheckoutItem::findOrFail($request->id);

        if ($user->user_type === 'provider' && $order->provider_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $stages = ['confirmed', 'processed', 'shipped', 'en_route', 'arrived'];
        $currentIndex = array_search($order->tracking_status, $stages);

        if ($currentIndex !== false && $currentIndex < count($stages) - 1) {
            $order->tracking_status = $stages[$currentIndex + 1];
            $order->save();
        }

        return response()->json(['success' => true, 'message' => 'Tracking status updated successfully']);
    }

    public function deleteBooking(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:checkout_items,id'
        ]);

        $user = Auth::user();
        $bookingItem = CheckoutItem::find($request->id);

        if (!$bookingItem) {
            return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
        }

        if ($user->user_type === 'provider' && $bookingItem->provider_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $bookingItem->delete();

        return response()->json(['success' => true, 'message' => 'Booking removed successfully']);
    }

    public function editAccessory($id)
    {
        $accessory = Accessory::findOrFail($id);

        if (Auth::user()->user_type !== 'admin' && Auth::id() !== $accessory->provider_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json($accessory);
    }

    public function updateAccessory(Request $request, $id)
    {
        $accessory = Accessory::findOrFail($id);

        if (Auth::user()->user_type !== 'admin' && Auth::id() !== $accessory->provider_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'description' => 'sometimes|string',
            'weight' => 'sometimes|numeric|min:0',
            'power_source' => 'sometimes|string|max:255',
            'operating_voltage' => 'sometimes|string|max:255',
            'material' => 'sometimes|string|max:255',
            'working_temprature' => 'sometimes|string|max:255',
            'IP_routing' => 'sometimes|string|max:255',
            'stock' => 'sometimes|integer|min:0',
            'warranty' => 'sometimes|integer|min:0',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($request->has('remove_image') && $request->remove_image == '1') {
            if ($accessory->image) {
                $oldImagePath = public_path($accessory->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $validatedData['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($accessory->image) {
                $oldImagePath = public_path($accessory->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('img/accessories/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $image->move($destinationPath, $imageName);
            $validatedData['image'] = 'img/accessories/' . $imageName;
        }

        $accessory->fill($validatedData)->save();

        return response()->json(['success' => 'Accessory updated successfully!']);
    }

    public function destroyAccessory($id)
    {
        $accessory = Accessory::find($id);

        if (!$accessory) {
            return response()->json(['success' => false, 'message' => 'Accessory not found!'], 404);
        }

        if (Auth::user()->user_type !== 'admin' && Auth::id() !== $accessory->provider_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $accessory->delete();

        return response()->json(['success' => true, 'message' => 'Accessory deleted successfully!']);
    }

    public function editFireExtinguisher($id)
    {
        $fireExtinguisher = FireExtinguisher::find($id);

        if (!$fireExtinguisher) {
            return response()->json(['success' => false, 'message' => 'Fire Extinguisher not found!'], 404);
        }

        return response()->json($fireExtinguisher);
    }

    public function updateFireExtinguisher(Request $request, $id)
    {
        $fireExtinguisher = FireExtinguisher::find($id);

        if (!$fireExtinguisher) {
            return response()->json(['success' => false, 'message' => 'Fire Extinguisher not found!'], 404);
        }

        if (Auth::user()->user_type !== 'admin' && Auth::id() !== $fireExtinguisher->provider_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'description' => 'sometimes|required|string',
            'fire_class' => 'sometimes|required|string|max:255',
            'suitability' => 'sometimes|required|string|max:255',
            'capacity' => 'sometimes|required|string|max:255',
            'extinguishing_agent' => 'sometimes|required|string|max:255',
            'discharge_time' => 'sometimes|required|string|max:255',
            'working_pressure' => 'sometimes|required|string|max:255',
            'cylinder_material' => 'sometimes|required|string|max:255',
            'operating_temprature' => 'sometimes|required|string|max:255',
            'weight' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'warranty' => 'sometimes|required|string|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($request->has('remove_image') && $request->remove_image == '1') {
            if ($fireExtinguisher->image) {
                $oldImagePath = public_path($fireExtinguisher->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $fireExtinguisher->image = null;
        }

        if ($request->hasFile('image')) {
            if ($fireExtinguisher->image) {
                $oldImagePath = public_path($fireExtinguisher->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('img/extinguisher/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $image->move($destinationPath, $imageName);

            $fireExtinguisher->image = 'img/extinguisher/' . $imageName;
        }

        // Sirf wahi fields update hongi jo request me bheji gayi hain
        $fireExtinguisher->fill($request->except(['image', '_method', 'remove_image']))->save();

        return response()->json(['success' => true, 'message' => 'Fire Extinguisher updated successfully!']);
    }

    public function destroyFireExtinguisher($id)
    {
        $fireExtinguisher = FireExtinguisher::find($id);

        if (!$fireExtinguisher) {
            return response()->json(['success' => false, 'message' => 'Fire Extinguisher not found!'], 404);
        }

        if (Auth::user()->user_type !== 'admin' && Auth::id() !== $fireExtinguisher->provider_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $fireExtinguisher->delete();

        return response()->json(['success' => true, 'message' => 'Fire Extinguisher deleted successfully!']);
    }

    public function editFireSuppression($id)
    {
        $fireSuppression = FireSuppressionSystem::find($id);

        if (!$fireSuppression) {
            return response()->json(['success' => false, 'message' => 'Fire Suppression not found!'], 404);
        }

        return response()->json($fireSuppression);
    }

    public function updateFireSuppression(Request $request, $id)
    {
        $fireSuppression = FireSuppressionSystem::find($id);

        if (!$fireSuppression) {
            return response()->json(['success' => false, 'message' => 'Fire Suppression not found!'], 404);
        }

        if (Auth::user()->user_type !== 'admin' && Auth::id() !== $fireSuppression->provider_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'description' => 'sometimes|required|string',
            'suppression_type' => 'sometimes|required|string|max:255',
            'installation_type' => 'sometimes|required|string|max:255',
            'application_area' => 'sometimes|required|string|max:255',
            'cylinder_capacity' => 'sometimes|required|numeric|max:255',
            'activation_method' => 'sometimes|required|string|max:255',
            'response_time' => 'sometimes|required|string|max:255',
            'working_temprature_range' => 'sometimes|required|string|max:255',
            'stock' => 'sometimes|required|integer|max:255',
            'warranty' => 'sometimes|required|string|min:0',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($request->has('remove_image') && $request->remove_image == '1') {
            if ($fireSuppression->image) {
                $oldImagePath = public_path($fireSuppression->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
                $fireSuppression->image = null;
            }
        }

        if ($request->hasFile('image')) {
            if ($fireSuppression->image) {
                $oldImagePath = public_path($fireSuppression->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('img/suppression/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $image->move($destinationPath, $imageName);
            $fireSuppression->image = 'img/suppression/' . $imageName;
        }

        $fireSuppression->fill($request->except(['image', '_method', 'remove_image']));
        $fireSuppression->save();

        return response()->json(['success' => true, 'message' => 'Fire Suppression updated successfully!']);
    }

    public function destroyFireSuppression($id)
    {
        $fireSuppression = FireSuppressionSystem::find($id);

        if (!$fireSuppression) {
            return response()->json(['success' => false, 'message' => 'Fire Suppression not found!'], 404);
        }

        if (Auth::user()->user_type !== 'admin' && Auth::id() !== $fireSuppression->provider_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $fireSuppression->delete();

        return response()->json(['success' => true, 'message' => 'Fire Suppression deleted successfully!']);
    }


    public function editFireWatermist($id)
    {
        $fireWatermist = WatermistSystem::find($id);

        if (!$fireWatermist) {
            return response()->json(['success' => false, 'message' => 'Fire Watermist system not found!'], 404);
        }

        return response()->json($fireWatermist);
    }

    public function updateFireWatermist(Request $request, $id)
    {
        $fireWatermist = WatermistSystem::find($id);

        if (!$fireWatermist) {
            return response()->json(['success' => false, 'message' => 'Fire Watermist system not found!'], 404);
        }

        if (Auth::user()->user_type !== 'admin' && Auth::id() !== $fireWatermist->provider_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'description' => 'sometimes|required|string',
            'technology_type' => 'sometimes|required|string|max:255',
            'nozzle_type' => 'sometimes|required|string|max:255',
            'droplet_size' => 'sometimes|required|string|max:255',
            'flow_rate' => 'sometimes|required|integer|max:255',
            'working_pressure' => 'sometimes|required|string|max:255',
            'application_area' => 'sometimes|required|string|max:255',
            'stock' => 'sometimes|required|integer|max:255',
            'warranty' => 'sometimes|required|string|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($request->has('remove_image') && $request->remove_image == '1') {
            if ($fireWatermist->image) {
                $oldImagePath = public_path($fireWatermist->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $fireWatermist->image = null;
        }

        if ($request->hasFile('image')) {
            if ($fireWatermist->image) {
                $oldImagePath = public_path($fireWatermist->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('img/watermist/');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $image->move($destinationPath, $imageName);
            $fireWatermist->image = 'img/watermist/' . $imageName;
        }

        $updateData = $request->except(['image', '_method', 'remove_image']);
        foreach ($updateData as $key => $value) {
            $fireWatermist->$key = $value;
        }

        $fireWatermist->save();

        return response()->json(['success' => 'Fire Watermist/CAFS updated successfully!']);
    }

    public function destroyFireWatermist($id)
    {
        $fireWatermist = WatermistSystem::find($id);

        if (!$fireWatermist) {
            return response()->json(['success' => false, 'message' => 'Fire Watermist system not found!'], 404);
        }

        if (Auth::user()->user_type !== 'admin' && Auth::id() !== $fireWatermist->provider_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $fireWatermist->delete();

        return response()->json(['success' => true, 'message' => 'Fire Watermist system deleted successfully!']);
    }

    public function getServices()
    {
        $services = Service::where('status', 1)->select('id', 'service_name')->get();
        return response()->json($services);
    }

    public function getSubServices($service_name)
    {
        $subServices = Service::where('service_name', $service_name)
            ->select('id', 'sub_service_name')
            ->distinct()
            ->get();
        return response()->json($subServices);
    }

    public function getProviders($sub_service_name)
    {
        $providers = Service::where('sub_service_name', $sub_service_name)
            ->with('provider')
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
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You need to login first to submit a service request!'
            ], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255', 
            'email' => 'required|email|max:255',
            'service_id' => 'required|exists:services,id',
            'sub_service_id' => 'nullable|exists:services,id',
            'provider_id' => 'required|exists:users,id',
            'address' => 'required|string|max:500',
            'contact' => 'required|numeric|digits_between:10,15',
        ]);

        $serviceRequest = ServiceRequest::create([
            'user_id' => Auth::id(),  
            'name' => $request->name, 
            'email' => $request->email,  
            'service_id' => $request->service_id,
            'sub_service_id' => $request->sub_service_id,
            'provider_id' => $request->provider_id,
            'address' => $request->address,
            'contact' => $request->contact,
            'created_at' => now(),
        ]);

        $provider = User::find($request->provider_id);
        if ($provider) {
            $provider->notify(new ServiceRequestNotification($request->name, $request->service_id, $request->sub_service_id, now()));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Service request submitted successfully!'
        ]);
    }

    public function orderHistory()
    {
        $user_id = Auth::id(); 

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

        return response()->json([
            'status' => 'success',
            'latest_orders' => $latestOrder,
            'past_orders' => $pastOrders,
        ]);
    }

    public function cancelOrder(Request $request): JsonResponse
    {
        $user_id = Auth::id();

        $order = CheckoutItem::find($request->order_id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }

        if ($order->checkout->user_id !== $user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only cancel your own orders.'
            ], 403);
        }

        if (now()->diffInHours($order->created_at) > 24) {
            return response()->json([
                'success' => false,
                'message' => 'Order cancellation time exceeded.'
            ], 400);
        }

        if (!in_array($order->tracking_status, ['confirmed', 'processed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be canceled at this stage.'
            ], 400);
        }

        $table = $this->getCategoryTable($order->category_id);
        DB::table($table)->where('id', $order->product_id)->increment('stock', $order->quantity);

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order canceled successfully.'
        ]);
    }


    private function getCategoryTable($category_id)
    {
        switch ($category_id) {
            case 1:
                return 'accessories';
            case 2:
                return 'fire_extinguishers';
            case 3:
                return 'fire_suppression_systems';
            case 4:
                return 'watermist_systems';
            default:
                return 'unknown';
        }
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

        switch ($category) {
            case 'accessories':
                $products = $fetchProducts(Accessory::class);
                break;
            case 'fire-extinguishers':
                $products = $fetchProducts(FireExtinguisher::class);
                break;
            case 'fire-suppression':
                $products = $fetchProducts(FireSuppressionSystem::class);
                break;
            case 'watermist':
                $products = $fetchProducts(WatermistSystem::class);
                break;
            default:
                $products = collect()
                    ->merge($fetchProducts(Accessory::class))
                    ->merge($fetchProducts(FireExtinguisher::class))
                    ->merge($fetchProducts(FireSuppressionSystem::class))
                    ->merge($fetchProducts(WatermistSystem::class));
                break;
        }

        return response()->json($products);
    }
}
