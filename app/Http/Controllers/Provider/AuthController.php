<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    function index()
    {
        return view('admin.dashboard');
    }

    public function showRegisterForm()
    {

        return view('provider.login');
    }


    public function register(Request $request)
    {


        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'mobile_no' => 'required|string|max:10',
            'address' => 'required|string',
        ]);

        User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'mobile_no' => $validated['mobile_no'],
            'address' => $validated['address'],
        ]);

        return response()->json(['message' => 'User registered successfully.']);
    }


    public function showLogin()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('login')->withInput()->withErrors($validator);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if ($user->user_type == 'provider') {
                return redirect()->route('provider.dashboard');
            } elseif ($user->user_type == 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->user_type == 'user') {
                return redirect()->route('dashboard');
            }

            return redirect()->route('dashboard');
        } else {
            return redirect()->route('login')->with('error', 'Invalid login credentials');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
