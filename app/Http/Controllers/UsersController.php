<?php

// app/Http/Controllers/UserController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Hitung jumlah user aktif
        $activeCount = User::where('status', 'active')->count();

        // // Hitung jumlah user tidak aktif
        // $inactiveCount = User::where('status', 'inactive')->count();

        // Kirim ke view
        return view('super-admin.index', compact('activeCount'));
    }
}
