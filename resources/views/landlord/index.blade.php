@extends('layout.layout')

@php
    $title='Dashboard';
    $subTitle = 'Statistic';
    $script= '<script>
        // Pass PHP data to JavaScript
        var monthlyRevenueData = ' . json_encode($monthlyRevenue ?? []) . ';
    </script>
    <script src="' . asset('assets/js/homeOneChartLandlord.js') . '"></script>';
@endphp

@section('content')

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 3xl:grid-cols-5 gap-6">
        <div class="card shadow-none border border-gray-200 dark:border-neutral-600 dark:bg-neutral-700 rounded-lg h-full bg-gradient-to-r from-cyan-600/10 to-bg-white">
            <div class="card-body p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-medium text-neutral-900 dark:text-white mb-1">Renters</p>
                        <h6 class="mb-0 dark:text-white">{{ number_format($statistics['totalRenters'] ?? 0) }}</h6>
                    </div>
                    <div class="w-[50px] h-[50px] bg-cyan-600 rounded-full flex justify-center items-center">
                        <iconify-icon icon="gridicons:multiple-users" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="font-medium text-sm text-neutral-600 dark:text-white mt-3 mb-0 flex items-center gap-2">
                    @if($statistics['newRentersLast30Days'] > 0)
                        <span class="inline-flex items-center gap-1 text-success-600 dark:text-success-400">
                            <iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> 
                            +{{ $statistics['newRentersLast30Days'] }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-neutral-600 dark:text-neutral-400">
                            <iconify-icon icon="bx:minus" class="text-xs"></iconify-icon> 
                            0
                        </span>
                    @endif
                    Last 30 days renters
                </p>
            </div>
        </div>
        
        <div class="card shadow-none border border-gray-200 dark:border-neutral-600 dark:bg-neutral-700 rounded-lg h-full bg-gradient-to-r from-purple-600/10 to-bg-white">
            <div class="card-body p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-medium text-neutral-900 dark:text-white mb-1">Properties</p>
                        <h6 class="mb-0 dark:text-white">{{ number_format($statistics['totalProperties'] ?? 0) }}</h6>
                    </div>
                    <div class="w-[50px] h-[50px] bg-purple-600 rounded-full flex justify-center items-center">
                        <iconify-icon icon="fa-solid:home" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="font-medium text-sm text-neutral-600 dark:text-white mt-3 mb-0 flex items-center gap-2">
                    @if($statistics['newPropertiesLast30Days'] > 0)
                        <span class="inline-flex items-center gap-1 text-success-600 dark:text-success-400">
                            <iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> 
                            +{{ $statistics['newPropertiesLast30Days'] }}
                        </span>
                    @elseif($statistics['newPropertiesLast30Days'] < 0)
                        <span class="inline-flex items-center gap-1 text-danger-600 dark:text-danger-400">
                            <iconify-icon icon="bxs:down-arrow" class="text-xs"></iconify-icon> 
                            {{ $statistics['newPropertiesLast30Days'] }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-neutral-600 dark:text-neutral-400">
                            <iconify-icon icon="bx:minus" class="text-xs"></iconify-icon> 
                            0
                        </span>
                    @endif
                    Last 30 days properties
                </p>
            </div>
        </div>
        
        <div class="card shadow-none border border-gray-200 dark:border-neutral-600 dark:bg-neutral-700 rounded-lg h-full bg-gradient-to-r from-success-600/10 to-bg-white">
            <div class="card-body p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-medium text-neutral-900 dark:text-white mb-1">Total Income</p>
                        <h6 class="mb-0 dark:text-white">Rp {{ number_format($statistics['totalIncome'] ?? 0, 0, ',', '.') }}</h6>
                    </div>
                    <div class="w-[50px] h-[50px] bg-success-600 rounded-full flex justify-center items-center">
                        <iconify-icon icon="solar:wallet-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="font-medium text-sm text-neutral-600 dark:text-white mt-3 mb-0 flex items-center gap-2">
                    @if($statistics['incomeLast30Days'] > 0)
                        <span class="inline-flex items-center gap-1 text-success-600 dark:text-success-400">
                            <iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> 
                            +Rp {{ number_format($statistics['incomeLast30Days'], 0, ',', '.') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-neutral-600 dark:text-neutral-400">
                            <iconify-icon icon="bx:minus" class="text-xs"></iconify-icon> 
                            Rp 0
                        </span>
                    @endif
                    Last 30 days income
                </p>
            </div>
        </div>
        
        <div class="card shadow-none border border-gray-200 dark:border-neutral-600 dark:bg-neutral-700 rounded-lg h-full bg-gradient-to-r from-red-600/10 to-bg-white">
            <div class="card-body p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-medium text-neutral-900 dark:text-white mb-1">Pending Bills</p>
                        <h6 class="mb-0 dark:text-white">Rp {{ number_format($statistics['totalPending'] ?? 0, 0, ',', '.') }}</h6>
                    </div>
                    <div class="w-[50px] h-[50px] bg-red-600 rounded-full flex justify-center items-center">
                        <iconify-icon icon="fa6-solid:file-invoice-dollar" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="font-medium text-sm text-neutral-600 dark:text-white mt-3 mb-0 flex items-center gap-2">
                    @if($statistics['pendingLast30Days'] > 0)
                        <span class="inline-flex items-center gap-1 text-warning-600 dark:text-warning-400">
                            <iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon> 
                            +Rp {{ number_format($statistics['pendingLast30Days'], 0, ',', '.') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-neutral-600 dark:text-neutral-400">
                            <iconify-icon icon="bx:minus" class="text-xs"></iconify-icon> 
                            Rp 0
                        </span>
                    @endif
                    Last 30 days pending
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 mt-6">
        <div class="xl:col-span-12">
            <div class="card h-full rounded-lg border-0">
                <div class="card-body">
                    <div class="flex flex-wrap items-center justify-between">
                        <h6 class="text-lg mb-0">Revenue Statistic</h6>
                        <select class="form-select bg-white dark:bg-neutral-700 form-select-sm w-auto">
                            <option>Monthly</option>
                            <option>Yearly</option>
                            <option>Weekly</option>
                            <option>Today</option>
                        </select>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <h6 class="mb-0">Rp {{ number_format($statistics['totalIncome'] ?? 0, 0, ',', '.') }}</h6>
                        @if($statistics['incomeLast30Days'] > 0)
                            <span class="text-sm font-semibold rounded-full bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 border border-success-200 dark:border-success-600/50 px-2 py-1.5 line-height-1 flex items-center gap-1">
                                {{ round(($statistics['incomeLast30Days'] / max($statistics['totalIncome'], 1)) * 100, 1) }}% 
                                <iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon>
                            </span>
                        @endif
                        <span class="text-xs font-medium">Total Revenue</span>
                    </div>
                    <div id="revenueChart" class="pt-[28px] apexcharts-tooltip-style-1"></div>
                </div>
            </div>
        </div>
        
    </div>

@endsection