@extends('layout.layout')
@php
    $title = 'View Statistics';
    $subTitle = 'View Statistics';
    $script = '<script src="' . asset('assets/js/lineChartPageChart.js') . '"></script>
    <script>
        // ======================== Upload Image Start =====================
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $("#imagePreview").css("background-image", "url(" + e.target.result + ")");
                    $("#imagePreview").hide();
                    $("#imagePreview").fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#imageUpload").change(function() {
            readURL(this);
        });
        // ======================== Upload Image End =====================

        // ================== Password Show Hide Js Start ==========
        function initializePasswordToggle(toggleSelector) {
            $(toggleSelector).on("click", function() {
                $(this).toggleClass("ri-eye-off-line");
                var input = $($(this).attr("data-toggle"));
                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });
        }
        // Call the function
        initializePasswordToggle(".toggle-password");
        // ========================= Password Show Hide Js End ===========================

        // ================================ Semi Circle Gauge (Daily Conversion) chart Start ================================ 
        var options = {
            series: [75],
            chart: {
                height: 165,
                width: 120,
                type: "radialBar",
                sparkline: {
                    enabled: false
                },
                toolbar: {
                    show: false
                },
                padding: {
                    left: -32,
                    right: -32,
                    top: -32,
                    bottom: -32
                },
                margin: {
                    left: -32,
                    right: -32,
                    top: -32,
                    bottom: -32
                }
            },
            plotOptions: {
                radialBar: {
                    offsetY: -24,
                    offsetX: -14,
                    startAngle: -90,
                    endAngle: 90,
                    track: {
                        background: "#E3E6E9",
                        dropShadow: {
                            enabled: false,
                            top: 2,
                            left: 0,
                            color: "#999",
                            opacity: 1,
                            blur: 2
                        }
                    },
                    dataLabels: {
                        show: false,
                        name: {
                            show: false
                        },
                        value: {
                            offsetY: -2,
                            fontSize: "22px"
                        }
                    }
                }
            },
            fill: {
                type: "gradient",
                colors: ["#9DBAFF"],
                gradient: {
                    shade: "dark",
                    type: "horizontal",
                    shadeIntensity: 0.5,
                    gradientToColors: ["#487FFF"],
                    inverseColors: true,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 100]
                }
            },
            stroke: {
                lineCap: "round",
            },
            labels: ["Percent"],
        };

        var chart1 = new ApexCharts(document.querySelector("#semiCircleGauge"), options);
        chart1.render();
        // ================================ Semi Circle Gauge (Daily Conversion) chart End ================================ 

        // ================================ Area chart Start ================================ 
        function createChart(chartId, chartColor) {
            let currentYear = new Date().getFullYear();

            var options = {
                series: [{
                    name: "series1",
                    data: [0, 10, 8, 25, 15, 26, 13, 35, 15, 39, 16, 46, 42],
                }, ],
                chart: {
                    type: "area",
                    width: 164,
                    height: 72,
                    sparkline: {
                        enabled: true
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
                    curve: "smooth",
                    width: 2,
                    colors: [chartColor],
                    lineCap: "round"
                },
                grid: {
                    show: true,
                    borderColor: "transparent",
                    strokeDashArray: 0,
                    position: "back",
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
                    type: "gradient",
                    colors: [chartColor],
                    gradient: {
                        shade: "light",
                        type: "vertical",
                        shadeIntensity: 0.5,
                        gradientToColors: [chartColor + "00"],
                        inverseColors: false,
                        opacityFrom: .8,
                        opacityTo: 0.3,
                        stops: [0, 100],
                    },
                },
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
                    categories: ["Jan " + currentYear, "Feb " + currentYear, "Mar " + currentYear, "Apr " + currentYear,
                        "May " + currentYear, "Jun " + currentYear, "Jul " + currentYear, "Aug " + currentYear,
                        "Sep " + currentYear, "Oct " + currentYear, "Nov " + currentYear, "Dec " + currentYear
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
                        format: "dd/MM/yy HH:mm"
                    },
                },
            };

            var chart = new ApexCharts(document.querySelector("#" + chartId), options);
            chart.render();
        }

        createChart("areaChart", "#FF9F29");
        // ================================ Area chart End ================================ 

        // ================================ Bar chart (Today Income) Start ================================ 
        var barOptions = {
            series: [{
                name: "Sales",
                data: [{
                    x: "Mon",
                    y: 20,
                }, {
                    x: "Tue",
                    y: 40,
                }, {
                    x: "Wed",
                    y: 20,
                }, {
                    x: "Thur",
                    y: 30,
                }, {
                    x: "Fri",
                    y: 40,
                }, {
                    x: "Sat",
                    y: 35,
                }]
            }],
            chart: {
                type: "bar",
                width: 164,
                height: 80,
                sparkline: {
                    enabled: true
                },
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    horizontal: false,
                    columnWidth: 14,
                }
            },
            dataLabels: {
                enabled: false
            },
            states: {
                hover: {
                    filter: {
                        type: "none"
                    }
                }
            },
            fill: {
                type: "gradient",
                colors: ["#E3E6E9"],
                gradient: {
                    shade: "light",
                    type: "vertical",
                    shadeIntensity: 0.5,
                    gradientToColors: ["#E3E6E9"],
                    inverseColors: false,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 100],
                },
            },
            grid: {
                show: false,
                borderColor: "#D1D5DB",
                strokeDashArray: 1,
                position: "back",
            },
            xaxis: {
                labels: {
                    show: false
                },
                type: "category",
                categories: ["Mon", "Tue", "Wed", "Thur", "Fri", "Sat"]
            },
            yaxis: {
                labels: {
                    show: false,
                    formatter: function(value) {
                        return (value / 1000).toFixed(0) + "k";
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return value / 1000 + "k";
                    }
                }
            }
        };

        var chart3 = new ApexCharts(document.querySelector("#dailyIconBarChart"), barOptions);
        chart3.render();
        // ================================ Bar chart (Today Income) End ================================ 
    </script>';
