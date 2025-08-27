{{-- resources/views/super-admin/index2.blade.php --}}
@extends('layout.layout')

@php
    $title = 'Tenants Management';
    $subTitle = 'Manage Tenants';
@endphp

@section('content')
    <div class="grid grid-cols-12">
        <div class="col-span-12">
            <div class="card h-full p-0 rounded-xl border-0 overflow-hidden">
                <div
                    class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6 flex items-center flex-wrap gap-3 justify-between">
                    <div class="flex items-center flex-wrap gap-3">
                        <span class="text-base font-medium text-secondary-light mb-0">Show</span>
                        <select id="perPageSelect"
                            class="form-select form-select-sm w-auto dark:bg-neutral-600 dark:text-white border-neutral-200 dark:border-neutral-500 rounded-lg">
                            <option value="5" selected>5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                        </select>
                        <form class="navbar-search" onsubmit="return false;">
                            <input id="searchInput" type="text" class="bg-white dark:bg-neutral-700 h-10 w-auto"
                                name="search" placeholder="Search tenants...">
                            <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                        </form>
                        <select id="statusFilter"
                            class=" form-select form-select-sm w-auto dark:bg-neutral-600 dark:text-white border-neutral-200 dark:border-neutral-500 rounded-lg">
                            <option value="">Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <button id="btnOpenCreate"
                        class="btn btn-primary text-sm btn-sm px-3 py-3 rounded-lg flex items-center gap-2">
                        <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                        Add New Tenant
                    </button>
                </div>

                <div class="card-body p-6">
                    <div id="loadingSpinner" class="hidden text-center py-4">
                        <div
                            class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-indigo-500">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Loading...
                        </div>
                    </div>

                    <div class="table-responsive scroll-sm overflow-x-auto">
                        <table class="table bordered-table sm-table mb-0 w-full">
                            <thead class="bg-neutral-50 dark:bg-neutral-800">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left w-24">
                                        <div class="flex items-center gap-3">
                                            <div class="form-check style-check flex items-center">
                                                <input class="form-check-input rounded border input-form-dark"
                                                    type="checkbox" name="checkbox" id="selectAll">
                                            </div>
                                            <span
                                                class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">No.</span>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left w-32">
                                        <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Join
                                            Date</span>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left min-w-[200px]">
                                        <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Tenant
                                            Info</span>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left min-w-[180px]">
                                        <span
                                            class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Email</span>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-center w-28">
                                        <span
                                            class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Status</span>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-center w-32">
                                        <span
                                            class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tableBody" class="divide-y divide-neutral-200 dark:divide-neutral-700">
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between flex-wrap gap-2 mt-6">
                        <span id="paginationInfo" class="text-sm text-neutral-600 dark:text-neutral-400">Showing 0 to 0 of 0
                            entries</span>
                        <ul id="pageNumbers" class="pagination flex flex-wrap items-center gap-2 justify-center">
                            <div id="pageNumbers" class="flex gap-1"></div>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalBackdrop" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/60"></div>
        <div class="bg-white dark:bg-neutral-700 rounded-xl mx-4 shadow-lg z-10 overflow-hidden border detailStatusborder-neutral-200 dark:border-neutral-600"
            style="width: 42rem !important; max-width: 90vw !important;">

            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-600 flex justify-between items-center">
                <h3 id="modalTitle" class="text-lg font-semibold text-neutral-900 dark:text-white">Add Tenant</h3>
                <button id="closeModalBtn"
                    class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 text-xl">&times;</button>
            </div>

            <form id="tenantForm" class="px-6 py-6 space-y-4">
                <input type="hidden" id="formId" value="">
                <input type="hidden" id="formMethod" value="POST">

                <div id="errorMessages"
                    class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"></div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Tenant Name
                            *</label>
                        <input id="formName" type="text" required placeholder="Enter tenant name"
                            class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Email Address
                            *</label>
                        <input id="formEmail" type="email" required placeholder="Enter email address"
                            class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">
                            Password <span class="text-xs text-neutral-400" id="passwordHint">*</span>
                        </label>
                        <input id="formPassword" type="password" placeholder="Enter password"
                            class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            autocomplete="new-password">
                    </div>

                    <div>
                        <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Country
                            *</label>
                        <select id="formCountry" required
                            class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">Select Country</option>
                            <option value="Afghanistan">Afghanistan</option>
                            <option value="Albania">Albania</option>
                            <option value="Algeria">Algeria</option>
                            <option value="Argentina">Argentina</option>
                            <option value="Australia">Australia</option>
                            <option value="Austria">Austria</option>
                            <option value="Belgium">Belgium</option>
                            <option value="Brazil">Brazil</option>
                            <option value="Canada">Canada</option>
                            <option value="China">China</option>
                            <option value="Denmark">Denmark</option>
                            <option value="Egypt">Egypt</option>
                            <option value="Finland">Finland</option>
                            <option value="France">France</option>
                            <option value="Germany">Germany</option>
                            <option value="Indonesia">Indonesia</option>
                            <option value="Italy">Italy</option>
                            <option value="Japan">Japan</option>
                            <option value="Malaysia">Malaysia</option>
                            <option value="Netherlands">Netherlands</option>
                            <option value="Norway">Norway</option>
                            <option value="Philippines">Philippines</option>
                            <option value="Singapore">Singapore</option>
                            <option value="South Korea">South Korea</option>
                            <option value="Spain">Spain</option>
                            <option value="Sweden">Sweden</option>
                            <option value="Switzerland">Switzerland</option>
                            <option value="Thailand">Thailand</option>
                            <option value="United Kingdom">United Kingdom</option>
                            <option value="United States">United States</option>
                            <option value="Vietnam">Vietnam</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Status
                            *</label>
                        <select id="formStatus" required
                            class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Notes</label>
                        <textarea id="formNote" rows="3" placeholder="Additional notes (optional)"
                            class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" id="formCancel"
                            class="px-4 py-2 rounded-lg  bg-neutral-200 dark:bg-neutral-600 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-300 dark:hover:bg-neutral-500">Cancel</button>
                        <button type="submit" id="formSubmit" class="btn btn-primary px-4 py-2 rounded-lg text-sm">
                            <span class="submit-text">Add Tenant</span>
                            <span class="submit-loading hidden">
                                <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="detailsBackdrop" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/60"></div>
        <div class="bg-white dark:bg-neutral-700 rounded-xl mx-4 shadow-xl z-10 overflow-hidden border border-neutral-200 dark:border-neutral-600"
            style="width: 36rem !important; max-width: 90vw !important;">

            <div
                class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-600 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-neutral-800 dark:to-neutral-700">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-white flex items-center gap-2">
                        <iconify-icon icon="ph:user-circle"
                            class="text-2xl text-blue-600 dark:text-blue-400"></iconify-icon>
                        Tenant Details
                    </h3>
                    <button id="closeDetailsBtn"
                        class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 text-xl">&times;</button>
                </div>
            </div>

            <div class="px-6 py-6">
                <div id="tenantDetails" class="space-y-6">
                    <div class="flex items-center gap-4 pb-4 border-b border-neutral-200 dark:border-neutral-600">
                        <img id="detailAvatar" src="" alt="Avatar"
                            class="w-16 h-16 rounded-full object-cover border-4 border-blue-100 dark:border-blue-900 shadow-md">
                        <div>
                            <h4 id="detailName" class="text-xl font-bold text-neutral-900 dark:text-white"></h4>
                            <p id="detailEmail" class="text-sm text-neutral-600 dark:text-neutral-400"></p>
                            <div id="detailStatus" class="mt-1"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <iconify-icon icon="ph:calendar"
                                    class="text-green-600 dark:text-green-400"></iconify-icon>
                                <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Join Date</span>
                            </div>
                            <p id="detailJoinDate" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                        </div>

                        <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <iconify-icon icon="ph:identification-badge"
                                    class="text-purple-600 dark:text-purple-400"></iconify-icon>
                                <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Tenant ID</span>
                            </div>
                            <p id="detailId" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                        </div>

                        <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <iconify-icon icon="ph:globe" class="text-blue-600 dark:text-blue-400"></iconify-icon>
                                <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Country</span>
                            </div>
                            <p id="detailCountry" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <iconify-icon icon="ph:user-gear" class="text-indigo-600 dark:text-indigo-400"></iconify-icon>
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Created By</span>
                        </div>
                        <p id="detailCreator" class="text-sm text-neutral-700 dark:text-neutral-300"></p>
                    </div>

                    <div id="detailNotesSection" class="hidden">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <iconify-icon icon="ph:note" class="text-yellow-600 dark:text-yellow-400"></iconify-icon>
                                <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Notes</span>
                            </div>
                            <p id="detailNotes" class="text-sm text-neutral-700 dark:text-neutral-300"></p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-neutral-200 dark:border-neutral-600 mt-6">
                    <button id="closeDetailsFooterBtn"
                        class="px-6 py-2   bg-danger-600 hover:bg-danger-700 text-white rounded-lg transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteBackdrop" class="fixed inset-0 z-40 hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/60"></div>
        <div
            class="bg-white dark:bg-neutral-700 rounded-xl w-96 max-w-[90vw] mx-4 shadow-lg z-10 overflow-hidden border border-neutral-200 dark:border-neutral-600">
            <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-600">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Confirm Delete</h3>
            </div>

            <div class="px-6 py-6">
                <p class="text-sm text-neutral-600 dark:text-neutral-300">Are you sure you want to delete <span
                        id="deleteName" class="font-semibold text-neutral-900 dark:text-white"></span>? This action cannot
                    be undone.</p>

                <div class="flex justify-end gap-3 mt-6">
                    <button id="deleteCancel"
                        class="px-4 py-2 rounded-lg bg-neutral-200 dark:bg-neutral-600 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-300 dark:hover:bg-neutral-500">Cancel</button>
                    <button id="deleteConfirm"
                        class="px-4 py-2 rounded-lg bg-danger-600 text-white text-sm hover:bg-danger-700">
                        <span class="delete-text">Delete</span>
                        <span class="delete-loading hidden">Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="bulkDeleteModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/60"></div>
        <div
            class="bg-white dark:bg-neutral-700 rounded-xl w-[28rem] max-w-[90vw] mx-4 shadow-xl z-10 overflow-hidden border border-red-200 dark:border-red-800">
            <div class="px-6 py-4 border-b border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-800/40 rounded-full flex items-center justify-center">
                        <iconify-icon icon="ph:trash" class="text-red-600 dark:text-red-400 text-lg"></iconify-icon>
                    </div>
                    <h3 class="text-lg font-semibold text-red-900 dark:text-red-100">Delete Multiple Tenants</h3>
                </div>
            </div>

            <div class="px-6 py-6">
                <div class="text-center mb-6">
                    <div
                        class="w-16 h-16 bg-red-100 dark:bg-red-800/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <iconify-icon icon="ph:warning-circle"
                            class="text-red-600 dark:text-red-400 text-2xl"></iconify-icon>
                    </div>
                    <h4 class="text-lg font-semibold text-neutral-900 dark:text-white mb-2">Are you absolutely sure?</h4>
                    <p class="text-sm text-neutral-600 dark:text-neutral-300">
                        You are about to permanently delete <span id="bulkDeleteCount"
                            class="font-semibold text-red-600 dark:text-red-400">0</span> tenant(s).
                    </p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2">
                        This action cannot be undone and will remove all associated data.
                    </p>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <iconify-icon icon="ph:info" class="text-red-600 dark:text-red-400 text-lg mt-0.5"></iconify-icon>
                        <div class="text-sm text-red-800 dark:text-red-200">
                            <p class="font-medium mb-1">This will permanently delete:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>All tenant account information</li>
                                <li>Associated user data and permissions</li>
                                <li>Any related system configurations</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button id="bulkDeleteCancel"
                        class="px-4 py-2 rounded-lg bg-neutral-200 dark:bg-neutral-600 text-neutral-700 dark:text-neutral-200 text-sm font-medium hover:bg-neutral-300 dark:hover:bg-neutral-500 transition-colors">
                        Cancel
                    </button>
                    <button id="bulkDeleteConfirm"
                        class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition-colors">
                        <span class="bulk-delete-text">Yes, Delete All</span>
                        <span class="bulk-delete-loading hidden flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Deleting...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="bulkStatusModal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-black/60"></div>
        <div
            class="bg-white dark:bg-neutral-700 rounded-xl w-[26rem] max-w-[90vw] mx-4 shadow-xl z-10 overflow-hidden border border-blue-200 dark:border-blue-800">
            <div class="px-6 py-4 border-b border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800/40 rounded-full flex items-center justify-center">
                        <iconify-icon icon="ph:toggle-left"
                            class="text-blue-600 dark:text-blue-400 text-lg"></iconify-icon>
                    </div>
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Change Status</h3>
                </div>
            </div>

            <div class="px-6 py-6">
                <div class="text-center mb-6">
                    <div
                        class="w-16 h-16 bg-blue-100 dark:bg-blue-800/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <iconify-icon icon="ph:users-three"
                            class="text-blue-600 dark:text-blue-400 text-2xl"></iconify-icon>
                    </div>
                    <h4 class="text-lg font-semibold text-neutral-900 dark:text-white mb-2">Update Tenant Status</h4>
                    <p class="text-sm text-neutral-600 dark:text-neutral-300">
                        Change status for <span id="bulkStatusCount"
                            class="font-semibold text-blue-600 dark:text-blue-400">0</span> selected tenant(s) to
                        <span id="bulkNewStatus" class="font-semibold text-blue-600 dark:text-blue-400">Active</span>
                    </p>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6">
                    <div class="flex items-center gap-3">
                        <iconify-icon icon="ph:info" class="text-blue-600 dark:text-blue-400 text-lg"></iconify-icon>
                        <div class="text-sm text-blue-800 dark:text-blue-200">
                            <p id="statusChangeDescription">This will update the status for all selected tenants.</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button id="bulkStatusCancel"
                        class="px-4 py-2 rounded-lg bg-neutral-200 dark:bg-neutral-600 text-neutral-700 dark:text-neutral-200 text-sm font-medium hover:bg-neutral-300 dark:hover:bg-neutral-500 transition-colors">
                        Cancel
                    </button>
                    <button id="bulkStatusConfirm"
                        class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                        <span class="bulk-status-text">Update Status</span>
                        <span class="bulk-status-loading hidden flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Updating...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.tenantsData = @json($tenants);
    </script>
    <script src="{{ asset('assets/js/tenantList.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const editId = urlParams.get('edit');

            if (editId) {
                // Tunggu sampai window.editTenant tersedia
                const checkFunction = setInterval(() => {
                    if (typeof window.editTenant === 'function') {
                        window.editTenant(parseInt(editId));
                        clearInterval(checkFunction);
                        // Hapus parameter dari URL setelah modal dibuka
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }
                }, 100);
            }
        });
    </script>
@endsection