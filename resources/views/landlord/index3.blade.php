@extends('layout.layout')
@php
    $title='Properties Management';
    $subTitle = 'Manage Properties';
    $script = '<script src="' . asset('assets/js/manageproperties.js') . '"></script>';
@endphp

@section('content')
<div class="grid grid-cols-12">
    <div class="col-span-12">
        <div class="card h-full p-0 rounded-xl border-0 overflow-hidden">
            <div class="card-header border-b border-neutral-200 dark:border-neutral-600 bg-white dark:bg-neutral-700 py-4 px-6 flex items-center flex-wrap gap-3 justify-between">
                <div class="flex items-center flex-wrap gap-3">
                    <span class="text-base font-medium text-secondary-light mb-0">Show</span>
                    <select id="perPageSelect" class="form-select form-select-sm w-auto dark:bg-neutral-600 dark:text-white border-neutral-200 dark:border-neutral-500 rounded-lg">
                        <option value="5" selected>5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                    </select>
                    <form class="navbar-search" onsubmit="return false;">
                        <input id="searchInput" type="text" class="bg-white dark:bg-neutral-700 h-10 w-auto" name="search" placeholder="Search properties...">
                        <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                    </form>
                    <select id="statusFilter" class="form-select form-select-sm w-auto dark:bg-neutral-600 dark:text-white border-neutral-200 dark:border-neutral-500 rounded-lg">
                    <option value="">Status</option>
                    <option value="Available">Available</option>
                    <option value="Processing">Processing</option>
                    <option value="Rented">Rented</option>
                </select>
                </div>
                <button id="btnOpenCreate" class="btn btn-primary text-sm btn-sm px-3 py-3 rounded-lg flex items-center gap-2">
                    <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                    Add New Property
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

                <div class="table-responsive scroll-sm overflow-x-auto">
                    <table class="table bordered-table sm-table mb-0 w-full">
                        <thead class="bg-neutral-50 dark:bg-neutral-800">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left w-24"  style="padding-right:0 !important">
                                    <div class="flex items-center">
                                        <div class="form-check style-check flex items-center">
                                            <input class="form-check-input rounded border input-form-dark" type="checkbox" name="checkbox" id="selectAll">
                                        </div>
                                    </div>
                                </th>
                                <th scope="col" class="px-4 py-3 text-left w-32"  style="padding-left:0 !important">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Name</span>
                                </th>
                                <th scope="col" class="px-4 py-3 text-left min-w-[200px]">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Type</span>
                                </th>
                                <th scope="col" class="px-4 py-3 text-left min-w-[180px]">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Address</span>
                                </th>
                                <th scope="col" class="px-4 py-3 text-center w-28">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Price</span>
                                </th>
                                <th scope="col" class="px-4 py-3 text-center w-32">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Rent Type</span>
                                </th>
                                <th scope="col" class="px-4 py-3 text-center w-32">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Status</span>
                                </th>
                                <th scope="col" class="px-4 py-3 text-center w-32">
                                    <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tableBody" class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between flex-wrap gap-2 mt-6">
                    <span id="paginationInfo" class="text-sm text-neutral-600 dark:text-neutral-400">Showing 0 to 0 of 0 entries</span>
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
    <div class="bg-white dark:bg-neutral-700 rounded-xl mx-4 shadow-lg z-10 overflow-hidden border border-neutral-200 dark:border-neutral-600" 
     style="width: 42rem !important; max-width: 90vw !important;">

        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-600 flex justify-between items-center">
            <h3 id="modalTitle" class="text-lg font-semibold text-neutral-900 dark:text-white">Add Tenant</h3>
            <button id="closeModalBtn" class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 text-xl">&times;</button>
        </div>

        <form id="propertyForm" class="px-6 py-6 space-y-1">
            <input type="hidden" id="formId" value="">
            <input type="hidden" id="formMethod" value="POST">
            
            <div id="errorMessages" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"></div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Property Name *</label>
                    <input id="formName" type="text" required 
                        placeholder="Enter property name"
                        class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Property Type *</label>
                    <input id="formType" type="text" required
                        placeholder="Enter property type (e.g., Apartment, House, Villa)"
                        class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Property Address *</label>
                    <input id="formAddress" type="text" required
                        placeholder="Enter property address"
                        class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Property Price *</label>
                    <input id="formPrice" type="text" required
                        placeholder="Enter property price"
                        class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Rent Type *</label>
                    <select id="formRentType" required class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="" selected>Select Type</option>
                        <option value="Monthly">Monthly</option>
                        <option value="Yearly">Yearly</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Status *</label>
                    <select id="formStatus" required class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="Available">Available</option>
                        <option value="Rented">Rented</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" id="formCancel" class="px-4 py-2 rounded bg-neutral-200 dark:bg-neutral-600 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-300 dark:hover:bg-neutral-500">Cancel</button>
                    <button type="submit" id="formSubmit" class="btn btn-primary px-4 py-2 rounded text-sm">
                        <span class="submit-text">Add Property</span>
                        <span class="submit-loading hidden">
                            <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="rentNow" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="bg-white dark:bg-neutral-700 rounded-xl mx-4 shadow-xl z-10 overflow-hidden border border-neutral-200 dark:border-neutral-600" 
         style="width: 36rem !important; max-width: 90vw !important;">

        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-600 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-neutral-800 dark:to-neutral-700">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white flex items-center gap-2">
                    <iconify-icon icon="material-symbols:apartment" class="text-2xl text-blue-600 dark:text-blue-400"></iconify-icon>
                    Rent Property
                </h3>
                <button id="closeRentBtn" class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 text-xl">&times;</button>
            </div>
        </div>

        <div class="px-6 py-6">
            <form id="rentForm">
                <div class="flex items-center gap-4 pb-4 border-b border-neutral-200 dark:border-neutral-600">
                    <div>
                        <h4 id="rentPropertyName" class="text-xl font-bold text-neutral-900 dark:text-white"></h4>
                        <div id="rentPropertyPrice" class="mt-1 text-lg font-semibold text-blue-600"></div>
                    </div>
                </div>

                <input type="hidden" id="rentPropertyId" name="property_id">

                <h6 class="text-2xl mb-4 mt-6 text-center">Renter Information</h6>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Full Name *</label>
                        <input id="renterName" name="renter_name" type="text" required
                            placeholder="Enter full name"
                            class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Phone Number *</label>
                        <input id="renterPhone" name="renter_phone" type="tel" required
                            placeholder="Enter phone number"
                            class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Email Address *</label>
                        <input id="renterEmail" name="renter_email" type="email" required
                            placeholder="Enter email address"
                            class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Address *</label>
                        <input id="renterAddress" name="renter_address" type="text" required
                            placeholder="Enter house address"
                            class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>

                <div class=" gap-4 mt-4">
                    <div>
                        <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium" for="startDate">Start Date *</label>
                        <input id="startDate" name="start_date" type="date" min="" required 
                            class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>

                    <div>
                        <label class="block mt-2 text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium" for="endDate">End Date</label>
                        <input id="endDate" name="end_date" type="date" readonly
                            class="w-full bg-neutral-100 dark:bg-neutral-700 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none">
                    </div>
                </div>

                <div class="flex justify-end pt-6 border-t border-neutral-200 dark:border-neutral-600 mt-6 gap-3">
                    <button type="button" id="closeRentFooterBtn" class="px-6 py-2 rounded-lg bg-neutral-200 dark:bg-neutral-600 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-300 dark:hover:bg-neutral-500 transition-colors">
                        Close
                    </button>
                    <button type="submit" id="generatePaymentBtn" class="px-6 py-2 rounded-lg text-sm bg-info-600 hover:bg-info-700 text-white transition-colors">
                        <span class="btn-text">Generate Payment</span>
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
</div>

