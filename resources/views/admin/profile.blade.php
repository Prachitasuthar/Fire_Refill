@extends('admin-layouts.app')

@section('content')
    <div style="display: flex; flex-direction: column; align-items: center; min-height: 100vh; background-color: #f3f4f6;">

       

        <div style="width: 100%; max-width: 600px; background-color: white; 
                    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1); border-radius: 10px; padding: 20px; margin: auto; margin-top: 50px;">
            
            <div style="position: relative; width: 100%; max-width: 600px; margin-bottom: 10px; text-align: center;">
                <h2 style="font-size: 24px; font-weight: 600; color: #2d3748; margin: 0;">
                    {{ auth()->user()->user_type === 'provider' ? 'Provider' : 'Admin' }} Profile
                </h2>
            
                {{-- @if(auth()->user()->user_type === 'admin') --}}
                    <a href="{{ route('admin.dashboard') }}" 
                       style="position: absolute; left: 0; background-color: #4a5568; color: white; 
                              padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 14px;">
                        ⬅
                    </a>
                {{-- @endif --}}
            </div>
            

            @if (session('success'))
                <div style="background-color: #d1fae5; color: #047857; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div style="background-color: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                    <ul style="list-style-type: none; padding: 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            

            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data"
                style="display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('PUT')

                <div style="display: flex; justify-content: center; align-items: center; margin-top: 15px;">
                    <img src="{{ asset(auth()->user()->profile_image ?? 'img/profile/Profile-image.png') }}" 
                         alt="Profile"
                         style="width: 150px; height: 150px; object-fit: cover; border-radius: 10px;">
                </div>

                <div style="text-align: center; margin-top: 10px;">
                    <label for="profile_image"
                        style="display: inline-block; padding: 6px 10px; background: #007bff; color: white; 
                              border-radius: 4px; cursor: pointer; font-size: 14px;">
                        📷 Change Image
                    </label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;">
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #4a5568;">First Name</label>
                    <input type="text" name="first_name" value="{{ auth()->user()->first_name }}"
                        style="width: 100%; border: 1px solid #cbd5e0; border-radius: 8px; padding: 12px; margin-top: 8px;">
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #4a5568;">Last Name</label>
                    <input type="text" name="last_name" value="{{ auth()->user()->last_name }}"
                        style="width: 100%; border: 1px solid #cbd5e0; border-radius: 8px; padding: 12px; margin-top: 8px;">
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #4a5568;">Email Address</label>
                    <input type="email" name="email" value="{{ auth()->user()->email }}"
                        style="width: 100%; border: 1px solid #cbd5e0; border-radius: 8px; padding: 12px; margin-top: 8px;">
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #4a5568;">Mobile Number</label>
                    <input type="text" name="mobile_no" value="{{ auth()->user()->mobile_no }}"
                        style="width: 100%; border: 1px solid #cbd5e0; border-radius: 8px; padding: 12px; margin-top: 8px;">
                </div>

                <div>
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #4a5568;">Address</label>
                    <textarea name="address"
                        style="width: 100%; border: 1px solid #cbd5e0; border-radius: 8px; padding: 12px; margin-top: 8px;">{{ auth()->user()->address }}</textarea>
                </div>

                @if (auth()->user()->user_type === 'provider')
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #4a5568;">Business Name</label>
                        <input type="text" name="business_name"
                            value="{{ auth()->user()->serviceProvider->business_name }}"
                            style="width: 100%; border: 1px solid #cbd5e0; border-radius: 8px; padding: 12px; margin-top: 8px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #4a5568;">License</label>
                        <input type="file" name="license" accept="image/*,.pdf,.avif,.doc,.docx,.txt"
                            style="margin-top: 8px;">

                        @if (auth()->user()->serviceProvider->license)
                            <img src="{{ asset(auth()->user()->serviceProvider->license) }}" alt="License"
                                style="max-width: 100px; margin-top: 10px;">
                        @endif
                    </div>
                @endif

                <button type="submit"
                    style="width: 100%; background-color: #2563eb; color: white; padding: 12px; border-radius: 8px; margin-top: 12px;">
                    Update Profile
                </button>
            </form>
        </div>
    </div>
@endsection
