<?php
// app/Http/Controllers/SuperAdminController.php
namespace App\Http\Controllers;

use App\Models\Tenants;

class SuperAdminController extends Controller
{
    public function index()
    {
        $totalTenants = Tenants::count();

        // Hitung total active tenants
        $activeTenants = Tenants::where('status', 'Active')->count();

        // Hitung total inactive tenants
        $inactiveTenants = Tenants::where('status', 'Inactive')->count();

        // Hitung tenants baru dalam 30 hari terakhir
        $newTenantsCount = Tenants::where('created_at', '>=', now()->subDays(30))->count();

        // Hitung tenants yang ditambahkan hari ini
        $newTenantsToday = Tenants::whereDate('created_at', today())->count();

        // ✅ TAMBAHAN: Ambil recent activities untuk komponen Recent Activities
        $recentTenants = Tenants::with('user')
            ->select('id', 'name', 'created_at', 'user_id')
            ->orderBy('created_at', 'desc')
            ->limit(6) // Ambil 5 aktivitas terbaru
            ->get();

        // Data untuk paginate (existing)
        $tenants = Tenants::select('name', 'status', 'created_at', 'avatar')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        return view('super-admin.index', compact(
            'totalTenants',
            'activeTenants',
            'inactiveTenants',
            'newTenantsCount',
            'newTenantsToday',
            'tenants',
            'recentTenants' // ✅ Tambahan untuk Recent Activities
        ));
    }

    // Methods lainnya tetap sama...
    public function index2()
    {
        $tenants = Tenants::with('user')->orderBy('id', 'desc')->get();
        return view('super-admin.index2', compact('tenants'));
    }

    public function index3()
    {
        return view('super-admin.index3');
    }

    public function index4()
    {
        return view('super-admin.index4');
    }

    public function index5()
    {
        return view('super-admin.index5');
    }

    public function index6()
    {
        return view('super-admin.index6');
    }

    public function index7()
    {
        return view('super-admin.index7');
    }

    public function index8()
    {
        return view('super-admin.index8');
    }

    public function index9()
    {
        return view('super-admin.index9');
    }
}