<div id="paymentLinkModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="bg-white dark:bg-neutral-700 rounded-xl mx-4 shadow-xl z-10 overflow-hidden border border-neutral-200 dark:border-neutral-600" 
         style="width: 40rem !important; max-width: 90vw !important;">

        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-600 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white flex items-center gap-2">
                    <iconify-icon icon="ph:check-circle" class="text-2xl text-green-600 dark:text-green-400"></iconify-icon>
                    Payment Link Generated
                </h3>
                <button id="closePaymentLinkBtn" class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 text-xl">&times;</button>
            </div>
        </div>

        <div class="px-6 py-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-800/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <iconify-icon icon="ph:check-circle" class="text-green-600 dark:text-green-400 text-3xl"></iconify-icon>
                </div>
                <h4 class="text-lg font-semibold text-neutral-900 dark:text-white mb-2">Payment Link Successfully Created!</h4>
                <p class="text-sm text-neutral-600 dark:text-neutral-300">
                    Share this payment link with the renter to complete the payment.
                </p>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-4 mb-6">
                <h5 class="font-semibold text-neutral-900 dark:text-white mb-3">Payment Details</h5>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-neutral-600 dark:text-neutral-400">Property:</span>
                        <p id="paymentPropertyName" class="font-semibold text-neutral-900 dark:text-white"></p>
                    </div>
                    <div>
                        <span class="text-neutral-600 dark:text-neutral-400">Renter:</span>
                        <p id="paymentRenterName" class="font-semibold text-neutral-900 dark:text-white"></p>
                    </div>
                    <div>
                        <span class="text-neutral-600 dark:text-neutral-400">Amount:</span>
                        <p id="paymentAmount" class="font-semibold text-green-600 dark:text-green-400"></p>
                    </div>
                    <div>
                        <span class="text-neutral-600 dark:text-neutral-400">Due Date:</span>
                        <p id="paymentDueDate" class="font-semibold text-red-600 dark:text-red-400"></p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-sm font-medium text-neutral-600 dark:text-neutral-400">Payment Link</label>
                <div class="flex gap-2">
                    <input id="generatedPaymentLink" type="text" readonly
                        class="flex-1 bg-neutral-100 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 text-sm font-mono">
                    <button id="copyPaymentLink" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium transition-colors flex items-center gap-2">
                        <iconify-icon icon="ph:copy" class="text-lg"></iconify-icon>
                        <span class="copy-text">Copy</span>
                        <span class="copy-success hidden text-green-300">Copied!</span>
                    </button>
                </div>
                <p class="text-xs text-neutral-500 dark:text-neutral-400">
                    This link will expire after the due date. Make sure to share it with the renter as soon as possible.
                </p>
            </div>

            <div class="flex justify-end pt-6 border-t border-neutral-200 dark:border-neutral-600 mt-6">
                <div class="flex gap-3">
                    <button id="closePaymentLinkFooterBtn" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium transition-colors">
                        Done
                    </button>
                </div>
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
                    <h3 class="text-lg font-semibold text-red-900 dark:text-red-100">Delete Multiple Properties</h3>
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
                            class="font-semibold text-red-600 dark:text-red-400">0</span> property(ies).
                    </p>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-2">
                        This action cannot be undone and will remove all associated data.
                    </p>
                </div>

                <div class="flex justify-end gap-3">
                    <button id="bulkDeleteCancel"
                        class="px-4 py-2 rounded-lg bg-neutral-200 dark:bg-neutral-600 text-neutral-700 dark:text-neutral-200 text-sm font-medium hover:bg-neutral-300 dark:hover:bg-neutral-500 transition-colors">
                        Cancel
                    </button>
                    <button id="bulkDeleteConfirm"
                        class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition-colors">
                        <span class="bulk-delete-text">Yes, Delete</span>
                        <span class="bulk-delete-loading hidden items-center gap-2">
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

