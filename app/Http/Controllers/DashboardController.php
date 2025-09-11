<?php

namespace App\Http\Controllers;

use App\Models\Tenants;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        
        $totalTenants = Tenants::count();
        $activeTenants = Tenants::where('status', 'Active')->count();
        $inactiveTenants = Tenants::where('status', 'Inactive')->count();
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
                    'created_by' => $tenant->user ? $tenant->user->name : 'rentbyte',
                    'created_at' => $tenant->created_at,
                    'description' => 'registered as new landlord',
                    'icon' => 'solar:user-plus-bold',
                    'bg_color' => 'bg-success-100 dark:bg-success-600/10',
                    'icon_color' => 'text-success-600 dark:text-success-400'
                ];
            });

        
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

        
        $recentBills = Bill::with('tenant')
            ->select('id', 'tenant_id', 'amount', 'due_date', 'created_at')
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

        
        $recentFailedPayments = Transaction::with(['bill.tenant'])
            ->where('status', Transaction::STATUS_FAILED)
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get()
            ->map(function ($transaction) {
                return (object)[
                    'type' => 'payment_failed',
                    'tenant_name' => $transaction->bill->tenant->name ?? 'Unknown',
                    'amount' => $transaction->amount,
                    'created_at' => $transaction->created_at,
                    'description' => 'payment failed',
                    'icon' => 'solar:close-circle-bold',
                    'bg_color' => 'bg-danger-100 dark:bg-danger-600/10',
                    'icon_color' => 'text-danger-600 dark:text-danger-400'
                ];
            });

        
        $recentActivities = $recentTenants
            ->concat($recentPayments)
            ->concat($recentBills)
            ->concat($recentFailedPayments)
            ->sortByDesc('created_at')
            ->take(5); 

        
        $ownerDistribution = Tenants::select('country')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->get();

        
        $monthlyBillings = Bill::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount'); 

        
        $billsThisMonth = Bill::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount'); 

        $billsLastMonth = Bill::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('amount'); 

        $billsDecrease = $billsThisMonth - $billsLastMonth;

        
        $platformRevenue = Transaction::where('status', Transaction::STATUS_SUCCESS)
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('amount');

        
        $revenue30DaysAgo = Transaction::where('status', Transaction::STATUS_SUCCESS)
            ->where('created_at', '>=', now()->subDays(60))
            ->where('created_at', '<', now()->subDays(30))
            ->sum('amount');
        $revenueIncrease = $platformRevenue - $revenue30DaysAgo;

        
        $totalTransactionsThisWeek = Transaction::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();

        $totalTransactionsLastWeek = Transaction::whereBetween('created_at', [
            now()->subWeek()->startOfWeek(),
            now()->subWeek()->endOfWeek()
        ])->count();

        $transactionPercentageChange = $totalTransactionsLastWeek > 0
            ? (($totalTransactionsThisWeek - $totalTransactionsLastWeek) / $totalTransactionsLastWeek) * 100
            : ($totalTransactionsThisWeek > 0 ? 100 : 0); 

        
        $totalTransactionsForSuccessRate = Transaction::whereBetween('created_at', [
            now()->subDays(30), 
            now()
        ])->count();

        $successfulTransactions = Transaction::where('status', Transaction::STATUS_SUCCESS)
            ->whereBetween('created_at', [
                now()->subDays(30), 
                now()
            ])
            ->count();

        $paymentSuccessRate = $totalTransactionsForSuccessRate > 0
            ? ($successfulTransactions / $totalTransactionsForSuccessRate) * 100
            : 0;

        
        $totalSuccessfulTransactions = Transaction::where('status', Transaction::STATUS_SUCCESS)
            ->whereBetween('created_at', [
                now()->subDays(30), 
                now()
            ])
            ->count();

        $totalTransactionAmount = Transaction::where('status', Transaction::STATUS_SUCCESS)
            ->whereBetween('created_at', [
                now()->subDays(30), 
                now()
            ])
            ->sum('amount');

        
        $averageTransactionPerTenant = $totalSuccessfulTransactions > 0
            ? $totalTransactionAmount / $totalSuccessfulTransactions
            : 0;

        
        $chartData = $this->getChartData();

        return view('super-admin.index', compact(
            'totalTenants',
            'activeTenants',
            'inactiveTenants',
            'newTenantsToday',
            'recentActivities',
            'ownerDistribution',
            'monthlyBillings',
            'billsDecrease',
            'platformRevenue',
            'revenueIncrease',
            'totalTransactionsThisWeek',
            'transactionPercentageChange',
            'paymentSuccessRate',
            'averageTransactionPerTenant',
            'successfulTransactions',
            'totalTransactionsForSuccessRate', 
            'chartData'
        ));
    }

    private function getChartData()
    {
        $currentYear = date('Y');
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        
        $billingData = array_fill(0, 12, 0);

        
        $billingResults = Bill::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->get();

        
        foreach ($billingResults as $result) {
            $monthIndex = $result->month - 1; 
            $billingData[$monthIndex] = (float) $result->total;
        }

        return [
            'months' => $months,
            'billing' => $billingData,
        ];
    }
}
