<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\Tenants;

class DashboardController extends Controller
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
            ->select('id', 'name', 'created_at', 'user_id', 'country')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Data untuk paginate (existing)
        $tenants = Tenants::select('name', 'status', 'created_at', 'avatar', 'country')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // ✅ TAMBAHAN: Country stats untuk dashboard
        $topCountry = Tenants::select('country')
            ->selectRaw('COUNT(*) as tenant_count')
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('tenant_count', 'desc')
            ->first();

        $countryName = $topCountry ? $topCountry->country : 'Indonesia';

        // ✅ UPDATED: Owner Distribution by Country (SEMUA NEGARA, tidak dibatasi 4)
        $ownerDistribution = Tenants::select('country')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('country')
            ->where('country', '!=', '') // Pastikan country tidak kosong
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->get(); // ✅ REMOVE limit(4) - sekarang ambil semua data

        return view('super-admin.index', compact(
            'totalTenants',
            'activeTenants',
            'inactiveTenants',
            'newTenantsCount',
            'newTenantsToday',
            'tenants',
            'recentTenants',
            'countryName',
            'ownerDistribution' // ✅ Sekarang berisi semua negara
        ));
    }

    // Methods lainnya tetap sama...
}
