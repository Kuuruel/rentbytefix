<?php

namespace App\Http\Controllers;

use App\Models\Tenants;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SuperAdminController extends Controller
{
    public function index()
    {
        $totalTenants = Tenants::count();
        $activeTenants = Tenants::where('status', 'Active')->count();
        $inactiveTenants = Tenants::where('status', 'Inactive')->count();
        $newTenantsCount = Tenants::where('created_at', '>=', now()->subDays(30))->count();
        $newTenantsToday = Tenants::whereDate('created_at', today())->count();

        
        $recentActivities = collect();

        
        $recentTenants = Tenants::with('user')
            ->select('id', 'name', 'created_at', 'user_id', 'country')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($tenant) {
                return (object)[
                    'type' => 'tenant_registered',
                    'tenant_name' => $tenant->name,
                    'created_by' => $tenant->user ? $tenant->user->name : null,
                    'created_at' => $tenant->created_at,
                    'description' => 'registered as new landlord',
                    'icon' => 'solar:user-plus-bold',
                    'bg_color' => 'bg-success-100 dark:bg-success-600/10',
                    'icon_color' => 'text-success-600 dark:text-success-400'
                ];
            });

        
        try {
            $recentPayments = Transaction::with(['bill.tenant'])
                ->where('status', Transaction::STATUS_SUCCESS)
                ->whereNotNull('paid_at')
                ->orderBy('paid_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($transaction) {
                    return (object)[
                        'type' => 'payment_completed',
                        'tenant_name' => $transaction->bill->tenant->name ?? 'Unknown',
                        'amount' => $transaction->amount,
                        'created_at' => $transaction->paid_at,
                        'description' => 'completed payment',
                        'icon' => 'solar:wallet-money-bold',
                        'bg_color' => 'bg-primary-100 dark:bg-primary-600/10',
                        'icon_color' => 'text-primary-600 dark:text-primary-400'
                    ];
                });
        } catch (\Exception $e) {
            $recentPayments = collect();
        }

        
        try {
            $recentBills = Bill::with('tenant')
                ->orderBy('created_at', 'desc')
                ->limit(2)
                ->get()
                ->map(function ($bill) {
                    return (object)[
                        'type' => 'bill_created',
                        'tenant_name' => $bill->tenant->name ?? 'Unknown',
                        'amount' => $bill->amount,
                        'due_date' => $bill->due_date,
                        'created_at' => $bill->created_at,
                        'description' => 'received new bill',
                        'icon' => 'solar:document-add-bold',
                        'bg_color' => 'bg-warning-100 dark:bg-warning-600/10',
                        'icon_color' => 'text-warning-600 dark:text-warning-400'
                    ];
                });
        } catch (\Exception $e) {
            $recentBills = collect();
        }

        
        $recentActivities = $recentTenants
            ->concat($recentPayments)
            ->concat($recentBills)
            ->sortByDesc('created_at')
            ->take(5);

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

        
        $chartData = $this->getChartData();

        
        $monthlyBillings = Bill::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $lastMonthBillings = Bill::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');

        $billsDecrease = $lastMonthBillings > 0
            ? $monthlyBillings - $lastMonthBillings
            : $monthlyBillings;

        $platformRevenue = Bill::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $lastMonthRevenue = Transaction::where('status', Transaction::STATUS_SUCCESS)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');

        $revenueIncrease = $lastMonthRevenue > 0
            ? $platformRevenue - $lastMonthRevenue
            : $platformRevenue;

        $totalTransactionsThisWeek = Transaction::where('status', Transaction::STATUS_SUCCESS)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $totalBills = Bill::count();
        $paidBills = Bill::where('status', 'paid')->count();
        $paymentSuccessRate = $totalBills > 0 ? round(($paidBills / $totalBills) * 100, 1) : 0;

        $totalTenants = Tenants::count();
        $totalSuccessTransactions = Transaction::where('status', Transaction::STATUS_SUCCESS)->count();
        $averageTransactionPerTenant = $totalTenants > 0 ? round($totalSuccessTransactions / $totalTenants, 1) : 0;

        return view('super-admin.index', compact(
            'totalTenants',
            'activeTenants',
            'inactiveTenants',
            'newTenantsCount',
            'newTenantsToday',
            'tenants',
            'recentActivities',
            'countryName',
            'ownerDistribution',
            'chartData',
            'monthlyBillings',
            'billsDecrease',
            'platformRevenue',
            'revenueIncrease',
            'totalTransactionsThisWeek',
            'paymentSuccessRate',
            'averageTransactionPerTenant'
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

    public function index4(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        
        $query = Tenants::query();

        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('country', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%");
            });
        }

        
        $tenants = $query->select('id', 'name', 'status', 'created_at', 'avatar', 'country', 'email')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        
        $topCountry = Tenants::select('country')
            ->selectRaw('COUNT(*) as tenant_count')
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderBy('tenant_count', 'desc')
            ->first();

        $countryName = $topCountry ? $topCountry->country : 'Indonesia';
        $countryCount = $topCountry ? $topCountry->tenant_count : 0;

        
        $tenantsWithStats = $tenants->map(function ($tenant) {
            return $this->calculateTenantStats($tenant);
        });

        return view('super-admin.index4', compact(
            'tenants',
            'tenantsWithStats',
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
        
        $tenant = Tenants::select('id', 'name', 'email', 'status', 'created_at', 'avatar', 'country')
            ->where('id', $tenant_id)
            ->first();

        if (!$tenant) {
            return redirect()->route('super-admin.index')->with('error', 'Tenant tidak ditemukan');
        }

        
        $pendingBills = Bill::where('tenant_id', $tenant->id)->where('status', 'pending')->count();
        $overdueBills = Bill::where('tenant_id', $tenant->id)
            ->where(function($query) {
                $query->where('status', 'overdue')
                      ->orWhere(function($q) {
                          $q->where('due_date', '<', now())
                            ->where('status', 'pending');
                      });
            })->count();
        $paidBills = Bill::where('tenant_id', $tenant->id)->where('status', 'paid')->count();

        
        $transactionsThisMonth = Transaction::where('tenant_id', $tenant->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $transactionsLastMonth = Transaction::where('tenant_id', $tenant->id)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $transactionsChange = $transactionsLastMonth > 0 
            ? (($transactionsThisMonth - $transactionsLastMonth) / $transactionsLastMonth) * 100 
            : ($transactionsThisMonth > 0 ? 100 : 0);

        
        $salesThisMonth = Transaction::where('tenant_id', $tenant->id)
            ->where('status', Transaction::STATUS_SUCCESS)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $salesLastMonth = Transaction::where('tenant_id', $tenant->id)
            ->where('status', Transaction::STATUS_SUCCESS)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');

        $salesChange = $salesLastMonth > 0 
            ? (($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100 
            : ($salesThisMonth > 0 ? 100 : 0);

        
        $averagePerTransaction = $transactionsThisMonth > 0 ? $salesThisMonth / $transactionsThisMonth : 0;
        $lastMonthAverage = $transactionsLastMonth > 0 ? $salesLastMonth / $transactionsLastMonth : 0;
        $avgChange = $lastMonthAverage > 0 
            ? (($averagePerTransaction - $lastMonthAverage) / $lastMonthAverage) * 100 
            : ($averagePerTransaction > 0 ? 100 : 0);

        
        $chartData = $this->getTenantChartData($tenant->id);

        
        $income = $salesThisMonth;
        $expenses = $income * 0.65; 

        $lastMonthIncome = $salesLastMonth;
        $lastMonthExpenses = $lastMonthIncome * 0.65;

        $incomeChange = $lastMonthIncome > 0 
            ? (($income - $lastMonthIncome) / $lastMonthIncome) * 100 
            : ($income > 0 ? 100 : 0);

        $expensesChange = $lastMonthExpenses > 0 
            ? (($expenses - $lastMonthExpenses) / $lastMonthExpenses) * 100 
            : ($expenses > 0 ? 100 : 0);

    
$totalTransactionsThisWeek = Transaction::where('tenant_id', $tenant->id)
    ->where('status', 'success')
    ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
    ->count();

$totalTransactionsLastWeek = Transaction::where('tenant_id', $tenant->id)
    ->where('status', 'success') 
    ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
    ->count();

$transactionPercentageChange = $totalTransactionsLastWeek > 0 
    ? (($totalTransactionsThisWeek - $totalTransactionsLastWeek) / $totalTransactionsLastWeek) * 100 
    : ($totalTransactionsThisWeek > 0 ? 100 : 0);


return view('super-admin.index8', compact(
    
    
            'tenant',
            'pendingBills',
            'overdueBills', 
            'paidBills',
            'transactionsThisMonth',
            'transactionsChange',
            'salesThisMonth',
            'salesChange',
            'averagePerTransaction',
            'avgChange',
            'chartData',
            'income',
            'expenses',
            'incomeChange',
            'expensesChange',
            'totalTransactionsThisWeek',
    'transactionPercentageChange'
        ));
    }

    public function index9()
    {
        return view('super-admin.index9');
    }

    private function getChartData()
    {
        $currentYear = date('Y');
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $billingData = array_fill(0, 12, 0);

        try {
            $billingResults = Bill::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
                ->whereYear('created_at', $currentYear)
                ->groupBy('month')
                ->get();

            foreach ($billingResults as $result) {
                $monthIndex = $result->month - 1;
                $billingData[$monthIndex] = (float) $result->total;
            }
        } catch (\Exception $e) {
            Log::error('Error getting chart data: ' . $e->getMessage());
        }

        return [
            'months' => $months,
            'billing' => $billingData
        ];
    }

    
    private function getTenantChartData($tenant_id)
    {
        $currentYear = date('Y');
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data = array_fill(0, 12, 0);

        try {
            
            $results = Transaction::where('tenant_id', $tenant_id)
                ->where('status', Transaction::STATUS_SUCCESS)
                ->whereYear('created_at', $currentYear)
                ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
                ->groupBy('month')
                ->get();

            foreach ($results as $result) {
                $monthIndex = $result->month - 1;
                $data[$monthIndex] = (float) ($result->total / 1000); 
            }
        } catch (\Exception $e) {
            Log::error('Error getting tenant chart data: ' . $e->getMessage());
        }

        return [
            'months' => $months,
            'data' => $data
        ];
    }

    private function calculateTenantStats($tenant)
    {
        try {
            
            $now = Carbon::now();
            $startOfMonth = $now->copy()->startOfMonth();
            $startOfWeek = $now->copy()->startOfWeek();
            $lastWeekStart = $now->copy()->subWeek()->startOfWeek();
            $lastWeekEnd = $now->copy()->subWeek()->endOfWeek();

            
            $monthlyRevenue = Transaction::where('status', Transaction::STATUS_SUCCESS)
                ->whereHas('bill', function ($q) use ($tenant) {
                    $q->where('tenant_id', $tenant->id);
                })
                ->whereMonth('paid_at', $startOfMonth->month)
                ->whereYear('paid_at', $startOfMonth->year)
                ->sum('amount') ?? 0;

            
            $billsCount = Bill::where('tenant_id', $tenant->id)->count();

            
            $totalBills = Bill::where('tenant_id', $tenant->id)->count();
            $paidBills = Bill::where('tenant_id', $tenant->id)
                ->where('status', 'paid')
                ->count();
            
            $paymentSuccessRate = $totalBills > 0 ? round(($paidBills / $totalBills) * 100, 1) : 0;

            
            
            $thisWeekBills = Bill::where('tenant_id', $tenant->id)
                ->whereBetween('created_at', [$startOfWeek, $now])
                ->count();
            
            $thisWeekPaidBills = Bill::where('tenant_id', $tenant->id)
                ->where('status', 'paid')
                ->whereBetween('created_at', [$startOfWeek, $now])
                ->count();

            $thisWeekRate = $thisWeekBills > 0 ? ($thisWeekPaidBills / $thisWeekBills) * 100 : 0;

            
            $lastWeekBills = Bill::where('tenant_id', $tenant->id)
                ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
                ->count();
            
            $lastWeekPaidBills = Bill::where('tenant_id', $tenant->id)
                ->where('status', 'paid')
                ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
                ->count();

            $lastWeekRate = $lastWeekBills > 0 ? ($lastWeekPaidBills / $lastWeekBills) * 100 : 0;

            
            $weeklyChange = $thisWeekRate - $lastWeekRate;
            $weeklyChange = round($weeklyChange, 1);

            
            $chartData = $this->generateChartData($tenant->id);

            
            $tenantClone = clone $tenant;
            $tenantClone->monthly_revenue = $monthlyRevenue;
            $tenantClone->bills_count = $billsCount;
            $tenantClone->payment_success_rate = $paymentSuccessRate;
            $tenantClone->weekly_change = $weeklyChange;
            $tenantClone->chart_data = $chartData;

            return $tenantClone;

        } catch (\Exception $e) {
            
            Log::error('Error calculating tenant stats for tenant ' . $tenant->id . ': ' . $e->getMessage());
            
            $tenantClone = clone $tenant;
            $tenantClone->monthly_revenue = 0;
            $tenantClone->bills_count = 0;
            $tenantClone->payment_success_rate = 0;
            $tenantClone->weekly_change = 0;
            $tenantClone->chart_data = [35, 40, 38, 42, 39, 44, 41, 45, 43];

            return $tenantClone;
        }
    }

    private function generateChartData($tenantId)
    {
        try {
            $data = [];
            $now = Carbon::now();

            
            for ($i = 8; $i >= 0; $i--) {
                $month = $now->copy()->subMonths($i);
                
                
                $monthBills = Bill::where('tenant_id', $tenantId)
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count();
                
                
                $monthPaidBills = Bill::where('tenant_id', $tenantId)
                    ->where('status', 'paid')
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count();

                
                $rate = $monthBills > 0 ? ($monthPaidBills / $monthBills) * 100 : 0;
                
                
                $chartValue = 35 + ($rate / 100 * 20); 
                $chartValue = max(30, min(60, $chartValue)); 
                
                $data[] = round($chartValue, 0);
            }

            
            if (array_sum($data) === 0) {
                return [35, 40, 38, 42, 39, 44, 41, 45, 43];
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('Error generating chart data for tenant ' . $tenantId . ': ' . $e->getMessage());
            
            return [35, 40, 38, 42, 39, 44, 41, 45, 43];
        }
    }
}