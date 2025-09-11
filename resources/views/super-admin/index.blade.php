@extends('layout.layout')

@php
   $title = 'Dashboard';
    $subTitle = 'Admin';
    $script = '<script src="' . asset('assets/js/homeOneChart.js') . '"></script>' . '<script src="' . asset('assets/js/lineChartPageChart.js') . '"></script>';
    $script .= '<script src="' . asset('assets/js/widgets.js') . '"></script>';

@endphp

@section('content')

    <div class="grid grid-cols-12 gap-6">

        <div class="col-span-12 sm:col-span-6 lg:col-span-3">
            <div
                class="card shadow-none border border-gray-200 dark:border-neutral-600 dark:bg-neutral-700 rounded-lg h-full bg-gradient-to-r from-cyan-600/10 to-bg-white">
                <div class="card-body p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-neutral-900 dark:text-white mb-1">Total Users</p>
                            <h6 class="mb-0 dark:text-white mt-2">{{ $totalTenants }}</h6>
                        </div>
                        
                        <div class="w-[50px] h-[50px] bg-cyan-600 rounded-full flex justify-center items-center">
                            <iconify-icon icon="gridicons:multiple-users" class="text-white text-2xl"></iconify-icon>
                        </div>
                    </div>
                     <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-neutral-100 text-neutral-500 dark:bg-success-900 dark:text-success-600">
                                   All registered accounts
                                </span> 
                    <p class="font-medium text-sm text-neutral-600 dark:text-white mt-3 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 text-success-600 dark:text-success-400">
                            <iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> +{{ $newTenantsToday }}
                        </span>
                        Added today
                    </p>

                </div>
            </div>
        </div>



        <div class="col-span-12 sm:col-span-6 lg:col-span-3">
            <div
                class="card shadow-none border border-gray-200 dark:border-neutral-600 dark:bg-neutral-700 rounded-lg h-full bg-gradient-to-r from-blue-600/10 to-bg-white w-full">
                <div class="card-body p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-neutral-900 dark:text-white mb-1">Active Users</p>
                            <h6 class="mb-0 dark:text-white mt-2">{{ $activeTenants }}</h6>
                        </div>
                        <div class="w-[50px] h-[50px] bg-blue-600 rounded-full flex justify-center items-center">
                            <iconify-icon icon="fluent:people-20-filled" class="text-white text-2xl"></iconify-icon>
                        </div>
                    </div>
  <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-neutral-100 text-neutral-600 dark:bg-success-900 dark:text-success-600">
                                 Currently logged-in or active status
                                </span> 
                    <p class="font-medium text-sm text-neutral-600 dark:text-white mt-3 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 text-danger-600 dark:text-danger-400">
                            <iconify-icon icon="bxs:down-arrow" class="text-xs"></iconify-icon> -{{ $inactiveTenants }}
                        </span>
                        Inactive Users
                    </p>
                </div>
            </div>
        </div>

        <!-- Card Monthly Billings -->
        <div class="col-span-12 sm:col-span-6 lg:col-span-3">
            <div
                class="card shadow-none border border-gray-200 dark:border-neutral-600 dark:bg-neutral-700 rounded-lg h-full bg-gradient-to-r from-purple-600/10 to-bg-white w-full">
                <div class="card-body p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-neutral-900 dark:text-white mb-1">Monthly Billings</p>
                            <h6 class="mb-0 dark:text-white mt-2">Rp{{ number_format($monthlyBillings ?? 0) }}</h6>
                        </div>
                        <div class="w-[50px] h-[50px] bg-purple-600 rounded-full flex justify-center items-center ">
                        
                            <iconify-icon icon="fa-solid:award" class="text-white text-2xl"></iconify-icon>
                        </div>
                    </div>
                          <div class="mt-2">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-success-100 text-success-600 dark:bg-success-900 dark:text-success-600">
                                    Sum of all tenant billings
                                </span>
                            </div>
                    <p class="font-medium text-sm text-neutral-600 dark:text-white mt-3 flex items-center gap-2">
                        @if (($billsDecrease ?? 0) < 0)
                            <span class="inline-flex items-center gap-1 text-danger-600 dark:text-danger-400">
                                <iconify-icon icon="bxs:down-arrow" class="text-xs"></iconify-icon>
                                {{ $billsDecrease ?? 0 }}
                            </span>
                        @elseif(($billsDecrease ?? 0) > 0)
                            <span class="inline-flex items-center gap-1 text-success-600 dark:text-success-400">
                                <iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon>
                                +Rp{{ number_format(abs($billsDecrease ?? 0)) }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-info-600 dark:text-info-400">
                                <iconify-icon icon="bxs:minus" class="text-xs"></iconify-icon> {{ $billsDecrease ?? 0 }}
                            </span>
                        @endif
                        Last 30 days
                    </p>
                </div>
            </div>
        </div>

        <div class="col-span-12 sm:col-span-6 lg:col-span-3">
            <div
                class="card shadow-none border border-gray-200 dark:border-neutral-600 dark:bg-neutral-700 rounded-lg h-full bg-gradient-to-r from-success-600/10 to-bg-white w-full">
                <div class="card-body p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-neutral-900 dark:text-white mb-1">Platform Revenue</p>
                            <h6 class="mb-0 dark:text-white mt-2">Rp {{ number_format($platformRevenue ?? 0) }}</h6>

                            <div class="mt-2">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-success-100 text-success-600 dark:bg-success-900 dark:text-success-600">
                                    5% + Rp {{ number_format(2500) }} per transaction
                                </span> 
                            </div>
                        </div>
                        <div class="w-[50px] h-[50px] bg-success-600 rounded-full flex justify-center items-center ">
                            <iconify-icon icon="solar:wallet-bold" class="text-white text-2xl"></iconify-icon>
                        </div>
                    </div>

                    <p class="font-medium text-sm text-neutral-600 dark:text-white mt-3 flex items-center gap-2">
                        @if (($revenueIncrease ?? 0) >= 0)
                            <span class="inline-flex items-center gap-1 text-success-600 dark:text-success-400">
                                <iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon>
                                +Rp {{ number_format(abs($revenueIncrease ?? 0)) }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-danger-600 dark:text-danger-400">
                                <iconify-icon icon="bxs:down-arrow" class="text-xs"></iconify-icon>
                                -Rp {{ number_format(abs($revenueIncrease ?? 0)) }}
                            </span>
                        @endif
                        30 days income
                    </p>

                    @if (isset($revenueBreakdown))
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-neutral-600">
                            <details class="group">
                                <summary
                                    class="cursor-pointer text-xs text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-300">
                                    <span class="inline-flex items-center gap-1">
                                        <iconify-icon icon="solar:info-circle-linear" class="text-sm"></iconify-icon>
                                        Revenue Details
                                        <iconify-icon icon="solar:alt-arrow-down-linear"
                                            class="text-xs group-open:rotate-180 transition-transform"></iconify-icon>
                                    </span>
                                </summary>
                                <div class="mt-2 text-xs text-neutral-600 dark:text-neutral-400 space-y-1">
                                    <div class="flex justify-between">
                                        <span>Total Transactions:</span>
                                        <span
                                            class="font-medium">{{ $revenueBreakdown['successful_transactions_count'] ?? 0 }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Transaction Value:</span>
                                        <span class="font-medium">Rp
                                            {{ number_format($revenueBreakdown['total_transaction_value_30_days'] ?? 0) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Revenue Rate:</span>
                                        <span
                                            class="font-medium">{{ number_format($revenueBreakdown['revenue_percentage_of_total'] ?? 0, 1) }}%</span>
                                    </div>
                                </div>
                            </details>
                        </div>
                    @endif

           
                    @if (($platformRevenue ?? 0) == 0)
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-neutral-600">
                            <div class="flex items-center gap-2 text-warning-600 dark:text-warning-400 text-xs">
                                <iconify-icon icon="solar:danger-triangle-linear" class="text-sm"></iconify-icon>
                                <span>No revenue in last 30 days</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-8">
            <div class="card h-full p-0 border-0 overflow-hidden">
                <div
                    class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6">
                    <h6 class="text-lg font-semibold mb-0">Growth Comparison</h6>
                </div>
                <div class="card-body p-6">
                    <div id="lineDataLabel"></div>
                </div>
            </div>
        </div>


        <script id="chart-data" type="application/json">
@if(isset($chartData))
{!! json_encode($chartData) !!}
@else
{
    "months": ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    "billing": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    "payment": [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
}
@endif
</script>

        <script>
            // =========================== Line Chart With Data labels Start ===============================
            document.addEventListener('DOMContentLoaded', function() {
                // Ambil data dari controller
                const chartData = JSON.parse(document.getElementById('chart-data').textContent);

                var options = {
                    series: [{
                        name: "Total Billing",
                        data: chartData.billing
                    }],
                    chart: {
                        height: 264,
                        type: 'line',
                        colors: '#000',
                        zoom: {
                            enabled: false
                        },
                        toolbar: {
                            show: false
                        },
                    },
                    colors: ['#487FFF'], // Set the color of the series
                    dataLabels: {
                        enabled: true
                    },
                    stroke: {
                        curve: 'straight',
                        width: 4,
                        color: "#000"
                    },
                    markers: {
                        size: 0,
                        strokeWidth: 3,
                        hover: {
                            size: 8
                        }
                    },
                    grid: {
                        show: true,
                        borderColor: '#D1D5DB',
                        strokeDashArray: 3,
                        row: {
                            colors: ['#f3f3f3', 'transparent'],
                            opacity: 0,
                        },
                    },
                    // Customize the circle marker color on hover
                    markers: {
                        colors: '#487FFF',
                        strokeWidth: 3,
                        size: 0,
                        hover: {
                            size: 10
                        }
                    },
                    xaxis: {
                        categories: chartData.months,
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(value) {
                                if (value >= 1000000000) {
                                    return "Rp " + (value / 1000000000).toFixed(1) + "M";
                                } else if (value >= 1000000) {
                                    return "Rp " + (value / 1000000).toFixed(1) + "Jt";
                                } else if (value >= 1000) {
                                    return "Rp " + (value / 1000).toFixed(0) + "k";
                                } else {
                                    return "Rp " + value;
                                }
                            },
                            style: {
                                fontSize: "14px"
                            }
                        },
                    },
                };

                var chart = new ApexCharts(document.querySelector("#lineDataLabel"), options);
                chart.render();
            });
            // =========================== Line Chart With Data labels End ===============================
        </script>


        <!-- Owner Distribution - Fully Responsive -->
        <div class="col-span-12 lg:col-span-4">
            <div class="card h-full rounded-xl border-0 overflow-hidden shadow-sm">
                <div class="card-body p-4 sm:p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4">
                        <h6 class="font-bold text-base sm:text-lg text-neutral-900 dark:text-white">Owner Distribution</h6>
                    </div>

                    <!-- Chart Container -->
                    <div class="flex justify-center mb-6">
                        <div id="userOverviewDonutChart" class="apexcharts-tooltip-z-none"
                            style="min-height: 200px; height: clamp(200px, 30vw, 280px); width: 280px; margin: 0 auto ;">
                        </div>
                    </div>

                    <!-- Legend Section -->
                    <div class="legend-wrapper">
                        @if (isset($ownerDistribution) && $ownerDistribution->count() > 0)
                            <div
                                class="legend-container grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-1 {{ $ownerDistribution->count() > 8 ? 'max-h-40 overflow-y-auto pr-2' : '' }}">
                                @foreach ($ownerDistribution as $index => $country)
                                    <div class="flex items-center gap-2 min-w-0 py-1 px-2">
                                        <span
                                            class="w-3 h-3 rounded-sm flex-shrink-0 border border-gray-200 dark:border-gray-600"
                                            id="legend-color-{{ $index }}">
                                        </span>
                                        <span
                                            class="text-xs sm:text-sm text-neutral-600 dark:text-neutral-300 truncate min-w-0">
                                            <span class="truncate">{{ $country->country }}</span>
                                            <span
                                                class="text-neutral-400 dark:text-neutral-500 ml-1">({{ number_format($country->count) }})</span>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex items-center justify-center w-full py-8">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto text-neutral-300 dark:text-neutral-600 mb-2"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                        </path>
                                    </svg>
                                    <span class="text-neutral-400 dark:text-neutral-500 text-sm">No data available</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <style>
            /* Chart Container */
            .chart-container {
                width: 100% !important;
                max-width: 280px !important;
                height: 250px !important;
                margin: 0 auto !important;
            }

            /* Legend Grid */
            .legend-grid {
                display: grid !important;
                gap: 4px !important;
                grid-template-columns: repeat(2, 1fr) !important;
            }

            /* Responsive Legend */
            @media (min-width: 640px) {
                .legend-grid {
                    grid-template-columns: repeat(2, 1fr) !important;
                }
            }

            @media (min-width: 1024px) {
                .legend-grid {
                    grid-template-columns: repeat(1, 1fr) !important;
                }
            }

            @media (min-width: 1280px) {
                .legend-grid {
                    grid-template-columns: repeat(2, 1fr) !important;
                }
            }

            /* Legend Item */
            .legend-item {
                display: flex !important;
                align-items: center !important;
                gap: 8px !important;
                min-width: 0 !important;
                padding: 4px 8px !important;
            }

            /* Legend Color */
            .legend-color {
                width: 12px !important;
                height: 12px !important;
                border-radius: 2px !important;
                flex-shrink: 0 !important;
                border: 1px solid #e5e7eb !important;
            }

            /* Legend Text */
            .legend-text {
                font-size: 12px !important;
                color: #6b7280 !important;
                truncate: true !important;
                min-width: 0 !important;
            }

            @media (min-width: 640px) {
                .legend-text {
                    font-size: 14px !important;
                }
            }

            /* Country Name */
            .country-name {
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }

            /* Country Count */
            .country-count {
                color: #9ca3af !important;
                margin-left: 4px !important;
            }

            /* Scrollable Legend */
            .legend-scrollable {
                max-height: 160px !important;
                overflow-y: auto !important;
                padding-right: 8px !important;
            }

            /* Custom Scrollbar */
            .legend-scrollable::-webkit-scrollbar {
                width: 4px !important;
            }

            .legend-scrollable::-webkit-scrollbar-track {
                background: transparent !important;
            }

            .legend-scrollable::-webkit-scrollbar-thumb {
                background: rgba(156, 163, 175, 0.3) !important;
                border-radius: 2px !important;
            }

            .legend-scrollable::-webkit-scrollbar-thumb:hover {
                background: rgba(156, 163, 175, 0.5) !important;
            }

            /* Dark Mode */
            .dark .legend-color {
                border-color: #6b7280 !important;
            }

            .dark .legend-text {
                color: #d1d5db !important;
            }

            .dark .country-count {
                color: #6b7280 !important;
            }

            /* Mobile Specific */
            @media (max-width: 639px) {
                .chart-container {
                    max-width: 280px !important;
                    height: 280px !important;
                }

                .legend-grid {
                    gap: 2px !important;
                }

                .legend-item {
                    padding: 3px 6px !important;
                }
            }
        </style>
        <!-- JavaScript for Enhanced Responsiveness -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-adjust chart height based on container
                function adjustChartHeight() {
                    const chartContainer = document.getElementById('userOverviewDonutChart');
                    const cardBody = chartContainer?.closest('.card-body');

                    if (chartContainer && cardBody) {
                        const containerWidth = cardBody.offsetWidth;
                        const optimalHeight = Math.min(Math.max(containerWidth * 0.6, 200), 300);
                        chartContainer.style.height = `${optimalHeight}px`;
                    }
                }

                // Handle window resize
                let resizeTimer;
                window.addEventListener('resize', function() {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(adjustChartHeight, 250);
                });

                // Initial adjustment
                adjustChartHeight();

                // Auto-scroll legend if too many items
                const legendContainer = document.querySelector('.legend-container');
                if (legendContainer && legendContainer.children.length > 8) {
                    legendContainer.classList.add('legend-scrollable');
                }

                // Handle legend item clicks (if chart interaction needed)
                document.querySelectorAll('.legend-container > div').forEach((item, index) => {
                    item.addEventListener('click', function() {
                        // Toggle chart series visibility
                        if (typeof ApexCharts !== 'undefined' && window.userOverviewChart) {
                            window.userOverviewChart.toggleSeries(`series-${index}`);
                        }
                    });
                });

                // Intersection Observer for lazy loading optimization
                if ('IntersectionObserver' in window) {
                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                entry.target.classList.remove('loading');
                            }
                        });
                    });

                    const chartElement = document.getElementById('userOverviewDonutChart');
                    if (chartElement) {
                        observer.observe(chartElement);
                    }
                }
            });
        </script>


    
        {{-- 🔥 FIXED: Average Transaction per Tenant Card --}}
        <div class="col-span-12 sm:col-span-6 lg:col-span-3">
            <div
                class="card shadow-none border border-gray-200 dark:border-neutral-600 dark:bg-neutral-700 rounded-lg h-full bg-gradient-to-r from-primary-600/10 to-bg-white w-full">
                <div class="card-body p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-neutral-900 dark:text-white mb-1">Average Transaction</p>

                            {{-- 🎯 TAMPILKAN NILAI YANG BENAR --}}
                            <h6 class="mb-0 dark:text-white mt-2">
                                @if (($averageTransactionPerTenant ?? 0) > 0)
                                    Rp {{ number_format($averageTransactionPerTenant) }}
                                @else
                                    <span class="text-neutral-400">No Data</span>
                                @endif
                            </h6>

                            <div class="mt-2">
                                @php
                                    $totalTransactions = $successfulTransactions ?? 0;
                                    $contextText =
                                        $totalTransactions > 0
                                            ? "Based on {$totalTransactions} successful transactions"
                                            : 'No successful transactions in last 30 days';
                                @endphp
                                <span class="text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ $contextText }}
                                </span>
                            </div>
                        </div>
                        <div class="w-[50px] h-[50px] bg-primary-600 rounded-full flex justify-center items-center">
                            <iconify-icon icon="solar:calculator-bold" class="text-white text-2xl"></iconify-icon>
                        </div>
                    </div>

                    <p class="font-medium text-sm text-neutral-600 dark:text-white mt-3 flex items-center gap-2">
                        @php
                            // Hitung trend berdasarkan data yang ada
                            $currentValue = $averageTransactionPerTenant ?? 0;
                            $isPositive = $currentValue > 0;
                            $trendClass = $isPositive
                                ? 'text-success-600 dark:text-success-400'
                                : 'text-neutral-500 dark:text-neutral-400';
                            $trendIcon = $isPositive ? 'bxs:up-arrow' : 'solar:minus-linear';
                        @endphp

                        <span class="inline-flex items-center gap-1 {{ $trendClass }}">
                            <iconify-icon icon="{{ $trendIcon }}" class="text-xs"></iconify-icon>
                            @if ($currentValue > 0)
                                Active transactions
                            @else
                                No recent activity
                            @endif
                        </span>
                        last 30 days
                    </p>
{{-- 
                    @if (config('app.debug') && isset($averageTransactionPerTenant))
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-neutral-600">
                            <details class="group">
                                <summary
                                    class="cursor-pointer text-xs text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-300">
                                    <span class="inline-flex items-center gap-1">
                                        <iconify-icon icon="solar:bug-linear" class="text-sm"></iconify-icon>
                                        Debug Info
                                        <iconify-icon icon="solar:alt-arrow-down-linear"
                                            class="text-xs group-open:rotate-180 transition-transform"></iconify-icon>
                                    </span>
                                </summary>
                                <div class="mt-2 text-xs text-neutral-600 dark:text-neutral-400 space-y-1">
                                    <div class="flex justify-between">
                                        <span>Successful Transactions:</span>
                                        <span class="font-medium">{{ $successfulTransactions ?? 0 }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Total Trans. for Success Rate:</span>
                                        <span class="font-medium">{{ $totalTransactionsForSuccessRate ?? 0 }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Raw Average Value:</span>
                                        <span class="font-medium">{{ $averageTransactionPerTenant ?? 0 }}</span>
                                    </div>
                                </div>
                            </details>
                        </div>
                    @endif --}}

                    @if (($averageTransactionPerTenant ?? 0) > 0 && ($averageTransactionPerTenant ?? 0) < 100000)
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-neutral-600">
                            <div class="flex items-center gap-2 text-warning-600 dark:text-warning-400 text-xs">
                                <iconify-icon icon="solar:danger-triangle-linear" class="text-sm"></iconify-icon>
                                <span>Unusually low average - check calculation</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
   
        <div class="col-span-12 sm:col-span-6 lg:col-span-3">
            <div
                class="card shadow-none border border-gray-200 dark:border-neutral-600 dark:bg-neutral-700 rounded-lg h-full bg-gradient-to-r from-warning-600/10 to-bg-white w-full">
                <div class="card-body p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-neutral-900 dark:text-white mb-1">Payment Success Rate</p>

                      
                            <h6
                                class="mb-0 mt-2 {{ ($paymentSuccessRate ?? 0) >= 80 ? 'text-success-600 dark:text-success-400' : (($paymentSuccessRate ?? 0) >= 60 ? 'text-warning-600 dark:text-warning-400' : 'text-danger-600 dark:text-danger-400') }}">
                                {{ number_format($paymentSuccessRate ?? 0, 1) }}%
                            </h6>

                            <div class="mt-2">
                                @php
                                    $successRate = $paymentSuccessRate ?? 0;
                                    $badgeClass =
                                        $successRate >= 80
                                            ? 'bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200'
                                            : ($successRate >= 60
                                                ? 'bg-warning-100 text-warning-600 dark:bg-warning-900 dark:text-warning-600'
                                                : 'bg-danger-100 text-danger-600 dark:bg-danger-900 dark:text-danger-600');
                                    $badgeText =
                                        $successRate >= 80
                                            ? 'Excellent'
                                            : ($successRate >= 60
                                                ? 'Good'
                                                : 'Needs Attention');
                                @endphp
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                                    {{ $badgeText }}
                                </span>
                            </div>
                        </div>
                        <div
                            class="w-[50px] h-[50px] {{ ($paymentSuccessRate ?? 0) >= 80 ? 'bg-success-600' : (($paymentSuccessRate ?? 0) >= 60 ? 'bg-warning-600' : 'bg-danger-600') }} rounded-full flex justify-center items-center">
                            <iconify-icon
                                icon="{{ ($paymentSuccessRate ?? 0) >= 80 ? 'solar:check-circle-bold' : (($paymentSuccessRate ?? 0) >= 60 ? 'solar:danger-triangle-bold' : 'solar:close-circle-bold') }}"
                                class="text-white text-2xl"></iconify-icon>
                        </div>
                    </div>

         
                    <p class="font-medium text-sm text-neutral-600 dark:text-white mt-3">
                        <span class="inline-flex items-center gap-2">
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2 h-2 bg-success-500 rounded-full"></span>
                                {{ $successfulTransactions ?? 0 }} successful
                            </span>
                            <span class="text-neutral-400">/</span>
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2 h-2 bg-neutral-400 rounded-full"></span>
                                {{ $totalTransactionsForSuccessRate ?? 0 }} total
                            </span>
                        </span>
                    </p>

                    <div class="mt-3 text-xs text-neutral-500 dark:text-neutral-400">
                        Last 30 days •
                        @php
                            $failedCount = ($totalTransactionsForSuccessRate ?? 0) - ($successfulTransactions ?? 0);
                        @endphp
                        {{ $failedCount }} failed/pending
                    </div>

            
                    {{-- @if (($paymentSuccessRate ?? 0) < 70)
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-neutral-600">
                            <div class="flex items-start gap-2 text-warning-600 dark:text-warning-400 text-xs">
                                <iconify-icon icon="solar:lightbulb-linear"
                                    class="text-sm mt-0.5 flex-shrink-0"></iconify-icon>
                                <div>
                                    <div class="font-medium mb-1">Improvement Tips:</div>
                                    <ul class="space-y-1 text-neutral-600 dark:text-neutral-400">
                                        <li>• Check for duplicate bills</li>
                                        <li>• Review payment gateway issues</li>
                                        <li>• Send payment reminders</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif --}}

        
                    @if (($paymentSuccessRate ?? 0) >= 80)
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-neutral-600">
                            <div class="flex items-center gap-2 text-success-600 dark:text-success-400 text-xs">
                                <iconify-icon icon="solar:star-bold" class="text-sm"></iconify-icon>
                                <span>Above industry average (80%)</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
 
        <div class="col-span-12 lg:col-span-6">
            <div class="card h-full border-0">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-8">
                        <h6 class="font-bold text-lg">Recent Activities</h6>
                        {{-- <a href="{{ route('super-admin.index2') }}"
                            class="text-primary-600 dark:text-primary-600 flex items-center gap-1">
                            View All
                            <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                        </a> --}}
                    </div>

                    <div class="space-y-4">
                        @forelse($recentActivities as $activity)
                            <div
                                class="flex items-start justify-between gap-3 py-3 border-b border-neutral-200 dark:border-neutral-600 last:border-b-0">
                                <div class="flex items-start gap-3 flex-1">
                                    <!-- Dynamic Icon -->
                                    <div
                                        class="w-10 h-10 rounded-full {{ $activity->bg_color }} flex items-center justify-center shrink-0">
                                        <iconify-icon icon="{{ $activity->icon }}"
                                            class="{{ $activity->icon_color }} text-lg"></iconify-icon>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <!-- Activity Description -->
                                        @if ($activity->type == 'tenant_registered')
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span
                                                    class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Landlord</span>
                                                <span
                                                    class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $activity->tenant_name }}</span>
                                                <span
                                                    class="text-sm text-neutral-600 dark:text-neutral-400">{{ $activity->description }}</span>
                                            </div>
                                            @if ($activity->created_by)
                                                <p class="text-xs text-neutral-500 dark:text-neutral-500 mt-1">
                                                    Created by: {{ $activity->created_by }}
                                                </p>
                                            @endif
                                        @elseif($activity->type == 'payment_completed')
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span
                                                    class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $activity->tenant_name }}</span>
                                                <span
                                                    class="text-sm text-neutral-600 dark:text-neutral-400">{{ $activity->description }}</span>
                                            </div>
                                            <p class="text-xs text-success-600 dark:text-success-400 mt-1 font-medium">
                                                Amount: Rp{{ number_format($activity->amount, 0, ',', '.') }}
                                            </p>
                                        @elseif($activity->type == 'bill_created')
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span
                                                    class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $activity->tenant_name }}</span>
                                                <span
                                                    class="text-sm text-neutral-600 dark:text-neutral-400">{{ $activity->description }}</span>
                                            </div>
                                            <p class="text-xs text-neutral-500 dark:text-neutral-500 mt-1">
                                                Amount: Rp{{ number_format($activity->amount, 0, ',', '.') }} • Due:
                                                {{ $activity->due_date->format('d M Y') }}
                                            </p>
                                        @elseif($activity->type == 'payment_failed')
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span
                                                    class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $activity->tenant_name }}</span>
                                                <span
                                                    class="text-sm text-danger-600 dark:text-danger-400">{{ $activity->description }}</span>
                                            </div>
                                            <p class="text-xs text-danger-600 dark:text-danger-400 mt-1 font-medium">
                                                Amount: Rp{{ number_format($activity->amount, 0, ',', '.') }}
                                            </p>
                                        @else
                                            <!-- Fallback for unknown activity types -->
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span
                                                    class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{{ $activity->tenant_name ?? 'Unknown' }}</span>
                                                <span
                                                    class="text-sm text-neutral-600 dark:text-neutral-400">{{ $activity->description ?? 'activity' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Timestamp -->
                                <div class="text-right shrink-0">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                        @empty
                            <div class="text-center py-8">
                                <iconify-icon icon="solar:inbox-line-duotone"
                                    class="text-4xl text-neutral-400 dark:text-neutral-500 mb-2"></iconify-icon>
                                <p class="text-neutral-500 dark:text-neutral-400 text-sm">No recent activities</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ApexCharts Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ================================ Users Overview Donut chart Start ================================ 
            try {
                console.log('Initializing donut chart...');

                // Get data from PHP
                var ownerData = @json($ownerDistribution ?? []);
                console.log('Owner Data:', ownerData);

                // Check if element exists
                var chartElement = document.querySelector("#userOverviewDonutChart");
                if (!chartElement) {
                    console.error('Chart element not found');
                    return;
                }

                // Check if data exists and is valid
                if (!ownerData || !Array.isArray(ownerData) || ownerData.length === 0) {
                    console.log('No owner distribution data available');
                    chartElement.innerHTML =
                        '<div class="flex items-center justify-center h-64"><div class="text-center text-gray-500">No data available</div></div>';
                    return;
                }

                // Process data
                var series = ownerData.map(function(item) {
                    return parseInt(item.count) || 0;
                });

                var labels = ownerData.map(function(item) {
                    return item.country || 'Unknown';
                });

                console.log('Processed Series:', series);
                console.log('Processed Labels:', labels);

                // Validate processed data
                if (series.length === 0 || labels.length === 0) {
                    console.log('No valid data to display');
                    chartElement.innerHTML =
                        '<div class="flex items-center justify-center h-64"><div class="text-center text-gray-500">No valid data to display</div></div>';
                    return;
                }

                // ✅ UPDATED: Dynamic color generation for unlimited countries
                function generateColors(count) {
                    const baseColors = [
                        // Baris 1
                        '#487FFF', '#FF9F29', '#E4F1FF', '#FFD580', '#28A745',

                        // Baris 2 (variasi tone sedikit lebih tua/soft)
                        '#335FCC', '#CC6F1F', '#CFE4FF', '#FFC966', '#218838',

                        // Baris 3 (variasi lagi biar beda tapi tetap tone sama)
                        '#1E3A8A', '#B45309', '#BFDBFE', '#FACC15', '#15803D'



                    ];

                    const colors = [];
                    for (let i = 0; i < count; i++) {
                        if (i < baseColors.length) {
                            colors.push(baseColors[i]);
                        } else {
                            // Generate HSL colors with good contrast for additional countries
                            const hue = (i * 137.508) % 360; // Golden angle approximation
                            const saturation = 60 + (i % 3) * 15; // Vary saturation: 60%, 75%, 90%
                            const lightness = 45 + (i % 4) * 10; // Vary lightness: 45%, 55%, 65%, 75%
                            colors.push(`hsl(${Math.round(hue)}, ${saturation}%, ${lightness}%)`);
                        }
                    }
                    return colors;
                }

                const dynamicColors = generateColors(ownerData.length);
                console.log(`Generated ${dynamicColors.length} colors for ${ownerData.length} countries`);

                // Chart configuration
                var options = {
                    series: series,
                    colors: dynamicColors,
                    labels: labels,
                    legend: {
                        show: false
                    },
                    chart: {
                        type: 'donut',
                        height: 250,
                        sparkline: {
                            enabled: true
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '60%'
                            }
                        }
                    },
                    stroke: {
                        width: 0,
                    },
                    dataLabels: {
                        enabled: false
                    },
                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function(val, opts) {
                                return val + ' users';
                            }
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200,
                                height: 200
                            }
                        }
                    }]
                };

                // Create and render chart
                console.log('Creating ApexChart with options:', options);
                var chart = new ApexCharts(chartElement, options);

                chart.render().then(function() {
                    console.log('Chart rendered successfully');

                    // ✅ UPDATED: Update legend colors for all countries
                    dynamicColors.forEach(function(color, index) {
                        const legendElement = document.getElementById('legend-color-' + index);
                        if (legendElement) {
                            legendElement.style.backgroundColor = color;
                            console.log(`Set legend color ${index}: ${color}`);
                        }
                    });
                }).catch(function(error) {
                    console.error('Error rendering chart:', error);
                });

            } catch (error) {
                console.error('Donut chart error:', error);
                var chartElement = document.querySelector("#userOverviewDonutChart");
                if (chartElement) {
                    chartElement.innerHTML =
                        '<div class="flex items-center justify-center h-64"><div class="text-center text-red-500">Error loading chart</div></div>';
                }
            }
            // ================================ Users Overview Donut chart End ================================
        });
    </script>

@endsection