@endphp

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="col-span-12 lg:col-span-4">
            <div
                class="user-grid-card relative border border-neutral-200 dark:border-neutral-600 rounded-2xl overflow-hidden bg-white dark:bg-neutral-700 h-full">
                <img src="{{ asset('assets/images/user-grid/user-grid-bg1.png') }}" alt=""
                    class="w-full object-fit-cover hidden">
                <div class="pb-6 ms-6 mb-6 me-6 -mt-[100px]">
                    <!-- Manage Button - positioned at top left -->
                    <div class="flex justify-end mb-4" style="padding-top: 120px">

                    </div>

                    <div class="text-center border-b border-neutral-200 dark:border-neutral-600">
                        <img src="{{ asset('assets/images/user-grid/user-grid-img14.png') }}" alt=""
                            class="border br-white border-width-2-px w-200-px h-[200px] rounded-full object-fit-cover mx-auto">
                        <div class="flex items-center justify-center gap-2">
                            <h6 class="mb-0 mt-4">{{ $tenant->name }}</h6>
                            <button type="button" title="Edit Tenant"
                                class="w-8 mt-4 h-8 bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 rounded-full inline-flex items-center justify-center hover:bg-success-200 dark:hover:bg-success-600/40 transition-all duration-200"
                                onclick="window.location.href='{{ route('super-admin.index2') }}?edit={{ $tenant->id }}'">
                                <iconify-icon icon="lucide:edit" class="text-sm"></iconify-icon>
                            </button>
                        </div>


                        <!-- Email jadi block -->
                        <div class="text-secondary-light mb-2">{{ $tenant->email }}</div>

                        <div class="text-center w-full mb-6">
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
                    </div>
                    <div class="mt-6 mb-6">
                        <h6 class="text-lg mb-4">Personal Info</h6>
                        <ul>
                            <li class="flex items-center gap-1 mb-3">

                                <span class="w-[30%] text-base font-semibold text-neutral-600 dark:text-neutral-200">
                                    Name
                                </span>
                                <span class="w-[70%] text-secondary-light font-medium">:
                                    {{ $tenant->name }}
                                </span>
                            </li>

                            {{-- <li class="flex items-center gap-1 mb-3">
                                <span class="w-[30%] text-base font-semibold text-neutral-600 dark:text-neutral-200">
                                    Email
                                </span>
                                <span class="w-[70%] text-secondary-light font-medium">:
                                    {{ $tenant->email }}
                                </span>
                            </li> --}}

                            <li class="flex items-center gap-1 mb-3">
                                <span class="w-[30%] text-base font-semibold text-neutral-600 dark:text-neutral-200">
                                    Country
                                </span>
                                <span class="w-[70%] text-secondary-light font-medium">:
                                    {{ $tenant->country ?? '-' }}
                                </span>
                            </li>

                            <li class="flex items-center gap-1 mb-3">
                                <span class="w-[30%] text-base font-semibold text-neutral-600 dark:text-neutral-200">
                                    Join
                                </span>
                                <span class="w-[70%] text-secondary-light font-medium">:
                                    {{ $tenant->created_at->format('d M Y') }}
                                </span>
                            </li>

                        </ul>
                    </div>
                    {{-- <div class="mt-8">
                        <h6 class="text-lg mb-4">Billing Summary</h6>
                        <ul>
                            <li class="flex items-center gap-1 mb-3">

                                <span class="w-[30%] text-base font-semibold text-neutral-600 dark:text-neutral-200">
                                    Pending 
                                </span>
                                <span class="w-[70%] text-secondary-light font-medium">:
                                    {{ $tenant->name }}
                                </span>
                            </li>

                            <li class="flex items-center gap-1 mb-3">
                                <span class="w-[30%] text-base font-semibold text-neutral-600 dark:text-neutral-200">
                                    Overdue 
                                </span>
                                <span class="w-[70%] text-secondary-light font-medium">:
                                    {{ $tenant->email }}
                                </span>
                            </li>

                            <li class="flex items-center gap-1 mb-3">
                                <span class="w-[30%] text-base font-semibold text-neutral-600 dark:text-neutral-200">
                                    Paid 
                                </span>
                                <span class="w-[70%] text-secondary-light font-medium">:
                                    {{ $tenant->country ?? '-' }}
                                </span>
                            </li>

                            

                        </ul>
                    </div> --}}

                </div>
            </div>
        </div>
        <div class="col-span-12 lg:col-span-8">
            <div class="card h-full border-0">
                <div class="card-body p-6">
                    <div class="col-span-12 mb-6">
                        <h6 class="mb-4">Tenant Statistics</h6>
                        <div class="gap-6 grid grid-cols-1 sm:grid-cols-12">
                            <!-- Dashboard Widget Start -->
                            <div class="col-span-12 sm:col-span-6 lg:col-span-4 ">
                                <div
                                    class="card px-4 py-5 shadow-2 rounded-lg border-gray-200 dark:border-neutral-600 h-full bg-gradient-to-t from-success-600/10 to-bg-white">
                                    <div class="card-body p-0">
                                        <div class="flex flex-wrap items-center justify-between gap-1">
                                            <div class="flex items-center flex-wrap gap-4">
                                                <span
                                                    class="mb-0 w-[44px] h-[44px] bg-success-600 shrink-0 text-white flex justify-center items-center rounded-full h6">
                                                    <iconify-icon icon="solar:wallet-bold" class="icon"></iconify-icon>
                                                </span>
                                                <div class="flex-grow-1 ">
                                                    <h6 class="font-semibold mb-0">24,000</h6>
                                                    <span class="font-medium text-secondary-light text-base">Transactions
                                                    </span>
                                                    <p class="text-sm mb-0 flex items-center flex-wrap gap-3 mt-3">
                                                        <span
                                                            class="bg-success-focus px-1.5 py-0.5 rounded-sm font-medium text-success-600 dark:text-success-600 text-sm flex items-center gap-2 shadow-sm">
                                                            +12%
                                                            <i class="ri-arrow-up-line"></i>
                                                        </span> vs last month
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Dashboard Widget End -->
                            <!-- Dashboard Widget Start -->
                            <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                                <div
                                    class="card px-4 py-5 shadow-2 rounded-lg border-gray-200 dark:border-neutral-600 h-full bg-gradient-to-t from-warning-600/10 to-bg-white">
                                    <div class="card-body p-0">
                                        <div class="flex flex-wrap items-center justify-between gap-1">
                                            <div class="flex items-center flex-wrap gap-4">
                                                <span
                                                    class="mb-0 w-[44px] h-[44px] bg-warning-600 text-white shrink-0 flex justify-center items-center rounded-full h6">
                                                    <iconify-icon icon="iconamoon:discount-fill"
                                                        class="icon"></iconify-icon>
                                                </span>
                                                <div class="flex-grow-1">
                                                    <h6 class="font-semibold mb-0">82,000</h6>
                                                    <span class="font-medium text-secondary-light text-base">Total
                                                        Sales</span>
                                                    <p class="text-sm mb-0 flex items-center flex-wrap gap-3 mt-3">
                                                        <span
                                                            class="bg-danger-focus px-1.5 py-0.5 rounded-sm font-medium text-danger-600 dark:text-danger-600 text-sm flex items-center gap-2 shadow-sm">
                                                            +18%
                                                            <i class="ri-arrow-down-line"></i>
                                                        </span> vs last month
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Dashboard Widget End -->
                            <!-- Dashboard Widget Start -->
                            <div class="col-span-12 sm:col-span-6 lg:col-span-4">
                                <div
                                    class="card px-4 py-5 shadow-2 rounded-lg border-gray-200 dark:border-neutral-600 h-full bg-gradient-to-t from-purple-600/10 to-bg-white">
                                    <div class="card-body p-0">
                                        <div class="flex flex-wrap items-center justify-between gap-1">
                                            <div class="flex items-center flex-wrap gap-4">
                                                <span
                                                    class="mb-0 w-[44px] h-[44px] bg-purple-600 text-white shrink-0 flex justify-center items-center rounded-full h6">
                                                    <iconify-icon icon="fa6-solid:file-invoice-dollar"
                                                        class="icon"></iconify-icon>
                                                </span>
                                                <div class="flex-grow-1">
                                                    <h6 class="font-semibold mb-0">Rp 500,000</h6>
                                                    <span class="font-medium text-secondary-light text-base">Average per
                                                        Transaction</span>
                                                    <p class="text-sm mb-0 flex items-center flex-wrap gap-3 mt-3">
                                                        <span
                                                            class="bg-success-focus px-1.5 py-0.5 rounded-sm font-medium text-success-600 dark:text-success-600 text-sm flex items-center gap-2 shadow-sm">
                                                            +168.001%
                                                            <i class="ri-arrow-up-line"></i>
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Dashboard Widget End -->
                        </div>
                    </div>
                    {{-- Line Chart --}}
                    <div class="card h-full p-0 border-0 overflow-hidden">
                        <div
                            class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6">
                            <h6 class="text-lg font-semibold mb-0">Sales Overview</h6>
                            {{-- <div class="flex flex-wrap items-center gap-2 mt-2">
                                <h6 class="mb-0">Rp 15,000,000</h6>
                                <span
                                    class="text-sm font-semibold rounded-full bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 border border-success-200 dark:border-success-600/50 px-2 py-1.5 line-height-1 flex items-center gap-1">
                                    10% <iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon>
                                </span>
                                <span class="text-xs font-medium">vs previous month</span>
                            </div> --}}
                            <div class="mt-4 mb-6 flex flex-wrap">
                                <div class="me-10">
                                    <span class="text-secondary-light text-sm mb-1">Income</span>
                                    <div class="">
                                        <h6 class="font-semibold inline-block mb-0">$26,201</h6>
                                        <span class="!text-success-600 font-bold inline-flex items-center gap-1">10%
                                            <iconify-icon icon="iconamoon:arrow-up-2-fill" class="icon"></iconify-icon>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-secondary-light text-sm mb-1">Expenses</span>
                                    <div class="">
                                        <h6 class="font-semibold inline-block mb-0">$18,120</h6>
                                        <span class="!text-danger-600 font-bold inline-flex items-center gap-1">10%
                                            <iconify-icon icon="iconamoon:arrow-down-2-fill"
                                                class="icon"></iconify-icon> </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-6">
                                <div id="defaultLineChart" class="apexcharts-tooltip-style-1"></div>
                            </div>
                        </div>


                    </div>
                    {{-- end Line Card --}}
                </div>
            </div>
        </div>
    </div>
@endsection
