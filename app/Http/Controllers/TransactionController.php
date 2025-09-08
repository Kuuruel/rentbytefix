<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Bill;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function data(Request $request)
    {
        try {
            $perPage = (int) $request->input('per_page', 10);

            Log::info('Transaction data request', [
                'per_page' => $perPage,
                'status' => $request->status,
                'search' => $request->search,
                'month' => $request->month,
                'year' => $request->year,
                'all_params' => $request->all()
            ]);

            $query = Transaction::with(['bill.renter', 'bill.property'])
                ->orderBy('created_at', 'desc');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('month')) {
                $query->whereMonth('created_at', $request->month);
            }

            if ($request->filled('year')) {
                $query->whereYear('created_at', $request->year);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_id', 'like', "%$search%")
                      ->orWhere('id', 'like', "%$search%")
                      ->orWhereHas('bill', function($billQuery) use ($search) {
                          $billQuery->where('id', 'like', "%$search%")
                                   ->orWhereHas('renter', function($renterQuery) use ($search) {
                                       $renterQuery->where('name', 'like', "%$search%");
                                   })
                                   ->orWhereHas('property', function($propertyQuery) use ($search) {
                                       $propertyQuery->where('id', 'like', "%$search%")
                                                   ->orWhere('name', 'like', "%$search%")
                                                   ->orWhere('address', 'like', "%$search%");
                                   });
                      });
                });
            }

            $totalRevenue = (clone $query)->where('status', 'success')->sum('amount');
            $totalSuccessTransactions = (clone $query)->where('status', 'success')->count();

            $totalCount = $query->count();
            Log::info('Total transactions found', ['count' => $totalCount]);

            $items = $query->paginate($perPage);

            Log::info('Paginated transactions', [
                'items_count' => $items->count(),
                'first_item' => $items->first() ? $items->first()->toArray() : null
            ]);

            $transformedData = $items->map(function ($tx) {
                return [
                    'id'          => $tx->id,
                    'bill_id'     => $tx->bill_id,
                    'property_id' => $tx->bill?->property?->id ?? null,
                    'property_name' => $tx->bill?->property?->name ?? 'Unknown Property',
                    'order_id'    => $tx->order_id,
                    'renter_name' => $tx->bill?->renter?->name ?? 'Unknown',
                    'reciept_name' => $tx->bill?->renter?->name ?? 'Unknown',
                    'amount'      => $tx->amount,
                    'formatted_amount' => 'Rp ' . number_format($tx->amount, 0, ',', '.'),
                    'status'      => $tx->status,
                    'status_badge_class' => $this->getStatusBadgeClass($tx->status),
                    // FORMAT TANGGAL DENGAN WIB
                    'created_at'  => $tx->created_at ? Carbon::parse($tx->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB' : null,
                    'created_at_iso'  => $tx->created_at ? Carbon::parse($tx->created_at)->setTimezone('Asia/Jakarta')->toISOString() : null,
                    'paid_at'     => $tx->paid_at ? Carbon::parse($tx->paid_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB' : null,
                    'paid_at_iso' => $tx->paid_at ? Carbon::parse($tx->paid_at)->setTimezone('Asia/Jakarta')->toISOString() : null,
                ];
            });

            $response = [
                'success' => true,
                'data' => [
                    'data' => $transformedData,
                    'from'      => $items->firstItem(),
                    'to'        => $items->lastItem(),
                    'total'     => $items->total(),
                    'last_page' => $items->lastPage(),
                    'current_page' => $items->currentPage(),
                    'per_page'  => $items->perPage(),
                ],
                'summary' => [
                    'total_revenue' => (float) $totalRevenue,
                    'formatted_total_revenue' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'),
                    'total_success_transactions' => $totalSuccessTransactions,
                ]
            ];

            Log::info('Transaction API response', [
                'success' => $response['success'],
                'total' => $response['data']['total'],
                'data_count' => count($response['data']['data']),
                'total_revenue' => $response['summary']['total_revenue']
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Transaction data error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading transactions: ' . $e->getMessage(),
                'data' => [
                    'data' => [],
                    'total' => 0,
                    'from' => 0,
                    'to' => 0,
                    'last_page' => 1,
                    'current_page' => 1,
                    'per_page' => $perPage
                ],
                'summary' => [
                    'total_revenue' => 0,
                    'formatted_total_revenue' => 'Rp 0',
                    'total_success_transactions' => 0,
                ]
            ], 500);
        }
    }

    public function monthlyRevenue(Request $request)
    {
        try {
            // SET TIMEZONE UNTUK QUERY
            $year = $request->input('year', date('Y'));
            
            $monthlyRevenue = Transaction::selectRaw('
                    MONTH(CONVERT_TZ(paid_at, "+00:00", "+07:00")) as month,
                    YEAR(CONVERT_TZ(paid_at, "+00:00", "+07:00")) as year,
                    SUM(amount) as total_amount,
                    COUNT(*) as transaction_count
                ')
                ->where('status', 'success')
                ->whereRaw('YEAR(CONVERT_TZ(paid_at, "+00:00", "+07:00")) = ?', [$year])
                ->whereNotNull('paid_at')
                ->groupBy('year', 'month')
                ->orderBy('month')
                ->get();

            $formattedData = [];
            $monthNames = [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ];

            foreach ($monthlyRevenue as $data) {
                $formattedData[] = [
                    'month' => $data->month,
                    'month_name' => $monthNames[$data->month],
                    'year' => $data->year,
                    'total_amount' => (float) $data->total_amount,
                    'formatted_amount' => 'Rp ' . number_format($data->total_amount, 0, ',', '.'),
                    'transaction_count' => $data->transaction_count,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $formattedData,
                'total_year' => $monthlyRevenue->sum('total_amount'),
                'formatted_total_year' => 'Rp ' . number_format($monthlyRevenue->sum('total_amount'), 0, ',', '.')
            ]);

        } catch (\Exception $e) {
            Log::error('Monthly revenue error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading monthly revenue: ' . $e->getMessage()
            ], 500);
        }
    }

    public function revenueSummary(Request $request)
    {
        try {
            $now = Carbon::now('Asia/Jakarta');
            $currentMonth = $now->month;
            $currentYear = $now->year;
            $previousMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
            $previousYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;

            $thisMonthRevenue = Transaction::where('status', 'success')
                ->whereRaw('MONTH(CONVERT_TZ(paid_at, "+00:00", "+07:00")) = ?', [$currentMonth])
                ->whereRaw('YEAR(CONVERT_TZ(paid_at, "+00:00", "+07:00")) = ?', [$currentYear])
                ->sum('amount');

            $lastMonthRevenue = Transaction::where('status', 'success')
                ->whereRaw('MONTH(CONVERT_TZ(paid_at, "+00:00", "+07:00")) = ?', [$previousMonth])
                ->whereRaw('YEAR(CONVERT_TZ(paid_at, "+00:00", "+07:00")) = ?', [$previousYear])
                ->sum('amount');

            $thisYearRevenue = Transaction::where('status', 'success')
                ->whereRaw('YEAR(CONVERT_TZ(paid_at, "+00:00", "+07:00")) = ?', [$currentYear])
                ->sum('amount');

            $percentageChange = 0;
            if ($lastMonthRevenue > 0) {
                $percentageChange = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'this_month' => [
                        'amount' => (float) $thisMonthRevenue,
                        'formatted' => 'Rp ' . number_format($thisMonthRevenue, 0, ',', '.'),
                        'month' => $now->format('F Y')
                    ],
                    'last_month' => [
                        'amount' => (float) $lastMonthRevenue,
                        'formatted' => 'Rp ' . number_format($lastMonthRevenue, 0, ',', '.'),
                        'month' => Carbon::create($previousYear, $previousMonth, 1)->setTimezone('Asia/Jakarta')->format('F Y')
                    ],
                    'this_year' => [
                        'amount' => (float) $thisYearRevenue,
                        'formatted' => 'Rp ' . number_format($thisYearRevenue, 0, ',', '.'),
                        'year' => $currentYear
                    ],
                    'percentage_change' => round($percentageChange, 2),
                    'trend' => $percentageChange > 0 ? 'up' : ($percentageChange < 0 ? 'down' : 'stable')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Revenue summary error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading revenue summary'
            ], 500);
        }
    }

    public function printTransaction($billId)
    {
        try {
            $bill = Bill::with(['renter', 'property', 'transaction'])
                ->findOrFail($billId);
            
            return view('landlord.transactions.print', compact('bill'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Transaction not found');
        }
    }

    private function getStatusBadgeClass($status)
    {
        return match(strtolower($status)) {
            'success' => 'bg-success-100 text-success-600',
            'pending' => 'bg-warning-100 text-warning-600',
            'failed' => 'bg-danger-100 text-danger-600',
            default => 'bg-neutral-100 text-neutral-600'
        };
    }
}