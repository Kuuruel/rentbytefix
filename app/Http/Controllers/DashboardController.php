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
        // Data Tenants (existing)
        $totalTenants = Tenants::count();
        $activeTenants = Tenants::where('status', 'Active')->count();
        $inactiveTenants = Tenants::where('status', 'Inactive')->count();
        $newTenantsToday = Tenants::whereDate('created_at', today())->count();

        // ✅ ENHANCED: Recent Activities dengan berbagai jenis aktivitas
        $recentActivities = collect();

        // 1. Recent Tenant Registrations
        $recentTenants = Tenants::with('user')
            ->select('id', 'name', 'created_at', 'user_id', 'country')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($tenant) {
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

        // 2. Recent Successful Payments
        $recentPayments = Transaction::with(['bill.tenant'])
            ->where('status', Transaction::STATUS_SUCCESS)
            ->whereNotNull('paid_at')
            ->orderBy('paid_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($transaction) {
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

        // 3. Recent Bill Creations
        $recentBills = Bill::with('tenant')
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get()
            ->map(function($bill) {
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

        // 4. Recent Failed Payments
        $recentFailedPayments = Transaction::with(['bill.tenant'])
            ->where('status', Transaction::STATUS_FAILED)
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get()
            ->map(function($transaction) {
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

        // 5. Gabungkan semua aktivitas dan sort by created_at
        $recentActivities = $recentTenants
            ->concat($recentPayments)
            ->concat($recentBills)
            ->concat($recentFailedPayments)
            ->sortByDesc('created_at')
            ->take(5); // Ambil 5 aktivitas terbaru

        // Owner Distribution
        $ownerDistribution = Tenants::select('country')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->get();

        // NEW DASHBOARD DATA - Updated sesuai model Anda

        // 1. Monthly Billings
        $monthlyBillings = Bill::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        // Data untuk Monthly Billings comparison
        $billsThisMonth = Bill::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        $billsLastMonth = Bill::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();
        $billsDecrease = $billsThisMonth - $billsLastMonth;

        // 2. Platform Revenue - pakai konstanta STATUS_SUCCESS
        $platformRevenue = Transaction::where('status', Transaction::STATUS_SUCCESS)
            ->sum('amount');

        // Revenue comparison (30 days ago)
        $revenue30DaysAgo = Transaction::where('status', Transaction::STATUS_SUCCESS)
            ->where('created_at', '>=', now()->subDays(60))
            ->where('created_at', '<', now()->subDays(30))
            ->sum('amount');
        $revenueIncrease = $platformRevenue - $revenue30DaysAgo;

        // 3. Total Transactions This Week
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
            : 0;

        // 4. Payment Success Rate - pakai konstanta STATUS_SUCCESS
        $totalTransactions = Transaction::count();
        $successfulTransactions = Transaction::where('status', Transaction::STATUS_SUCCESS)->count();
        $paymentSuccessRate = $totalTransactions > 0
            ? ($successfulTransactions / $totalTransactions) * 100
            : 0;

        // 5. Average Transaction per Tenant - pakai konstanta STATUS_SUCCESS
        $totalTransactionAmount = Transaction::where('status', Transaction::STATUS_SUCCESS)
            ->sum('amount');
        $averageTransactionPerTenant = $totalTenants > 0
            ? $totalTransactionAmount / $totalTenants
            : 0;

        // NEW: Chart Data untuk Growth Comparison
        $chartData = $this->getChartData();

        return view('super-admin.index', compact(
            'totalTenants',
            'activeTenants',
            'inactiveTenants',
            'newTenantsToday',
            'recentActivities', // ✅ Ganti recentTenants dengan recentActivities
            'ownerDistribution',
            'monthlyBillings',
            'billsDecrease', // ✅ Tambahkan untuk comparison
            'platformRevenue',
            'revenueIncrease',
            'totalTransactionsThisWeek',
            'transactionPercentageChange',
            'paymentSuccessRate',
            'averageTransactionPerTenant',
            'successfulTransactions',
            'totalTransactions',
            'chartData'
        ));
    }

    /**
     * Get chart data untuk Growth Comparison (Billing vs Payment)
     */
    private function getChartData()
    {
        $currentYear = date('Y');
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Initialize arrays
        $billingData = array_fill(0, 12, 0);
        $paymentData = array_fill(0, 12, 0);

        // Query untuk total billing per bulan (semua tenant karena ini super admin)
        $billingResults = Bill::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->get();

        // Query untuk total payment per bulan (dari transactions yang success)
        $paymentResults = Transaction::selectRaw('MONTH(transactions.created_at) as month, SUM(transactions.amount) as total')
            ->join('bills', 'transactions.bill_id', '=', 'bills.id')
            ->where('transactions.status', Transaction::STATUS_SUCCESS)
            ->whereYear('transactions.created_at', $currentYear)
            ->groupBy('month')
            ->get();

        // Mapping hasil billing ke array
        foreach ($billingResults as $result) {
            $monthIndex = $result->month - 1; // Convert 1-12 to 0-11
            $billingData[$monthIndex] = (float) $result->total;
        }

        // Mapping hasil payment ke array
        foreach ($paymentResults as $result) {
            $monthIndex = $result->month - 1; // Convert 1-12 to 0-11
            $paymentData[$monthIndex] = (float) $result->total;
        }

        return [
            'months' => $months,
            'billing' => $billingData,
            'payment' => $paymentData
        ];
    }

    /**
     * Get chart data untuk beberapa tahun terakhir (optional method)
     */
    private function getChartDataMultiYear($years = 2)
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $currentYear = date('Y');
        $startYear = $currentYear - $years + 1;

        $billingData = array_fill(0, 12, 0);
        $paymentData = array_fill(0, 12, 0);

        for ($year = $startYear; $year <= $currentYear; $year++) {
            // Query billing data
            $billingResults = Bill::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
                ->whereYear('created_at', $year)
                ->groupBy('month')
                ->get();

            // Query payment data
            $paymentResults = Transaction::selectRaw('MONTH(transactions.created_at) as month, SUM(transactions.amount) as total')
                ->join('bills', 'transactions.bill_id', '=', 'bills.id')
                ->where('transactions.status', Transaction::STATUS_SUCCESS)
                ->whereYear('transactions.created_at', $year)
                ->groupBy('month')
                ->get();

            // Accumulate data
            foreach ($billingResults as $result) {
                $monthIndex = $result->month - 1;
                $billingData[$monthIndex] += (float) $result->total;
            }

            foreach ($paymentResults as $result) {
                $monthIndex = $result->month - 1;
                $paymentData[$monthIndex] += (float) $result->total;
            }
        }

        return [
            'months' => $months,
            'billing' => $billingData,
            'payment' => $paymentData
        ];
    }

    /**
     * Get chart data dengan filter tenant tertentu (jika diperlukan)
     */
    private function getChartDataByTenant($tenantIds = [])
    {
        $currentYear = date('Y');
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $billingData = array_fill(0, 12, 0);
        $paymentData = array_fill(0, 12, 0);

        $billingQuery = Bill::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->whereYear('created_at', $currentYear);

        $paymentQuery = Transaction::selectRaw('MONTH(transactions.created_at) as month, SUM(transactions.amount) as total')
            ->join('bills', 'transactions.bill_id', '=', 'bills.id')
            ->where('transactions.status', Transaction::STATUS_SUCCESS)
            ->whereYear('transactions.created_at', $currentYear);

        // Filter by tenant jika ada
        if (!empty($tenantIds)) {
            $billingQuery->whereIn('tenant_id', $tenantIds);
            $paymentQuery->whereIn('bills.tenant_id', $tenantIds);
        }

        $billingResults = $billingQuery->groupBy('month')->get();
        $paymentResults = $paymentQuery->groupBy('month')->get();

        foreach ($billingResults as $result) {
            $monthIndex = $result->month - 1;
            $billingData[$monthIndex] = (float) $result->total;
        }

        foreach ($paymentResults as $result) {
            $monthIndex = $result->month - 1;
            $paymentData[$monthIndex] = (float) $result->total;
        }

        return [
            'months' => $months,
            'billing' => $billingData,
            'payment' => $paymentData
        ];
    }
    
}