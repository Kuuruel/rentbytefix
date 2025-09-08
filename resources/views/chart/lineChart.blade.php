@extends('layout.layout')
@php
    $title = 'Line Chart';
    $subTitle = 'Components / Line Chart';
    $script = ' <script src="' . asset('assets/js/lineChartPageChart.js') . '"></script>';
@endphp

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card h-full p-0 border-0 overflow-hidden">
            <div
                class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6">
                <h6 class="text-lg font-semibold mb-0">Default Line Chart</h6>
            </div>
            <div class="card-body p-6">
                <div id="defaultLineChart" class="apexcharts-tooltip-style-1"></div>
            </div>
        </div>
        <div class="card h-full p-0 border-0 overflow-hidden">
            <div
                class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6">
                <h6 class="text-lg font-semibold mb-0">Zoomable Chart</h6>
            </div>
            <div class="card-body p-6">
                <div id="zoomAbleLineChart"></div>
            </div>
        </div>
        <div class="card h-full p-0 border-0 overflow-hidden">
            <div
                class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6">
                <h6 class="text-lg font-semibold mb-0">Line Chart with Data Labels</h6>
            </div>
            <div class="card-body p-6">
                <div id="lineDataLabel"></div>
            </div>
        </div>
        <div class="card h-full p-0 border-0 overflow-hidden">
            <div
                class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6">
                <h6 class="text-lg font-semibold mb-0">Line Chart Animation</h6>
            </div>
            <div class="card-body p-6">
                <div id="doubleLineChart"></div>
            </div>
        </div>
        <div class="card h-full p-0 border-0 overflow-hidden">
            <div
                class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6">
                <h6 class="text-lg font-semibold mb-0">Stepline Charts</h6>
            </div>
            <div class="card-body p-6">
                <div id="stepLineChart"></div>
            </div>
        </div>
        <div class="card h-full p-0 border-0 overflow-hidden">
            <div
                class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6">
                <h6 class="text-lg font-semibold mb-0">Gradient Charts</h6>
            </div>
            <div class="card-body p-6">
                <div id="gradientLineChart"></div>
            </div>
        </div>
    </div>
@endsection
<!-- Statistics End -->

{{-- <!-- Users Activity -->
        <div class="col-span-12 lg:col-span-6">
            <div class="card border-0 overflow-hidden">
                <div class="card-header">
                    <h5 class="card-title text-lg mb-0">Landloards Activity</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table striped-table mb-0">
                            <thead>
                                <tr>
                                    <th
                                        class="!bg-neutral-100 dark:!bg-neutral-700 border-b border-neutral-200 dark:border-neutral-600">
                                        Tenants
                                    </th>
                                    <th
                                        class="!bg-neutral-100 dark:!bg-neutral-700 border-b border-neutral-200 dark:border-neutral-600">
                                        Join Date
                                    </th>
                                    <th
                                        class="!bg-neutral-100 dark:!bg-neutral-700 border-b border-neutral-200 dark:border-neutral-600 text-center">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tenants as $tenant)
                                    <tr class="odd:bg-white dark:odd:bg-neutral-600">
                                        <td>
                                            <div class="flex items-center">
                                                <img src="{{ $tenant->avatar }}" alt="{{ $tenant->name }}"
                                                    class="shrink-0 me-3 rounded-lg w-10 h-10 object-cover">
                                                <div class="grow">
                                                    <h6 class="text-base mb-0 font-normal">{{ $tenant->name }}</h6>
                                                    <span
                                                        class="text-sm text-secondary-light font-normal">{{ $tenant->country ?? 'Unknown' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $tenant->created_at->format('d M Y') }}</td>
                                        <td class="text-center">
                                            <span
                                                class="px-8 py-1.5 rounded-full font-medium text-sm
                                        @if ($tenant->status == 'Active') bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400
                                        @else
                                            bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:text-danger-400 @endif">
                                                {{ $tenant->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-secondary-light">
                                            No Landlords found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Custom Pagination -->
                    @if ($tenants->hasPages())
                        <div class="flex items-center justify-between flex-wrap gap-2 mt-6">
                            <span id="paginationInfo">
                                Showing {{ $tenants->firstItem() ?? 0 }} to {{ $tenants->lastItem() ?? 0 }} of
                                {{ $tenants->total() }} entries
                            </span>
                            <ul class="pagination flex flex-wrap items-center gap-2 justify-center">
                                <!-- Previous Button -->
                                <li class="page-item">
                                    @if ($tenants->onFirstPage())
                                        <button disabled
                                            class="page-link bg-neutral-300 dark:bg-neutral-600 text-secondary-light font-semibold rounded-lg border-0 flex items-center justify-center h-8 w-8 text-base opacity-50 cursor-not-allowed">
                                            <iconify-icon icon="ep:d-arrow-left"></iconify-icon>
                                        </button>
                                    @else
                                        <a href="{{ $tenants->previousPageUrl() }}"
                                            class="page-link bg-neutral-300 dark:bg-neutral-600 text-secondary-light font-semibold rounded-lg border-0 flex items-center justify-center h-8 w-8 text-base hover:bg-primary-600 hover:text-white">
                                            <iconify-icon icon="ep:d-arrow-left"></iconify-icon>
                                        </a>
                                    @endif
                                </li>

                                <!-- Page Numbers -->
                                <div class="flex gap-1">
                                    @foreach ($tenants->getUrlRange(1, $tenants->lastPage()) as $page => $url)
                                        @if ($page == $tenants->currentPage())
                                            <li class="page-item">
                                                <span
                                                    class="page-link bg-primary-600 text-white rounded-lg border-0 flex items-center justify-center h-8 w-8 text-base">
                                                    {{ $page }}
                                                </span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a href="{{ $url }}"
                                                    class="page-link bg-neutral-300 dark:bg-neutral-600 text-secondary-light rounded-lg border-0 flex items-center justify-center h-8 w-8 text-base hover:bg-primary-600 hover:text-white">
                                                    {{ $page }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </div>

                                <!-- Next Button -->
                                <li class="page-item">
                                    @if ($tenants->hasMorePages())
                                        <a href="{{ $tenants->nextPageUrl() }}"
                                            class="page-link bg-neutral-300 dark:bg-neutral-600 text-secondary-light font-semibold rounded-lg border-0 flex items-center justify-center h-8 w-8 text-base hover:bg-primary-600 hover:text-white">
                                            <iconify-icon icon="ep:d-arrow-right"></iconify-icon>
                                        </a>
                                    @else
                                        <button disabled
                                            class="page-link bg-neutral-300 dark:bg-neutral-600 text-secondary-light font-semibold rounded-lg border-0 flex items-center justify-center h-8 w-8 text-base opacity-50 cursor-not-allowed">
                                            <iconify-icon icon="ep:d-arrow-right"></iconify-icon>
                                        </button>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    @else
                        <!-- Show info even when no pagination -->
                        <div class="flex items-center justify-between flex-wrap gap-2 mt-6">
                            <span id="paginationInfo">
                                Showing {{ $tenants->count() }} of {{ $tenants->count() }} entries
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div> --}}
