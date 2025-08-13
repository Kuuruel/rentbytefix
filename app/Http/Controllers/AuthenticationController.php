<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

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
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Attempt login
    if (Auth::attempt($request->only('email', 'password'))) {
        $request->session()->regenerate();

        $user = Auth::user();
        
        // Route berdasarkan role
        if ($user->role === 'admin') {
            return redirect()->route('super-admin.index');
        } elseif ($user->role === 'landlord') {
            return redirect()->route('landlord.index');
        } else {
            // Default redirect jika role tidak dikenali
            return redirect()->route('dashboard'); // atau route default lainnya
        }
    }

    // Login gagal - kembali dengan error dan input lama
    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->withInput($request->except('password'));
}
}
