<?php

namespace App\Http\Controllers;

use App\Models\Tenants;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTenants = Tenants::count();

        // SOLUSI 1: Gunakan paginate() (REKOMENDASI)
        $tenants = Tenants::select('name', 'status', 'created_at', 'avatar')
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Ganti limit(10)->get() dengan paginate(10)

        return view('super-admin.index', compact('totalTenants', 'tenants'));
    }
}

// ATAU SOLUSI 2: Jika ingin tetap pakai get(), ubah view-nya
// Uncomment kode di bawah jika mau pakai solusi 2:

/*
class DashboardController extends Controller
{
    public function index()
    {
        $totalTenants = Tenants::count();
       
        // Tetap pakai get() - tapi harus ubah view
        $tenants = Tenants::select('name', 'status', 'created_at', 'avatar')
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get();
       
        return view('super-admin.index', compact('totalTenants', 'tenants'));
    }
}
*/