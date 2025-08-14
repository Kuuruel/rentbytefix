<?php

namespace App\Http\Controllers;

use App\Models\Tenants;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTenants = \App\Models\Tenants::count();
        // $activeCount = \App\Models\Tenants::where('status', 'active')->count(); // Pastikan kolom 'status' ada

        return view('super-admin.index', compact('totalTenants', 'activeCount'));
    }
}
