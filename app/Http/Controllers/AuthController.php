<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use App\Models\ServiceProvider;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }



    // public function register(Request $request)
    // {
    //     $validated = $request->validate([
    //         'first_name' => 'required|string|max:255',
    //         'last_name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'password' => 'required|string|min:8|confirmed',
    //         'mobile_no' => 'required|string|max:10',
    //         'address' => 'required|string',
    //         'user_type' => 'required|in:user,admin,provider',
    //         'business_name' => 'required_if:user_type,provider|string|max:255',
    //         'license' => 'required_if:user_type,provider|file|mimes:jpg,jpeg,png,pdf|max:2048',
    //     ]);

    //     $verificationToken = Str::random(60);

    //     $user = User::create([
    //         'first_name' => $validated['first_name'],
    //         'last_name' => $validated['last_name'],
    //         'email' => $validated['email'],
    //         'password' => bcrypt($validated['password']),
    //         'mobile_no' => $validated['mobile_no'],
    //         'address' => $validated['address'],
    //         'user_type' => $validated['user_type'],
    //         'verification_token' => $verificationToken,
    //         'is_email_verified' => 0,
    //     ]);

    //     if ($user->user_type === 'provider') {
    //         $licenseFolder = public_path('img/licenses');
    //         if (!File::exists($licenseFolder)) {
    //             File::makeDirectory($licenseFolder, 0775, true);
    //         }

    //         $providerRole = Role::firstOrCreate(['name' => 'provider']);
    //     $user->assignRole($providerRole);

    //         $licenseFile = $request->file('license');
    //         $licensePath = $licenseFile->move($licenseFolder, $licenseFile->getClientOriginalName());

    //         ServiceProvider::create([
    //             'user_id' => $user->id,
    //             'business_name' => $validated['business_name'],
    //             'license' => 'img/licenses/' . $licenseFile->getClientOriginalName(),
    //             'status' => 'pending',
    //         ]);
    //     }

    //     Mail::send('emails.provider_verification', ['user' => $user], function ($message) use ($user) {
    //         $message->to($user->email)
    //             ->subject('Email Verification');
    //     });

    //     return response()->json(['message' => 'User registered successfully. Please check your email for verification.']);
    // }
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'mobile_no' => 'required|string|max:10',
                'address' => 'required|string',
                'user_type' => 'required|in:user,admin,provider',
                'business_name' => 'required_if:user_type,provider|string|max:255',
                'license' => 'required_if:user_type,provider|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first() // Show first validation error
                ], 422);
            }

            $verificationToken = Str::random(60);

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'mobile_no' => $request->mobile_no,
                'address' => $request->address,
                'user_type' => $request->user_type,
                'verification_token' => $verificationToken,
                'is_email_verified' => 0,
            ]);

            if ($user->user_type === 'provider') {
                $licenseFolder = public_path('img/licenses');
                if (!File::exists($licenseFolder)) {
                    File::makeDirectory($licenseFolder, 0775, true);
                }

                $providerRole = Role::firstOrCreate(['name' => 'provider']);
                $user->assignRole($providerRole);

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
                $message->to($user->email)
                    ->subject('Email Verification');
            });

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully. Please check your email for verification.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }



    public function showLogin()
    {
        return view('auth.login');
    }


    // public function login(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect()->route('login')->withInput()->withErrors($validator);
    //     }

    //     $admin = User::where('user_type', 'admin')->first();

    //     if ($admin && $request->email === $admin->email && Hash::check($request->password, $admin->password)) {
    //         // Log in the admin user
    //         Auth::login($admin);
    //         return redirect()->route('admin.dashboard');
    //     }

    //     if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
    //         $user = Auth::user();

    //         if (!$user->is_email_verified) {
    //             Auth::logout();

    //             if ($user->user_type === 'provider') {
    //                 return redirect()->route('provider.login')->with('error', 'Please verify your email before logging in.');
    //             } else {
    //                 return redirect()->route('login')->with('error', 'Please verify your email before logging in.');
    //             }
    //         }

    //         // Redirect based on user role
    //         if ($user->user_type === 'provider') {
    //             return redirect()->route('provider.dashboard');
    //         } elseif ($user->user_type === 'admin') {
    //             return redirect()->route('admin.dashboard');
    //         } else { // Regular user
    //             return redirect()->route('dashboard.index');
    //         }
    //     }

    //     return redirect()->route('login')->with('error', 'Invalid login credentials');
    // }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('login')->withInput()->withErrors($validator);
        }


        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'This email is not registered. Please register first.');
        }

        $admin = User::where('user_type', 'admin')->first();
        if ($admin && $request->email === $admin->email && Hash::check($request->password, $admin->password)) {
            Auth::login($admin);
            return redirect()->route('admin.dashboard');
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if (!$user->is_email_verified) {
                Auth::logout();

                $redirectRoute = $user->user_type === 'provider' ? 'provider.login' : 'login';
                return redirect()->route($redirectRoute)->with('error', 'Please verify your email before logging in.');
            }

            switch ($user->user_type) {
                case 'provider':
                    return redirect()->route('provider.dashboard');
                case 'admin':
                    return redirect()->route('admin.dashboard');
                default:
                    return redirect()->route('dashboard.index');
            }
        }

        return redirect()->route('login')->with('error', 'Invalid login credentials');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('dashboard.index');
    }

    public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)->first();

        if ($user) {
            $user->is_email_verified = 1;
            $user->verification_token = null;
            $user->save();

            return view('emails.provider_verification');
        }

        return redirect()->route('login')->with('error', 'Invalid verification link.');
    }


    public function verifyProvider($id)
    {
        $user = User::find($id);

        if ($user && !$user->is_email_verified) {
            $user->is_email_verified = 1;
            $user->save();

            Mail::send('emails.emailVerificationSuccess', ['user' => $user], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Email Verification Success');
            });

            return redirect()->route($user->user_type === 'provider' ? 'provider.login' : 'login')
                ->with('success', '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Success!</strong> Your email has been successfully verified. Please log in.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>');
        }

        return redirect()->route('login')->with('error', '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Oops!</strong> Invalid verification link or already verified.
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
         </div>');
    }


    public function checkEmailExists(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }
}
