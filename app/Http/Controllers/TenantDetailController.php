<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TenantDetailController extends Controller
{
    public function show($id)
    {
        $tenant = User::findOrFail($id);

        // Data untuk widget statistics
        $statistics = $this->getTenantStatistics($id);

        // Data untuk Sales Overview chart
        $salesOverview = $this->getSalesOverviewData($id);

        return view('index8', compact('tenant', 'statistics', 'salesOverview'));
    }

    private function getTenantStatistics($tenantId)
    {
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        // Hitung Pending Bills
        $pendingBills = Bill::where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->count();

        $pendingBillsPrevious = Bill::where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->whereMonth('created_at', $previousMonth->month)
            ->whereYear('created_at', $previousMonth->year)
            ->count();

        $pendingBillsPercentage = $this->calculatePercentageChange($pendingBills, $pendingBillsPrevious);

        // Hitung Overdue Bills
        $overdueBills = Bill::where('tenant_id', $tenantId)
            ->where('status', 'overdue')
            ->orWhere(function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId)
                    ->where('status', 'pending')
                    ->where('due_date', '<', Carbon::now());
            })
            ->count();

        $overdueBillsPrevious = Bill::where('tenant_id', $tenantId)
            ->where('status', 'overdue')
            ->whereMonth('created_at', $previousMonth->month)
            ->whereYear('created_at', $previousMonth->year)
            ->count();

        $overdueBillsPercentage = $this->calculatePercentageChange($overdueBills, $overdueBillsPrevious);

        // Hitung Paid Bills
        $paidBills = Bill::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->count();

        $paidBillsPrevious = Bill::where('tenant_id', $tenantId)
            ->where('status', 'paid')
            ->whereMonth('created_at', $previousMonth->month)
            ->whereYear('created_at', $previousMonth->year)
            ->count();

        $paidBillsPercentage = $this->calculatePercentageChange($paidBills, $paidBillsPrevious);

        // Hitung Total Transactions
        $totalTransactions = Transaction::whereHas('bill', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })->count();

        $totalTransactionsPrevious = Transaction::whereHas('bill', function ($query) use ($tenantId, $previousMonth) {
            $query->where('tenant_id', $tenantId)
                ->whereMonth('bills.created_at', $previousMonth->month)
                ->whereYear('bills.created_at', $previousMonth->year);
        })->count();

        $transactionsPercentage = $this->calculatePercentageChange($totalTransactions, $totalTransactionsPrevious);

        // Hitung Total Sales (amount dari transactions yang sukses)
        $totalSales = Transaction::where('status', 'success')
            ->whereHas('bill', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->sum('amount');

        $totalSalesPrevious = Transaction::where('status', 'success')
            ->whereHas('bill', function ($query) use ($tenantId, $previousMonth) {
                $query->where('tenant_id', $tenantId)
                    ->whereMonth('bills.created_at', $previousMonth->month)
                    ->whereYear('bills.created_at', $previousMonth->year);
            })
            ->sum('amount');

        $salesPercentage = $this->calculatePercentageChange($totalSales, $totalSalesPrevious);

        // Hitung Average per Transaction
        $averagePerTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
        $averagePerTransactionPrevious = $totalTransactionsPrevious > 0 ? $totalSalesPrevious / $totalTransactionsPrevious : 0;
        $averagePercentage = $this->calculatePercentageChange($averagePerTransaction, $averagePerTransactionPrevious);

        return [
            'pending_bills' => $pendingBills,
            'pending_bills_percentage' => $pendingBillsPercentage,
            'overdue_bills' => $overdueBills,
            'overdue_bills_percentage' => $overdueBillsPercentage,
            'paid_bills' => $paidBills,
            'paid_bills_percentage' => $paidBillsPercentage,
            'total_transactions' => $totalTransactions,
            'transactions_percentage' => $transactionsPercentage,
            'total_sales' => $totalSales,
            'sales_percentage' => $salesPercentage,
            'average_per_transaction' => $averagePerTransaction,
            'average_percentage' => $averagePercentage,
        ];
    }

    private function getSalesOverviewData($tenantId)
    {
        $currentYear = Carbon::now()->year;

        // Income data (total amount dari bills yang paid per bulan)
        $incomeData = [];
        $expenseData = [];

        for ($month = 1; $month <= 12; $month++) {
            // Income: total amount dari transactions yang success
            $income = Transaction::where('status', 'success')
                ->whereHas('bill', function ($query) use ($tenantId) {
                    $query->where('tenant_id', $tenantId);
                })
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $currentYear)
                ->sum('amount');

            $incomeData[] = (int)($income / 1000); // Convert ke ribuan untuk chart

            // Expense: bisa dihitung dari fee atau cost lainnya (untuk contoh saya set random)
            // Kamu bisa adjust sesuai logic bisnis
            $expense = $income * 0.3; // Misalnya 30% dari income sebagai expense
            $expenseData[] = (int)($expense / 1000);
        }

        // Total income dan expense tahun ini
        $totalIncome = Transaction::where('status', 'success')
            ->whereHas('bill', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->whereYear('created_at', $currentYear)
            ->sum('amount');

        $totalExpense = $totalIncome * 0.3; // 30% dari income

        // Hitung persentase vs tahun sebelumnya
        $totalIncomePrevious = Transaction::where('status', 'success')
            ->whereHas('bill', function ($query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            })
            ->whereYear('created_at', $currentYear - 1)
            ->sum('amount');

        $totalExpensePrevious = $totalIncomePrevious * 0.3;

        $incomePercentage = $this->calculatePercentageChange($totalIncome, $totalIncomePrevious);
        $expensePercentage = $this->calculatePercentageChange($totalExpense, $totalExpensePrevious);

        return [
            'income_data' => $incomeData,
            'expense_data' => $expenseData,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'income_percentage' => $incomePercentage,
            'expense_percentage' => $expensePercentage,
        ];
    }

    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
