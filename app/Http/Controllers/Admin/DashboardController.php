<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use function optional;
use App\Mail\ResetPasswordMail;
use App\Mail\RejectionMail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CheckoutItem;
use App\Models\ServiceRequest;
use App\Models\Accessory;
use App\Models\FireExtinguisher;
use App\Models\FireSuppressionSystem;
use App\Models\WatermistSystem;
use App\Models\Coupon;
use App\Models\Checkout;
use App\Models\Category;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    // Dashboard
    public function index()
    {
        $user = auth()->user(); 

       
        $totalUsers = User::where('user_type', 'user')->count();
        $totalProviders = User::where('user_type', 'provider')
            ->whereHas('serviceProvider', function ($query) {
                $query->where('status', 'approved');
            })
            ->count();

       
        if ($user->user_type === 'admin') {
            $totalServiceRequests = ServiceRequest::count();
            $totalArrivedOrders = CheckoutItem::where('tracking_status', 'arrived')->count();

            $monthlyServiceRequests = ServiceRequest::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->groupBy('month')
                ->orderBy('month', 'ASC')
                ->pluck('total', 'month')
                ->toArray();
        } elseif ($user->user_type === 'provider') {
            $totalServiceRequests = ServiceRequest::where('provider_id', $user->id)->count();
            $totalArrivedOrders = CheckoutItem::where('provider_id', $user->id)
                ->where('tracking_status', 'arrived')
                ->count();

            $monthlyServiceRequests = ServiceRequest::where('provider_id', $user->id)
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->groupBy('month')
                ->orderBy('month', 'ASC')
                ->pluck('total', 'month')
                ->toArray();
        } else {
            $totalServiceRequests = 0;
            $totalArrivedOrders = 0;
            $monthlyServiceRequests = [];
        }

        $query = ServiceRequest::with('provider:id,first_name,last_name');

        if ($user->user_type === 'provider') {
            $query->where('provider_id', $user->id);
        }

        $fireRefillStats = $query->select('provider_id')
            ->selectRaw('COUNT(id) as total_requests')
            ->selectRaw('SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_requests')
            ->selectRaw('SUM(CASE WHEN created_at BETWEEN CURDATE() - INTERVAL WEEKDAY(CURDATE()) DAY AND CURDATE() THEN 1 ELSE 0 END) as week_requests')
            ->groupBy('provider_id')
            ->get();

     
        $chartData = $fireRefillStats->map(function ($item) {
            return [
                'provider' => optional($item->provider)->first_name . ' ' . optional($item->provider)->last_name,
                'total' => $item->total_requests
            ];
        });

        
        if ($user->user_type === 'admin') {
            $categoryCounts = [
                'accessories' => \App\Models\Accessory::count(),
                'fire_extinguishers' => \App\Models\FireExtinguisher::count(),
                'fire_suppression' => \App\Models\FireSuppressionSystem::count(),
                'watermist' => \App\Models\WatermistSystem::count(),
            ];
        } else {
            $categoryCounts = [
                'accessories' => \App\Models\Accessory::where('provider_id', $user->id)->count(),
                'fire_extinguishers' => \App\Models\FireExtinguisher::where('provider_id', $user->id)->count(),
                'fire_suppression' => \App\Models\FireSuppressionSystem::where('provider_id', $user->id)->count(),
                'watermist' => \App\Models\WatermistSystem::where('provider_id', $user->id)->count(),
            ];
        }

        $totalProducts = array_sum($categoryCounts);

       
        $lowStockItems = [];
        $tables = [
            'accessories' => 'accessories',
            'fire_extinguishers' => 'fire_extinguishers',
            'fire_suppression' => 'fire_suppression_systems',
            'watermist' => 'watermist_systems'
        ];

        foreach ($tables as $key => $table) {
            $query = DB::table($table)
                ->select('id', 'name', 'stock', 'provider_id')
                ->where('stock', '<', 10);

            if ($user->user_type === 'provider') {
                $query->where('provider_id', $user->id);
            }

            $items = $query->get();

            foreach ($items as $item) {
                $lowStockItems[] = [
                    'name' => $item->name,
                    'stock' => $item->stock,
                    'category' => ucfirst(str_replace('_', ' ', $key))
                ];
            }
        }
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalProviders',
            'totalServiceRequests',
            'totalArrivedOrders',
            'monthlyServiceRequests',
            'fireRefillStats',
            'chartData',
            'categoryCounts',
            'user',
            'totalProducts',
            'lowStockItems'
        ));
    }


    //Profile

    public function editProfile()
    {
        $user = Auth::user();
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!in_array($user->user_type, ['admin', 'provider'])) {
            abort(403, 'Unauthorized action.');
        }

        $validationRules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'mobile_no' => 'required|string|max:15',
            'address' => 'required|string|max:500',
        ];

        if ($user->user_type === 'provider') {
            $validationRules['business_name'] = 'required|string|max:255';
            $validationRules['license'] = 'nullable|image|max:1024';
        }

        $request->validate($validationRules);

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');

            $imageName = 'profile_' . $user->id . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('img/profile');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $image->move($destinationPath, $imageName);

            $user->update(['profile_image' => 'img/profile/' . $imageName]);
        }

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'mobile_no' => $request->mobile_no,
            'address' => $request->address,
        ]);

        if ($user->user_type === 'provider') {
            $serviceProvider = $user->serviceProvider;

            $serviceProvider->update([
                'business_name' => $request->business_name,
            ]);

            if ($request->hasFile('license')) {
                $image = $request->file('license');
                $imageName = uniqid() . '_' . $image->getClientOriginalName();
                $destinationPath = public_path('img/licenses');


                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }


                $image->move($destinationPath, $imageName);

                $serviceProvider->update(['license' => 'img/licenses/' . $imageName]);
            }
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }


    //password
    public function showChangePasswordForm()
    {
        if (auth()->user()->user_type === 'admin' || auth()->user()->user_type === 'provider') {
            return view('auth.change-password'); // Admin & Provider
        } else {
            return view('dashboard.password.change-password'); // Normal User
        }
    }


    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        // Check if current password matches the one in the database
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Your current password does not match our records.']);
        }

        // Update the new password
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('status', 'Password updated successfully!');
    }

    public function showForgotPasswordForm()
    {
        if (auth()->user()->user_type === 'admin' || auth()->user()->user_type === 'provider') {
            return view('auth.forgot-password'); // Admin & Provider
        } else {
            return view('dashboard.password.forgot-password'); // Normal User
        }
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We can\'t find a user with that e-mail address.']);
        }

        $resetLink = route('password.reset', ['id' => $user->id]);

        Mail::to($user->email)->send(new ResetPasswordMail($resetLink));

        return back()->with('status', 'We have emailed your password reset link!');
    }


    public function showResetPasswordForm($id)
    {
        // Ensure $id is an integer
        if (!is_numeric($id)) {
            abort(404); // Invalid ID should return 404
        }

        return view('auth.reset-password', ['id' => (int) $id]);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'id' => 'required|integer'
        ]);

        $user = User::where('id', $request->id)->where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Invalid email or user ID.']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
    }




    //Services 
    public function services()
    {
        return view('admin.services.service-list');
    }


    public function getData()
    {
        $query = Service::select(
            'services.*',
            'users.first_name',
            'users.last_name',
            'service_providers.business_name',
            'services.sub_service_name'
        )
            ->join('users', 'services.provider_id', '=', 'users.id')
            ->leftJoin('service_providers', 'users.id', '=', 'service_providers.user_id');


        if (auth()->user()->user_type === 'provider') {
            $query->where('services.provider_id', auth()->id());
        }

        return DataTables::of($query)
            ->addColumn('provider_name', function ($service) {
                return $service->first_name . ' ' . $service->last_name;
            })
            ->addColumn('business_name', function ($service) {
                return $service->business_name ?? 'N/A';
            })
            ->addColumn('sub_service_name', function ($service) {
                return $service->sub_service_name ?? 'N/A';
            })
            ->addColumn('actions', function ($service) {
                $user = auth()->user();
                $buttons = '';

                if ($user->can('view services')) {
                    $buttons .= '<button class="btn btn-info btn-sm view-service me-2" 
                    data-id="' . $service->id . '" 
                    data-provider="' . $service->first_name . ' ' . $service->last_name . '" 
                    data-business="' . $service->business_name . '" 
                    data-name="' . $service->service_name . '" 
                    data-sub-service="' . ($service->sub_service_name ?? 'N/A') . '">
                   
                    <i class="fas fa-eye"></i> 
                </button>';
                }

                if ($user->can('edit services')) {
                    $buttons .= '<button class="btn btn-primary btn-sm edit-service me-2" 
                data-id="' . $service->id . '" 
                data-name="' . $service->service_name . '" 
                data-sub-service="' . $service->sub_service_name . '" >
               
                <i class="fas fa-edit"></i>
            </button>';
                }

                if ($user->can('delete services')) {
                    $buttons .= '<button class="btn btn-danger btn-sm delete-service" 
                    data-id="' . $service->id . '">
                    <i class="fas fa-trash-alt"></i>
                </button>';
                }

                return $buttons ?: 'No Actions';
            })

            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create()
    {
        if (auth()->user()->user_type === 'provider') {

            $providers = User::where('id', auth()->id())
                ->whereHas('serviceProvider', function ($query) {
                    $query->where('status', 'approved');
                })
                ->with('serviceProvider')
                ->get()
                ->map(function ($provider) {
                    return [
                        'id' => $provider->id,
                        'name' => $provider->first_name . ' ' . $provider->last_name,
                        'business_name' => $provider->serviceProvider->business_name ?? 'N/A',
                        'status' => $provider->serviceProvider->status,
                    ];
                });
        } else {

            $providers = User::where('user_type', 'provider')
                ->whereHas('serviceProvider', function ($query) {
                    $query->where('status', 'approved');
                })
                ->with('serviceProvider')
                ->get()
                ->map(function ($provider) {
                    return [
                        'id' => $provider->id,
                        'name' => $provider->first_name . ' ' . $provider->last_name,
                        'business_name' => $provider->serviceProvider->business_name ?? 'N/A',
                        'status' => $provider->serviceProvider->status,
                    ];
                });
        }

        return view('admin.services.add-services', compact('providers'));
    }

    public function editServices($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'provider_id'      => 'required|exists:users,id',
            'service_name'     => 'required|string|max:255',
            'sub_service_name' => 'required|string|max:255',
            // 'service_image'    => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'description'      => 'nullable|string',
            'status'           => 'required|in:0,1',
        ]);

        $provider = User::with('serviceProvider')->findOrFail($validatedData['provider_id']);

        if (auth()->user()->user_type === 'provider' && auth()->id() !== $provider->id) {
            return redirect()->back()->with('error', 'You are not authorized to add services for other providers.');
        }

        $imagePath = null;
        if ($request->hasFile('service_image')) {
            $image = $request->file('service_image');
            $imageName = time() . '_' . $image->getClientOriginalName();

            $destinationPath = public_path('img/services');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $image->move($destinationPath, $imageName);
            $imagePath = 'img/services/' . $imageName;
        }

        // ✅ Service Create
        $service = Service::create([
            'provider_id'      => $request->provider_id,
            'service_name'     => $request->service_name,
            'sub_service_name' => $request->sub_service_name,
            // 'service_image'    => $imagePath,
            // 'description'      => $request->description,
            'status'           => $request->status,
        ]);

        if (!$service) {
            return redirect()->back()->with('error', 'Failed to create service. Try again.');
        }

        return redirect()->route('services')->with('success', 'Service created successfully!');
    }


    public function updateService(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'sub_service_name' => 'required|string|max:255',
            // 'description' => 'required|string',
            // 'image' => 'nullable|image|max:2048',
        ]);

        // Find the service by ID
        $service = Service::find($request->id);

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service not found']);
        }

        // Update the service fields
        $service->service_name = $request->service_name;
        $service->sub_service_name = $request->sub_service_name;
        // $service->description = $request->description;

        // Handle image upload if provided
        // if ($request->hasFile('image')) {
        //     $imagePath = null;
        //     $image = $request->file('image');
        //     $imageName = time() . '_' . $image->getClientOriginalName();
        //     $destinationPath = public_path('img/services');
        //     if (!file_exists($destinationPath)) {
        //         mkdir($destinationPath, 0777, true);
        //     }
        //     $image->move($destinationPath, $imageName);
        //     $imagePath = 'img/services/' . $imageName;
        //     $service->service_image = $imagePath;
        // }

        // Save the updated service
        $service->save();

        return response()->json(['success' => true, 'message' => 'Service updated successfully']);
    }


    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:services,id',
            'status' => 'required|in:0,1',
        ]);

        $service = Service::findOrFail($request->id);
        $service->status = $request->status;
        $service->save();

        return response()->json([
            'success' => 'Service status updated successfully.',
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'provider_id' => 'required|exists:users,id',

            'service_name' => 'required|string|max:255',
            // 'service_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'description' => 'nullable|string',
            'status' => 'nullable|boolean',
            // 'company_name' => 'nullable|string|max:255',
            'business_name' => 'nullable|string|max:255',
            // 'license_expiry' => 'nullable|date',
        ]);

        // Handle the image upload if exists
        // if ($request->hasFile('image')) {
        //     $imagePath = $request->file('image')->store('images/services', 'public');
        // } else {
        //     $imagePath = $service->image;
        // }

        // Update service
        $service->update([
            'provider_id' => $request->provider_id,
            'service_name' => $request->name,
            // 'service_image' => $imagePath,
            // 'description' => $request->description,
            'status' => $request->status ?? 0,
            // 'company_name' => $request->company_name,
            'business_name' => $request->business_name,
            // 'license_expiry' => $request->license_expiry,
        ]);

        return redirect()->route('services')->with('success', 'Service updated successfully!');
    }


    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        if ($service->service_image) {
            Storage::delete($service->service_image);
        }

        $service->delete();

        return response()->json(['message' => 'Service deleted successfully!']);
    }

    //request service
    public function serviceAdmin()
    {
        return view('admin.services.service-request');
    }


    public function getServiceData(Request $request)
    {
        $user = Auth::user();

        if ($request->ajax()) {
            $requests = ServiceRequest::with(['service', 'subService', 'provider'])
                ->where('status', 0)
                ->select('service_requests.*');

            if ($user->user_type === 'provider') {
                $requests->where('provider_id', $user->id);
            }

            return DataTables::of($requests)
                ->addColumn('service_name', fn($row) => $row->service->service_name ?? 'N/A')
                ->addColumn('sub_service_name', fn($row) => $row->subService->sub_service_name ?? 'N/A')
                ->addColumn('provider_name', fn($row) => $row->provider->first_name . ' ' . $row->provider->last_name)
                ->addColumn('action', function ($row) {
                    $buttons = '';

                    if (auth()->user()->can('accept service request')) {
                        $buttons .= '<button class="btn btn-sm btn-success acceptRequest" 
                                    data-id="' . $row->id . '" 
                                    data-email="' . $row->email . '">
                                    <i class="fas fa-check"></i> 
                                </button> ';
                    }

                    if (auth()->user()->can('delete service request')) {
                        $buttons .= '<button class="btn btn-sm btn-danger deleteRequest" 
                                     data-id="' . $row->id . '" 
                                     data-email="' . $row->email . '">
                                     <i class="fas fa-times"></i>
                                </button>';
                    }

                    return $buttons ?: '<span class="text-muted">No Actions</span>';
                })


                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.services.service-request');
    }

    public function acceptServiceRequest(Request $request)
    {
        $serviceRequest = ServiceRequest::find($request->id);

        if (!$serviceRequest) {
            return response()->json(['message' => 'Service request not found!'], 404);
        }

        $serviceRequest->status = 1;
        $serviceRequest->save();

        Mail::raw("Your service request has been accepted successfully.", function ($message) use ($serviceRequest) {
            $message->to($serviceRequest->email)
                ->subject('Service Request Accepted');
        });

        return response()->json(['message' => 'Service request accepted and email sent successfully!']);
    }

    public function getAcceptedServiceData(Request $request)
    {
        if ($request->ajax()) {
            $acceptedRequests = ServiceRequest::with(['service', 'subService', 'provider'])
                ->where('status', 1)
                ->select('service_requests.*');

            return DataTables::of($acceptedRequests)
                ->addColumn('service_name', fn($row) => $row->service->service_name ?? 'N/A')
                ->addColumn('sub_service_name', fn($row) => $row->subService->sub_service_name ?? 'N/A')
                ->addColumn('provider_name', fn($row) => $row->provider->first_name . ' ' . $row->provider->last_name)
                ->addColumn('name', fn($row) => $row->name ?? 'N/A')
                ->addColumn('contact', fn($row) => $row->contact ?? 'N/A')
                ->addColumn('email', fn($row) => $row->email)
                ->addColumn('action', function ($row) {
                    $buttons = '';

                    if (auth()->user()->can('delete accepted service')) {
                        $buttons .= '<button class="btn btn-sm btn-danger deleteRequest" 
                                    data-id="' . $row->id . '" 
                                    data-email="' . $row->email . '">
                                    <i class="fas fa-trash"></i>
                                </button>';
                    }
                    return $buttons ?: '<span class="text-muted">No Actions</span>';
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.services.accept-service');
    }

    public function deleteAcceptedServiceRequest(Request $request)
    {
        $serviceRequest = ServiceRequest::find($request->id);

        if (!$serviceRequest) {
            return response()->json(['message' => 'Service request not found!'], 404);
        }
        $userEmail = $serviceRequest->email;

        $serviceRequest->delete();

        Mail::raw("Your accepted service request has been removed by the admin.", function ($message) use ($userEmail) {
            $message->to($userEmail)
                ->subject('Service Request Deleted');
        });

        return response()->json(['message' => 'Service request deleted and email sent successfully!']);
    }


    public function deleteServiceRequest(Request $request)
    {
        $serviceRequest = ServiceRequest::find($request->id);

        if (!$serviceRequest) {
            return response()->json(['message' => 'Service request not found!'], 404);
        }
        $userEmail = $serviceRequest->email;
        $reason = $request->reason;
        $serviceRequest->delete();
        Mail::raw("Your service request has been denied.\nReason: $reason", function ($message) use ($userEmail) {
            $message->to($userEmail)
                ->subject('Service Request Denied');
        });

        return response()->json(['message' => 'Service request denied and email sent successfully!']);
    }



    //    USER LIST

    public function getUsers(Request $request)
    {
        if ($request->ajax()) {
            $users = User::where('user_type', 'user')
                ->withTrashed()
                ->select('id', 'first_name', 'last_name', 'email', 'mobile_no', 'address', 'is_email_verified', 'deleted_at');

            return DataTables::of($users)
                ->addColumn('email_verified', function ($row) {
                    return $row->is_email_verified
                        ? '<span class="badge" style="background-color: #28a745; color: white; font-size: 13px; padding: 7px 14px; border-radius: 5px;">Verified</span>'
                        : '<span class="badge" style="background-color: #dc3545; color: white; font-size: 13px; padding: 7px 14px; border-radius: 5px;">Not Verified</span>';
                })
                ->addColumn('actions', function ($row) {
                    $actions = '';


                    if ($row->deleted_at) {
                        if (auth()->user()->can('restore user')) {
                            $actions .= '<button class="btn btn-warning btn-sm restore-user" data-id="' . $row->id . '">
                                            <i class="fas fa-undo"></i>
                                         </button>';
                        }
                    } else {

                        if (auth()->user()->can('edit user')) {
                            $actions .= '<button class="btn btn-primary btn-sm edit-user me-1" data-id="' . $row->id . '">
                                            <i class="fas fa-edit"></i>
                                         </button>';
                        }

                        if (auth()->user()->can('delete user')) {
                            $actions .= '<button class="btn btn-danger btn-sm delete-user me-1" data-id="' . $row->id . '">
                                            <i class="fas fa-trash"></i>
                                         </button>';
                        }
                    }

                    return !empty($actions) ? $actions : 'No Actions';
                })

                ->rawColumns(['email_verified', 'actions'])
                ->make(true);
        }

        return view('admin.user-list');
    }


    // Update Email Verification Status
    public function updateEmailVerification(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->is_email_verified = $request->is_email_verified;
        $user->save();

        return response()->json(['success' => 'Email verification status updated successfully!']);
    }


    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only(['first_name', 'last_name', 'mobile_no', 'address']));

        return response()->json(['success' => 'User updated successfully!']);
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => 'User deleted successfully!']);
    }

    public function restoreUser($id)
    {
        $user = User::onlyTrashed()->find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found or not deleted!'], 404);
        }

        $user->restore();

        return response()->json(['success' => 'User restored successfully!']);
    }

    public function userDownloadPDF()
    {
        $users = User::select(
            'id',
            'first_name',
            'last_name',
            'email',
            'mobile_no',
            'address',
            'is_email_verified'
        )
            ->where('user_type', 'user')
            ->get();

        $pdf = Pdf::loadView('admin.users.pdf', compact('users'));
        return $pdf->download('users_list.pdf');
    }




    //SERVICE_PROVIDERS

    public function showRequestList(Request $request)
    {
        if ($request->ajax()) {
            $providers = ServiceProvider::with('user')->select('service_providers.*');

            return DataTables::of($providers)
                ->addColumn('name', function ($row) {
                    return $row->user ? $row->user->first_name . ' ' . $row->user->last_name : 'N/A';
                })
                ->addColumn('email', function ($row) {
                    return $row->user ? $row->user->email : 'N/A';
                })
                ->addColumn('phone', function ($row) {
                    return $row->user ? $row->user->mobile_no : 'N/A';
                })
                ->addColumn('address', function ($row) {
                    return $row->user ? $row->user->address : 'N/A';
                })
                ->addColumn('business_name', function ($row) {
                    return $row->business_name ?? 'N/A';
                })
                ->addColumn('status', function ($row) {
                    return '
                    <select class="form-control status-change" data-id="' . $row->id . '">
                        <option value="pending" ' . ($row->status == 'pending' ? 'selected' : '') . '>Pending</option>
                        <option value="approved" ' . ($row->status == 'approved' ? 'selected' : '') . '>Approved</option>
                        <option value="rejected" ' . ($row->status == 'rejected' ? 'selected' : '') . '>Rejected</option>
                    </select>
                ';
                })
                ->addColumn('email_verified', function ($row) {
                    if ($row->user && $row->user->is_email_verified) {
                        return '<span class="badge" style="background-color: #28a745; color: white; font-size: 13px; padding: 7px 14px; border-radius: 5px;">Verified</span>';
                    } else {
                        return '<span class="badge" style="background-color: #dc3545; color: white; font-size: 13px; padding: 7px 14px; border-radius: 5px;">Not Verified</span>';
                    }
                })


                ->rawColumns(['status', 'email_verified'])
                ->make(true);
        }

        return view('admin.service-provider.request-list');
    }


    public function updateRequestStatus(Request $request)
    {
        $provider = ServiceProvider::findOrFail($request->id);
        $provider->status = $request->status;

        if ($request->status == 'rejected' && $request->has('reason')) {
            $provider->rejection_reason = $request->reason;
            $provider->save();


            Mail::to($provider->user->email)->send(new RejectionMail($provider->rejection_reason));


            return response()->json(['success' => 'Status updated and rejection email sent successfully.']);
        }

        $provider->save();

        return response()->json(['success' => 'Status updated successfully.']);
    }


    public function updateEmailVerified(Request $request)
    {
        $provider = ServiceProvider::findOrFail($request->id);
        $provider->user->is_email_verified = $request->email_verified;
        $provider->user->save();

        return response()->json(['success' => 'Email verification status updated successfully.']);
    }

    public function getProviders(Request $request)
    {
        if ($request->ajax()) {
            $providers = User::where('user_type', 'provider')
                ->withTrashed()
                ->where('status', 'approved')
                ->join('service_providers', 'users.id', '=', 'service_providers.user_id')
                ->select('users.id', 'first_name', 'last_name', 'email', 'mobile_no', 'address', 'service_providers.business_name', 'service_providers.license', 'service_providers.deleted_at'); // Include business_name and license

            return DataTables::of($providers)
                ->addColumn('actions', function ($row) {
                    $actions = '';

                    if (auth()->user()->can('edit service provider')) {
                        $actions .= '<button class="btn btn-primary btn-sm edit-provider" data-id="' . $row->id . '"><i class="fas fa-edit"></i></button>';
                    }
                    return $actions ?: '<span class="text-muted">No Actions</span>';
                })
                ->addColumn('business_name', function ($row) {
                    return $row->business_name ? $row->business_name : 'N/A';
                })
                ->addColumn('license', function ($row) {
                    $licensePath =  $row->license;
                    if (file_exists(public_path($licensePath))) {
                        return '<img src="' . asset($licensePath) . '" alt="License Image" style="width: 100px; height: auto;" />';
                    } else {
                        $placeholderPath = 'img/licenses/placeholder-license.png';
                        return '<img src="' . asset($placeholderPath) . '" alt="License Image" style="width: 100px; height: auto;" />';
                    }
                })


                ->rawColumns(['actions', 'license'])
                ->make(true);
        }

        return view('admin.service-provider.provider-list');
    }


    public function editProvider($id)
    {
        $provider = User::with('serviceProvider')->findOrFail($id);

        return response()->json([
            'id' => $provider->id,
            'first_name' => $provider->first_name,
            'last_name' => $provider->last_name,
            'email' => $provider->email,
            'mobile_no' => $provider->mobile_no,
            'address' => $provider->address,
            'business_name' => $provider->serviceProvider->business_name ?? 'N/A',
            'license' => $provider->serviceProvider->license ?? null,
        ]);
    }


    public function updateProvider(Request $request, $id)
    {
        // Validate input data
        $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'mobile_no' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:500',
            'business_name' => 'nullable|string|max:255',
            'license' => 'nullable|file|mimes:jpg,jpeg,png,gif,avif|max:2048', // Validate image file
        ]);

        // Find the user (Service Provider)
        $provider = User::findOrFail($id);

        // Update User table fields
        $provider->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile_no' => $request->mobile_no,
            'address' => $request->address,
        ]);

        // Find the related service provider record
        $serviceProvider = ServiceProvider::where('user_id', $id)->first();

        if (!$serviceProvider) {
            return response()->json(['error' => 'Service Provider details not found!'], 404);
        }

        if ($request->hasFile('license')) {
            $image = $request->file('license');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('img/licenses');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $image->move($destinationPath, $imageName);
            $serviceProvider->license = 'img/licenses/' . $imageName;
        }



        if ($request->has('business_name')) {
            $serviceProvider->business_name = $request->business_name;
        }

        // Save the updated service provider details
        $serviceProvider->save();

        return response()->json(['success' => 'Provider updated successfully!']);
    }


    public function destroyProvider($id)
    {
        $provider = User::findOrFail($id);
        $provider->delete(); // Soft delete

        return response()->json(['success' => 'Provider deleted successfully!']);
    }


    public function restore($id)
    {
        $provider = User::onlyTrashed()->find($id);

        if (!$provider) {
            return response()->json(['error' => 'Provider not found or not deleted!'], 404);
        }

        $provider->restore();

        return response()->json(['success' => 'Provider restored successfully!']);
    }


    public function rejectedProviders(Request $request)
    {
        if ($request->ajax()) {
            $providers = ServiceProvider::with('user')
                ->where('status', 'rejected')
                ->select('service_providers.*');

            return DataTables::of($providers)
                ->addColumn('name', function ($row) {
                    return $row->user ? $row->user->first_name . ' ' . $row->user->last_name : 'N/A';
                })
                ->addColumn('email', function ($row) {
                    return $row->user ? $row->user->email : 'N/A';
                })
                ->addColumn('rejection_reason', function ($row) {
                    return $row->rejection_reason ? $row->rejection_reason : 'No reason provided';
                })
                ->make(true);
        }

        return view('admin.service-provider.reject-list');
    }

    public function downloadPDF()
    {
        $providers = User::leftJoin('service_providers', 'users.id', '=', 'service_providers.user_id')
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.mobile_no',
                'users.address',
                'service_providers.business_name',
                'service_providers.license'
            )
            ->where('users.user_type', 'provider')
            ->get();

        $pdf = Pdf::loadView('admin.service-provider.pdf', compact('providers'));
        return $pdf->download('providers_list.pdf');
    }


    //PRODUCTS

    // ACCESSORIES
    public function accessory()
    {
        return view('admin.products.accessory');
    }

    public function getAccessoriesData(Request $request)
    {

        $accessories = Accessory::with(['category', 'provider'])
            ->where('category_id', 1)
            ->select(
                'id',
                'category_id',
                'provider_id',
                'name',
                'image',
                'price',
                'description',
                'weight',
                'power_source',
                'operating_voltage',
                'material',
                'working_temprature',
                'IP_routing',
                'stock',
                'warranty'
            );


        return DataTables::of($accessories)
            ->addColumn('provider_name', function ($row) {
                return $row->provider ? $row->provider->first_name . ' ' . $row->provider->last_name : 'N/A';
            })
            ->editColumn('image', function ($accessory) {

                return asset($accessory->image);
            })
            ->rawColumns(['image'])
            ->make(true);
    }

    public function createAccessory()
{
    if (auth()->user()->user_type === 'admin') {
        $providers = User::where('user_type', 'provider')
            ->whereHas('serviceProvider', function ($query) {
                $query->where('status', 'approved'); 
            })
            ->get();
    } else {
        $providers = User::where('id', auth()->id())->get();
    }

    return view('admin.products.create-accessory', compact('providers'));
}


    public function storeAccessory(Request $request)
    {
        $request->validate([
            'provider_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
          'weight' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'power_source' => 'nullable|string|max:255',
            'operating_voltage' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'working_temprature' => 'nullable|string|max:255',
            'IP_routing' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'warranty' => 'required|integer|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $destinationPath = public_path('img/accessories');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move($destinationPath, $imageName);

            $imagePath = 'img/accessories/' . $imageName;
        }


        Accessory::create([
            'category_id' => 1,
            'provider_id' => $request->provider_id,
            'name' => $request->name,
            'image' => $imagePath,
            'price' => $request->price,
            'description' => $request->description,
            'weight' => $request->weight,
            'power_source' => $request->power_source,
            'operating_voltage' => $request->operating_voltage,
            'material' => $request->material,
            'working_temprature' => $request->working_temprature,
            'IP_routing' => $request->IP_routing,
            'stock' => $request->stock,
            'warranty' => $request->warranty,
        ]);

        return redirect()->route('accessories.index')->with('success', 'Accessory added successfully!');
    }

    public function editAccessory($id)
    {
        $accessory = Accessory::findOrFail($id);
        return response()->json($accessory);
    }

    public function updateAccessory(Request $request, $id)
    {
        $accessory = Accessory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'weight' => 'required|numeric|min:0',
            'power_source' => 'nullable|string|max:255',
            'operating_voltage' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'working_temprature' => 'nullable|string|max:255',
            'IP_routing' => 'nullable|string|max:255',
            'stock' => 'required|integer|min:0',
            'warranty' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);

        if ($request->has('remove_image') && $request->remove_image == '1') {
            if ($accessory->image) {
                $oldImagePath = public_path($accessory->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $accessory->image = null;
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

            $accessory->image = 'img/accessories/' . $imageName;
        }

        $accessory->update($request->except(['image', '_method', 'remove_image']));

        return response()->json(['success' => 'Accessory updated successfully!']);
    }


    public function destroyAccessory($id)
    {
        $accessory = Accessory::findOrFail($id);
        $accessory->delete();
        return response()->json(['success' => 'Accessory deleted successfully!']);
    }


    //    FIRE-EXTINGUISHER

    public function fireExtinguisher()
    {
        return view('admin.products.fire_extinguisher');
    }

    public function getFireExtinguisherData(Request $request)
    {
        $fireExtinguishers = FireExtinguisher::with(['provider'])
            ->where('category_id', 2)
            ->select(
                'id',
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
                'warranty'
            );

        return DataTables::of($fireExtinguishers)
            ->addColumn('provider_name', function ($row) {
                return optional($row->provider)->first_name . ' ' . optional($row->provider)->last_name ?? 'N/A';
            })
            ->editColumn('image', function ($fireExtinguishers) {

                return asset($fireExtinguishers->image);
            })
            ->rawColumns(['image'])
            ->make(true);
    }

    public function createFireExtinguisher()
    {
        if (auth()->user()->user_type === 'admin') {
            $providers = User::where('user_type', 'provider')
                ->whereHas('serviceProvider', function ($query) {
                    $query->where('status', 'approved'); 
                })
                ->get();
        } else {
            $providers = User::where('id', auth()->id())->get();
        }
        return view('admin.products.create-fire-extingusiher', compact('providers'));
    }

    public function storeFireExtinguisher(Request $request)
    {

        $validatedData = $request->validate([
            'provider_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'fire_class' => 'required|string|max:255',
            'suitability' => 'required|string|max:255',
            'capacity' => 'required|numeric|max:255',
            'extinguishing_agent' => 'required|string|max:255',
            'discharge_time' => 'required|string|max:255',
            'working_pressure' => 'required|string|max:255',
            'cylinder_material' => 'required|string|max:255',
            'operating_temprature' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'warranty' => 'required|string|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $destinationPath = public_path('img/extinguisher');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move($destinationPath, $imageName);
            $imagePath = 'img/extinguisher/' . $imageName;
        }


        FireExtinguisher::create([
            'category_id' => 2,
            'provider_id' => $request->provider_id,
            'name' => $request->name,
            'image' => $imagePath,
            'price' => $request->price,
            'description' => $request->description,
            'fire_class' => $request->fire_class,
            'suitability' => $request->suitability,
            'capacity' => $request->capacity,
            'extinguishing_agent' => $request->extinguishing_agent,
            'discharge_time' => $request->discharge_time,
            'working_pressure' => $request->working_pressure,
            'cylinder_material' => $request->cylinder_material,
            'operating_temprature' => $request->operating_temprature,
            'weight' => $request->weight,
            'stock' => $request->stock,
            'warranty' => $request->warranty,
        ]);

        return redirect()->route('fire_extinguishers.index')->with('success', 'Fire Extinguisher added successfully!');
    }

    public function editFireExtinguisher($id)
    {
        $fireExtinguisher = FireExtinguisher::findOrFail($id);
        return response()->json($fireExtinguisher);
    }

    public function updateFireExtinguisher(Request $request, $id)
    {
        $fireExtinguisher = FireExtinguisher::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'fire_class' => 'required|string|max:255',
            'suitability' => 'required|string|max:255',
            'capacity' => 'required|string|max:255',
            'extinguishing_agent' => 'required|string|max:255',
            'discharge_time' => 'required|string|max:255',
            'working_pressure' => 'required|string|max:255',
            'cylinder_material' => 'required|string|max:255',
            'operating_temprature' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'warranty' => 'required|string|min:0',
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
        $fireExtinguisher->update($request->except(['image', '_method', 'remove_image']));

        return response()->json(['success' => 'Fire Extinguisher updated successfully!']);
    }

    public function destroyFireExtinguisher($id)
    {
        $accessory = FireExtinguisher::findOrFail($id);
        $accessory->delete();
        return response()->json(['success' => 'Accessory deleted successfully!']);
    }


    // Fire-Supression System 
    public function fireSuppression()
    {
        return view('admin.products.fire_suppression');
    }

    public function getfireSuppressionData(Request $request)
    {
        $fireSuppression = FireSuppressionSystem::with(['provider'])
            ->where('category_id', 3)
            ->select(
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
            );

        return DataTables::of($fireSuppression)
            ->addColumn('provider_name', function ($row) {
                return optional($row->provider)->first_name . ' ' . optional($row->provider)->last_name ?? 'N/A';
            })
            ->editColumn('image', function ($fireSuppression) {

                return asset($fireSuppression->image);
            })
            ->rawColumns(['image'])
            ->make(true);
    }

    public function createfireSuppression()
    {
        if (auth()->user()->user_type === 'admin') {
            $providers = User::where('user_type', 'provider')
                ->whereHas('serviceProvider', function ($query) {
                    $query->where('status', 'approved'); 
                })
                ->get();
        } else {
            $providers = User::where('id', auth()->id())->get();
        }
        return view('admin.products.create-fire-supression', compact('providers'));
    }

    public function storefireSuppression(Request $request)
    {

        $validatedData = $request->validate([
            'provider_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'price' => 'required|min:0',
            'description' => 'required|string',
            'suppression_type' => 'required|string|max:255',
            'installation_type' => 'required|string|max:255',
            'application_area' => 'required|string|max:255',
            'cylinder_capacity' => 'required|numeric|min:1',
            'activation_method' => 'required|string|max:255',
            'response_time' => 'required|string|max:255',
            'working_temprature_range' => 'required|string',
            'stock' => 'required|integer|min:0',
            'warranty' => 'required|string|max:255',
        ]);




        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $destinationPath = public_path('img/suppression');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move($destinationPath, $imageName);
            $imagePath = 'img/suppression/' . $imageName;
        }

        FireSuppressionSystem::create([
            'category_id' => 3,
            'provider_id' => $request->provider_id,
            'name' => $request->name,
            'image' => $imagePath,
            'price' => $request->price,
            'description' => $request->description,
            'suppression_type' => $request->suppression_type,
            'installation_type' => $request->installation_type,
            'application_area' => $request->application_area,
            'cylinder_capacity' => $request->cylinder_capacity,
            'activation_method' => $request->activation_method,
            'response_time' => $request->response_time,
            'working_temprature_range' => $request->working_temprature_range,
            'stock' => $request->stock,
            'warranty' => $request->warranty,
        ]);

        return redirect()->route('fire_suppression.index')->with('success', 'Fire Extinguisher added successfully!');
    }

    public function editfireSuppression($id)
    {
        $fireExtinguisher = FireSuppressionSystem::findOrFail($id);
        return response()->json($fireExtinguisher);
    }

    // public function updatefireSuppression(Request $request, $id)
    // {
    //     $fireSuppression = FireSuppressionSystem::findOrFail($id);

    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'price' => 'required|numeric|min:0',
    //         'description' => 'required|string',
    //         'suppression_type' => 'required|string|max:255',
    //         'installation_type' => 'required|string|max:255',
    //         'application_area' => 'required|string|max:255',
    //         'cylinder_capacity' => 'required|numeric|max:255',
    //         'activation_method' => 'required|string|max:255',
    //         'response_time' => 'required|string|max:255',
    //         'working_temprature_range' => 'required|string|max:255',
    //         'stock' => 'required|integer|max:255',
    //         'warranty' => 'required|string|min:0',
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
    //     ]);

    //     if ($request->hasFile('image')) {
    //         if ($fireSuppression->image) {
    //             $oldImagePath = public_path($fireSuppression->image);
    //             if (file_exists($oldImagePath)) {
    //                 unlink($oldImagePath);
    //             }
    //         }

    //         $image = $request->file('image');
    //         $imageName = time() . '.' . $image->getClientOriginalExtension();
    //         $destinationPath = public_path('img/suppression/');

    //         if (!file_exists($destinationPath)) {
    //             mkdir($destinationPath, 0777, true);
    //         }

    //         $image->move($destinationPath, $imageName);

    //         $fireSuppression->image = 'img/suppression/' . $imageName;
    //     }


    //     // Update Fields Except Image
    //     $fireSuppression->update($request->except(['image', '_method']));

    //     return response()->json(['success' => 'Fire Supression updated successfully!']);
    // }
    public function updatefireSuppression(Request $request, $id)
    {
        $fireSuppression = FireSuppressionSystem::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'suppression_type' => 'required|string|max:255',
            'installation_type' => 'required|string|max:255',
            'application_area' => 'required|string|max:255',
            'cylinder_capacity' => 'required|numeric|max:255',
            'activation_method' => 'required|string|max:255',
            'response_time' => 'required|string|max:255',
            'working_temprature_range' => 'required|string|max:255',
            'stock' => 'required|integer|max:255',
            'warranty' => 'required|string|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp'
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

        $fireSuppression->update($request->except(['image', 'remove_image', '_method']));

        return response()->json(['success' => 'Fire Suppression updated successfully!']);
    }

    public function destroyfireSuppression($id)
    {
        $suppression = FireSuppressionSystem::findOrFail($id);
        $suppression->delete();
        return response()->json(['success' => 'Accessory deleted successfully!']);
    }


    // products: Fire Watermist
    public function fireWatermist()
    {
        return view('admin.products.watermist_system');
    }

    public function getfireWatermistData(Request $request)
    {
        $fireWatermist = WatermistSystem::with('provider')
            ->where('category_id', 4)
            ->select(
                'id',
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
                'warranty'
            )->get();


        return DataTables::of($fireWatermist)
            ->addColumn('provider_name', function ($row) {
                return optional($row->provider)->first_name . ' ' . optional($row->provider)->last_name ?: 'N/A';
            })

            ->editColumn('image', function ($fireWatermist) {

                return asset($fireWatermist->image);
            })
            ->addColumn('actions', function ($row) {
                return '
                    <button class="btn btn-sm btn-warning edit-fire-btn" data-id="' . $row->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-fire-btn" data-id="' . $row->id . '">
                        <i class="fas fa-trash"></i>
                    </button>';
            })

            ->rawColumns(['image', 'actions'])
            ->make(true);
    }


    public function createfireWatermist()
    {
        if (auth()->user()->user_type === 'admin') {
            $providers = User::where('user_type', 'provider')
                ->whereHas('serviceProvider', function ($query) {
                    $query->where('status', 'approved'); 
                })
                ->get();
        } else {
            $providers = User::where('id', auth()->id())->get();
        }
        return view('admin.products.create-fire-watermist', compact('providers'));
    }

    public function storefireWatermist(Request $request)
    {

        $validatedData = $request->validate([
            'provider_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'price' => 'required|min:0',
            'description' => 'required|string',
            'technology_type' => 'required|string|max:255',
            'nozzle_type' => 'required|string|max:255',
            'working_pressure' => 'required|string|max:255',
            'droplet_size' => 'required|numeric|min:1',
            'flow_rate' => 'required|integer|max:255',
            'application_area' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'warranty' => 'required|string|max:255',
        ]);



        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $destinationPath = public_path('img/watermist');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move($destinationPath, $imageName);
            $imagePath = 'img/watermist/' . $imageName;
        }


        WatermistSystem::create([
            'category_id' => 4,
            'provider_id' => $request->provider_id,
            'name' => $request->name,
            'image' => $imagePath,
            'price' => $request->price,
            'description' => $request->description,
            'technology_type' => $request->technology_type,
            'nozzle_type' => $request->nozzle_type,
            'working_pressure' => $request->working_pressure,
            'droplet_size' => $request->droplet_size,
            'flow_rate' => $request->flow_rate,
            'application_area' => $request->application_area,
            'stock' => $request->stock,
            'warranty' => $request->warranty,
        ]);

        return redirect()->route('fire_watermist.index')->with('success', 'Fire Watermist added successfully!');
    }

    public function editfireWatermist($id)
    {
        $fireExtinguisher = WatermistSystem::findOrFail($id);
        return response()->json($fireExtinguisher);
    }


    public function updatefireWatermist(Request $request, $id)
    {
        $fireWatermist = WatermistSystem::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'technology_type' => 'required|string|max:255',
            'nozzle_type' => 'required|string|max:255',
            'droplet_size' => 'required|string|max:255',
            'flow_rate' => 'required|integer|max:255',
            'working_pressure' => 'required|string|max:255',
            'application_area' => 'required|string|max:255',
            'stock' => 'required|integer|max:255',
            'warranty' => 'required|string|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp'
        ]);

        if ($request->has('remove_image')) {
            if ($fireWatermist->image) {
                $oldImagePath = public_path($fireWatermist->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
                $fireWatermist->image = null; // Remove from database
            }
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

        // 🔄 **Update Fields Except Image**
        $fireWatermist->update($request->except(['image', '_method', 'remove_image']));

        return response()->json(['success' => 'Fire Watermist/CAFS updated successfully!']);
    }


    public function destroyfireWatermist($id)
    {
        $suppression = WatermistSystem::findOrFail($id);
        $suppression->delete();
        return response()->json(['success' => 'Accessory deleted successfully!']);
    }

    public function indexBooking()
    {
        return view('admin.booking.booking-list');
    }

    public function getBookingsData()
    {
        $user = auth()->user();
        $query = Checkout::with(['checkoutItems' => function ($q) {
            $q->whereNull('deleted_at')->where('tracking_status', '!=', 'arrived'); // Arrived filter added
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

    public function deliveredData()
    {
        return view('admin.booking.delivered-order');
    }


    public function deliveredOrder()
    {
        $user = auth()->user();
        $query = Checkout::with(['checkoutItems' => function ($q) {
            $q->whereNull('deleted_at')->where('tracking_status', 'arrived');
        }, 'user']);

        if ($user->user_type === 'provider') {
            $query->whereHas('checkoutItems', function ($q) use ($user) {
                $q->where('provider_id', $user->id)->whereNull('deleted_at')->where('tracking_status', 'arrived');
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
                    'payment_status' => $booking->status == 1 ? 'Paid' : 'Pending',
                    'status' => $item->tracking_status,
                    'order_date' => $booking->created_at->format('d-m-Y'),
                    'arrival_date' => $item->arrival_date ? Carbon::parse($item->arrival_date)->format('d-m-Y') : null,
                    'id' => $item->id,
                ];
            })->filter();
        });


        return DataTables::of($formattedData)->make(true);
    }

    public function updateArrivalDate(Request $request)
    {
        Log::info("Update Arrival Date: ", $request->all());

        $request->validate([
            'id' => 'required|exists:checkout_items,id',
            'arrival_date' => 'nullable|date'
        ]);

        $item = CheckoutItem::findOrFail($request->id);
        $item->arrival_date = $request->arrival_date;
        $item->save();

        Log::info("Arrival Date Updated: Order ID {$item->id} -> {$item->arrival_date}");

        return response()->json(['success' => true]);
    }


    public function getOrderStatus($id)
    {
        $order = CheckoutItem::findOrFail($id);
        return response()->json([
            'tracking_status' => $order->tracking_status,
            'arrival_date' => $order->arrival_date ? \Carbon\Carbon::parse($order->arrival_date)->format('d-m-Y') : null
        ]);
    }


    public function updateTrackingStatus(Request $request)
    {
        $order = CheckoutItem::findOrFail($request->id);
        $stages = ['confirmed', 'processed', 'shipped', 'en_route', 'arrived'];
        $currentIndex = array_search($order->tracking_status, $stages);
    
        if ($currentIndex !== false && $currentIndex < count($stages) - 1) {
            $order->tracking_status = $stages[$currentIndex + 1];
            $order->save();
        }
        if ($order->tracking_status === 'arrived') {
            $checkout = $order->checkout;
            if ($checkout) {
                $checkout->status = 1;
                $checkout->save();
            }
        }
    
        return response()->json(['success' => true]);
    }
    


    public function deleteBooking(Request $request)
    {
        $id = $request->id;
        $bookingItem = CheckoutItem::find($id);

        if ($bookingItem) {
            $bookingItem->delete();
            return response()->json(['success' => true, 'message' => 'Booking removed from List.']);
        }

        return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
    }




    // COUPON
    public function indexCoupon()
    {
        if (request()->ajax()) {
            $coupons = Coupon::with(['provider', 'category'])
                ->get()
                ->map(function ($coupon) {
                    $providerName = $coupon->provider ? $coupon->provider->first_name . ' ' . $coupon->provider->last_name : 'N/A';
                    $categoryName = $coupon->category ? $coupon->category->name : 'Unknown';
                    $product = null;
                    switch ($coupon->category_id) {
                        case 1:
                            $product = Accessory::find($coupon->product_id);
                            break;
                        case 2:
                            $product = FireExtinguisher::find($coupon->product_id);
                            break;
                        case 3:
                            $product = FireSuppressionSystem::find($coupon->product_id);
                            break;
                        case 4:
                            $product = WatermistSystem::find($coupon->product_id);
                            break;
                    }
                    $productName = $product ? $product->name : 'N/A';

                    $createdAt = Carbon::parse($coupon->created_at);
                    $expiryDate = Carbon::parse($coupon->expiry_date);
                    $currentTime = Carbon::now();

                    $timeRemaining = $expiryDate->diffInSeconds($currentTime, false);
                    $countdown = $timeRemaining > 0
                        ? gmdate("d days H:i:s", $timeRemaining)
                        : "<span class='text-danger'>Expired</span>";

                    if (
                        $currentTime->year == $expiryDate->year &&
                        $currentTime->month == $expiryDate->month &&
                        $currentTime->day == $expiryDate->day &&
                        $currentTime->hour == $expiryDate->hour &&
                        $currentTime->minute == $expiryDate->minute
                    ) {
                        $coupon->delete();
                    }

                    return [
                        'id' => $coupon->id,
                        'coupon_code' => $coupon->coupon_code,
                        'provider' => $providerName,
                        'category' => $categoryName,
                        'product' => $productName,
                        'price' => number_format($coupon->price, 2),
                        'discount' => $coupon->discount . '%',
                        'final_price' => number_format($coupon->final_price, 2),
                        // 'max_usage' => $coupon->max_usage,
                        // 'used_count' => $coupon->used_count,
                        // 'status' => ucfirst($coupon->status),
                        'expiry_date' => $expiryDate->format('Y-m-d H:i'),
                        'expiry_timestamp' => $expiryDate->timestamp,
                        'created_at' => $createdAt->format('Y-m-d H:i'),
                        'countdown' => $timeRemaining > 0 ? gmdate("d days H:i:s", $timeRemaining) : 'Expired',
                    ];
                });

            return DataTables::of($coupons)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                    <button class="btn btn-sm btn-primary editCoupon" data-id="' . $row['id'] . '">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger deleteCoupon" data-id="' . $row['id'] . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
                })
                ->rawColumns(['countdown', 'action'])
                ->make(true);
        }

        return view('admin.coupon.index');
    }


    public function createCoupon()
    {
        if (auth()->user()->user_type === 'admin') {
            $providers = User::where('user_type', 'provider')
                ->whereHas('serviceProvider', function ($query) {
                    $query->where('status', 'approved'); 
                })
                ->get();
        } else {
            $providers = User::where('id', auth()->id())->get();
        }

        $categories = Category::all();

        return view('admin.coupon.coupon', compact('providers', 'categories'));
    }

    public function getProductsCoupon(Request $request)
    {
        $products = [];

        if ($request->category_id == 1) {
            $products = Accessory::where('provider_id', $request->provider_id)
                ->get(['id', 'name', 'price']);
        } elseif ($request->category_id == 2) {
            $products = FireExtinguisher::where('provider_id', $request->provider_id)
                ->get(['id', 'name', 'price']);
        } elseif ($request->category_id == 3) {
            $products = FireSuppressionSystem::where('provider_id', $request->provider_id)
                ->get(['id', 'name', 'price']);
        } elseif ($request->category_id == 4) {
            $products = WatermistSystem::where('provider_id', $request->provider_id)
                ->get(['id', 'name', 'price']);
        }

        return response()->json(['products' => $products]);
    }

    // Store Coupon Data
    public function storeCoupon(Request $request)
    {
        $request->validate([
            'provider_id'  => 'required',
            'category_id'  => 'required',
            'product_id'   => 'required',
            'price'        => 'required|numeric',
            'discount'     => 'required|numeric|min:1|max:100',
            'final_price'  => 'required|numeric',
            'coupon_code'  => 'required|unique:coupons,coupon_code|max:6',
            'expiry_date'  => 'required|date',
            'max_usage'    => 'required|integer|min:10'
        ]);

        Coupon::create([
            'provider_id'  => $request->provider_id,
            'category_id'  => $request->category_id,
            'product_id'   => $request->product_id,
            'price'        => $request->price,
            'discount'     => $request->discount,
            'final_price'  => $request->final_price,
            'coupon_code'  => $request->coupon_code,
            'expiry_date'  => $request->expiry_date,
            'max_usage'    => $request->max_usage
        ]);


        return redirect()->route('admin.coupons.index')->with('success', 'Coupon Created Successfully!');
    }

    public function getCategories($provider_id)
    {
        $categories = Category::where('provider_id', $provider_id)->get();
        return response()->json($categories);
    }


    public function editCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);

        return response()->json($coupon);
    }

    public function updateCoupon(Request $request)
    {
        $coupon = Coupon::findOrFail($request->coupon_id);
        $coupon->update([
            'coupon_code' => $request->coupon_code,
            'discount' => $request->discount,
            // 'max_usage' => $request->max_usage,
            'expiry_date' => $request->expiry_date,
        ]);

        return response()->json(['success' => 'Coupon updated successfully!']);
    }

    public function deleteCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json(['success' => 'Coupon deleted successfully!']);
    }

    public function indexPermission()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.roles.roles-permissions', compact('roles', 'permissions'));
    }

    public function updatePermission(Request $request)
    {
        $role = Role::findOrFail($request->role_id);
        $permission = Permission::where('name', $request->permission)->firstOrFail();

        if ($request->has('has_permission')) {
            $role->givePermissionTo($permission);
        } else {
            $role->revokePermissionTo($permission);
        }

        session(['activeTab' => $request->active_tab]);

        return back()->with('success', 'Permission updated successfully!');
    }
}
