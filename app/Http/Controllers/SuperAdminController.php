<?php
// app/Http/Controllers/SuperAdminController.php
namespace App\Http\Controllers;

use App\Models\Tenants;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index()
    {
        $totalTenants = Tenants::count();
        $activeTenants = Tenants::where('status', 'Active')->count();
        $inactiveTenants = Tenants::where('status', 'Inactive')->count();
        $newTenantsCount = Tenants::where('created_at', '>=', now()->subDays(30))->count();
        $newTenantsToday = Tenants::whereDate('created_at', today())->count();

        // ✅ ENHANCED: Recent Activities dengan berbagai jenis aktivitas
        $recentActivities = collect();

        // 1. Recent Tenant Registrations
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

        // 2. Recent Successful Payments (jika tabel ada)
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

        // 3. Recent Bill Creations (jika tabel ada)
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

        // 4. Gabungkan semua aktivitas dan sort by created_at
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

        // NEW: Chart Data untuk Growth Comparison
        $chartData = $this->getChartData();

        return view('super-admin.index', compact(
            'totalTenants',
            'activeTenants',
            'inactiveTenants',
            'newTenantsCount',
            'newTenantsToday',
            'tenants',
            'recentActivities', // ✅ Kirim recentActivities
            'countryName',
            'ownerDistribution',
            'chartData'
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
            ->appends($request->query());

        // Country stats
        $topCountry = Tenants::select('country')
            ->selectRaw('COUNT(*) as tenant_count')
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('tenant_count', 'desc')
            ->first();

        $countryName = $topCountry ? $topCountry->country : 'Indonesia';
        $countryCount = $topCountry ? $topCountry->tenant_count : 0;

        // NEW: Hitung statistik real untuk setiap tenant
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
        // Ambil tenant spesifik berdasarkan ID
        $tenant = Tenants::select('id', 'name', 'email', 'status', 'created_at', 'avatar', 'country')
            ->where('id', $tenant_id)
            ->first();

        if (!$tenant) {
            return redirect()->route('super-admin.index')->with('error', 'Tenant not found');
        }

        // NEW: Hitung statistik detail untuk tenant ini
        $tenantWithStats = $this->calculateTenantStats($tenant);

        return view('super-admin.index8', compact('tenant', 'tenantWithStats'));
    }

    public function index9()
    {
        return view('super-admin.index9');
    }

    /**
     * Get chart data untuk Growth Comparison (Billing vs Payment)
     */
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
            \Log::error('Error getting chart data: ' . $e->getMessage());
        }

        return [
            'months' => $months,
            'billing' => $billingData
        ];
    }

    /**
     * Hitung statistik real untuk tenant
     */
    private function calculateTenantStats($tenant)
    {
        $tenantUser = User::where('id', $tenant->user_id)->first();

        if (!$tenantUser) {
            return (object) array_merge($tenant->toArray(), [
                'payment_success_rate' => 0,
                'weekly_change' => 0,
                'monthly_revenue' => 0,
                'bills_count' => 0,
                'transactions_count' => 0,
            ]);
        }

        // Hitung statistik berdasarkan data real
        $thisWeekStart = now()->startOfWeek();
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();

        // Bills statistics
        $totalBills = Bill::where('tenant_id', $tenantUser->id)->count();
        $paidBills = Bill::where('tenant_id', $tenantUser->id)
            ->where('status', 'paid')
            ->count();

        // Payment success rate
        $paymentSuccessRate = $totalBills > 0 ? ($paidBills / $totalBills) * 100 : 0;

        // Weekly comparison
        $thisWeekTransactions = Transaction::whereHas('bill', function ($query) use ($tenantUser) {
            $query->where('tenant_id', $tenantUser->id);
        })
            ->where('created_at', '>=', $thisWeekStart)
            ->where('status', Transaction::STATUS_SUCCESS)
            ->count();

        $lastWeekTransactions = Transaction::whereHas('bill', function ($query) use ($tenantUser) {
            $query->where('tenant_id', $tenantUser->id);
        })
            ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->where('status', Transaction::STATUS_SUCCESS)
            ->count();

        // Calculate weekly change
        $weeklyChange = 0;
        if ($lastWeekTransactions > 0) {
            $weeklyChange = (($thisWeekTransactions - $lastWeekTransactions) / $lastWeekTransactions) * 100;
        } elseif ($thisWeekTransactions > 0) {
            $weeklyChange = 100; // 100% increase if last week was 0
        }

        // Monthly revenue
        $monthlyRevenue = Transaction::whereHas('bill', function ($query) use ($tenantUser) {
            $query->where('tenant_id', $tenantUser->id);
        })
            ->where('status', Transaction::STATUS_SUCCESS)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Transaction count
        $transactionsCount = Transaction::whereHas('bill', function ($query) use ($tenantUser) {
            $query->where('tenant_id', $tenantUser->id);
        })
            ->where('status', Transaction::STATUS_SUCCESS)
            ->count();

        return (object) array_merge($tenant->toArray(), [
            'payment_success_rate' => round($paymentSuccessRate, 1),
            'weekly_change' => round($weeklyChange, 1),
            'monthly_revenue' => $monthlyRevenue,
            'bills_count' => $totalBills,
            'transactions_count' => $transactionsCount,
        ]);
    }

    /**
     * Statistik dashboard untuk tenant detail (index8)
     */
    private function getTenantDashboardStats($tenant_id)
    {
        $tenantUser = User::where('id', $tenant_id)->first();

        if (!$tenantUser) {
            return [
                'pending_bills' => 0,
                'overdue_bills' => 0,
                'transactions_monthly_change' => 0,
                'sales_monthly_change' => 0,
                'average_per_transaction' => 0,
                'average_transaction_change' => 0,
            ];
        }

        // 1. Pending Bills
        $pendingBills = Bill::where('tenant_id', $tenantUser->id)
            ->where('status', 'pending')
            ->count();

        // 2. Overdue Bills
        $overdueBills = Bill::where('tenant_id', $tenantUser->id)
            ->where('status', 'overdue')
            ->orWhere(function ($query) use ($tenantUser) {
                $query->where('tenant_id', $tenantUser->id)
                    ->where('due_date', '<', now())
                    ->where('status', 'pending');
            })
            ->count();

        // 3. Transactions Monthly Change (+12% vs last month)
        $thisMonthTransactions = Transaction::whereHas('bill', function ($query) use ($tenantUser) {
            $query->where('tenant_id', $tenantUser->id);
        })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastMonthTransactions = Transaction::whereHas('bill', function ($query) use ($tenantUser) {
            $query->where('tenant_id', $tenantUser->id);
        })
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        $transactionsMonthlyChange = $lastMonthTransactions > 0
            ? (($thisMonthTransactions - $lastMonthTransactions) / $lastMonthTransactions) * 100
            : ($thisMonthTransactions > 0 ? 100 : 0);

        // 4. Total Sales Monthly Change (+18% vs last month)
        $thisMonthSales = Transaction::whereHas('bill', function ($query) use ($tenantUser) {
            $query->where('tenant_id', $tenantUser->id);
        })
            ->where('status', Transaction::STATUS_SUCCESS)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $lastMonthSales = Transaction::whereHas('bill', function ($query) use ($tenantUser) {
            $query->where('tenant_id', $tenantUser->id);
        })
            ->where('status', Transaction::STATUS_SUCCESS)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');

        $salesMonthlyChange = $lastMonthSales > 0
            ? (($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100
            : ($thisMonthSales > 0 ? 100 : 0);

        // 5. Average per Transaction (Rp 500,000 +168.001%)
        $totalSuccessTransactions = Transaction::whereHas('bill', function ($query) use ($tenantUser) {
            $query->where('tenant_id', $tenantUser->id);
        })
            ->where('status', Transaction::STATUS_SUCCESS)
            ->count();

        $averagePerTransaction = $totalSuccessTransactions > 0
            ? Transaction::whereHas('bill', function ($query) use ($tenantUser) {
                $query->where('tenant_id', $tenantUser->id);
            })
            ->where('status', Transaction::STATUS_SUCCESS)
            ->avg('amount')
            : 0;

        // Average change comparison (current month vs last month)
        $thisMonthAvg = $thisMonthTransactions > 0 ? $thisMonthSales / $thisMonthTransactions : 0;
        $lastMonthAvg = $lastMonthTransactions > 0 ? $lastMonthSales / $lastMonthTransactions : 0;

        $averageTransactionChange = $lastMonthAvg > 0
            ? (($thisMonthAvg - $lastMonthAvg) / $lastMonthAvg) * 100
            : ($thisMonthAvg > 0 ? 100 : 0);

        return [
            'pending_bills' => $pendingBills,
            'overdue_bills' => $overdueBills,
            'transactions_monthly_change' => round($transactionsMonthlyChange, 1),
            'sales_monthly_change' => round($salesMonthlyChange, 1),
            'total_sales_this_month' => $thisMonthSales,
            'average_per_transaction' => round($averagePerTransaction, 0),
            'average_transaction_change' => round($averageTransactionChange, 3),
        ];
    }

    /**
     * Data untuk Sales Overview Chart (Income vs Expenses)
     */
    private function getTenantSalesOverviewData($tenant_id)
    {
        $tenantUser = User::where('id', $tenant_id)->first();

        if (!$tenantUser) {
            return [
                'income_data' => array_fill(0, 12, 0),
                'income_total' => 0,
                'income_change' => 0,
                'expenses_data' => array_fill(0, 12, 0),
                'expenses_total' => 0,
                'expenses_change' => 0,
            ];
        }

        $currentYear = now()->year;

        // Income data (dari successful transactions per bulan)
        $incomeData = array_fill(0, 12, 0);
        $incomeResults = Transaction::whereHas('bill', function ($query) use ($tenantUser) {
            $query->where('tenant_id', $tenantUser->id);
        })
            ->where('status', Transaction::STATUS_SUCCESS)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->get();

        foreach ($incomeResults as $result) {
            $incomeData[$result->month - 1] = (float) $result->total;
        }

        // Expenses data (asumsi 60-70% dari income sebagai expenses)
        $expensesData = array_map(function ($income) {
            return $income * (rand(60, 70) / 100);
        }, $incomeData);

        // Calculate totals and changes
        $incomeTotal = array_sum($incomeData);
        $expensesTotal = array_sum($expensesData);

        // Monthly changes (current month vs last month)
        $currentMonth = now()->month - 1;
        $lastMonth = $currentMonth > 0 ? $currentMonth - 1 : 11;

        $incomeChange = $incomeData[$lastMonth] > 0
            ? (($incomeData[$currentMonth] - $incomeData[$lastMonth]) / $incomeData[$lastMonth]) * 100
            : ($incomeData[$currentMonth] > 0 ? 100 : 0);

        $expensesChange = $expensesData[$lastMonth] > 0
            ? (($expensesData[$currentMonth] - $expensesData[$lastMonth]) / $expensesData[$lastMonth]) * 100
            : ($expensesData[$currentMonth] > 0 ? 100 : 0);

        return [
            'income_data' => $incomeData,
            'income_total' => $incomeTotal,
            'income_change' => round($incomeChange, 1),
            'expenses_data' => $expensesData,
            'expenses_total' => $expensesTotal,
            'expenses_change' => round($expensesChange, 1),
        ];
    }
}
