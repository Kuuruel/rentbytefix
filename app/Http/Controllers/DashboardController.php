<?php

namespace App\Http\Controllers;

use App\Models\Tenants;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTenants = \App\Models\Tenants::count();

        return view('super-admin.index', compact('totalTenants'));
    }
}
