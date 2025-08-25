@extends('layout.layout')
@php
    $title = 'Statistic Users';
    $subTitle = 'Statistic Users';
    $script = ' <script src="' . asset('assets/js/homeFourChart.js') . '"></script>';
    $script = '<script src="' . asset('assets/js/widgets.js') . '"></script>';
@endphp

@section('content')
    <div class="card h-full p-0 rounded-xl border-0 overflow-hidden">
        <div
            class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6 flex items-center flex-wrap gap-3 justify-between">
            <div class="flex items-center flex-wrap gap-3">
                <span class="text-base font-medium text-secondary-light mb-0">Show</span>
                <form method="GET" action="{{ request()->url() }}" id="perPageForm" class="inline">
                    <input type="hidden" name="search" value="{{ $search ?? '' }}">
                    <select name="per_page" onchange="document.getElementById('perPageForm').submit()"
                        class="form-select form-select-sm w-auto dark:bg-neutral-600 dark:text-white border-neutral-200 dark:border-neutral-500 rounded-lg">
                        <option value="5" {{ ($perPage ?? 10) == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ ($perPage ?? 10) == 15 ? 'selected' : '' }}>15</option>
                        <option value="20" {{ ($perPage ?? 10) == 20 ? 'selected' : '' }}>20</option>
                        <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </form>

                <form class="navbar-search" method="GET" action="{{ request()->url() }}">
                    <input type="hidden" name="per_page" value="{{ $perPage ?? 10 }}">
                    <input id="searchInput" type="text" class="bg-white dark:bg-neutral-700 h-10 w-auto" name="search"
                        placeholder="Search tenants..." value="{{ $search ?? '' }}">
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
        </div>

        <div class="card-body p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 3xl:grid-cols-4 gap-6" id="tenantGrid">
                @if (isset($tenants) && count($tenants) > 0)
                    @foreach ($tenants as $tenant)
                        <div class="user-grid-card">
                            <div
                                class="relative border border-neutral-200 dark:border-neutral-600 rounded-2xl overflow-hidden">
                                {{-- Tenant info --}}
                                <div class="pe-6 pb-4 ps-6 text-center mt--50 pt-5">
                                    <img src="{{ asset('assets/images/user-grid/user-grid-img1.png') }}" alt=""
                                        class="border br-white border-width-8-px w-[120px] h-[120px] ms-auto me-auto -mt-[0px] rounded-full object-fit-cover">

                                    <h6 class="text-lg mb-1 mt-2">{{ $tenant->name }}</h6>
                                    <div class="text-center w-full mb-4">
                                        @if (strtolower($tenant->status) == 'active')
                                            <span
                                                class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-8 py-1.5 rounded-full font-medium text-sm">
                                                Active
                                            </span>
                                        @else
                                            <span
                                                class="bg-danger-100 text-danger-600 dark:bg-danger-600/25 dark:text-danger-400 px-4 py-1 rounded-full font-medium text-sm">
                                                Inactive
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Chart Cards --}}
                                    @if (strtolower($tenant->status) == 'active')
                                        {{-- Active User Charts - Semua Hijau --}}
                                        <div
                                            class="center-border relative shadow-2 rounded-lg border-gray-200 dark:border-neutral-600 h-full bg-gradient-to-l from-success-600/10 to-bg-white p-3 flex items-center gap-4">
                                            <div class="text-center w-1/2">
                                                <h6 class="text-bold mb-0">{{ rand(60, 95) }}%</h6>
                                                <p class="text-sm mb-0"><span
                                                        class="bg-success-100 dark:bg-success-600/25 px-1 py-px rounded font-medium text-success-600 dark:text-success-400 text-sm">+{{ rand(10, 30) }}</span>
                                                    this week</p>
                                            </div>
                                            <div id="active-user-chart-{{ $tenant->id }}"
                                                class="remove-tooltip-title rounded-tooltip-value w-1/2">
                                            </div>
                                        </div>
                                    @else
                                        {{-- Inactive User Charts - Semua Merah --}}
                                        <div
                                            class="center-border relative shadow-2 rounded-lg border-gray-200 dark:border-neutral-600 h-full bg-gradient-to-l from-red-600/10 to-bg-white p-3 flex items-center gap-4">
                                            <div class="text-center w-1/2">
                                                <h6 class="text-bold mb-0">{{ rand(20, 45) }}%</h6>
                                                <p class="text-sm mb-0"><span
                                                        class="bg-danger-100 dark:bg-danger-600/25 px-1 py-px rounded font-medium text-danger-600 dark:text-danger-400 text-sm">-{{ rand(5, 15) }}</span>
                                                    this week</p>
                                            </div>
                                            <div id="inactive-user-chart-{{ $tenant->id }}"
                                                class="remove-tooltip-title rounded-tooltip-value w-1/2">
                                            </div>
                                        </div>
                                    @endif

                                    <a href="{{ route('super-admin.index8', $tenant->id) }}"
                                        class="bg-primary-50 hover:bg-primary-600 dark:hover:bg-primary-600 hover:text-white dark:hover:text-white dark:bg-primary-600/25 text-primary-600 dark:text-primary-400 bg-hover-primary-600 hover-text-white p-2.5 text-sm btn-sm px-3 py-3 rounded-lg flex items-center justify-center mt-4 font-medium gap-2 w-full">
                                        View Statistic
                                        <iconify-icon icon="solar:alt-arrow-right-linear"
                                            class="icon text-xl line-height-1"></iconify-icon>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-8">
                        @if (isset($search) && !empty($search))
                            <div class="flex flex-col items-center justify-center">
                                <p class="text-gray-500 dark:text-gray-400 text-lg mb-2">No tenants found</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mb-4">No results for
                                    "{{ $search }}"</p>
                                <a href="{{ request()->url() }}?per_page={{ $perPage ?? 10 }}"
                                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                                    Clear Search
                                </a>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center">
                                <p class="text-gray-500 dark:text-gray-400">Belum ada Landlord yang terdaftar</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Custom Pagination -->
            <div class="flex items-center justify-between flex-wrap gap-2 mt-6">
                <span id="paginationInfo">
                    @if ($tenants->total() > 0)
                        Showing {{ $tenants->firstItem() }} to {{ $tenants->lastItem() }} of {{ $tenants->total() }}
                        @if (isset($search) && !empty($search))
                            results for "{{ $search }}"
                        @else
                            entries
                        @endif
                    @else
                        Showing 0 entries
                    @endif
                </span>

                @if ($tenants->hasPages())
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
                @endif
            </div>
        </div>
    </div>

    {{-- SCRIPT UNTUK CHARTS ONLY (Search removed because it's handled server-side now) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Chart script loaded');

            // Initialize charts for each tenant
            initializeCharts();

            // ================================== CHART FUNCTIONS START =================================
            function initializeCharts() {
                @if (isset($tenants) && count($tenants) > 0)
                    @foreach ($tenants as $tenant)
                        @if (strtolower($tenant->status) == 'active')
                            // Active tenant charts - SEMUA HIJAU
                            createWidgetChart('active-user-chart-{{ $tenant->id }}', '#45b369');
                        @else
                            // Inactive tenant charts - SEMUA MERAH
                            createWidgetChart('inactive-user-chart-{{ $tenant->id }}', '#dc2626');
                        @endif
                    @endforeach
                @endif
            }

            function createWidgetChart(chartId, chartColor) {
                let currentYear = new Date().getFullYear();

                var options = {
                    series: [{
                        name: 'series1',
                        data: [35, 45, 38, 41, 36, 43, 37, 55, 40],
                    }],
                    chart: {
                        type: 'area',
                        width: 100,
                        height: 42,
                        sparkline: {
                            enabled: true // Remove whitespace
                        },
                        toolbar: {
                            show: false
                        },
                        padding: {
                            left: 0,
                            right: 0,
                            top: 0,
                            bottom: 0
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2,
                        colors: [chartColor],
                        lineCap: 'round'
                    },
                    grid: {
                        show: true,
                        borderColor: 'transparent',
                        strokeDashArray: 0,
                        position: 'back',
                        xaxis: {
                            lines: {
                                show: false
                            }
                        },
                        yaxis: {
                            lines: {
                                show: false
                            }
                        },
                        row: {
                            colors: undefined,
                            opacity: 0.5
                        },
                        column: {
                            colors: undefined,
                            opacity: 0.5
                        },
                        padding: {
                            top: -3,
                            right: 0,
                            bottom: 0,
                            left: 0
                        },
                    },
                    fill: {
                        type: 'gradient',
                        colors: [chartColor], // Set the starting color (top color) here
                        gradient: {
                            shade: 'light', // Gradient shading type
                            type: 'vertical', // Gradient direction (vertical)
                            shadeIntensity: 0.5, // Intensity of the gradient shading
                            gradientToColors: [`${chartColor}00`], // Bottom gradient color (with transparency)
                            inverseColors: false, // Do not invert colors
                            opacityFrom: .75, // Starting opacity
                            opacityTo: 0.3, // Ending opacity
                            stops: [0, 100],
                        },
                    },
                    // Customize the circle marker color on hover
                    markers: {
                        colors: [chartColor],
                        strokeWidth: 2,
                        size: 0,
                        hover: {
                            size: 8
                        }
                    },
                    xaxis: {
                        labels: {
                            show: false
                        },
                        categories: [`Jan ${currentYear}`, `Feb ${currentYear}`, `Mar ${currentYear}`,
                            `Apr ${currentYear}`, `May ${currentYear}`, `Jun ${currentYear}`,
                            `Jul ${currentYear}`, `Aug ${currentYear}`, `Sep ${currentYear}`,
                            `Oct ${currentYear}`, `Nov ${currentYear}`, `Dec ${currentYear}`
                        ],
                        tooltip: {
                            enabled: false,
                        },
                    },
                    yaxis: {
                        labels: {
                            show: false
                        }
                    },
                    tooltip: {
                        x: {
                            format: 'dd/MM/yy HH:mm'
                        },
                    },
                };

                const chartElement = document.querySelector(`#${chartId}`);
                if (chartElement) {
                    var chart = new ApexCharts(chartElement, options);
                    chart.render();
                    console.log(`Chart created for ${chartId} with color ${chartColor}`);
                }
            }
            // ================================== CHART FUNCTIONS END =================================

            console.log('Charts initialized successfully');
        });

        // Search form auto-submit with delay
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.form.submit();
                }, 500); // Submit after 500ms of no typing
            });
        }
    </script>

@endsection
