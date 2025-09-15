@extends('layout.layout')

@php
    $title = 'Global Notifications';
    $metaTags = '<meta name="csrf-token" content="' . csrf_token() . '">';
    $subTitle = 'Global Notifications';
    $script = '<script>
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
    </script>';
@endphp

@section('content')
    <script src="{{ asset('assets/js/tenantList.js') }}"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="col-span-12 lg:col-span-12">
            <div class="card h-full border-0">
                <div class="card-body p-6">
                    <ul class="tab-style-gradient flex flex-wrap text-sm font-medium text-center mb-6" id="default-tab"
                        data-tabs-toggle="#default-tab-content" role="tablist">
                        <li class="" role="presentation">
                            <button
                                class="py-2.5 px-4 border-t-2 font-semibold text-base inline-flex items-center gap-3 text-neutral-600"
                                id="edit-profile-tab" data-tabs-target="#edit-profile" type="button" role="tab"
                                aria-controls="edit-profile" aria-selected="false">
                                Create New Notification
                            </button>
                        </li>

                        <li class="" role="presentation">
                            <button
                                class="py-2.5 px-4 border-t-2 font-semibold text-base inline-flex items-center gap-3 text-neutral-600 hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                id="notification-password-tab" data-tabs-target="#notification-password" type="button"
                                role="tab" aria-controls="notification-password" aria-selected="false">
                                Notification Settings
                            </button>
                        </li>
                        <li class="" role="presentation">
                            <button
                                class="py-2.5 px-4 border-t-2 font-semibold text-base inline-flex items-center gap-3 text-neutral-600 hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                id="change-password-tab" data-tabs-target="#change-password" type="button" role="tab"
                                aria-controls="change-password" aria-selected="false">
                                All Notifications
                            </button>
                        </li>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                if (window.location.hash === '#change-password' ||
                                    new URLSearchParams(window.location.search).get('tab') === 'all-notifications') {
                                    document.getElementById('change-password-tab').click();
                                }
                            });
                        </script>
                    </ul>
                    <div id="default-tab-content">
                        <div class="hidden" id="edit-profile" role="tabpanel" aria-labelledby="edit-profile-tab">
                            <form action="javascript:void(0)">
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-6">
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="name"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Full
                                                Title <span class="text-danger-600">*</span></label>
                                            <input type="text" class="form-control rounded-lg" id="name"
                                                placeholder="Enter Full Title">
                                        </div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="desig"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Priority
                                                <span class="text-danger-600">*</span> </label>
                                            <select class="bg-white dark:bg-neutral-700 w-full text-left px-4 py-2 rounded-lg form-select" id="desig">
                                                <option>Normal</option>
                                                <option>Important</option>
                                                <option>Critical</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-span-12">
                                        <div class="mb-5">
                                            <label for="desc"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Message</label>
                                            <textarea name="#0" class="form-control rounded-lg" id="desc" placeholder="Message..." style="height: 100px;"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="depart"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Target
                                                Audience
                                                <span class="text-danger-600">*</span> </label>
                                            <select
                                                class="bg-white dark:bg-neutral-700 w-full text-left px-4 py-2 rounded-lg"
                                                id="depart" onchange="toggleTenantList()">
                                                <option value="all">All Tenants</option>
                                                <option value="specific">Specific Tenant(s)</option>
                                            </select>
                                        </div>


                                        <div id="tenantSelection" class="mb-5" style="display: none;">
                                            <label
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">
                                                Select Tenants
                                            </label>

                                            <div class="mb-3">
                                                <input type="text" id="tenantSearch" class="form-control rounded-lg"
                                                    placeholder="Search tenants..." onkeyup="searchTenants()">
                                            </div>

                                            <div id="tenantList"
                                                class="form-control rounded-lg max-h-60 overflow-y-auto p-2 bg-white">

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="depart"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Delivery
                                                Method
                                                <span class="text-danger-600">*</span> </label>
                                            <div class="dropdown-container" style="position: relative; width: 100%;">
                                                <button type="button"
                                                    class="bg-white dark:bg-neutral-700 w-full text-left px-4 py-2 rounded-lg border border-gray-300 dark:border-neutral-600"
                                                    id="depart" onclick="toggleDropdown()"
                                                    style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                                                    <span id="selected-text">Select delivery methods...</span>
                                                </button>
                                                <div id="dropdown-content"
                                                    class="hidden border border-gray-300 dark:border-neutral-600"
                                                    style="position: absolute; top: 100%; left: 0; right: 0;   border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); z-index: 1000; max-height: 200px; overflow-y: auto;">
                                                    <div class="dropdown-item" onclick="toggleOption('dashboard')"
                                                        style="padding: 0.5rem 0.75rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                                        <input type="checkbox" id="dashboard" value="Dashboard"
                                                            onchange="updateSelection()"
                                                            style="border-radius: 4px; border: 1px solid #d1d5db;">
                                                        <label for="dashboard" style="cursor: pointer;">Dashboard</label>
                                                    </div>
                                                    <div class="dropdown-item" onclick="toggleOption('push')"
                                                        style="padding: 0.5rem 0.75rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                                        <input type="checkbox" id="push" value="Push Notifications"
                                                            onchange="updateSelection()"
                                                            style="border-radius: 4px; border: 1px solid #d1d5db;">
                                                        <label for="push" style="cursor: pointer;">Push
                                                            Notifications</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end gap-3 mt-6">
                                    <button type="button"
                                        class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-base px-14 py-[11px] rounded-lg">
                                        Cancel
                                    </button>
                                    <button type="button" onclick="submitNotification()"
                                        class="btn btn-primary border border-primary-600 text-base px-14 py-3 rounded-lg">
                                        Send
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="hidden" id="change-password" role="tabpanel" aria-labelledby="change-password-tab">

                            <div class="lg:col-span-12 2xl:col-span-6">
                                <div class="card h-full border-0 overflow-hidden">
                                    <div
                                        class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 ps-0 py-0 pe-6 flex items-center justify-between">
                                        <div class="border-b border-gray-200 dark:border-gray-700">
                                            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center"
                                                id="default-styled-tab" data-tabs-toggle="#default-styled-tab-content"
                                                data-tabs-active-classes="text-purple-600 hover:text-purple-600 dark:text-purple-500 dark:hover:text-purple-500 border-purple-600 dark:border-purple-500"
                                                data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300"
                                                role="tablist">

                                                <li role="presentation">
                                                    <button
                                                        class="inline-block p-4 border-b-2 rounded-t-lg transition-colors ease-in-out duration-300 text-neutral-600 dark:text-white"
                                                        id="todoList-styled-tab" data-tabs-target="#styled-todoList"
                                                        type="button" role="tab" aria-controls="styled-todoList"
                                                        aria-selected="false">Active</button>
                                                </li>

                                                <li role="presentation">
                                                    <button
                                                        class="inline-block p-4 border-b-2 rounded-t-lg transition-colors ease-in-out duration-300 text-neutral-600 dark:text-white hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300"
                                                        id="recentLead-styled-tab" data-tabs-target="#styled-recentLead"
                                                        type="button" role="tab" aria-controls="styled-recentLead"
                                                        aria-selected="false">Archived</button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="card-body p-0 mt-5">
                                        <div id="default-styled-tab-content">

                                            <div class="hidden rounded-lg" id="styled-todoList" role="tabpanel">
                                                <div class="grid grid-cols-12">
                                                    <div class="col-span-12">
                                                        <div
                                                            class="card w-full h-full p-0 rounded-xl border-0 overflow-hidden">
                                                            <div
                                                                class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6 flex items-center flex-wrap gap-3 justify-between">
                                                                <div class="flex items-center flex-wrap gap-3">
                                                                    <form class="navbar-search" onsubmit="return false;">
                                                                        <input id="searchInput" type="text"
                                                                            class="bg-white dark:bg-neutral-700 h-10 w-auto"
                                                                            name="search"
                                                                            placeholder="Search tenants...">
                                                                        <iconify-icon icon="ion:search-outline"
                                                                            class="icon"></iconify-icon>
                                                                    </form>
                                                                    <select id="statusFilter"
                                                                        class="dark:bg-neutral-600 dark:text-white border-neutral-200 dark:border-neutral-500 rounded-lg">
                                                                        <option value="">Filter by Priority</option>
                                                                        <option value="Normal">Normal</option>
                                                                        <option value="Important">Important</option>
                                                                        <option value="Critical">Critical</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="card-body p-6">
                                                                <div id="loadingSpinner" class="hidden text-center py-4">
                                                                    <div
                                                                        class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-indigo-500">
                                                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-yellow-500"
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            fill="none" viewBox="0 0 24 24">
                                                                            <circle class="opacity-25" cx="12"
                                                                                cy="12" r="10"
                                                                                stroke="currentColor" stroke-width="4">
                                                                            </circle>
                                                                            <path class="opacity-75" fill="currentColor"
                                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                            </path>
                                                                        </svg>
                                                                        Loading...
                                                                    </div>
                                                                </div>

                                                                <div class="table-responsive scroll-sm overflow-x-auto">
                                                                    <table
                                                                        class="table bordered-table sm-table mb-0 w-full">
                                                                        <thead class="bg-neutral-50 dark:bg-neutral-800 ">
                                                                            <tr>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-left w-24">
                                                                                    <div class="flex items-center gap-3">
                                                                                        <div
                                                                                            class="form-check style-check flex items-center">
                                                                                            <input
                                                                                                class="form-check-input rounded border input-form-dark"
                                                                                                type="checkbox"
                                                                                                name="checkbox"
                                                                                                id="selectAll">
                                                                                        </div>
                                                                                        <span
                                                                                            class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">No.</span>
                                                                                    </div>
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-left w-32">
                                                                                    <span
                                                                                        class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Title</span>
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-left min-w-[200px]">
                                                                                    <span
                                                                                        class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Priority</span>
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-left min-w-[180px]">
                                                                                    <span
                                                                                        class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Message</span>
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-center w-28">
                                                                                    <span
                                                                                        class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Target</span>
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-center w-28">
                                                                                    <span
                                                                                        class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Date</span>
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-center w-32">
                                                                                    <span
                                                                                        class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Actions</span>
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="tableBody"
                                                                            class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                                <div
                                                                    class="flex items-center justify-between flex-wrap gap-2 mt-6">
                                                                    <span id="paginationInfo"
                                                                        class="text-sm text-neutral-600 dark:text-neutral-400">
                                                                        Showing 0 to 0 of 0 entries
                                                                    </span>
                                                                    <ul id="pageNumbers"
                                                                        class="pagination flex flex-wrap items-center gap-2 justify-center">
                                                                        <div id="pageNumbers" class="flex gap-1"></div>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="hidden rounded-lg bg-gray-50 dark:bg-gray-800"
                                                id="styled-recentLead" role="tabpanel">
                                                <div class="grid grid-cols-12">
                                                    <div class="col-span-12">
                                                        <div class="card h-full p-0 rounded-xl border-0 overflow-hidden">
                                                            <div
                                                                class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6 flex items-center flex-wrap gap-3 justify-between">
                                                                <div class="flex items-center flex-wrap gap-3">
                                                                    <form class="navbar-search" onsubmit="return false;">
                                                                        <input id="archivedSearchInput" type="text"
                                                                            class="bg-white dark:bg-neutral-700 h-10 w-auto"
                                                                            name="search"
                                                                            placeholder="Search tenants...">
                                                                        <iconify-icon icon="ion:search-outline"
                                                                            class="icon"></iconify-icon>
                                                                    </form>

                                                                    <select id="archivedTargetFilter"
                                                                        class="dark:bg-neutral-600 dark:text-white border-neutral-200 dark:border-neutral-500 rounded-lg">
                                                                        <option value="">Filter by Target</option>
                                                                        <option value="all">All Tenants</option>
                                                                        <option value="specific">Specific Tenants</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="card-body p-6">
                                                                <div id="archivedLoadingSpinner"
                                                                    class="hidden text-center py-4">
                                                                    <div
                                                                        class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-indigo-500">
                                                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                            fill="none" viewBox="0 0 24 24">
                                                                            <circle class="opacity-25" cx="12"
                                                                                cy="12" r="10"
                                                                                stroke="currentColor" stroke-width="4">
                                                                            </circle>
                                                                            <path class="opacity-75" fill="currentColor"
                                                                                d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                            </path>
                                                                        </svg>
                                                                        Loading...
                                                                    </div>
                                                                </div>

                                                                <div class="table-responsive scroll-sm overflow-x-auto">
                                                                    <table
                                                                        class="table bordered-table sm-table mb-0 w-full">
                                                                        <thead class="bg-neutral-50 dark:bg-neutral-800">
                                                                            <tr>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-left w-24">
                                                                                    <div class="flex items-center gap-3">
                                                                                        <div
                                                                                            class="form-check style-check flex items-center">
                                                                                            <input
                                                                                                class="form-check-input rounded border input-form-dark"
                                                                                                type="checkbox"
                                                                                                name="checkbox"
                                                                                                id="archivedSelectAll">
                                                                                        </div>
                                                                                        <span
                                                                                            class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">No.</span>
                                                                                    </div>
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-left w-32">
                                                                                    <span
                                                                                        class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Title</span>
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-left min-w-[200px]">
                                                                                    <span
                                                                                        class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Priority</span>
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-left min-w-[180px]">
                                                                                    <span
                                                                                        class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Message</span>
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-center w-28">
                                                                                    <span
                                                                                        class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Target</span>
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-center w-28">
                                                                                    <span
                                                                                        class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Date</span>
                                                                                </th>
                                                                                <th scope="col"
                                                                                    class="px-4 py-3 text-center w-32">
                                                                                    <span
                                                                                        class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Actions</span>
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="archivedTableBody"
                                                                            class="divide-y divide-neutral-200 dark:divide-neutral-700">
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                                <div
                                                                    class="flex items-center justify-between flex-wrap gap-2 mt-6">
                                                                    <span id="archivedPaginationInfo"
                                                                        class="text-sm text-neutral-600 dark:text-neutral-400">
                                                                        Showing 0 to 0 of 0 entries
                                                                    </span>
                                                                    <ul id="archivedPageNumbers"
                                                                        class="pagination flex flex-wrap items-center gap-2 justify-center">
                                                                        <div id="archivedPageNumbers" class="flex gap-1">
                                                                        </div>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="hidden" id="notification-password" role="tabpanel"
                            aria-labelledby="notification-password-tab">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">General Settings</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                                <div class="col-span-1">
                                    <div class="mb-0">
                                        <label for="default-priority"
                                            class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">
                                            Default Priority
                                            <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="bg-white dark:bg-neutral-700 w-full text-left px-4 py-2 rounded-lg"
                                            <option>Normal</option>
                                            <option>Important</option>
                                            <option>Critical</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-span-1">
                                    <div class="mb-0">
                                        <label
                                            class="inline-block font-semibold text-neutral-600 dark:text-neutral-200  text-sm mb-3">
                                            Default Delivery Method
                                        </label>
                                        <div class="dropdown-container-settings" style="position: relative; width: 100%;">
                                            <button type="button"
                                                class="bg-white dark:bg-neutral-700 w-full text-left px-4 py-2 rounded-lg border border-gray-300 dark:border-neutral-600"
                                                id="default-delivery-dropdown" onclick="toggleSettingsDropdown()"
                                                style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                                                <span id="settings-selected-text">Select delivery methods...</span>
                                            </button>
                                            <div id="settings-dropdown-content"
                                                class="bg-white dark:bg-neutral-700 w-full text-left  rounded-lg border border-gray-300 dark:border-neutral-600"
                                                style="position: absolute; top: 100%; left: 0; right: 0;  border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); z-index: 1000; max-height: 200px; overflow-y: auto;">
                                                <div class="dropdown-item"
                                                    onclick="toggleSettingsOption('settings-dashboard')"
                                                    style="padding: 0.5rem 0.75rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                                    <input type="checkbox" id="settings-dashboard" value="Dashboard"
                                                        onchange="updateSettingsSelection()"
                                                        style="border-radius: 4px; border: 1px solid #d1d5db;">
                                                    <label for="settings-dashboard"
                                                        style="cursor: pointer;">Dashboard</label>
                                                </div>
                                                <div class="dropdown-item" onclick="toggleSettingsOption('settings-push')"
                                                    style="padding: 0.5rem 0.75rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                                    <input type="checkbox" id="settings-push" value="Push Notifications"
                                                        onchange="updateSettingsSelection()"
                                                        style="border-radius: 4px; border: 1px solid #d1d5db;">
                                                    <label for="settings-push" style="cursor: pointer;">Push
                                                        Notifications</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Push Notification Settings</h2>

                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-6 mb-8">
                                <div class="col-span-12 sm:col-span-6">
                                    <div class="mb-0">
                                        <label
                                            class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">
                                            Push Notification Settings <span class="text-danger-600">*</span>
                                        </label>

                                        <label class="flex items-center cursor-pointer mb-6 mt-4">
                                            <input type="checkbox" class="sr-only peer" id="notificationToggle" checked
                                                onchange="updateToggleText()">
                                            <span
                                                class="relative w-11 h-6 bg-gray-400 peer-focus:outline-none rounded-full peer dark:bg-gray-500 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></span>
                                            <div class="w-4"></div>
                                            <span id="statusText"
                                                class="line-height-1 font-medium text-md text-primary-600">
                                                Enable
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-span-12 sm:col-span-6">
                                    <div class="mb-6">
                                        <label
                                            class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">
                                            Display Notification Settings <span class="text-danger-600">*</span>
                                        </label>
                                        <div class="mb-5">

                                            <div class="relative">
                                                <input type="number" class="form-control rounded-lg pr-10"
                                                    id="displayNotifications" placeholder="5" value="5"
                                                    min="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-end gap-3">
                                <button type="button" id="settingsSaveButton" onclick="handleSaveSettings()"
                                    class="btn btn-primary border border-primary-600 text-base px-14 py-3 rounded-lg">
                                    Save
                                </button>
                            </div>
                        </div>

                        <div id="deleteModal"
                            class="fixed inset-0 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
                            <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-xl w-96 transform transition-all scale-95 opacity-0 overflow-hidden"
                                id="deleteModalContent">
                                <div class="px-6 py-4 border-b border-neutral-200">
                                    <h2 class="text-lg font-semibold text-gray-800">Confirm Delete</h2>
                                </div>
                                <div class="px-6 py-6">
                                    <p class="text-sm text-gray-600 mb-5">
                                        Are you sure you want to delete <span class="font-semibold text-gray-800">this
                                            notification</span>? This action cannot be undone.
                                    </p>
                                    <div class="flex justify-end gap-3 mt-6">
                                        <button onclick="closeDeleteModal()"
                                            class="px-4 py-2  text-sm rounded-lg bg-gray-200 text-neutral-700 hover:bg-gray-300 transition">
                                            Cancel
                                        </button>
                                        <button id="confirmDeleteBtn"
                                            class="px-4 py-2 text-sm rounded-lg bg-danger-500 text-white">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="restoreModal"
                            class="fixed inset-0 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
                            <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-xl w-96 transform transition-all scale-95 opacity-0 overflow-hidden"
                                id="restoreModalContent">
                                <div class="px-6 py-4 border-b border-neutral-200">
                                    <h2 class="text-lg font-semibold text-gray-800">Confirm Restore</h2>
                                </div>
                                <div class="px-6 py-6">
                                    <p class="text-sm text-gray-600 mb-5">
                                        Are you sure you want to restore <span class="font-semibold text-gray-800">this
                                            notification</span>?
                                    </p>
                                    <div class="flex justify-end gap-3 mt-6">
                                        <button onclick="closeRestoreModal()"
                                            class="px-4 py-2 text-sm rounded-lg bg-gray-200 text-neutral-700 hover:bg-gray-300 transition">
                                            Cancel
                                        </button>
                                        <button id="confirmRestoreBtn"
                                            class="px-4 py-2 text-sm rounded-lg bg-success-500 text-white hover:bg-success-600 transition">
                                            Restore
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div id="archiveModal"
                            class="fixed inset-0 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
                            <div class="bg-white dark:bg-neutral-800 rounded-2xl dark:bg-neutral-800 shadow-xl p-6 w-96 transform transition-all scale-95 opacity-0"
                                id="archiveModalContent">
                                <div class="border-b border-neutral-200 pb-3 mb-4">
                                    <h2 class="text-lg font-semibold text-gray-800">Confirm Archive</h2>
                                </div>
                                <p class="text-sm text-gray-600 mb-5">
                                    Are you sure you want to archive <span class="font-semibold text-gray-800">this
                                        notification</span>? This action cannot be undone.
                                </p>
                                <div class="flex justify-end gap-3">
                                    <button onclick="closeArchiveModal()"
                                        class="px-4 py-2 text-sm rounded-lg bg-gray-200 text-neutral-700 hover:bg-gray-300 transition">
                                        Cancel
                                    </button>
                                    <button id="confirmArchiveBtn"
                                        class="px-4 py-2 text-sm rounded-lg bg-warning-100 text-warning-600  dark:bg-warning-600/25 dark:text-warning-400 hover:bg-gray-300 dark:hover:bg-warning-600 transition flex items-center justify-center">
                                        <svg id="archiveLoadingSpinner"
                                            class="animate-spin -ml-1 mr-2 h-4 w-4 text-warning-600 hidden"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span id="archiveButtonText">Archive</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="toastContainer" class="toast-container"></div>

                        <div id="bulkArchiveModal"
                            class="fixed inset-0 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
                            <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-xl p-6 w-96 transform transition-all scale-95 opacity-0"
                                id="bulkArchiveModalContent">
                                <div class="border-b border-neutral-200 pb-3 mb-4">
                                    <h2 class="text-lg font-semibold text-gray-800">Confirm Bulk Archive</h2>
                                </div>
                                <p class="text-sm text-gray-600 mb-5">
                                    Are you sure you want to archive <span class="font-semibold text-gray-800"
                                        id="bulkArchiveCount">0 notifications</span>? This action cannot be undone.
                                </p>
                                <div class="flex justify-end gap-3">
                                    <button onclick="closeBulkArchiveModal()"
                                        class="px-4 py-2 text-sm rounded-lg bg-gray-200 text-neutral-700 hover:bg-gray-300 transition">
                                        Cancel
                                    </button>
                                    <button onclick="confirmBulkArchive()"
                                        class="px-4 py-2 text-sm rounded-lg bg-warning-100 text-warning-600 dark:bg-warning-600/25 dark:text-warning-400 hover:bg-gray-300 transition flex items-center justify-center">
                                        <svg id="bulkArchiveLoadingSpinner"
                                            class="animate-spin -ml-1 mr-2 h-4 w-4 text-warning-600 hidden"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span id="bulkArchiveButtonText">Archive All</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="bulkDeleteModal"
                            class="fixed inset-0 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
                            <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-xl w-96 transform transition-all scale-95 opacity-0 overflow-hidden"
                                id="bulkDeleteModalContent">
                                <div class="px-6 py-4 border-b border-neutral-200">
                                    <h2 class="text-lg font-semibold text-gray-800">Confirm Bulk Delete</h2>
                                </div>
                                <div class="px-6 py-6">
                                    <p class="text-sm text-gray-600 mb-5">
                                        Are you sure you want to delete <span class="font-semibold text-gray-800"
                                            id="bulkDeleteCount">0 notifications</span>? This action cannot be undone.
                                    </p>
                                    <div class="flex justify-end gap-3 mt-6">
                                        <button onclick="closeBulkDeleteModal()"
                                            class="px-4 py-2 text-sm rounded-lg bg-gray-200 text-neutral-700 hover:bg-gray-300 transition">
                                            Cancel
                                        </button>
                                        <button onclick="confirmBulkDelete()"
                                            class="px-4 py-2 text-sm rounded-lg bg-danger-500 text-white hover:bg-danger-600 transition">
                                            Delete All
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="bulkRestoreModal"
                            class="fixed inset-0 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
                            <div class="bg-white dark:bg-neutral-800 rounded-2xl shadow-xl w-96 transform transition-all scale-95 opacity-0 overflow-hidden"
                                id="bulkRestoreModalContent">
                                <div class="px-6 py-4 border-b border-neutral-200">
                                    <h2 class="text-lg font-semibold text-gray-800">Confirm Bulk Restore</h2>
                                </div>
                                <div class="px-6 py-6">
                                    <p class="text-sm text-gray-600 mb-5">
                                        Are you sure you want to restore <span class="font-semibold text-gray-800"
                                            id="bulkRestoreCount">0 notifications</span>?
                                    </p>
                                    <div class="flex justify-end gap-3 mt-6">
                                        <button onclick="closeBulkRestoreModal()"
                                            class="px-4 py-2 text-sm rounded-lg bg-gray-200 text-neutral-700 hover:bg-gray-300 transition">
                                            Cancel
                                        </button>
                                        <button onclick="confirmBulkRestore()"
                                            class="px-4 py-2 text-sm rounded-lg bg-success-500 text-white hover:bg-success-600 transition">
                                            Restore All
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/notifications.js') }}"></script>
@endsection
