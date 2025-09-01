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
                                                    <div class="dropdown-item" onclick="toggleOption('email')"
                                                        style="padding: 0.5rem 0.75rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                                        <input type="checkbox" id="email" value="Email"
                                                            onchange="updateSelection()"
                                                            style="border-radius: 4px; border: 1px solid #d1d5db;">
                                                        <label for="email" style="cursor: pointer;">Email</label>
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
                                                                    <select id="targetFilter"
                                                                        class="form-select form-select-sm w-auto">
                                                                        <option value="">Filter by Target</option>
                                                                        <option value="all">All Tenants</option>
                                                                        <option value="specific">Specific Tenants</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="card-body p-6">
                                                                <div id="loadingSpinner" class="hidden text-center py-4">
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
                                                <div class="dropdown-item"
                                                    onclick="toggleSettingsOption('settings-email')"
                                                    style="padding: 0.5rem 0.75rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                                    <input type="checkbox" id="settings-email" value="Email"
                                                        onchange="updateSettingsSelection()"
                                                        style="border-radius: 4px; border: 1px solid #d1d5db;">
                                                    <label for="settings-email" style="cursor: pointer;">Email</label>
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
                priority: priorityFilter
            });

            showLoading();

            fetch(`${endpoint}?${params}`)
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.data) {
                        populateTable(data.data);
                        updatePagination(data.pagination);
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error loading notifications:', error);
                    showAlert('Error loading notifications', 'error');
                });
        }

        // Populate table dengan data
        function populateTable(notifications) {
            const tbody = document.querySelector('#tableBody');
            tbody.innerHTML = '';

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

            tr.innerHTML = `
        <td class="px-4 py-3">
            <div class="flex items-center gap-3">
                <div class="form-check style-check flex items-center">
                    <input class="form-check-input rounded border input-form-dark notification-checkbox" 
                           type="checkbox" value="${notif.id}" onchange="updateSelectedNotifications()">
                </div>
                <span class="text-sm text-neutral-700 dark:text-neutral-300">${rowNumber}</span>
            </div>
        </td>
        <td class="px-4 py-3">
            <span class="text-sm font-medium text-neutral-900 dark:text-white">${notif.title}</span>
        </td>
        <td class="px-4 py-3">
            <span class="px-3 py-1.5 rounded-full font-medium text-xs ${notif.priority_badge}">
                ${notif.priority}
            </span>
        </td>
        <td class="px-4 py-3">
            <span class="text-sm text-neutral-700 dark:text-neutral-300" title="${notif.message}">
                ${notif.message}
            </span>
        </td>
        <td class="px-4 py-3 text-center">
            <span class="text-xs text-neutral-600 dark:text-neutral-400">
                ${notif.target_audience}
            </span>
        </td>
        <td class="px-4 py-3 text-center">
            <span class="text-xs text-neutral-600 dark:text-neutral-400">
                ${currentTab === 'active' ? notif.created_at : notif.archived_at}
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

        // Setup event listeners
        function setupEventListeners() {
            // Tab switching
            document.addEventListener('click', function(e) {
                if (e.target.closest('[data-tabs-target="#styled-todoList"]')) {
                    currentTab = 'active';
                    currentPage = 1;
                    loadNotifications();
                }
                if (e.target.closest('[data-tabs-target="#styled-recentLead"]')) {
                    currentTab = 'archived';
                    currentPage = 1;
                    loadNotifications();
                }
            });

            // Search input
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        searchTerm = this.value;
                        currentPage = 1;
                        loadNotifications();
                    }, 500);
                });
            }

            // Priority filter
            const prioritySelect = document.getElementById('statusFilter');
            if (prioritySelect) {
                prioritySelect.addEventListener('change', function() {
                    priorityFilter = this.value;
                    currentPage = 1;
                    loadNotifications();
                });
            }

            // Select all checkbox
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.notification-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateSelectedNotifications();
                });
            }
        }

        // Setup form submit
        function setupFormSubmit() {
            // Create notification form submit
            const createForm = document.querySelector('form[action="#"]');
            if (createForm) {
                const submitBtn = createForm.querySelector('button[type="button"]:last-child');
                if (submitBtn) {
                    submitBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        submitNotification();
                    });
                }
            }

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
                return;
            }

            if (formData.delivery_methods.length === 0) {
                showAlert('Please select at least one delivery method', 'error');
                return;
            }

            if (formData.target_type === 'specific' && formData.target_tenant_ids.length === 0) {
                showAlert('Please select at least one tenant', 'error');
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
                        showAlert('Notification created successfully', 'success');
                        resetCreateForm();
                        loadNotifications(); // Reload table
                    } else {
                        showAlert('Error creating notification', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error creating notification', 'error');
                });
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
                        showAlert('Settings updated successfully', 'success');
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

        // Archive notification
        function archiveNotification(id) {
            if (confirm('Are you sure you want to archive this notification?')) {
                fetch(`/admin/notifications/${id}/archive`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                    });
            }
        }

        // Restore notification
        function restoreNotification(id) {
            if (confirm('Are you sure you want to restore this notification?')) {
                fetch(`/admin/notifications/${id}/restore`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('Notification restored successfully', 'success');
                            loadNotifications();
                        } else {
                            showAlert('Error restoring notification', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('Error restoring notification', 'error');
                    });
            }
        }

        // Delete notification permanent
        function deleteNotification(id) {
            if (confirm('Are you sure you want to delete this notification permanently? This action cannot be undone.')) {
                fetch(`/admin/notifications/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('Notification deleted permanently', 'success');
                            loadNotifications();
                        } else {
                            showAlert('Error deleting notification', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('Error deleting notification', 'error');
                    });
            }
        }

        // Update selected notifications untuk bulk actions
        function updateSelectedNotifications() {
            const checkboxes = document.querySelectorAll('.notification-checkbox:checked');
            selectedNotifications = Array.from(checkboxes).map(cb => parseInt(cb.value));

            // Update select all checkbox
            const selectAllCheckbox = document.getElementById('selectAll');
            const allCheckboxes = document.querySelectorAll('.notification-checkbox');

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
            const paginationInfo = document.getElementById('paginationInfo');
            const pageNumbers = document.getElementById('pageNumbers');

            // Update info
            paginationInfo.textContent =
                `Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total} entries`;

            // Update page numbers
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

        // Show/hide loading
        function showLoading() {
            const spinner = document.getElementById('loadingSpinner');
            if (spinner) {
                spinner.classList.remove('hidden');
            }
        }

        function hideLoading() {
            const spinner = document.getElementById('loadingSpinner');
            if (spinner) {
                spinner.classList.add('hidden');
            }
        }

        // Show alert
        function showAlert(message, type = 'info') {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
            toast.textContent = message;

            document.body.appendChild(toast);

            // Auto remove after 3 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }

        // Replace script tenants hardcoded dengan ini:

        // Load tenants dari database saat halaman load
        document.addEventListener('DOMContentLoaded', function() {
            loadTenantsFromDatabase();
        });

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

        // Remove hardcoded tenants array - sekarang pakai dari database
        // Delete bagian const tenants = [...] yang hardcoded
    </script>
@endsection
