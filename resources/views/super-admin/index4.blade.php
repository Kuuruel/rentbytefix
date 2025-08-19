@extends('layout.layout')
@php
    $title = 'Statistic Users';
    $subTitle = 'Statistic Users';
    $script = ' <script src="' . asset('assets/js/homeFourChart.js') . '"></script>';
    
@endphp

@section('content')
    <!-- Crypto Home Widgets Start -->
    {{-- <div class="grid grid-cols-12">
        <div class="col-span-12">
            <div class="card h-full p-0 rounded-xl border-0 overflow-hidden">
                <div
                    class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6 flex items-center flex-wrap gap-3 justify-between">
                    <div class="flex items-center flex-wrap gap-3">


                        <form class="navbar-search" onsubmit="return false;">
                            <input id="searchInput" type="text" class="bg-white dark:bg-neutral-700 h-10 w-auto"
                                name="search" placeholder="Search users...">
                            <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div> --}}


    <div class="card h-full p-0 rounded-xl border-0 overflow-hidden">
        <div
            class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6 flex items-center flex-wrap gap-3 justify-between">
            <div class="flex items-center flex-wrap gap-3">
                <span class="text-base font-medium text-secondary-light mb-0">Show</span>
                <select
                    class="form-select form-select-sm w-auto dark:bg-neutral-600 dark:text-white border-neutral-200 dark:border-neutral-500 rounded-lg">
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                    <option>6</option>
                    <option>7</option>
                    <option>8</option>
                    <option>9</option>
                    <option>10</option>
                </select>
                <form class="navbar-search">
                    <input type="text" class="bg-white dark:bg-neutral-700 h-10 w-auto" name="search" placeholder="Search">
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            {{-- <a href="{{ route('viewProfile') }}"
                class="btn btn-primary text-sm btn-sm px-3 py-3 rounded-lg flex items-center gap-2">
                <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                Add New User
            </a> --}}
        </div>
        <div class="card-body p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 3xl:grid-cols-4 gap-6">
                @if (isset($tenants) && count($tenants) > 0)
                    @foreach ($tenants as $tenant)
                        <div class="user-grid-card">
                            <div
                                class="relative border border-neutral-200 dark:border-neutral-600 rounded-2xl overflow-hidden">
                                {{-- Dropdown --}}
                                <div class="dropdown absolute top-0 end-0 me-4 mt-4">
                                    <button data-dropdown-toggle="dropdown{{ $tenant->id }}"
                                        class="bg-gradient-to-r from-white/50 w-8 h-8 rounded-lg border flex justify-center items-center text-white"
                                        type="button">
                                        <i class="ri-more-2-fill text-gray-400 hover:text-gray-600"></i>
                                    </button>
                                    <div id="dropdown{{ $tenant->id }}"
                                        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-lg border border-neutral-100 dark:border-neutral-600 w-44 dark:bg-gray-700">
                                        <ul class="p-2 text-sm text-gray-700 dark:text-gray-200">
                                            <li>
                                                <button type="submit"
                                                    class="w-full text-start px-4 py-2.5 hover:bg-gray-100 dark:hover:bg-gray-600 rounded dark:hover:text-white flex items-center gap-2">
                                                    Edit
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button"
                                                    class="w-full text-start px-4 py-2.5 hover:bg-danger-100 dark:hover:bg-danger-600/25 rounded hover:text-danger-500 dark:hover:text-danger-600 flex items-center gap-2">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                {{-- Tenant info --}}
                                <div class="pe-6 pb-4 ps-6 text-center mt--50 pt-5">
                                    <img src="{{ asset('assets/images/user-grid/user-grid-img1.png') }}" alt=""
                                        class="border br-white border-width-8-px w-[120px] h-[120px] ms-auto me-auto -mt-[0px] rounded-full object-fit-cover">

                                    <h6 class="text-lg mb-1 mt-2">{{ $tenant->name }}</h6>
                                    <div class="text-center w-full mb-4">
                                        {{-- <h6 class="text-base mb-0">Status</h6> --}}
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


                                    <div
                                        class="center-border relative bg-gradient-to-r from-danger-500/10 to-danger-50/25 rounded-lg p-3 flex items-center gap-4">
                                        <div class="text-center w-1/2">
                                            <h6 class="text-base mb-0">Country</h6>
                                            <span class="text-secondary-light text-sm mb-0">Indonesia</span>
                                        </div>
                                        <div class="text-center w-1/2">
                                            <h6 class="text-base mb-0">Properties</h6>
                                            <span class="text-secondary-light text-sm mb-0">24</span>
                                        </div>

                                    </div>

                                    <a href="{{ route('super-admin.index8') }}"
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
                    <p class="text-center">Belum ada Landloard yang terdaftar</p>
                @endif
            </div>

            {{-- Pagination --}}
            <div class="flex items-center justify-between flex-wrap gap-2 mt-6">
                <span>Showing 1 to 10 of entries</span>
                <ul class="pagination flex flex-wrap items-center gap-2 justify-center">
                    <li class="page-item">
                        <a class="page-link bg-neutral-300 dark:bg-neutral-600 text-secondary-light font-semibold rounded-lg border-0 flex items-center justify-center h-8 w-8 text-base"
                            href="javascript:void(0)"><iconify-icon icon="ep:d-arrow-left"></iconify-icon></a>
                    </li>
                    <li class="page-item">
                        <a class="page-link text-secondary-light font-semibold rounded-lg border-0 flex items-center justify-center h-8 w-8 text-base bg-primary-600 text-white"
                            href="javascript:void(0)">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link bg-neutral-300 dark:bg-neutral-600 text-secondary-light font-semibold rounded-lg border-0 flex items-center justify-center h-8 w-8"
                            href="javascript:void(0)">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link bg-neutral-300 dark:bg-neutral-600 text-secondary-light font-semibold rounded-lg border-0 flex items-center justify-center h-8 w-8 text-base"
                            href="javascript:void(0)">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link bg-neutral-300 dark:bg-neutral-600 text-secondary-light font-semibold rounded-lg border-0 flex items-center justify-center h-8 w-8 text-base"
                            href="javascript:void(0)">4</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link bg-neutral-300 dark:bg-neutral-600 text-secondary-light font-semibold rounded-lg border-0 flex items-center justify-center h-8 w-8 text-base"
                            href="javascript:void(0)">5</a>
                    </li>
                    <li class="page-item">
                        <a
                            class="page-link bg-neutral-300 dark:bg-neutral-600 text-secondary-light font-semibold rounded-lg border-0 flex items-center justify-center h-8 w-8 text-base">
                            <iconify-icon icon="ep:d-arrow-right"></iconify-icon>
                        </a>
                    </li>
                </ul>
            </div>
        </div>


    </div>
   
@endsection