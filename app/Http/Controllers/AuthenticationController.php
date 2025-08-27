<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function signin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Attempt login
        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect sesuai role
            return match ($user->role) {
                'admin'   => redirect()->route('super-admin.index'),
                'tenants' => redirect()->route('landlord.index'),
                default   => redirect()->route('home'), // fallback biar ga error
            };
        }

        // Login gagal - kembali dengan error dan input lama
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->except('password'));
    }
}
