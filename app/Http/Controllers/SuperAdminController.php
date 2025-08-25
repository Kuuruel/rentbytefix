<?php
// app/Http/Controllers/SuperAdminController.php
namespace App\Http\Controllers;

use App\Models\Tenants;
use Illuminate\Http\Request; // ✅ TAMBAH INI

class SuperAdminController extends Controller
{
    public function index()
    {
        $totalTenants = Tenants::count();
        $activeTenants = Tenants::where('status', 'Active')->count();
        $inactiveTenants = Tenants::where('status', 'Inactive')->count();
        $newTenantsCount = Tenants::where('created_at', '>=', now()->subDays(30))->count();
        $newTenantsToday = Tenants::whereDate('created_at', today())->count();

        $recentTenants = Tenants::with('user')
            ->select('id', 'name', 'created_at', 'user_id', 'country')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $tenants = Tenants::select('id', 'name', 'status', 'created_at', 'avatar', 'country')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $topCountry = Tenants::select('country')
            ->selectRaw('COUNT(*) as tenant_count')
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('tenant_count', 'desc')
            ->first();

        $countryName = $topCountry ? $topCountry->country : 'Indonesia';

        $ownerDistribution = Tenants::select('country')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->get();

        return view('super-admin.index', compact(
            'totalTenants',
            'activeTenants',
            'inactiveTenants',
            'newTenantsCount',
            'newTenantsToday',
            'tenants',
            'recentTenants',
            'countryName',
            'ownerDistribution'
        ));
    }

    public function index2()
    {
        $tenants = Tenants::with('user')->orderBy('id', 'desc')->get();
        return view('super-admin.index2', compact('tenants'));
    }

    public function index3()
    {
        return view('super-admin.index3');
    }

    // ✅ UPDATE METHOD INI - Yang punya search dan show entries functionality
    public function index4(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10); // Default 10 per page

        // Start query builder
        $query = Tenants::query();

        // Apply search filter if exists
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('country', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%");
            });
        }

        // Select fields and apply pagination
        $tenants = $query->select('id', 'name', 'status', 'created_at', 'avatar', 'country')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($request->query()); // Maintain query parameters in pagination links

        // Country stats (tidak berubah)
        $topCountry = Tenants::select('country')
            ->selectRaw('COUNT(*) as tenant_count')
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('tenant_count', 'desc')
            ->first();

        $countryName = $topCountry ? $topCountry->country : 'Indonesia';
        $countryCount = $topCountry ? $topCountry->tenant_count : 0;

        return view('super-admin.index4', compact(
            'tenants',
            'countryName',
            'countryCount',
            'search',
            'perPage'
        ));
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

    public function index8($tenant_id)
    {
        // Ambil tenant spesifik berdasarkan ID
        $tenant = Tenants::select('id', 'name', 'email', 'status', 'created_at', 'avatar', 'country')
            ->where('id', $tenant_id)
            ->first();

        if (!$tenant) {
            // Jika tenant tidak ditemukan, redirect dengan pesan error
            return redirect()->route('super-admin.index')->with('error', 'Tenant not found');
        }

        return view('super-admin.index8', compact('tenant'));
    }

    public function index9()
    {
        return view('super-admin.index9');
    }
}