<div id="detailsBackdrop" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="bg-white dark:bg-neutral-700 rounded-xl mx-4 shadow-xl z-10 overflow-hidden border border-neutral-200 dark:border-neutral-600" 
         style="width: 36rem !important; max-width: 90vw !important;">

        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-600 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-neutral-800 dark:to-neutral-700">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white flex items-center gap-2">
                    <iconify-icon icon="ph:house" class="text-2xl text-blue-600 dark:text-blue-400"></iconify-icon>
                    Property Details
                </h3>
                <button id="closeDetailsBtn" class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 text-xl">&times;</button>
            </div>
        </div>

        <div class="px-6 py-6">
            <div id="propertyDetails" class="space-y-6">

                <div class="flex items-center gap-4 pb-4 border-b border-neutral-200 dark:border-neutral-600">
                    <div>
                        <h4 id="detailName" class="text-xl font-bold text-neutral-900 dark:text-white"></h4>
                        <div id="detailStatus" class="mt-1"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2">

                    <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <iconify-icon icon="ph:identification-card" class="text-purple-600 dark:text-purple-400"></iconify-icon>
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Property ID</span>
                        </div>
                        <p id="detailId" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                    </div>

                    <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <iconify-icon icon="ph:tag" class="text-green-600 dark:text-green-400"></iconify-icon>
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Type</span>
                        </div>
                        <p id="detailType" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                    </div>

                    <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <iconify-icon icon="ph:map-pin" class="text-red-600 dark:text-red-400"></iconify-icon>
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Address</span>
                        </div>
                        <p id="detailAddress" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                    </div>

                    <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <iconify-icon icon="ph:currency-circle-dollar" class="text-yellow-600 dark:text-yellow-400"></iconify-icon>
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Price</span>
                        </div>
                        <p id="detailPrice" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                    </div>

                </div>
                  <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4 mt-3">
                        <div class="flex items-center gap-2 mb-2">
                            <iconify-icon icon="ph:calendar" class="text-blue-600 dark:text-blue-400"></iconify-icon>
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Rent Type</span>
                        </div>
                        <p id="detailRentType" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                    </div>

            </div>

            <div class="flex justify-end pt-6 border-t border-neutral-200 dark:border-neutral-600 mt-6 gap-3">
                <button id="closeDetailsFooterBtn" class="px-6 py-2 rounded-lg bg-neutral-200 dark:bg-neutral-600 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-300 dark:hover:bg-neutral-500 transition-colors">
                    Close
                </button>
                <button onclick="rentNowFromDetails()" id="rentNowBtn" class="px-6 py-2  rounded-lg  text-sm bg-info-600 hover:bg-info-700 text-white  transition-colors">
                    Rent Now
                </button>
            </div>
        </div>
    </div>
</div>

<div id="deleteBackdrop" class="fixed inset-0 z-40 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="bg-white dark:bg-neutral-700 rounded-xl w-96 max-w-[90vw] mx-4 shadow-lg z-10 overflow-hidden border border-neutral-200 dark:border-neutral-600">
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

<div id="renterDetailsModal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="bg-white dark:bg-neutral-700 rounded-xl mx-4 shadow-xl z-10 overflow-hidden border border-neutral-200 dark:border-neutral-600" 
         style="width: 40rem !important; max-width: 90vw !important;">

        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-600 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-neutral-800 dark:to-neutral-700">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white flex items-center gap-2">
                    <iconify-icon icon="ph:user-circle" class="text-2xl text-green-600 dark:text-green-400"></iconify-icon>
                    Renter Details
                </h3>
                <button id="closeRenterDetailsBtn" class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 text-xl">&times;</button>
            </div>
        </div>

        <div class="px-6 py-6">
            <div class="text-center mb-6 pb-4 border-b border-neutral-200 dark:border-neutral-600">
                <h4 id="renterDetailPropertyName" class="text-xl font-bold text-neutral-900 dark:text-white"></h4>
                <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Property currently rented by</p>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <iconify-icon icon="ph:user" class="text-blue-600 dark:text-blue-400"></iconify-icon>
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Full Name</span>
                        </div>
                        <p id="renterDetailName" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                    </div>

                    <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <iconify-icon icon="ph:phone" class="text-green-600 dark:text-green-400"></iconify-icon>
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Phone Number</span>
                        </div>
                        <p id="renterDetailPhone" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <iconify-icon icon="ph:envelope" class="text-red-600 dark:text-red-400"></iconify-icon>
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Email Address</span>
                        </div>
                        <p id="renterDetailEmail" class="text-sm font-semibold text-neutral-900 dark:text-white break-all"></p>
                    </div>

                    <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <iconify-icon icon="ph:map-pin" class="text-purple-600 dark:text-purple-400"></iconify-icon>
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Address</span>
                        </div>
                        <p id="renterDetailAddress" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <iconify-icon icon="ph:calendar-check" class="text-blue-600 dark:text-blue-400"></iconify-icon>
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Start Date</span>
                        </div>
                        <p id="renterDetailStartDate" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                    </div>

                    <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <iconify-icon icon="ph:calendar-x" class="text-orange-600 dark:text-orange-400"></iconify-icon>
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">End Date</span>
                        </div>
                        <p id="renterDetailEndDate" class="text-sm font-semibold text-neutral-900 dark:text-white"></p>
                    </div>
                </div>

            <div class="flex justify-end pt-6 border-t border-neutral-200 dark:border-neutral-600 mt-6">
                <button id="closeRenterDetailsFooterBtn" class="px-6 py-2 rounded-lg bg-neutral-200 dark:bg-neutral-600 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-300 dark:hover:bg-neutral-500 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

</div>
@endsection