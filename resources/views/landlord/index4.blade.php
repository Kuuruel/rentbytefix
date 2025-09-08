@extends('layout.layout')
@php
    $title='Transaction History';
    $subTitle = 'Transaction History';
    $script = ' <script src="' . asset('assets/js/transactionhistory.js') . '"></script>';
@endphp

@section('content')

<div class="grid grid-cols-12">
    <div class="col-span-12">
        <div class="card h-full p-0 rounded-xl border-0 overflow-hidden">
            <div class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6 flex items-center flex-wrap gap-3 justify-between">
                <div class="flex items-center flex-wrap gap-3">
                    <span class="text-base font-medium text-secondary-light mb-0">Show</span>
                    <select id="perPageSelect" class="form-select form-select-sm w-auto dark:bg-neutral-600 dark:text-white border-neutral-200 dark:border-neutral-500 rounded-lg">
                        <option value="5" selected>5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                    </select>

                    <select id="monthFilter" class="form-select form-select-sm w-auto dark:bg-neutral-600 dark:text-white border-neutral-200 dark:border-neutral-500 rounded-lg">
                        <option value="">All Months</option>
                        @php
                            $months = [
                                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                            ];
                            $currentMonth = date('n');
                        @endphp
                        @foreach($months as $value => $label)
                            <option value="{{ $value }}" {{ $value == $currentMonth ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    <select id="yearFilter" class="form-select form-select-sm w-auto dark:bg-neutral-600 dark:text-white border-neutral-200 dark:border-neutral-500 rounded-lg">
                        @php
                            $currentYear = date('Y');
                            $startYear = $currentYear - 3;
                        @endphp
                        @for($year = $currentYear; $year >= $startYear; $year--)
                            <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                    
                    <form class="navbar-search" onsubmit="return false;">
                        <input id="searchInput" type="text" class="bg-white dark:bg-neutral-700 h-10 w-auto" name="search" placeholder="Search transactions...">
                        <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                    </form>
                    
                    <select id="statusFilter" class="form-select form-select-sm w-auto dark:bg-neutral-600 dark:text-white border-neutral-200 dark:border-neutral-500 rounded-lg">
                        <option value="">Status</option>
                        <option value="pending">Pending</option>
                        <option value="success">Success</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>

                <div class="flex items-center gap-4">
                    <div id="revenueSummary" class="hidden bg-blue-50 dark:bg-blue-900/20 px-4 py-2 rounded-lg border border-blue-200 dark:border-blue-800">
                        <span class="text-sm text-blue-700 dark:text-blue-300">
                            Total Revenue: <span id="totalRevenue" class="font-semibold">Rp 0</span>
                        </span>
                        <span class="text-sm text-blue-600 dark:text-blue-400 ml-2">
                            (<span id="totalTransactions">0</span> transactions)
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-6">
                <div id="loadingSpinner" class="hidden text-center py-4">
                    <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-indigo-500">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading...
                    </div>
                </div>

                <div class="table-responsive scroll-sm overflow-x-auto">
                    <table class="table bordered-table sm-table mb-0 w-full">
                        <thead class="bg-neutral-50 dark:bg-neutral-800">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left w-24"  style="padding-right:0 !important">
                                   
                                </th>
                                <th scope="col" class="px-4 py-3 text-left w-32"  style="padding-left:0 !important">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Invoice ID</span>
                                </th>
                                <th scope="col" class="px-4 py-3 text-left w-32">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Property ID</span>
                                </th>
                                <th scope="col" class="px-4 py-3 text-left min-w-[180px]">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Property Name</span>
                                </th>
                                <th scope="col" class="px-4 py-3 text-left min-w-[160px]">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Receipt Name</span>
                                </th>
                                <th scope="col" class="px-4 py-3 text-left min-w-[140px]">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Created At</span>
                                </th>
                                <th scope="col" class="px-4 py-3 text-left min-w-[140px]">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Paid At</span>
                                </th>
                                <th scope="col" class="px-4 py-3 text-center w-28">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Amount</span>
                                </th>
                                <th scope="col" class="px-4 py-3 text-center w-32">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Status</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tableBody" class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between flex-wrap gap-2 mt-6">
                    <span id="paginationInfo" class="text-sm text-neutral-600 dark:text-neutral-400">Showing 0 to 0 of 0 entries</span>
                    <ul id="pageNumbers" class="pagination flex flex-wrap items-center gap-2 justify-center">
                        <div id="pageNumbers" class="flex gap-1"></div>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="deleteBackdrop" class="fixed inset-0 z-40 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="bg-white dark:bg-neutral-700 rounded-xl w-96 max-w-[90vw] mx-4 shadow-lg z-10 overflow-hidden border border-neutral-200 dark:border-neutral-600">
        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-600">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Confirm Delete</h3>
        </div>

        <div class="px-6 py-6">
            <p class="text-sm text-neutral-600 dark:text-neutral-300">Are you sure you want to delete <span id="deleteName" class="font-semibold text-neutral-900 dark:text-white"></span>? This action cannot be undone.</p>

            <div class="flex justify-end gap-3 mt-6">
                <button id="deleteCancel" class="px-4 py-2 rounded bg-neutral-200 dark:bg-neutral-600 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-300 dark:hover:bg-neutral-500">Cancel</button>
                <button id="deleteConfirm" class="px-4 py-2 rounded bg-danger-600 text-white text-sm hover:bg-danger-700">
                    <span class="delete-text">Delete</span>
                    <span class="delete-loading hidden">Deleting...</span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection