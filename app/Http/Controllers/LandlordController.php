<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Renter;
use App\Models\Bill;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LandlordController extends Controller
{
    public function index()
    {
        // Get current authenticated tenant
        $tenantId = Auth::user()->tenant_id ?? Auth::id();
        
        // Calculate statistics
        $statistics = $this->calculateStatistics($tenantId);
        
        // Get monthly revenue data for chart
        $monthlyRevenue = $this->getMonthlyRevenue($tenantId);
        
        return view('landlord.index', compact('statistics', 'monthlyRevenue'));
    }
    
    private function calculateStatistics($tenantId)
    {
        // Total Renters (Users)
        $totalRenters = Renter::where('tenant_id', $tenantId)->count();
        $newRentersLast30Days = Renter::where('tenant_id', $tenantId)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();
        
        // Total Properties
        $totalProperties = Property::where('tenant_id', $tenantId)->count();
        $newPropertiesLast30Days = Property::where('tenant_id', $tenantId)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();
        
        // Total Free Tenants (Renters without any bills)
        $freeRenters = Renter::where('tenant_id', $tenantId)
            ->whereDoesntHave('bills')
            ->count();
        $newFreeRentersLast30Days = Renter::where('tenant_id', $tenantId)
            ->whereDoesntHave('bills')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();
        
        // Total Income (Paid Bills)
        $totalIncome = Bill::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->sum('amount');
        $incomeLast30Days = Bill::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->where('updated_at', '>=', Carbon::now()->subDays(30))
            ->sum('amount');
        
        // Total Pending Bills (as expense placeholder)
        $totalPending = Bill::where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->sum('amount');
        $pendingLast30Days = Bill::where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sum('amount');
        
        return [
            'totalRenters' => $totalRenters,
            'newRentersLast30Days' => $newRentersLast30Days,
            'totalProperties' => $totalProperties,
            'newPropertiesLast30Days' => $newPropertiesLast30Days,
            'freeRenters' => $freeRenters,
            'newFreeRentersLast30Days' => $newFreeRentersLast30Days,
            'totalIncome' => $totalIncome,
            'incomeLast30Days' => $incomeLast30Days,
            'totalPending' => $totalPending,
            'pendingLast30Days' => $pendingLast30Days,
        ];
    }
    
    private function getMonthlyRevenue($tenantId)
    {
        $monthlyData = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Bill::where('tenant_id', $tenantId)
                ->where('status', 'paid')
                ->whereYear('updated_at', $month->year)
                ->whereMonth('updated_at', $month->month)
                ->sum('amount');
            
            $monthlyData[] = [
                'month' => $month->format('M'),
                'revenue' => $revenue / 1000
            ];
        }
        
        return $monthlyData;
    }

    public function index2()
    {
        return view('landlord.index2');
    }

    public function index3()
    {
        return view('landlord.index3');
    }

    public function index4()
    {
        return view('landlord.index4');
    }

    public function index5()
    {
        return view('landlord.index5');
    }

    public function index6()
    {
        return view('landlord.index6');
    }

    public function index7()
    {
        return view('landlord.index7');
    }

    public function index8()
    {
        return view('landlord.index8');
    }

    public function index9()
    {
        return view('landlord.index9');
    }
}