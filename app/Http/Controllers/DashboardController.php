<?php

namespace App\Http\Controllers;

use App\Models\Tenants;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung total tenants dari DB
        $totalTenants = Tenants::count();

        // Kirim data ke view
        return view('super-admin.index', compact('totalTenants'));
    }
}