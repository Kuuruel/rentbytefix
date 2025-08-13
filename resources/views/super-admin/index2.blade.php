{{-- resources/views/super-admin/index2.blade.php --}}
@extends('layout.layout')

@php
    $title='Tenants Management';
    $subTitle = 'Manage Tenants';
@endphp

@section('content')
<div class="grid grid-cols-12">
    <div class="col-span-12">
        <div class="card h-full p-0 rounded-xl border-0 overflow-hidden">
            <div class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6 flex items-center flex-wrap gap-3 justify-between">
                <div class="flex items-center flex-wrap gap-3">
                    <span class="text-base font-medium text-secondary-light mb-0">Show</span>
                    <select id="perPageSelect" class="form-select form-select-sm w-auto dark:bg-neutral-600 dark:text-white border-neutral-200 dark:border-neutral-500 rounded-lg">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                    </select>
                    <form class="navbar-search" onsubmit="return false;">
                        <input id="searchInput" type="text" class="bg-white dark:bg-neutral-700 h-10 w-auto" name="search" placeholder="Search tenants...">
                        <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                    </form>
                    <select id="statusFilter" class="form-select form-select-sm w-auto dark:bg-neutral-600 dark:text-white border-neutral-200 dark:border-neutral-500 rounded-lg">
                        <option value="">All Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <button id="btnOpenCreate" class="btn btn-primary text-sm btn-sm px-3 py-3 rounded-lg flex items-center gap-2">
                    <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                    Add New Tenant
                </button>
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

                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table sm-table mb-0">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <div class="flex items-center gap-10">
                                        <div class="form-check style-check flex items-center">
                                            <input class="form-check-input rounded border input-form-dark" type="checkbox" name="checkbox" id="selectAll">
                                        </div>
                                        S.L
                                    </div>
                                </th>
                                <th scope="col">Join Date</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between flex-wrap gap-2 mt-6">
                    <span id="paginationInfo">Showing 0 to 0 of 0 entries</span>
                    <ul class="pagination flex flex-wrap items-center gap-2 justify-center">
                        <li class="page-item">
                            <button id="prevBtn" class="page-link bg-neutral-300 dark:bg-neutral-600 text-secondary-light font-semibold rounded-lg border-0 flex items-center justify-center h-8 w-8 text-base">
                                <iconify-icon icon="ep:d-arrow-left"></iconify-icon>
                            </button>
                        </li>
                        <div id="pageNumbers" class="flex gap-1"></div>
                        <li class="page-item">
                            <button id="nextBtn" class="page-link bg-neutral-300 dark:bg-neutral-600 text-secondary-light font-semibold rounded-lg border-0 flex items-center justify-center h-8 w-8 text-base">
                                <iconify-icon icon="ep:d-arrow-right"></iconify-icon>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Create/Edit --}}
<div id="modalBackdrop" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="bg-white dark:bg-neutral-700 rounded-xl w-full max-w-lg mx-4 shadow-lg z-10 overflow-hidden border border-neutral-200 dark:border-neutral-600">
        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-600 flex justify-between items-center">
            <h3 id="modalTitle" class="text-lg font-semibold text-neutral-900 dark:text-white">Add Tenant</h3>
            <button id="closeModalBtn" class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 text-xl">&times;</button>
        </div>

        <form id="tenantForm" class="px-6 py-6 space-y-4">
            <input type="hidden" id="formId" value="">
            <input type="hidden" id="formMethod" value="POST">
            
            <div id="errorMessages" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"></div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Tenant Name *</label>
                    <input id="formName" type="text" required 
                           placeholder="Enter tenant name"
                           class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Email Address *</label>
                    <input id="formEmail" type="email" required 
                           placeholder="Enter email address"
                           class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">
                        Password <span class="text-xs text-neutral-400" id="passwordHint">*</span>
                    </label>
                    <input id="formPassword" type="password" 
                           placeholder="Enter password"
                           class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500" 
                           autocomplete="new-password">
                </div>

                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Status *</label>
                    <select id="formStatus" required class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Notes</label>
                    <textarea id="formNote" rows="3" 
                              placeholder="Additional notes (optional)"
                              class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" id="formCancel" class="px-4 py-2 rounded bg-neutral-200 dark:bg-neutral-600 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-300 dark:hover:bg-neutral-500">Cancel</button>
                <button type="submit" id="formSubmit" class="btn btn-primary px-4 py-2 rounded text-sm">
                    <span class="submit-text">Add Tenant</span>
                    <span class="submit-loading hidden">
                        <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Delete --}}
<div id="deleteBackdrop" class="fixed inset-0 z-40 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="bg-white dark:bg-neutral-700 rounded-xl w-full max-w-md mx-4 shadow-lg z-10 overflow-hidden border border-neutral-200 dark:border-neutral-600">
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

<script>
    window.tenantsData = @json($tenants);
</script>
<script src="{{ asset('assets/js/tenantList.js') }}"></script>
@endsection