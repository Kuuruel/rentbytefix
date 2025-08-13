@extends('layout.layout')

@php
    $title = 'Dashboard';
    $subTitle = 'Admin';
    $script = '<script src="' . asset('assets/js/homeOneChart.js') . '"></script>' . '<script src="' . asset('assets/js/lineChartPageChart.js') . '"></script>';
@endphp


@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 2xl:grid-cols-4 3xl:grid-cols-5 gap-6">

        {{-- CARD-TOTAL-TENANTS --}}
        <div
            class="card shadow-none border border-gray-200 dark:border-neutral-600 dark:bg-neutral-700 rounded-lg h-full bg-gradient-to-r from-cyan-600/10 to-bg-white">
            <div class="card-body p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-medium text-neutral-900 dark:text-white mb-1">Total Tenants</p>
                        <h6 class="mb-0 dark:text-white">40</h6>
                    </div>
                    <div class="w-[50px] h-[50px] bg-cyan-600 rounded-full flex justify-center items-center">
                        <iconify-icon icon="gridicons:multiple-users" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="font-medium text-sm text-neutral-600 dark:text-white mt-3 mb-0 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-success-600 dark:text-success-400"><iconify-icon
                            icon="bxs:up-arrow" class="text-xs"></iconify-icon> +10</span>
                    Last 30 days users
                </p>
            </div>
        </div>
        <div
            class="card shadow-none border border-gray-200 dark:border-neutral-600 dark:bg-neutral-700 rounded-lg h-full bg-gradient-to-r from-purple-600/10 to-bg-white">
            <div class="card-body p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-medium text-neutral-900 dark:text-white mb-1">Total Properties</p>
                        <h6 class="mb-0 dark:text-white">400</h6>
                    </div>
                    <div class="w-[50px] h-[50px] bg-purple-600 rounded-full flex justify-center items-center">
                        <iconify-icon icon="fa-solid:award" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="font-medium text-sm text-neutral-600 dark:text-white mt-3 mb-0 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-danger-600 dark:text-danger-400"><iconify-icon
                            icon="bxs:down-arrow" class="text-xs"></iconify-icon> -80</span>
                    Last 30 days
                </p>
            </div>
        </div><!-- card end -->
        <div
            class="card shadow-none border border-gray-200 dark:border-neutral-600 dark:bg-neutral-700 rounded-lg h-full bg-gradient-to-r from-blue-600/10 to-bg-white">
            <div class="card-body p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-medium text-neutral-900 dark:text-white mb-1">Active Tenants</p>
                        <h6 class="mb-0 dark:text-white">95</h6>
                    </div>
                    <div class="w-[50px] h-[50px] bg-blue-600 rounded-full flex justify-center items-center">
                        <iconify-icon icon="fluent:people-20-filled" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="font-medium text-sm text-neutral-600 dark:text-white mt-3 mb-0 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-success-600 dark:text-success-400"><iconify-icon
                            icon="bxs:up-arrow" class="text-xs"></iconify-icon> +20</span>
                    Last 30
                </p>
            </div>
        </div><!-- card end -->
        <div
            class="card shadow-none border border-gray-200 dark:border-neutral-600 dark:bg-neutral-700 rounded-lg h-full bg-gradient-to-r from-success-600/10 to-bg-white">
            <div class="card-body p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="font-medium text-neutral-900 dark:text-white mb-1">Monthly Bilings</p>
                        <h6 class="mb-0 dark:text-white">$42,000</h6>
                    </div>
                    <div class="w-[50px] h-[50px] bg-success-600 rounded-full flex justify-center items-center">
                        <iconify-icon icon="solar:wallet-bold" class="text-white text-2xl mb-0"></iconify-icon>
                    </div>
                </div>
                <p class="font-medium text-sm text-neutral-600 dark:text-white mt-3 mb-0 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-success-600 dark:text-success-400"><iconify-icon
                            icon="bxs:up-arrow" class="text-xs"></iconify-icon> +$20</span>
                    30 days income
                </p>
            </div>
        </div><!-- card end -->

    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mt-6">

        <div class="xl:col-span-6 2xl:col-span-3">
            <div class="card h-full p-0 border-0 overflow-hidden">
                <div
                    class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6">
                    <h6 class="text-lg font-semibold mb-0">Billings vs. Payment</h6>
                </div>
                <div class="card-body p-6">
                    <div id="doubleLineChart"></div>
                </div>
            </div>
        </div>
        <div class="xl:col-span-6 2xl:col-span-3">
            <div class="card h-full rounded-lg border-0 overflow-hidden">
                <div class="card-body p-6">
                    <div class="flex items-center flex-wrap gap-2 justify-between">
                        <h6 class="mb-2 font-bold text-lg">Owner Distribution</h6>
                        <div class="">
                            <select
                                class="form-select form-select-sm w-auto bg-white dark:bg-neutral-700 border text-secondary-light">
                                <option>Today</option>
                                <option>Weekly</option>
                                <option>Monthly</option>
                                <option>Yearly</option>
                            </select>
                        </div>
                    </div>


                    <div id="userOverviewDonutChart" class="apexcharts-tooltip-z-none"></div>

                    <ul class="flex flex-wrap items-center justify-between mt-4 gap-3">
                        <li class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-sm bg-primary-600"></span>
                            <span class="text-secondary-light text-sm font-normal">
                                New:
                                <span class="text-neutral-600 dark:text-neutral-200 font-semibold">400</span>
                            </span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-sm bg-warning-600"></span>
                            <span class="text-secondary-light text-sm font-normal">
                                Subscribed:
                                <span class="text-neutral-600 dark:text-neutral-200 font-semibold">300</span>
                            </span>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
        
        <div class="xl:col-span-6 2xl:col-span-3">
            <div class="card h-full border-0">
                <div class="card-body">
                    <div class="flex items-center flex-wrap gap-2 justify-between">
                        <h6 class="font-bold text-lg mb-0">Top Performer</h6>
                        <a href="javascript:void(0)"
                            class="text-primary-600 dark:text-primary-600 hover-text-primary flex items-center gap-1">
                            View All
                            <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                        </a>
                    </div>

                    <div class="mt-8">

                        <div class="flex items-center justify-between gap-2 mb-6">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('assets/images/users/user1.png') }}" alt=""
                                    class="w-10 h-10 rounded-full shrink-0 overflow-hidden">
                                <div class="grow">
                                    <h6 class="text-base mb-0 font-medium">Dianne Russell</h6>
                                    <span class="text-sm text-secondary-light font-medium">Agent ID: 36254</span>
                                </div>
                            </div>
                            <span class="text-neutral-600 dark:text-neutral-200 text-base font-medium">$20</span>
                        </div>

                        <div class="flex items-center justify-between gap-2 mb-6">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('assets/images/users/user2.png') }}" alt=""
                                    class="w-10 h-10 rounded-full shrink-0 overflow-hidden">
                                <div class="grow">
                                    <h6 class="text-base mb-0 font-medium">Wade Warren</h6>
                                    <span class="text-sm text-secondary-light font-medium">Agent ID: 36254</span>
                                </div>
                            </div>
                            <span class="text-neutral-600 dark:text-neutral-200 text-base font-medium">$20</span>
                        </div>

                        <div class="flex items-center justify-between gap-2 mb-6">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('assets/images/users/user3.png') }}" alt=""
                                    class="w-10 h-10 rounded-full shrink-0 overflow-hidden">
                                <div class="grow">
                                    <h6 class="text-base mb-0 font-medium">Albert Flores</h6>
                                    <span class="text-sm text-secondary-light font-medium">Agent ID: 36254</span>
                                </div>
                            </div>
                            <span class="text-neutral-600 dark:text-neutral-200 text-base font-medium">$30</span>
                        </div>

                        <div class="flex items-center justify-between gap-2 mb-6">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('assets/images/users/user4.png') }}" alt=""
                                    class="w-10 h-10 rounded-full shrink-0 overflow-hidden">
                                <div class="grow">
                                    <h6 class="text-base mb-0 font-medium">Bessie Cooper</h6>
                                    <span class="text-sm text-secondary-light font-medium">Agent ID: 36254</span>
                                </div>
                            </div>
                            <span class="text-neutral-600 dark:text-neutral-200 text-base font-medium">$40</span>
                        </div>

                        <div class="flex items-center justify-between gap-2 mb-6">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('assets/images/users/user5.png') }}" alt=""
                                    class="w-10 h-10 rounded-full shrink-0 overflow-hidden">
                                <div class="grow">
                                    <h6 class="text-base mb-0 font-medium">Arlene McCoy</h6>
                                    <span class="text-sm text-secondary-light font-medium">Agent ID: 36254</span>
                                </div>
                            </div>
                            <span class="text-neutral-600 dark:text-neutral-200 text-base font-medium">$10</span>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('assets/images/users/user1.png') }}" alt=""
                                    class="w-10 h-10 rounded-full shrink-0 overflow-hidden">
                                <div class="grow">
                                    <h6 class="text-base mb-0 font-medium">Arlene McCoy</h6>
                                    <span class="text-sm text-secondary-light font-medium">Agent ID: 36254</span>
                                </div>
                            </div>
                            <span class="text-neutral-600 dark:text-neutral-200 text-base font-medium">$10</span>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    
        <div class="xl:col-span-6 2xl:col-span-6">
            <div class="col-span-12 lg:col-span-6">
                <div class="card border-0 overflow-hidden">
                    <div class="card-header">
                        <h5 class="card-title text-lg mb-0">Striped Rows</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table striped-table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col"
                                            class="!bg-white dark:!bg-neutral-700 border-b border-neutral-200 dark:border-neutral-600">
                                            Items</th>
                                        <th scope="col"
                                            class="!bg-white dark:!bg-neutral-700 border-b border-neutral-200 dark:border-neutral-600">
                                            Price</th>
                                        <th scope="col"
                                            class="!bg-white dark:!bg-neutral-700 border-b border-neutral-200 dark:border-neutral-600">
                                            Discount </th>
                                        <th scope="col"
                                            class="!bg-white dark:!bg-neutral-700 border-b border-neutral-200 dark:border-neutral-600">
                                            Sold</th>
                                        <th scope="col"
                                            class="!bg-white dark:!bg-neutral-700 border-b border-neutral-200 dark:border-neutral-600 text-center">
                                            Total Orders</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="odd:bg-neutral-100 dark:odd:bg-neutral-600">
                                        <td>
                                            <div class="flex items-center">
                                                <img src="{{ asset('assets/images/product/product-img1.png') }}"
                                                    alt="" class="shrink-0 me-3 rounded-lg me-3">
                                                <div class="grow">
                                                    <h6 class="text-base mb-0 font-normal">Blue t-shirt</h6>
                                                    <span class="text-sm text-secondary-light font-normal">Fashion</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$400.00</td>
                                        <td>15%</td>
                                        <td>300</td>
                                        <td class="text-center">
                                            <span
                                                class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-8 py-1.5 rounded-full font-medium text-sm">70</span>
                                        </td>
                                    </tr>
                                    <tr class="odd:bg-neutral-100 dark:odd:bg-neutral-600">
                                        <td>
                                            <div class="flex items-center">
                                                <img src="{{ asset('assets/images/product/product-img2.png') }}"
                                                    alt="" class="shrink-0 me-3 rounded-lg me-3">
                                                <div class="grow">
                                                    <h6 class="text-base mb-0 font-normal">Nike Air Shoe</h6>
                                                    <span class="text-sm text-secondary-light font-normal">Fashion</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$150.00</td>
                                        <td>N/A</td>
                                        <td>200</td>
                                        <td class="text-center">
                                            <span
                                                class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-8 py-1.5 rounded-full font-medium text-sm">70</span>
                                        </td>
                                    </tr>
                                    <tr class="odd:bg-neutral-100 dark:odd:bg-neutral-600">
                                        <td>
                                            <div class="flex items-center">
                                                <img src="{{ asset('assets/images/product/product-img3.png') }}"
                                                    alt="" class="shrink-0 me-3 rounded-lg me-3">
                                                <div class="grow">
                                                    <h6 class="text-base mb-0 font-normal">Woman Dresses</h6>
                                                    <span class="text-sm text-secondary-light font-normal">Fashion</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$300.00</td>
                                        <td>$50.00</td>
                                        <td>1400</td>
                                        <td class="text-center">
                                            <span
                                                class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-8 py-1.5 rounded-full font-medium text-sm">70</span>
                                        </td>
                                    </tr>
                                    <tr class="odd:bg-neutral-100 dark:odd:bg-neutral-600">
                                        <td>
                                            <div class="flex items-center">
                                                <img src="{{ asset('assets/images/product/product-img4.png') }}"
                                                    alt="" class="shrink-0 me-3 rounded-lg me-3">
                                                <div class="grow">
                                                    <h6 class="text-base mb-0 font-normal">Smart Watch</h6>
                                                    <span class="text-sm text-secondary-light font-normal">Fashion</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$400.00</td>
                                        <td>$50.00</td>
                                        <td>700</td>
                                        <td class="text-center">
                                            <span
                                                class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-8 py-1.5 rounded-full font-medium text-sm">70</span>
                                        </td>
                                    </tr>
                                    <tr class="odd:bg-neutral-100 dark:odd:bg-neutral-600">
                                        <td>
                                            <div class="flex items-center">
                                                <img src="{{ asset('assets/images/product/product-img5.png') }}"
                                                    alt="" class="shrink-0 me-3 rounded-lg me-3">
                                                <div class="grow">
                                                    <h6 class="text-base mb-0 font-normal">Hoodie Rose</h6>
                                                    <span class="text-sm text-secondary-light font-normal">Fashion</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>$300.00</td>
                                        <td>25%</td>
                                        <td>400</td>
                                        <td class="text-center">
                                            <span
                                                class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-8 py-1.5 rounded-full font-medium text-sm">70</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!-- card end -->
            </div>
        </div>
    </div>
    </div>
@endsection