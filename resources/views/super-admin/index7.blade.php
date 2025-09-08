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
                                            <select class="form-control rounded-lg form-select" id="desig">
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
                                            <select class="form-control rounded-lg form-select" id="depart"
                                                onchange="toggleTenantList()">
                                                <option value="all">All Tenants</option>
                                                <option value="specific">Specific Tenant(s)</option>
                                                {{-- <option>Specific Role(s)</option> --}}
                                            </select>
                                        </div>

                                        <!-- Tenant Selection Section (Hidden by default) -->
                                        <div id="tenantSelection" class="mb-5" style="display: none;">
                                            <label
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">
                                                Select Tenants
                                            </label>

                                            <!-- Search Input -->
                                            <div class="mb-3">
                                                <input type="text" id="tenantSearch" class="form-control rounded-lg"
                                                    placeholder="Search tenants..." onkeyup="searchTenants()">
                                            </div>

                                            <!-- Tenant List -->
                                            <div id="tenantList"
                                                class="form-control rounded-lg max-h-60 overflow-y-auto p-2 bg-white">
                                                <!-- Dynamic tenant items will be populated here -->
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        // Sample tenant data - replace with your actual data
                                        // const tenants = [{
                                        //         id: 1,
                                        //         name: 'PT. ABC Corporation',
                                        //         email: 'admin@abc.com',
                                        //         status: 'active'
                                        //     },
                                        //     {
                                        //         id: 2,
                                        //         name: 'CV. XYZ Solutions',
                                        //         email: 'contact@xyz.com',
                                        //         status: 'inactive'
                                        //     },
                                        //     {
                                        //         id: 3,
                                        //         name: 'UD. Maju Jaya',
                                        //         email: 'info@majujaya.com',
                                        //         status: 'active'
                                        //     },
                                        //     {
                                        //         id: 4,
                                        //         name: 'PT. Tech Innovate',
                                        //         email: 'admin@techinnovate.com',
                                        //         status: 'active'
                                        //     },
                                        //     {
                                        //         id: 5,
                                        //         name: 'CV. Digital Plus',
                                        //         email: 'hello@digitalplus.com',
                                        //         status: 'inactive'
                                        //     }
                                        // ];

                                        let selectedTenants = [];

                                        function toggleTenantList() {
                                            const select = document.getElementById('depart');
                                            const tenantSelection = document.getElementById('tenantSelection');

                                            if (select.value === 'specific') {
                                                tenantSelection.style.display = 'block';
                                                renderTenantList(tenants);
                                            } else {
                                                tenantSelection.style.display = 'none';
                                                selectedTenants = [];
                                            }
                                        }

                                        function searchTenants() {
                                            const searchTerm = document.getElementById('tenantSearch').value.toLowerCase();
                                            const filteredTenants = tenants.filter(tenant =>
                                                tenant.name.toLowerCase().includes(searchTerm) ||
                                                tenant.email.toLowerCase().includes(searchTerm)
                                            );
                                            renderTenantList(filteredTenants);
                                        }

                                        function renderTenantList(tenantData) {
                                            const tenantList = document.getElementById('tenantList');
                                            tenantList.innerHTML = '';

                                            if (tenantData.length === 0) {
                                                tenantList.innerHTML = '<p class="text-gray-500 text-sm p-2">No tenants found</p>';
                                                return;
                                            }

                                            tenantData.forEach(tenant => {
                                                const isSelected = selectedTenants.includes(tenant.id);
                                                const tenantItem = document.createElement('div');
                                                tenantItem.className =
                                                    `flex items-center p-4 hover:bg-gray-50 rounded-lg cursor-pointer border transition-colors mb-3 ${isSelected ? 'bg-blue-50 border-blue-200' : 'border-gray-200'}`;
                                                tenantItem.onclick = () => toggleTenantSelection(tenant.id);

                                                tenantItem.innerHTML = `
                                                <input type="checkbox" 
                                                    id="tenant_${tenant.id}" 
                                                    ${isSelected ? 'checked' : ''}
                                                    class="h-4 w-4 text-blue-600 rounded flex-shrink-0"
                                                    onchange="toggleTenantSelection(${tenant.id})"
                                                    onclick="event.stopPropagation();">
                                                <div class="flex-1" style="margin-left: 20px;">
                                                    <div class="font-medium text-gray-900">${tenant.name}</div>
                                                    <div class="text-sm text-gray-500">${tenant.email}</div>
                                                </div>
                                                <div class="flex-shrink-0" style="margin-left: 20px;">
                                                    ${tenant.status === 'active' ? 
                                                        `<span class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-3 py-1.5 rounded-full font-medium text-xs sm:text-sm">Active</span>` :
                                                        `<span class="bg-danger-100 text-danger-600 dark:bg-danger-600/25 dark:text-danger-400 px-3 py-1.5 rounded-full font-medium text-xs sm:text-sm">Inactive</span>`
                                                    }
                                                </div>
                                            `;

                                                // Event listener untuk mengubah background saat checkbox diklik
                                                const checkbox = tenantItem.querySelector('input[type="checkbox"]');
                                                checkbox.addEventListener('change', function() {
                                                    if (this.checked) {
                                                        tenantItem.style.backgroundColor = '#e0f2fe'; // biru pastel
                                                        tenantItem.style.borderColor = '#bae6fd';
                                                    } else {
                                                        tenantItem.style.backgroundColor = '#ffffff'; // putih
                                                        tenantItem.style.borderColor = '#e5e7eb';
                                                    }
                                                });

                                                tenantList.appendChild(tenantItem);
                                            });
                                        }

                                        function toggleTenantSelection(tenantId) {
                                            const index = selectedTenants.indexOf(tenantId);
                                            if (index > -1) {
                                                selectedTenants.splice(index, 1);
                                            } else {
                                                selectedTenants.push(tenantId);
                                            }

                                            // Re-render to update checkbox states and styling
                                            const searchTerm = document.getElementById('tenantSearch').value.toLowerCase();
                                            const filteredTenants = tenants.filter(tenant =>
                                                tenant.name.toLowerCase().includes(searchTerm) ||
                                                tenant.email.toLowerCase().includes(searchTerm)
                                            );
                                            renderTenantList(filteredTenants);

                                            console.log('Selected tenants:', selectedTenants);
                                        }

                                        // Add some CSS for better styling
                                        const style = document.createElement('style');
                                        style.textContent = `
                                        .form-control {
                                            width: 100%;
                                            padding: 0.5rem 0.75rem;
                                            border: 1px solid #d1d5db;
                                            border-radius: 0.5rem;
                                            background-color: white;
                                            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
                                        }
                                        
                                        .form-control:focus {
                                            outline: none;
                                            border-color: #3b82f6;
                                            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
                                        }
                                        
                                        .form-select {
                                            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
                                            background-position: right 0.5rem center;
                                            background-repeat: no-repeat;
                                            background-size: 1.5em 1.5em;
                                            padding-right: 2.5rem;
                                        }
                                        
                                        .text-danger-600 {
                                            color: #dc2626;
                                        }
                                    `;
                                        document.head.appendChild(style);
                                    </script>



                                    <div class="col-span-12 sm:col-span-6">
                                        <div class="mb-5">
                                            <label for="depart"
                                                class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">Delivery
                                                Method
                                                <span class="text-danger-600">*</span> </label>
                                            <div class="dropdown-container" style="position: relative; width: 100%;">
                                                <button type="button" class="form-control rounded-lg form-select"
                                                    id="depart" onclick="toggleDropdown()"
                                                    style="display: flex; justify-content: space-between; align-items: center; cursor: pointer; background-color: white; border: 1px solid #d1d5db;">
                                                    <span id="selected-text">Select delivery methods...</span>
                                                    {{-- <svg class="chevron"
                                                        style="width: 16px; height: 16px; transition: transform 0.2s;"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg> --}}
                                                </button>
                                                <div id="dropdown-content" class="hidden"
                                                    style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); z-index: 1000; max-height: 200px; overflow-y: auto;">
                                                    <div class="dropdown-item" onclick="toggleOption('dashboard')"
                                                        style="padding: 0.5rem 0.75rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                                        <input type="checkbox" id="dashboard" value="Dashboard"
                                                            onchange="updateSelection()"
                                                            style="border-radius: 4px; border: 1px solid #d1d5db;">
                                                        <label for="dashboard" style="cursor: pointer;">Dashboard</label>
                                                    </div>
                                                    {{-- <div class="dropdown-item" onclick="toggleOption('email')"
                                                        style="padding: 0.5rem 0.75rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                                        <input type="checkbox" id="email" value="Email"
                                                            onchange="updateSelection()"
                                                            style="border-radius: 4px; border: 1px solid #d1d5db;">
                                                        <label for="email" style="cursor: pointer;">Email</label>
                                                    </div> --}}
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

                                    <style>
                                        .dropdown-item:hover {
                                            background-color: #f3f4f6;
                                        }

                                        .dark .dropdown-item:hover {
                                            background-color: #4b5563;
                                        }

                                        .hidden {
                                            display: none;
                                        }

                                        .chevron.open {
                                            transform: rotate(180deg);
                                        }

                                        .dark #dropdown-content {
                                            background-color: #374151;
                                            border-color: #4b5563;
                                        }
                                    </style>

                                    <script>
                                        function toggleDropdown() {
                                            const content = document.getElementById('dropdown-content');
                                            const chevron = document.querySelector('.chevron');

                                            content.classList.toggle('hidden');
                                            chevron.classList.toggle('open');
                                        }

                                        function toggleOption(optionId) {
                                            const checkbox = document.getElementById(optionId);
                                            checkbox.checked = !checkbox.checked;
                                            updateSelection();
                                        }

                                        function updateSelection() {
                                            const checkboxes = document.querySelectorAll('#dropdown-content input[type="checkbox"]');
                                            const selectedOptions = [];

                                            checkboxes.forEach(checkbox => {
                                                if (checkbox.checked) {
                                                    selectedOptions.push(checkbox.value);
                                                }
                                            });

                                            const selectedText = document.getElementById('selected-text');
                                            if (selectedOptions.length === 0) {
                                                selectedText.textContent = 'Select delivery methods...';
                                            } else {
                                                selectedText.textContent = selectedOptions.join(', ');
                                            }
                                        }

                                        // Close dropdown when clicking outside
                                        document.addEventListener('click', function(event) {
                                            const container = document.querySelector('.dropdown-container');
                                            const content = document.getElementById('dropdown-content');
                                            const chevron = document.querySelector('.chevron');

                                            if (!container.contains(event.target)) {
                                                content.classList.add('hidden');
                                                chevron.classList.remove('open');
                                            }
                                        });
                                    </script>


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
                            <!-- Latest Performance Start -->
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
                                            <!-- Active Tab -->
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
                                                                        class="form-select form-select-sm w-auto">
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

                                            <!-- Archived Tab -->
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
                                                                        class="form-select form-select-sm w-auto">
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
                                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
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


                        <!-- Notification Settings Tab -->
                        <div class="hidden" id="notification-password" role="tabpanel"
                            aria-labelledby="notification-password-tab">
                            <!-- General Settings -->
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">General Settings</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                                <!-- Default Priority -->
                                <div class="col-span-1">
                                    <div class="mb-0">
                                        <label for="default-priority"
                                            class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">
                                            Default Priority
                                            <span class="text-danger-600">*</span>
                                        </label>
                                        <select class="form-control rounded-lg form-select" id="default-priority">
                                            <option>Normal</option>
                                            <option>Important</option>
                                            <option>Critical</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Default Delivery Method -->
                                <div class="col-span-1">
                                    <div class="mb-0">
                                        <label
                                            class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-3">
                                            Default Delivery Method
                                        </label>
                                        <div class="dropdown-container-settings" style="position: relative; width: 100%;">
                                            <button type="button" class="form-control rounded-lg form-select"
                                                id="default-delivery-dropdown" onclick="toggleSettingsDropdown()"
                                                style="display: flex; justify-content: space-between; align-items: center; cursor: pointer; background-color: white; border: 1px solid #d1d5db;">
                                                <span id="settings-selected-text">Select delivery methods...</span>
                                                {{-- <svg class="chevron-settings"
                        style="width: 16px; height: 16px; transition: transform 0.2s;"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7"></path>
                    </svg> --}}
                                            </button>
                                            <div id="settings-dropdown-content" class="hidden settings-dropdown"
                                                style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); z-index: 1000; max-height: 200px; overflow-y: auto;">
                                                <div class="dropdown-item"
                                                    onclick="toggleSettingsOption('settings-dashboard')"
                                                    style="padding: 0.5rem 0.75rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                                    <input type="checkbox" id="settings-dashboard" value="Dashboard"
                                                        onchange="updateSettingsSelection()"
                                                        style="border-radius: 4px; border: 1px solid #d1d5db;">
                                                    <label for="settings-dashboard"
                                                        style="cursor: pointer;">Dashboard</label>
                                                </div>
                                                {{-- <div class="dropdown-item"
                                                    onclick="toggleSettingsOption('settings-email')"
                                                    style="padding: 0.5rem 0.75rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                                    <input type="checkbox" id="settings-email" value="Email"
                                                        onchange="updateSettingsSelection()"
                                                        style="border-radius: 4px; border: 1px solid #d1d5db;">
                                                    <label for="settings-email" style="cursor: pointer;">Email</label>
                                                </div> --}}
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
                            <!-- Email Settings -->
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Email Settings</h2>

                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-x-6 mb-8">
                                <div class="col-span-12 sm:col-span-6">
                                    <div class="mb-5">
                                        <label for="from-email"
                                            class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">
                                            From Email <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="text" class="form-control rounded-lg" id="from-email"
                                            placeholder="Enter From Email">
                                    </div>
                                </div>
                                <div class="col-span-12 sm:col-span-6">
                                    <div class="mb-0">
                                        <label for="email-footer"
                                            class="inline-block font-semibold text-neutral-600 dark:text-neutral-200 text-sm mb-2">
                                            Email Footer <span class="text-danger-600">*</span>
                                        </label>
                                        <input type="text" class="form-control rounded-lg" id="email-footer"
                                            placeholder="Enter Email Footer">
                                    </div>
                                </div>
                            </div>

                            <!-- Push Notification Settings -->
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

                            <!-- Save Button -->
                            <div class="flex items-center justify-end gap-3">

                                <button type="button"
                                    class="btn btn-primary border border-primary-600 text-base px-14 py-3 rounded-lg">
                                    Save
                                </button>
                            </div>
                        </div>
                        <!-- Modal Delete Notification -->
                        <div id="deleteModal"
                            class="fixed inset-0 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
                            <div class="bg-white rounded-2xl shadow-xl w-96 transform transition-all scale-95 opacity-0 overflow-hidden"
                                id="deleteModalContent">
                                <!-- Header -->
                                <div class="px-6 py-4 border-b border-neutral-200">
                                    <h2 class="text-lg font-semibold text-gray-800">Confirm Delete</h2>
                                </div>
                                <div class="px-6 py-6">
                                    <p class="text-sm text-gray-600 mb-5">
                                        Are you sure you want to delete <span class="font-semibold text-gray-800">this
                                            notification</span>? This action cannot be undone.
                                    </p>
                                    <!-- Action Buttons -->
                                    <div class="flex justify-end gap-3 mt-6">
                                        <button onclick="closeDeleteModal()"
                                            class="px-4 py-2  text-sm rounded-lg bg-gray-200 text-neutral-700 hover:bg-gray-300 transition">
                                            Cancel
                                        </button>
                                        <button id="confirmDeleteBtn"
                                            class="px-4 py-2 text-sm rounded-lg bg-danger-500 text-white">
                                            <span class="delete-text">Delete</span>
                                            <span class="delete-loading" style="display: none;">
                                                <div class="loading-spinner"></div>
                                                Menghapus...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            let deleteId = null;

                            // Buka modal dengan animasi
                            function deleteNotification(id) {
                                deleteId = id;
                                const modal = document.getElementById('deleteModal');
                                const content = document.getElementById('deleteModalContent');
                                modal.classList.remove('hidden');
                                setTimeout(() => {
                                    content.classList.remove('scale-95', 'opacity-0');
                                    content.classList.add('scale-100', 'opacity-100');
                                }, 50);
                            }

                            // Tutup modal dengan animasi
                            function closeDeleteModal() {
                                const modal = document.getElementById('deleteModal');
                                const content = document.getElementById('deleteModalContent');
                                content.classList.remove('scale-100', 'opacity-100');
                                content.classList.add('scale-95', 'opacity-0');
                                setTimeout(() => {
                                    modal.classList.add('hidden');
                                }, 200);
                            }

                            // Konfirmasi Delete
                            document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
                                fetch(`/admin/notifications/${deleteId}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                                'content')
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            showAlert('Notification deleted permanently', 'delete');
                                            loadNotifications();
                                        } else {
                                            showAlert('Error deleting notification', 'error');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        showAlert('Error deleting notification', 'error');
                                    })
                                    .finally(() => {
                                        closeDeleteModal();
                                    });
                            });
                        </script>

                        <!-- Modal Restore Notification -->
                        <div id="restoreModal"
                            class="fixed inset-0 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
                            <div class="bg-white rounded-2xl shadow-xl w-96 transform transition-all scale-95 opacity-0 overflow-hidden"
                                id="restoreModalContent">
                                <!-- Header -->
                                <div class="px-6 py-4 border-b border-neutral-200">
                                    <h2 class="text-lg font-semibold text-gray-800">Confirm Restore</h2>
                                </div>
                                <div class="px-6 py-6">
                                    <p class="text-sm text-gray-600 mb-5">
                                        Are you sure you want to restore <span class="font-semibold text-gray-800">this
                                            notification</span>?
                                    </p>
                                    <!-- Action Buttons -->
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

                        <script>
                            let restoreId = null;

                            // Buka modal dengan animasi
                            function restoreNotification(id) {
                                restoreId = id;
                                const modal = document.getElementById('restoreModal');
                                const content = document.getElementById('restoreModalContent');
                                modal.classList.remove('hidden');
                                setTimeout(() => {
                                    content.classList.remove('scale-95', 'opacity-0');
                                    content.classList.add('scale-100', 'opacity-100');
                                }, 50);
                            }

                            // Tutup modal dengan animasi
                            function closeRestoreModal() {
                                const modal = document.getElementById('restoreModal');
                                const content = document.getElementById('restoreModalContent');
                                content.classList.remove('scale-100', 'opacity-100');
                                content.classList.add('scale-95', 'opacity-0');
                                setTimeout(() => {
                                    modal.classList.add('hidden');
                                }, 200);
                            }

                            // Konfirmasi Restore
                            // document.getElementById('confirmRestoreBtn').addEventListener('click', () => {
                            //     fetch(`/admin/notifications/${restoreId}/restore`, {
                            //             method: 'POST',
                            //             headers: {
                            //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            //                     'content')
                            //             }
                            //         })
                            //         .then(response => response.json())
                            //         .then(data => {
                            //             if (data.success) {
                            //                 showAlert('Notification restored successfully', 'success');
                            //                 loadNotifications();
                            //             } else {
                            //                 showAlert('Error restoring notification', 'error');
                            //             }
                            //         })
                            //         .catch(error => {
                            //             console.error('Error:', error);
                            //             showAlert('Error restoring notification', 'error');
                            //         })
                            //         .finally(() => {
                            //             closeRestoreModal();
                            //         });
                            // });
                        </script>


                        <!-- Modal Archive Notification -->
                        <div id="archiveModal"
                            class="fixed inset-0 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
                            <div class="bg-white rounded-2xl shadow-xl p-6 w-96 transform transition-all scale-95 opacity-0"
                                id="archiveModalContent">
                                <!-- Header -->
                                <div class="border-b border-neutral-200 pb-3 mb-4">
                                    <h2 class="text-lg font-semibold text-gray-800">Confirm Archive</h2>
                                </div>
                                <p class="text-sm text-gray-600 mb-5">
                                    Are you sure you want to archive <span class="font-semibold text-gray-800">this
                                        notification</span>? This action cannot be undone.
                                </p>
                                <!-- Action Buttons -->
                                <div class="flex justify-end gap-3">
                                    <button onclick="closeArchiveModal()"
                                        class="px-4 py-2 text-sm rounded-lg bg-gray-200 text-neutral-700 hover:bg-gray-300 transition">
                                        Cancel
                                    </button>
                                    <button id="confirmArchiveBtn"
                                        class="px-4 py-2 text-sm rounded-lg bg-warning-100 text-warning-600 hover:bg-gray-300 transition flex items-center justify-center">
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

                        <script>
                            let archiveId = null;

                            // Buka modal dengan animasi
                            function archiveNotification(id) {
                                archiveId = id;
                                const modal = document.getElementById('archiveModal');
                                const content = document.getElementById('archiveModalContent');
                                modal.classList.remove('hidden');
                                setTimeout(() => {
                                    content.classList.remove('scale-95', 'opacity-0');
                                    content.classList.add('scale-100', 'opacity-100');
                                }, 50);
                            }

                            // Tutup modal dengan animasi
                            function closeArchiveModal() {
                                const modal = document.getElementById('archiveModal');
                                const content = document.getElementById('archiveModalContent');
                                content.classList.remove('scale-100', 'opacity-100');
                                content.classList.add('scale-95', 'opacity-0');
                                setTimeout(() => {
                                    modal.classList.add('hidden');
                                }, 200);
                            }

                            // Konfirmasi Archive
                            document.getElementById('confirmArchiveBtn').addEventListener('click', () => {
                                // Show loading state
                                const button = document.getElementById('confirmArchiveBtn');
                                const spinner = document.getElementById('archiveLoadingSpinner');
                                const buttonText = document.getElementById('archiveButtonText');

                                button.disabled = true;
                                button.classList.add('opacity-50', 'cursor-not-allowed');
                                spinner.classList.remove('hidden');
                                buttonText.textContent = 'Archiving...';

                                fetch(`/admin/notifications/${archiveId}/archive`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                                'content')
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            showAlert('Notification archived successfully', 'success');
                                            loadNotifications();
                                        } else {
                                            showAlert('Error archiving notification', 'error');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        showAlert('Error archiving notification', 'error');
                                    })
                                    .finally(() => {
                                        // Reset loading state
                                        button.disabled = false;
                                        button.classList.remove('opacity-50', 'cursor-not-allowed');
                                        spinner.classList.add('hidden');
                                        buttonText.textContent = 'Archive';
                                        closeArchiveModal();
                                    });
                            });
                        </script>
                        <!-- Tambahkan CSS ini di bagian <style> di head atau sebelum </body> -->
                        <style>
                            /* Enhanced Toast Notification Styles */
                            .toast-container {
                                position: fixed;
                                top: 1rem;
                                right: 1rem;
                                z-index: 9999;
                                pointer-events: none;
                            }

                            .toast {
                                display: flex;
                                align-items: center;
                                padding: 1rem 1.25rem;
                                margin-bottom: 0.75rem;
                                border-radius: 0.75rem;
                                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                                transform: translateX(100%);
                                opacity: 0;
                                transition: all 0.4s ease-in-out;
                                pointer-events: auto;
                                min-width: 300px;
                                max-width: 400px;
                                backdrop-filter: blur(10px);
                            }

                            .toast.show {
                                transform: translateX(0);
                                opacity: 1;
                            }

                            .toast.hide {
                                transform: translateX(100%);
                                opacity: 0;
                            }

                            /* Success Toast */
                            .toast-success {
                                background: linear-gradient(135deg, rgba(34, 197, 94, 0.95) 0%, rgba(22, 163, 74, 0.95) 100%);
                                border-left: 4px solid #16a34a;
                                color: white;
                            }

                            /* Error Toast */
                            .toast-error {
                                background: linear-gradient(135deg, rgba(239, 68, 68, 0.95) 0%, rgba(220, 38, 38, 0.95) 100%);
                                border-left: 4px solid #dc2626;
                                color: white;
                            }

                            /* Info Toast */
                            .toast-info {
                                background: linear-gradient(135deg, rgba(59, 130, 246, 0.95) 0%, rgba(37, 99, 235, 0.95) 100%);
                                border-left: 4px solid #2563eb;
                                color: white;
                            }

                            /* Warning Toast */
                            .toast-warning {
                                background: linear-gradient(135deg, rgba(245, 158, 11, 0.95) 0%, rgba(217, 119, 6, 0.95) 100%);
                                border-left: 4px solid #d97706;
                                color: white;
                            }

                            .toast-icon {
                                flex-shrink: 0;
                                width: 1.5rem;
                                height: 1.5rem;
                                margin-right: 0.75rem;
                            }

                            .toast-content {
                                flex: 1;
                                font-weight: 500;
                                font-size: 0.875rem;
                                line-height: 1.25rem;
                            }

                            .toast-close {
                                flex-shrink: 0;
                                width: 1.25rem;
                                height: 1.25rem;
                                margin-left: 0.75rem;
                                cursor: pointer;
                                opacity: 0.7;
                                transition: opacity 0.2s ease;
                            }

                            .toast-close:hover {
                                opacity: 1;
                            }

                            /* Enhanced Modal Button Styles */
                            .btn-delete {
                                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
                                transition: all 0.3s ease;
                                box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
                            }

                            .btn-delete:hover {
                                background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
                                box-shadow: 0 6px 8px -1px rgba(239, 68, 68, 0.4);
                                transform: translateY(-1px);
                            }

                            .btn-delete:disabled {
                                background: #9ca3af;
                                cursor: not-allowed;
                                transform: none;
                                box-shadow: none;
                            }

                            .btn-archive {
                                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                                transition: all 0.3s ease;
                                box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.3);
                            }

                            .btn-archive:hover {
                                background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
                                box-shadow: 0 6px 8px -1px rgba(245, 158, 11, 0.4);
                                transform: translateY(-1px);
                            }

                            .btn-restore {
                                background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
                                transition: all 0.3s ease;
                                box-shadow: 0 4px 6px -1px rgba(34, 197, 94, 0.3);
                            }

                            .btn-restore:hover {
                                background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
                                box-shadow: 0 6px 8px -1px rgba(34, 197, 94, 0.4);
                                transform: translateY(-1px);
                            }

                            /* Loading Animation */
                            .loading-spinner {
                                width: 1rem;
                                height: 1rem;
                                border: 2px solid transparent;
                                border-top: 2px solid currentColor;
                                border-radius: 50%;
                                animation: spin 1s linear infinite;
                            }

                            @keyframes spin {
                                0% {
                                    transform: rotate(0deg);
                                }

                                100% {
                                    transform: rotate(360deg);
                                }
                            }

                            /* Progress Bar for Toast */
                            .toast-progress {
                                position: absolute;
                                bottom: 0;
                                left: 0;
                                height: 3px;
                                background-color: rgba(255, 255, 255, 0.8);
                                border-radius: 0 0 0.75rem 0;
                                animation: progress 3s linear;
                            }

                            @keyframes progress {
                                0% {
                                    width: 100%;
                                }

                                100% {
                                    width: 0%;
                                }
                            }
                        </style>

                        <!-- Tambahkan container toast di body -->
                        <div id="toastContainer" class="toast-container"></div>

                        <script>
                            // Enhanced showAlert function dengan style yang lebih baik
                            function showAlert(message, type = 'info', duration = 4000) {
                                const container = document.getElementById('toastContainer') || createToastContainer();

                                // Create toast element
                                const toast = document.createElement('div');
                                toast.className = `toast toast-${type}`;

                                // Icons untuk setiap type
                                const icons = {
                                    success: `<svg class="toast-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>`,
                                    error: `<svg class="toast-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>`,
                                    warning: `<svg class="toast-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>`,
                                    info: `<svg class="toast-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>`
                                };

                                // Toast content
                                toast.innerHTML = `
                                    ${icons[type] || icons.info}
                                    <div class="toast-content">${message}</div>
                                    <svg class="toast-close" fill="currentColor" viewBox="0 0 20 20" onclick="removeToast(this.parentElement)">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="toast-progress"></div>
                                `;

                                // Add to container
                                container.appendChild(toast);

                                // Show animation
                                setTimeout(() => {
                                    toast.classList.add('show');
                                }, 100);

                                // Auto remove
                                setTimeout(() => {
                                    removeToast(toast);
                                }, duration);

                                return toast;
                            }

                            function createToastContainer() {
                                const container = document.createElement('div');
                                container.id = 'toastContainer';
                                container.className = 'toast-container';
                                document.body.appendChild(container);
                                return container;
                            }

                            function removeToast(toast) {
                                if (toast && toast.parentElement) {
                                    toast.classList.add('hide');
                                    setTimeout(() => {
                                        if (toast.parentElement) {
                                            toast.parentElement.removeChild(toast);
                                        }
                                    }, 400);
                                }
                            }

                            // Enhanced Delete Modal dengan loading state
                            function deleteNotification(id) {
                                deleteId = id;
                                const modal = document.getElementById('deleteModal');
                                const content = document.getElementById('deleteModalContent');
                                const confirmBtn = document.getElementById('confirmDeleteBtn');

                                // Reset button state
                                confirmBtn.disabled = false;
                                confirmBtn.innerHTML = 'Delete';
                                confirmBtn.className = 'px-4 py-2 text-sm rounded-lg btn-delete text-white transition-all duration-300';

                                modal.classList.remove('hidden');
                                setTimeout(() => {
                                    content.classList.remove('scale-95', 'opacity-0');
                                    content.classList.add('scale-100', 'opacity-100');
                                }, 50);
                            }

                            // Enhanced Confirm Delete dengan loading dan feedback
                            document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
                                const confirmBtn = document.getElementById('confirmDeleteBtn');

                                // Show loading state
                                confirmBtn.disabled = true;
                                confirmBtn.innerHTML = `
                                <div class="loading-spinner"></div>
                                <span style="margin-left: 8px;">Menghapus...</span>
                            `;

                                fetch(`/admin/notifications/${deleteId}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                                'content')
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            showAlert('Notifikasi berhasil dihapus permanen!', 'success');
                                            loadNotifications();
                                            closeDeleteModal();
                                        } else {
                                            showAlert('Gagal menghapus notifikasi. Silakan coba lagi.', 'error');
                                            // Reset button
                                            confirmBtn.disabled = false;
                                            confirmBtn.innerHTML = 'Delete';
                                        }
                                    })
                                // .catch(error => {
                                //     console.error('Error:', error);
                                //     showAlert('Terjadi kesalahan saat menghapus notifikasi.', 'error');
                                //     // Reset button
                                //     confirmBtn.disabled = false;
                                //     confirmBtn.innerHTML = 'Delete';
                                // });
                            });

                            // Enhanced Archive Modal dengan loading state
                            function archiveNotification(id) {
                                archiveId = id;
                                const modal = document.getElementById('archiveModal');
                                const content = document.getElementById('archiveModalContent');
                                const confirmBtn = document.getElementById('confirmArchiveBtn');

                                // Reset button state
                                confirmBtn.disabled = false;
                                confirmBtn.innerHTML = 'Archive';
                                confirmBtn.className = 'px-4 py-2 text-sm rounded-lg btn-archive text-white transition-all duration-300';

                                modal.classList.remove('hidden');
                                setTimeout(() => {
                                    content.classList.remove('scale-95', 'opacity-0');
                                    content.classList.add('scale-100', 'opacity-100');
                                }, 50);
                            }

                            // Enhanced Confirm Archive dengan loading dan feedback
                            document.getElementById('confirmArchiveBtn').addEventListener('click', () => {
                                const confirmBtn = document.getElementById('confirmArchiveBtn');

                                // Show loading state
                                confirmBtn.disabled = true;
                                confirmBtn.innerHTML = `
        <div class="loading-spinner"></div>
        <span style="margin-left: 8px;">Mengarsip...</span>
    `;

                                fetch(`/admin/notifications/${archiveId}/archive`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                                'content')
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            showAlert('Notifikasi berhasil diarsipkan!', 'success');
                                            loadNotifications();
                                            closeArchiveModal();
                                        } else {
                                            showAlert('Gagal mengarsipkan notifikasi. Silakan coba lagi.', 'error');
                                            // Reset button
                                            confirmBtn.disabled = false;
                                            confirmBtn.innerHTML = 'Archive';
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        showAlert('Terjadi kesalahan saat mengarsipkan notifikasi.', 'error');
                                        // Reset button
                                        confirmBtn.disabled = false;
                                        confirmBtn.innerHTML = 'Archive';
                                    });
                            });

                            // Enhanced Restore Modal dengan loading state
                            function restoreNotification(id) {
                                restoreId = id;
                                const modal = document.getElementById('restoreModal');
                                const content = document.getElementById('restoreModalContent');
                                const confirmBtn = document.getElementById('confirmRestoreBtn');

                                // Reset button state
                                confirmBtn.disabled = false;
                                confirmBtn.innerHTML = 'Restore';
                                confirmBtn.className = 'px-4 py-2 text-sm rounded-lg btn-restore text-white transition-all duration-300';

                                modal.classList.remove('hidden');
                                setTimeout(() => {
                                    content.classList.remove('scale-95', 'opacity-0');
                                    content.classList.add('scale-100', 'opacity-100');
                                }, 50);
                            }

                            // Enhanced Confirm Restore dengan loading dan feedback
                            document.getElementById('confirmRestoreBtn').addEventListener('click', () => {
                                const confirmBtn = document.getElementById('confirmRestoreBtn');

                                // Show loading state
                                confirmBtn.disabled = true;
                                confirmBtn.innerHTML = `
        <div class="loading-spinner"></div>
        <span style="margin-left: 8px;">Memulihkan...</span>
    `;

                                fetch(`/admin/notifications/${restoreId}/restore`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                                'content')
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            showAlert('Notifikasi berhasil dipulihkan!', 'success');
                                            loadNotifications();
                                            closeRestoreModal();
                                        } else {
                                            showAlert('Gagal memulihkan notifikasi. Silakan coba lagi.', 'error');
                                            // Reset button
                                            confirmBtn.disabled = false;
                                            confirmBtn.innerHTML = 'Restore';
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        showAlert('Terjadi kesalahan saat memulihkan notifikasi.', 'error');
                                        // Reset button
                                        confirmBtn.disabled = false;
                                        confirmBtn.innerHTML = 'Restore';
                                    });
                            });

                            // Enhanced Submit Notification dengan feedback yang lebih baik
                            function submitNotification() {
                                const submitBtn = document.querySelector('button[onclick="submitNotification()"]');
                                if (submitBtn.disabled) {
                                    return;
                                }

                                // Show loading state
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = `
        <div class="loading-spinner"></div>
        <span style="margin-left: 8px;">Mengirim...</span>
    `;

                                const formData = {
                                    title: document.getElementById('name').value,
                                    message: document.getElementById('desc').value,
                                    priority: document.getElementById('desig').value,
                                    target_type: document.getElementById('depart').value,
                                    target_tenant_ids: document.getElementById('depart').value === 'specific' ? selectedTenants : [],
                                    delivery_methods: getSelectedDeliveryMethods()
                                };

                                // Validation
                                if (!formData.title || !formData.message || !formData.priority) {
                                    showAlert('Mohon isi semua field yang wajib diisi!', 'warning');
                                    enableSubmitButton();
                                    return;
                                }

                                if (formData.delivery_methods.length === 0) {
                                    showAlert('Mohon pilih minimal satu metode pengiriman!', 'warning');
                                    enableSubmitButton();
                                    return;
                                }

                                if (formData.target_type === 'specific' && formData.target_tenant_ids.length === 0) {
                                    showAlert('Mohon pilih minimal satu tenant!', 'warning');
                                    enableSubmitButton();
                                    return;
                                }

                                // Submit
                                fetch('/admin/notifications/store', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        },
                                        body: JSON.stringify(formData)
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            showAlert('Notifikasi berhasil dibuat dan dikirim!', 'success');
                                            resetCreateForm();
                                            if (currentTab === 'active') {
                                                loadNotifications();
                                            }
                                        } else {
                                            showAlert('Gagal membuat notifikasi. Periksa input Anda.', 'error');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        showAlert('Terjadi kesalahan saat membuat notifikasi.', 'error');
                                    })
                                    .finally(() => {
                                        enableSubmitButton();
                                    });
                            }

                            function enableSubmitButton() {
                                const submitBtn = document.querySelector('button[onclick="submitNotification()"]');
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = 'Send';
                                }
                            }
                        </script>


                        <script>
                            // Settings dropdown functions with unique names to avoid conflicts
                            function toggleSettingsDropdown() {
                                const content = document.getElementById('settings-dropdown-content');
                                const chevron = document.querySelector('.chevron-settings');

                                content.classList.toggle('hidden');
                                if (chevron) {
                                    chevron.classList.toggle('open');
                                }
                            }

                            function toggleSettingsOption(optionId) {
                                const checkbox = document.getElementById(optionId);
                                if (checkbox) {
                                    checkbox.checked = !checkbox.checked;
                                    updateSettingsSelection();
                                }
                            }

                            function updateSettingsSelection() {
                                const checkboxes = document.querySelectorAll('#settings-dropdown-content input[type="checkbox"]');
                                const selectedOptions = [];

                                checkboxes.forEach(checkbox => {
                                    if (checkbox.checked) {
                                        selectedOptions.push(checkbox.value);
                                    }
                                });

                                const selectedText = document.getElementById('settings-selected-text');
                                if (selectedText) {
                                    if (selectedOptions.length === 0) {
                                        selectedText.textContent = 'Select delivery methods...';
                                    } else {
                                        selectedText.textContent = selectedOptions.join(', ');
                                    }
                                }
                            }

                            function updateToggleText() {
                                const checkbox = document.getElementById('notificationToggle');
                                const statusText = document.getElementById('statusText');

                                if (checkbox && statusText) {
                                    if (checkbox.checked) {
                                        statusText.textContent = 'Enable';
                                        statusText.className = 'line-height-1 font-medium text-md text-primary-600';
                                    } else {
                                        statusText.textContent = 'Disable';
                                        statusText.className = 'line-height-1 font-medium text-md text-gray-600 dark:text-gray-300';
                                    }
                                }
                            }

                            // Close settings dropdown when clicking outside
                            document.addEventListener('click', function(event) {
                                const container = document.querySelector('.dropdown-container-settings');
                                const content = document.getElementById('settings-dropdown-content');
                                const chevron = document.querySelector('.chevron-settings');

                                if (container && !container.contains(event.target)) {
                                    if (content) content.classList.add('hidden');
                                    if (chevron) chevron.classList.remove('open');
                                }
                            });

                            // Initialize on page load
                            document.addEventListener('DOMContentLoaded', function() {
                                updateToggleText();
                            });
                        </script>

                        <style>
                            .form-control {
                                width: 100%;
                                padding: 0.5rem 0.75rem;
                                border: 1px solid #d1d5db;
                                border-radius: 0.5rem;
                                background-color: white;
                                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
                            }

                            .form-control:focus {
                                outline: none;
                                border-color: #3b82f6;
                                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
                            }

                            .form-select {
                                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
                                background-position: right 0.5rem center;
                                background-repeat: no-repeat;
                                background-size: 1.5em 1.5em;
                                padding-right: 2.5rem;
                            }

                            .text-danger-600 {
                                color: #dc2626;
                            }

                            .dropdown-item:hover {
                                background-color: #f3f4f6;
                            }

                            .dark .dropdown-item:hover {
                                background-color: #4b5563;
                            }

                            .hidden {
                                display: none;
                            }

                            .chevron-settings.open {
                                transform: rotate(180deg);
                            }

                            .dark .settings-dropdown {
                                background-color: #374151;
                                border-color: #4b5563;
                            }

                            input[type="checkbox"] {
                                transition: all 0.2s ease;
                            }

                            input[type="checkbox"]:checked {
                                background-color: #3b82f6;
                                border-color: #3b82f6;
                            }

                            input[type="checkbox"]:focus {
                                outline: none;
                                ring: 2px;
                                ring-color: rgba(59, 130, 246, 0.5);
                            }

                            .dark input[type="checkbox"] {
                                background-color: #374151;
                                border-color: #6b7280;
                            }

                            .dark input[type="checkbox"]:checked {
                                background-color: #60a5fa;
                                border-color: #60a5fa;
                            }
                        </style>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Global variables
        let currentPage = 1;
        let currentTab = 'active';
        let searchTerm = '';
        let priorityFilter = '';
        let targetFilter = '';
        let selectedNotifications = [];

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Load default settings
            loadSettings();

            // Load tenants for dropdown
            loadTenants();

            // Load notifications
            loadNotifications();

            // Setup event listeners
            setupEventListeners();

            // Setup form submit
            setupFormSubmit();

            // Load tenants from database
            loadTenantsFromDatabase();
        });

        // Load notification settings
        function loadSettings() {
            fetch('/admin/notifications/get-settings')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateSettingsForm(data.data);
                    }
                })
                .catch(error => console.error('Error loading settings:', error));
        }

        // Populate settings form
        function populateSettingsForm(settings) {
            // Default priority
            document.getElementById('default-priority').value = settings.priority;

            // Default delivery methods
            if (settings.delivery_methods && Array.isArray(settings.delivery_methods)) {
                settings.delivery_methods.forEach(method => {
                    const checkbox = document.getElementById(`settings-${method.toLowerCase().replace(' ', '-')}`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
                updateSettingsSelection();
            }

            // Email settings
            if (settings.email_from) {
                document.getElementById('from-email').value = settings.email_from;
            }
            if (settings.email_footer) {
                document.getElementById('email-footer').value = settings.email_footer;
            }

            // Push settings
            document.getElementById('notificationToggle').checked = settings.push_enabled;
            document.getElementById('displayNotifications').value = settings.display_count;
            updateToggleText();
        }

        // Load tenants untuk dropdown
        function loadTenants() {
            fetch('/admin/notifications/get-tenants')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.tenantsData = data.data;
                        updateTenantDropdown();
                    }
                })
                .catch(error => console.error('Error loading tenants:', error));
        }

        // Update tenant dropdown dengan data dari database
        function updateTenantDropdown() {
            // Update global tenants variable
            window.tenants = window.tenantsData;
        }

        // Load notifications berdasarkan tab aktif
        function loadNotifications(page = 1) {
            const endpoint = currentTab === 'active' ?
                '/admin/notifications/get-all-notifications' :
                '/admin/notifications/get-archived-notifications';

            const params = new URLSearchParams({
                page: page,
                per_page: 10,
                search: searchTerm,
                priority: priorityFilter,
                target: targetFilter // Tambahkan filter target
            });

            console.log('Loading notifications with params:', {
                tab: currentTab,
                page: page,
                search: searchTerm,
                priority: priorityFilter,
                target: targetFilter
            });

            fetch(`${endpoint}?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.data) {
                        populateTable(data.data);
                        updatePagination(data.pagination);
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    showAlert('Error loading notifications', 'error');
                });
        }

        // Populate table dengan data
        function populateTable(notifications) {
            // Gunakan selector yang sesuai dengan tab aktif
            const tbodyId = currentTab === 'active' ? 'tableBody' : 'archivedTableBody';
            const tbody = document.querySelector(`#${tbodyId}`);

            // Jika element tidak ditemukan, coba cari dengan cara lain
            if (!tbody) {
                // Fallback: cari berdasarkan tab yang aktif
                const activeTabContent = document.querySelector(currentTab === 'active' ? '#styled-todoList' :
                    '#styled-recentLead');
                if (activeTabContent) {
                    const fallbackTbody = activeTabContent.querySelector('tbody');
                    if (fallbackTbody) {
                        fallbackTbody.innerHTML = '';
                        populateTableRows(fallbackTbody, notifications);
                        return;
                    }
                }
                console.error(`Table body not found for ${currentTab} tab`);
                return;
            }

            tbody.innerHTML = '';
            populateTableRows(tbody, notifications);
        }

        // Helper function to populate table rows
        function populateTableRows(tbody, notifications) {
            if (notifications.length === 0) {
                tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-8 text-neutral-500">
                    No notifications found
                </td>
            </tr>
        `;
                return;
            }

            notifications.forEach((notif, index) => {
                const row = createTableRow(notif, index);
                tbody.appendChild(row);
            });
        }

        // Create table row
        function createTableRow(notif, index) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-neutral-50 dark:hover:bg-neutral-800';

            const rowNumber = ((currentPage - 1) * 10) + index + 1;
            const checkboxClass = currentTab === 'active' ? 'notification-checkbox' : 'archived-notification-checkbox';

            tr.innerHTML = `
        <td class="px-4 py-3">
            <div class="flex items-center gap-3">
                <div class="form-check style-check flex items-center">
                    <input class="form-check-input rounded border input-form-dark ${checkboxClass}" 
                           type="checkbox" value="${notif.id}" onchange="updateSelectedNotifications()">
                </div>
                <span class="text-sm text-neutral-700 dark:text-neutral-300">${rowNumber}</span>
            </div>
        </td>
        <td class="px-4 py-3">
            <span class="text-sm font-medium text-neutral-900 dark:text-white">${notif.title}</span>
        </td>
        <td class="px-4 py-3">
            <span class="px-3 py-1.5 rounded-full font-medium text-xs ${notif.priority_badge || 'bg-gray-100 text-gray-800'}">
                ${notif.priority}
            </span>
        </td>
        <td class="px-4 py-3">
            <span class="text-sm text-neutral-700 dark:text-neutral-300" title="${notif.message}">
                ${notif.message.length > 50 ? notif.message.substring(0, 50) + '...' : notif.message}
            </span>
        </td>
        <td class="px-4 py-3 text-center">
            <span class="text-xs text-neutral-600 dark:text-neutral-400">
                ${notif.target_audience || 'All Tenants'}
            </span>
        </td>
        <td class="px-4 py-3 text-center">
            <span class="text-xs text-neutral-600 dark:text-neutral-400">
                ${currentTab === 'active' ? (notif.created_at || 'N/A') : (notif.archived_at || 'N/A')}
            </span>
        </td>
        <td class="px-4 py-3 text-center">
            ${getActionButtons(notif)}
        </td>
    `;

            return tr;
        }

        // Get action buttons berdasarkan tab
        function getActionButtons(notif) {
            if (currentTab === 'active') {
                return `
            <div class="flex items-center justify-center gap-2">
                <button onclick="archiveNotification(${notif.id})" 
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-warning-100 text-warning-600 hover:bg-warning-200 transition-colors"
                        title="Archive">
                    <iconify-icon icon="lucide:archive" class="text-sm"></iconify-icon>
                </button>
                <button onclick="deleteNotification(${notif.id})" 
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-danger-100 text-danger-600 hover:bg-danger-200 transition-colors"
                        title="Delete Permanently">
                    <iconify-icon icon="lucide:trash-2" class="text-sm"></iconify-icon>
                </button>
            </div>
        `;
            } else {
                return `
            <div class="flex items-center justify-center gap-2">
                <button onclick="restoreNotification(${notif.id})" 
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-success-100 text-success-600 hover:bg-success-200 transition-colors"
                        title="Restore">
                    <iconify-icon icon="lucide:rotate-ccw" class="text-sm"></iconify-icon>
                </button>
                <button onclick="deleteNotification(${notif.id})" 
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-danger-100 text-danger-600 hover:bg-danger-200 transition-colors"
                        title="Delete Permanently">
                    <iconify-icon icon="lucide:trash-2" class="text-sm"></iconify-icon>
                </button>
            </div>
        `;
            }
        }

        // Reset filters function
        function resetFilters() {
            searchTerm = '';
            priorityFilter = '';
            targetFilter = '';
            currentPage = 1;

            // Reset input values - cek dulu apakah element ada
            const searchInputs = document.querySelectorAll('input[placeholder*="Search"]');
            searchInputs.forEach(input => input.value = '');

            const filters = document.querySelectorAll('select[id*="Filter"]');
            filters.forEach(filter => filter.value = '');

            console.log('Filters reset');
        }

        // Setup tab switching dengan reset filter
        function setupTabSwitching() {
            document.addEventListener('click', function(e) {
                // Active tab button
                if (e.target.closest('[data-tabs-target="#styled-todoList"]')) {
                    console.log('Switching to active tab');
                    currentTab = 'active';
                    resetFilters();
                    loadNotifications();
                }
                // Archived tab button
                if (e.target.closest('[data-tabs-target="#styled-recentLead"]')) {
                    console.log('Switching to archived tab');
                    currentTab = 'archived';
                    resetFilters();
                    loadNotifications();
                }
            });
        }

        // Setup event listeners
        function setupEventListeners() {
            // Tab switching
            setupTabSwitching();

            // Search inputs
            setupSearchInputs();

            // Priority filters
            setupPriorityFilters();

            // Select all checkboxes
            setupSelectAllCheckboxes();
        }

        // Setup search inputs
        function setupSearchInputs() {
            // Active search input
            const activeSearchInput = document.getElementById('searchInput');
            if (activeSearchInput) {
                activeSearchInput.addEventListener('input', function() {
                    clearTimeout(window.searchTimeout);
                    window.searchTimeout = setTimeout(() => {
                        searchTerm = this.value;
                        currentPage = 1;
                        console.log('Active search changed:', searchTerm);
                        loadNotifications();
                    }, 500);
                });
            }

            // Archived search input
            const archivedSearchInput = document.getElementById('archivedSearchInput');
            if (archivedSearchInput) {
                archivedSearchInput.addEventListener('input', function() {
                    clearTimeout(window.archivedSearchTimeout);
                    window.archivedSearchTimeout = setTimeout(() => {
                        searchTerm = this.value;
                        currentPage = 1;
                        console.log('Archived search changed:', searchTerm);
                        loadNotifications();
                    }, 500);
                });
            }
        }

        // Setup priority filters - DIPERBAIKI
        function setupPriorityFilters() {
            // Active tab filters
            const activeStatusFilter = document.getElementById('statusFilter');
            if (activeStatusFilter) {
                activeStatusFilter.addEventListener('change', function() {
                    priorityFilter = this.value;
                    currentPage = 1;
                    console.log('Active priority filter changed:', priorityFilter);
                    loadNotifications();
                });
            }

            // // Archived tab priority filter
            // const archivedStatusFilter = document.getElementById('archivedStatusFilter');
            // if (archivedStatusFilter) {
            //     archivedStatusFilter.addEventListener('change', function() {
            //         priorityFilter = this.value;
            //         currentPage = 1;
            //         console.log('Archived priority filter changed:', priorityFilter);
            //         loadNotifications();
            //     });
            // }

            // PERBAIKAN UTAMA: Archived target filter
            const archivedTargetFilter = document.getElementById('archivedTargetFilter');
            if (archivedTargetFilter) {
                archivedTargetFilter.addEventListener('change', function() {
                    targetFilter = this.value;
                    currentPage = 1;
                    console.log('Archived target filter changed:', targetFilter);
                    loadNotifications();
                });
            } else {
                console.warn('archivedTargetFilter element not found - pastikan ID di HTML benar');
            }
        }

        // Setup select all checkboxes
        function setupSelectAllCheckboxes() {
            const selectAllCheckboxes = document.querySelectorAll('input[id*="selectAll"]');

            selectAllCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const checkboxClass = this.id.includes('archived') ? '.archived-notification-checkbox' :
                        '.notification-checkbox';
                    const targetCheckboxes = document.querySelectorAll(checkboxClass);

                    targetCheckboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateSelectedNotifications();
                });
            });
        }

        // Setup form submit
        function setupFormSubmit() {
            // Settings form submit
            const settingsSubmitBtn = document.querySelector('#notification-password button[type="button"]:last-child');
            if (settingsSubmitBtn) {
                settingsSubmitBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    submitSettings();
                });
            }
        }

        // Submit notification baru
        function submitNotification() {
            // Prevent double submission
            const submitBtn = document.querySelector('button[onclick="submitNotification()"]');
            if (submitBtn.disabled) {
                return;
            }

            // Disable button to prevent double click
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';

            const formData = {
                title: document.getElementById('name').value,
                message: document.getElementById('desc').value,
                priority: document.getElementById('desig').value,
                target_type: document.getElementById('depart').value,
                target_tenant_ids: document.getElementById('depart').value === 'specific' ? selectedTenants : [],
                delivery_methods: getSelectedDeliveryMethods()
            };

            // Validation
            if (!formData.title || !formData.message || !formData.priority) {
                showAlert('Please fill all required fields', 'error');
                enableSubmitButton();
                return;
            }

            if (formData.delivery_methods.length === 0) {
                showAlert('Please select at least one delivery method', 'error');
                enableSubmitButton();
                return;
            }

            if (formData.target_type === 'specific' && formData.target_tenant_ids.length === 0) {
                showAlert('Please select at least one tenant', 'error');
                enableSubmitButton();
                return;
            }

            // Submit
            fetch('/admin/notifications/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    enableSubmitButton();
                    if (data.success) {
                        showAlert('Notification created successfully', 'success');
                        resetCreateForm();
                        if (currentTab === 'active') {
                            loadNotifications();
                        }
                    } else {
                        showAlert('Error creating notification', 'error');
                    }
                })
                .catch(error => {
                    enableSubmitButton();
                    console.error('Error:', error);
                    showAlert('Error creating notification', 'error');
                });
        }

        // Helper function to enable submit button
        function enableSubmitButton() {
            const submitBtn = document.querySelector('button[onclick="submitNotification()"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send';
            }
        }

        // Submit settings
        function submitSettings() {
            const formData = {
                default_priority: document.getElementById('default-priority').value,
                default_delivery_methods: getSelectedSettingsDeliveryMethods(),
                email_from: document.getElementById('from-email').value,
                email_footer: document.getElementById('email-footer').value,
                push_enabled: document.getElementById('notificationToggle').checked,
                dashboard_display_count: parseInt(document.getElementById('displayNotifications').value)
            };

            fetch('/admin/notifications/update-settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Settings updated successfully', 'info');
                    } else {
                        showAlert('Error updating settings', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error updating settings', 'error');
                });
        }

        // Get selected delivery methods dari create form
        function getSelectedDeliveryMethods() {
            const checkboxes = document.querySelectorAll('#dropdown-content input[type="checkbox"]:checked');
            return Array.from(checkboxes).map(cb => cb.value);
        }

        // Get selected delivery methods dari settings form
        function getSelectedSettingsDeliveryMethods() {
            const checkboxes = document.querySelectorAll('#settings-dropdown-content input[type="checkbox"]:checked');
            return Array.from(checkboxes).map(cb => cb.value);
        }





        // Update selected notifications untuk bulk actions
        function updateSelectedNotifications() {
            const checkboxClass = currentTab === 'active' ? '.notification-checkbox' : '.archived-notification-checkbox';
            const selectAllId = currentTab === 'active' ? 'selectAll' : 'archivedSelectAll';

            const checkboxes = document.querySelectorAll(`${checkboxClass}:checked`);
            selectedNotifications = Array.from(checkboxes).map(cb => parseInt(cb.value));

            // Update select all checkbox
            const selectAllCheckbox = document.getElementById(selectAllId);
            const allCheckboxes = document.querySelectorAll(checkboxClass);

            if (selectAllCheckbox && allCheckboxes.length > 0) {
                if (selectedNotifications.length === 0) {
                    selectAllCheckbox.indeterminate = false;
                    selectAllCheckbox.checked = false;
                } else if (selectedNotifications.length === allCheckboxes.length) {
                    selectAllCheckbox.indeterminate = false;
                    selectAllCheckbox.checked = true;
                } else {
                    selectAllCheckbox.indeterminate = true;
                }
            }
        }

        // Reset create form
        function resetCreateForm() {
            document.getElementById('name').value = '';
            document.getElementById('desc').value = '';
            document.getElementById('depart').value = 'all';
            document.getElementById('desig').value = 'Normal';

            // Reset delivery methods
            const deliveryCheckboxes = document.querySelectorAll('#dropdown-content input[type="checkbox"]');
            deliveryCheckboxes.forEach(cb => cb.checked = false);
            updateSelection();

            // Reset tenant selection
            selectedTenants = [];
            document.getElementById('tenantSelection').style.display = 'none';
        }

        // Update pagination
        function updatePagination(pagination) {
            const paginationInfoId = currentTab === 'active' ? 'paginationInfo' : 'archivedPaginationInfo';
            const pageNumbersId = currentTab === 'active' ? 'pageNumbers' : 'archivedPageNumbers';

            let paginationInfo = document.getElementById(paginationInfoId);
            let pageNumbers = document.getElementById(pageNumbersId);

            // Fallback jika tidak menemukan element berdasarkan ID
            if (!paginationInfo || !pageNumbers) {
                const activeTabContent = document.querySelector(currentTab === 'active' ? '#styled-todoList' :
                    '#styled-recentLead');
                if (activeTabContent) {
                    if (!paginationInfo) {
                        paginationInfo = activeTabContent.querySelector(
                            'span[id*="paginationInfo"], span.text-neutral-600');
                    }
                    if (!pageNumbers) {
                        pageNumbers = activeTabContent.querySelector('div[id*="pageNumbers"], div.flex.gap-1');
                    }
                }
            }

            // Update info
            if (paginationInfo) {
                paginationInfo.textContent =
                    `Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total} entries`;
            }

            // Update page numbers
            if (pageNumbers) {
                pageNumbers.innerHTML = '';

                // Previous button
                if (pagination.current_page > 1) {
                    const prevBtn = createPageButton('Previous', pagination.current_page - 1);
                    pageNumbers.appendChild(prevBtn);
                }

                // Page numbers
                const startPage = Math.max(1, pagination.current_page - 2);
                const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

                for (let i = startPage; i <= endPage; i++) {
                    const pageBtn = createPageButton(i, i, i === pagination.current_page);
                    pageNumbers.appendChild(pageBtn);
                }

                // Next button
                if (pagination.current_page < pagination.last_page) {
                    const nextBtn = createPageButton('Next', pagination.current_page + 1);
                    pageNumbers.appendChild(nextBtn);
                }
            }
        }

        // Create page button
        function createPageButton(text, page, isActive = false) {
            const button = document.createElement('button');
            button.textContent = text;
            button.className = `px-3 py-1 rounded-lg text-sm transition-colors ${
        isActive 
            ? 'bg-primary-600 text-white' 
            : 'bg-white text-neutral-700 border border-neutral-200 hover:bg-neutral-50'
    }`;

            if (!isActive) {
                button.addEventListener('click', () => {
                    currentPage = page;
                    loadNotifications(page);
                });
            }

            return button;
        }


        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab');

            if (activeTab === 'all-notifications') {
                document.getElementById('change-password-tab').click(); // ID tab "All Notifications"
            }
        });


        // Show alert
        function showAlert(message, type = 'info') {
            // Remove existing notifications
            document.querySelectorAll('.notification-toast').forEach(n => n.remove());

            // Create toast notification
            const toast = document.createElement('div');
            toast.className = 'notification-toast';
            toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        width: 380px;
        max-width: calc(100vw - 40px);
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        pointer-events: auto;
    `;

            const colors = {
                success: {
                    bg: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                    shadow: '0 10px 25px rgba(16, 185, 129, 0.3)',
                    icon: 'ph:check-circle-fill'
                },
                error: {
                    bg: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                    shadow: '0 10px 25px rgba(239, 68, 68, 0.3)',
                    icon: 'ph:warning-circle-fill'
                },
                delete: {
                    bg: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                    shadow: '0 10px 25px rgba(239, 68, 68, 0.3)',
                    icon: 'ph:warning-circle-fill'
                },
                info: {
                    bg: 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
                    shadow: '0 10px 25px rgba(59, 130, 246, 0.3)',
                    icon: 'ph:info-fill'
                }
            };

            const config = colors[type] || colors.info;

            toast.innerHTML = `
        <div style="
            background: ${config.bg};
            border-radius: 12px;
            box-shadow: ${config.shadow};
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        ">
            <div style="padding: 16px;">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="
                        width: 32px;
                        height: 32px;
                        background: rgba(255, 255, 255, 0.2);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        flex-shrink: 0;
                        margin-top: 2px;
                    ">
                        <iconify-icon icon="${config.icon}" style="
                            font-size: 18px;
                            color: white;
                        "></iconify-icon>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <h4 style="
                            color: white;
                            font-weight: 600;
                            font-size: 14px;
                            margin: 0 0 4px 0;
                            line-height: 1.2;
                        ">${type === 'success' ? 'Success!' : type === 'delete' ? 'Deleted!' : type === 'error' ? 'Error!' : 'Info!'}</h4>
                        <p style="
                            color: rgba(255, 255, 255, 0.9);
                            font-size: 13px;
                            margin: 0;
                            line-height: 1.4;
                        ">${message}</p>
                    </div>
                    <button onclick="this.closest('.notification-toast').remove()" style="
                        background: rgba(255, 255, 255, 0.1);
                        border: none;
                        color: rgba(255, 255, 255, 0.7);
                        width: 24px;
                        height: 24px;
                        border-radius: 4px;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: all 0.2s;
                        flex-shrink: 0;
                    " onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.color='white'" 
                       onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.color='rgba(255,255,255,0.7)'">
×
                    </button>
                </div>
            </div>
            <div style="
                height: 3px;
                background: rgba(255, 255, 255, 0.3);
            ">
                <div class="notification-progress" style="
                    height: 100%;
                    background: rgba(255, 255, 255, 0.8);
                    width: 100%;
                    transition: width 4s linear;
                "></div>
            </div>
        </div>
    `;

            document.body.appendChild(toast);

            // Trigger slide-in animation
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
                toast.style.opacity = '1';
            }, 10);

            // Start progress bar animation
            const progressBar = toast.querySelector('.notification-progress');
            if (progressBar) {
                setTimeout(() => {
                    progressBar.style.width = '0%';
                }, 100);
            }

            // Auto remove after 3 seconds
            const timeout = type === 'error' ? 6000 : type === 'delete' ? 4500 : 4500;
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.style.transform = 'translateX(100%)';
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.remove();
                        }
                    }, 400);
                }
            }, timeout);
        }
        // Load tenants dari database
        function loadTenantsFromDatabase() {
            console.log('Loading tenants from database...');

            fetch('/admin/notifications/get-tenants')
                .then(response => response.json())
                .then(data => {
                    console.log('Tenants loaded:', data);
                    if (data.success) {
                        // Replace global tenants variable dengan data dari database
                        window.tenants = data.data;
                        console.log('Tenants set to window:', window.tenants);
                    } else {
                        console.error('Failed to load tenants:', data);
                        // Fallback ke empty array
                        window.tenants = [];
                    }
                })
                .catch(error => {
                    console.error('Error loading tenants:', error);
                    // Fallback ke empty array
                    window.tenants = [];
                });
        }

        // Update toggleTenantList function
        function toggleTenantList() {
            const select = document.getElementById('depart');
            const tenantSelection = document.getElementById('tenantSelection');

            if (select.value === 'specific') {
                tenantSelection.style.display = 'block';

                // Pastikan tenants sudah load sebelum render
                if (window.tenants && window.tenants.length > 0) {
                    renderTenantList(window.tenants);
                } else {
                    // Reload tenants jika belum ada data
                    loadTenantsFromDatabase();
                    setTimeout(() => {
                        if (window.tenants) {
                            renderTenantList(window.tenants);
                        }
                    }, 1000);
                }
            } else {
                tenantSelection.style.display = 'none';
                selectedTenants = [];
            }
        }

        // Update searchTenants function
        function searchTenants() {
            const searchTerm = document.getElementById('tenantSearch').value.toLowerCase();

            if (!window.tenants) {
                console.log('Tenants not loaded yet, loading...');
                loadTenantsFromDatabase();
                return;
            }

            const filteredTenants = window.tenants.filter(tenant =>
                tenant.name.toLowerCase().includes(searchTerm) ||
                tenant.email.toLowerCase().includes(searchTerm)
            );

            console.log('Filtered tenants:', filteredTenants);
            renderTenantList(filteredTenants);
        }

        // Update renderTenantList untuk data dari database
        function renderTenantList(tenantData) {
            const tenantList = document.getElementById('tenantList');

            if (!tenantList) {
                console.error('Tenant list element not found');
                return;
            }

            tenantList.innerHTML = '';

            if (!tenantData || tenantData.length === 0) {
                tenantList.innerHTML = '<p class="text-gray-500 text-sm p-2">No tenants found</p>';
                return;
            }

            tenantData.forEach(tenant => {
                const isSelected = selectedTenants.includes(tenant.id);
                const tenantItem = document.createElement('div');
                tenantItem.className =
                    `flex items-center p-4 hover:bg-gray-50 rounded-lg cursor-pointer border transition-colors mb-3 ${isSelected ? 'bg-blue-50 border-blue-200' : 'border-gray-200'}`;
                tenantItem.onclick = () => toggleTenantSelection(tenant.id);

                // Status badge sesuai dengan data dari database
                const statusBadge = tenant.status === 'Active' ?
                    `<span class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-3 py-1.5 rounded-full font-medium text-xs sm:text-sm">Active</span>` :
                    `<span class="bg-danger-100 text-danger-600 dark:bg-danger-600/25 dark:text-danger-400 px-3 py-1.5 rounded-full font-medium text-xs sm:text-sm">Inactive</span>`;

                tenantItem.innerHTML = `
            <input type="checkbox" 
                id="tenant_${tenant.id}" 
                ${isSelected ? 'checked' : ''}
                class="h-4 w-4 text-blue-600 rounded flex-shrink-0"
                onchange="toggleTenantSelection(${tenant.id})"
                onclick="event.stopPropagation();">
            <div class="flex-1" style="margin-left: 20px;">
                <div class="font-medium text-gray-900 dark:text-white">${tenant.name}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">${tenant.email}</div>
            </div>
            <div class="flex-shrink-0" style="margin-left: 20px;">
                ${statusBadge}
            </div>
        `;

                tenantList.appendChild(tenantItem);
            });

            console.log('Tenant list rendered with', tenantData.length, 'items');
        }

        // Toggle tenant selection
        function toggleTenantSelection(tenantId) {
            const index = selectedTenants.indexOf(tenantId);
            if (index > -1) {
                selectedTenants.splice(index, 1);
            } else {
                selectedTenants.push(tenantId);
            }

            // Re-render to update checkbox states and styling
            const searchTerm = document.getElementById('tenantSearch').value.toLowerCase();
            const filteredTenants = window.tenants.filter(tenant =>
                tenant.name.toLowerCase().includes(searchTerm) ||
                tenant.email.toLowerCase().includes(searchTerm)
            );
            renderTenantList(filteredTenants);

            console.log('Selected tenants:', selectedTenants);
        }

        // DEBUGGING: Function untuk cek apakah filter element ada
        function checkFilterElements() {
            console.log('Checking filter elements...');
            console.log('archivedTargetFilter:', document.getElementById('archivedTargetFilter'));
            console.log('archivedStatusFilter:', document.getElementById('archivedStatusFilter'));
            console.log('archivedSearchInput:', document.getElementById('archivedSearchInput'));
            console.log('Active statusFilter:', document.getElementById('statusFilter'));
            console.log('Active searchInput:', document.getElementById('searchInput'));
        }

        // Call check function after page load untuk debugging
        setTimeout(checkFilterElements, 1000)

        function setupPriorityFilters() {
            // Filter untuk tab Active
            const activeStatusFilter = document.getElementById('statusFilter');
            if (activeStatusFilter) {
                activeStatusFilter.addEventListener('change', function() {
                    priorityFilter = this.value;
                    currentPage = 1;
                    console.log('Active priority filter changed:', priorityFilter);
                    loadNotifications();
                });
            }

            // Filter untuk tab Archived
            const archivedTargetFilter = document.getElementById('archivedTargetFilter');
            if (archivedTargetFilter) {
                console.log('archivedTargetFilter ditemukan:', archivedTargetFilter); // Debugging
                archivedTargetFilter.addEventListener('change', function() {
                    targetFilter = this.value;
                    currentPage = 1;
                    console.log('Archived target filter changed:', targetFilter); // Debugging
                    loadNotifications();
                });
            } else {
                console.warn('archivedTargetFilter tidak ditemukan - pastikan ID di HTML benar');
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab');

            if (activeTab === 'all-notifications') {
                document.getElementById('change-password-tab').click(); // ID tab "All Notifications"
            }
        });
    </script>
@endsection
