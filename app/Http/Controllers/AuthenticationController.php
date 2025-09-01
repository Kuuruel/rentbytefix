<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenants;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function forgotPassword()
    {
        return view('authentication.forgotPassword');
    }

    public function showSigninForm()
    {
        return view('authentication.signin');
    }
    
    public function signup()
    {
        return view('authentication.signup');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tenants,email',
            'password' => 'required|string|min:6|confirmed',
            'country' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create tenant account
        $tenant = Tenants::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country' => $request->country,
            'status' => 'Active',
            'user_id' => 1, // Default user_id, adjust as needed
        ]);

        // Login tenant using custom guard
        Auth::guard('tenant')->login($tenant);

        return redirect()->route('landlord.index');
    }

     public function signin(Request $request)
        {
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required',
            ]);

        // First, try to authenticate as admin from users table
        if (Auth::guard('web')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'admin'
        ], $request->filled('remember'))) {
            
            $request->session()->regenerate();
            return redirect()->route('super-admin.index');
        }

        // If admin login failed, try to authenticate as tenant from tenants table
        $tenant = Tenants::where('email', $request->email)
                       ->where('status', 'Active')
                       ->first();

        if ($tenant && Hash::check($request->password, $tenant->password)) {
            Auth::guard('tenant')->login($tenant, $request->filled('remember'));
            $request->session()->regenerate();
            return redirect()->route('landlord.index');
        }

        // If both attempts fail, return error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        // Logout from both guards
        Auth::guard('web')->logout();
        Auth::guard('tenant')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('showSigninForm');
    }
}