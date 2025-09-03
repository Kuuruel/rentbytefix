(function(){
    'use strict';

    const API_ENDPOINTS = {
        DATA: '/landlord/transactions/data',
        PAYMENT_STATUS: (billId) => `/landlord/rentals/${billId}/payment-status`,
        DELETE: (billId) => `/landlord/transactions/${billId}/delete`,
        EXPORT: '/landlord/transactions/export'
    };

    const state = {
        page: 1,
        perPage: parseInt(document.getElementById('perPageSelect')?.value || '10'),
        query: '',
        statusFilter: '',
        monthFilter: '', // NEW: Month filter
        yearFilter: new Date().getFullYear().toString(), // NEW: Year filter (default current year)
        selectedTransactions: [],
        currentDeleteId: null,
        currentDeleteName: ''
    };

    const DOM = {
        tableBody: document.getElementById('tableBody'),
        perPageSelect: document.getElementById('perPageSelect'),
        searchInput: document.getElementById('searchInput'),
        statusFilter: document.getElementById('statusFilter'),
        monthFilter: document.getElementById('monthFilter'), // NEW
        yearFilter: document.getElementById('yearFilter'), // NEW
        clearFilters: document.getElementById('clearFilters'), // NEW
        revenueSummary: document.getElementById('revenueSummary'), // NEW
        totalRevenue: document.getElementById('totalRevenue'), // NEW
        totalTransactions: document.getElementById('totalTransactions'), // NEW
        emptyState: document.getElementById('emptyState'), // NEW
        paginationInfo: document.getElementById('paginationInfo'),
        pageNumbers: document.getElementById('pageNumbers'),
        loadingSpinner: document.getElementById('loadingSpinner'),
        selectAllCheckbox: document.getElementById('selectAll'),
        // Single delete modal elements
        deleteBackdrop: document.getElementById('deleteBackdrop'),
        deleteName: document.getElementById('deleteName'),
        deleteCancel: document.getElementById('deleteCancel'),
        deleteConfirm: document.getElementById('deleteConfirm')
    };

    async function apiRequest(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            defaultOptions.headers['X-CSRF-TOKEN'] = csrfToken;
        }
        
        console.log('Making API request to:', url);
        
        const response = await fetch(url, { ...defaultOptions, ...options });
        
        console.log('Response status:', response.status, response.statusText);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('Response data:', data);
        
        return { data };
    }

    function showNotification(message, type = 'info') {
        console.log(`${type.toUpperCase()}: ${message}`);
        
        if (type === 'error') {
            alert('Error: ' + message);
        } else if (type === 'success') {
            alert('Success: ' + message);
        } else {
            console.info(message);
        }
    }

    const formatNumber = window.formatNumber || function(n){
        if (n === null || n === undefined) return '0';
        const num = parseFloat(n);
        if (isNaN(num)) return '0';
        const formatted = num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return formatted.replace(/\.00$/, '');
    };

    const formatDate = window.formatDate || function(d){
        if (!d) return '-';
        const dt = new Date(d);
        if (isNaN(dt)) return d;
        return dt.toLocaleString('id-ID', {
            year: 'numeric',
            month: '2-digit', 
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const escapeHtml = window.escapeHtml || function(str){
        if (!str && str !== 0) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    function showLoading(){
        if (DOM.loadingSpinner) DOM.loadingSpinner.classList.remove('hidden');
    }
    function hideLoading(){
        if (DOM.loadingSpinner) DOM.loadingSpinner.classList.add('hidden');
    }

    // NEW: Update revenue summary display
    function updateRevenueSummary(summary) {
        if (!summary) return;
        
        if (DOM.revenueSummary && DOM.totalRevenue && DOM.totalTransactions) {
            if (summary.total_revenue > 0) {
                DOM.totalRevenue.textContent = summary.formatted_total_revenue || `Rp ${formatNumber(summary.total_revenue)}`;
                DOM.totalTransactions.textContent = summary.total_success_transactions || 0;
                DOM.revenueSummary.classList.remove('hidden');
            } else {
                DOM.revenueSummary.classList.add('hidden');
            }
        }
    }

    // NEW: Clear all filters
    function clearAllFilters() {
        state.query = '';
        state.statusFilter = '';
        state.monthFilter = '';
        state.yearFilter = new Date().getFullYear().toString();
        state.page = 1;

        if (DOM.searchInput) DOM.searchInput.value = '';
        if (DOM.statusFilter) DOM.statusFilter.value = '';
        if (DOM.monthFilter) DOM.monthFilter.value = '';
        if (DOM.yearFilter) DOM.yearFilter.value = state.yearFilter;
        if (DOM.perPageSelect) DOM.perPageSelect.value = '10';
        
        loadTransactions();
    }

    // Update selected transactions tracking
    function updateSelectedTransactions() {
        state.selectedTransactions = [];
        const checkboxes = DOM.tableBody.querySelectorAll('.property-checkbox:checked');
        checkboxes.forEach(checkbox => {
            state.selectedTransactions.push(checkbox.value);
        });

        updateSelectAllCheckbox();
    }

    function updateSelectAllCheckbox() {
        const allCheckboxes = DOM.tableBody.querySelectorAll('.property-checkbox');
        const checkedCheckboxes = DOM.tableBody.querySelectorAll('.property-checkbox:checked');
        
        if (DOM.selectAllCheckbox) {
            if (checkedCheckboxes.length === 0) {
                DOM.selectAllCheckbox.indeterminate = false;
                DOM.selectAllCheckbox.checked = false;
            } else if (checkedCheckboxes.length === allCheckboxes.length) {
                DOM.selectAllCheckbox.indeterminate = false;
                DOM.selectAllCheckbox.checked = true;
            } else {
                DOM.selectAllCheckbox.indeterminate = true;
                DOM.selectAllCheckbox.checked = false;
            }
        }
    }

    // Single delete modal functions
    function openDeleteModal(billId, renterName = '') {
        state.currentDeleteId = billId;
        state.currentDeleteName = renterName;
        
        if (DOM.deleteName) {
            DOM.deleteName.textContent = renterName || `Transaction #${billId}`;
        }
        
        if (DOM.deleteBackdrop) {
            DOM.deleteBackdrop.classList.remove('hidden');
            DOM.deleteBackdrop.classList.add('flex');
        }
    }

    function closeDeleteModal() {
        state.currentDeleteId = null;
        state.currentDeleteName = '';
        
        if (DOM.deleteBackdrop) {
            DOM.deleteBackdrop.classList.add('hidden');
            DOM.deleteBackdrop.classList.remove('flex');
        }
    }

    async function confirmSingleDelete() {
        if (!state.currentDeleteId) return;

        const confirmButton = DOM.deleteConfirm;
        const loadingSpan = confirmButton?.querySelector('.delete-loading');
        const textSpan = confirmButton?.querySelector('.delete-text');

        try {
            if (confirmButton) confirmButton.disabled = true;
            if (loadingSpan) {
                loadingSpan.classList.remove('hidden');
            }
            if (textSpan) {
                textSpan.classList.add('hidden');
            }

            const response = await apiRequest(API_ENDPOINTS.DELETE(state.currentDeleteId), {
                method: 'DELETE'
            });

            if (!response.data || !response.data.success) {
                throw new Error(response.data?.message || 'Failed to delete transaction');
            }

            showNotification('Transaction deleted successfully', 'success');
            
            await loadTransactions();
            closeDeleteModal();

        } catch (error) {
            console.error('Error deleting transaction:', error);
            showNotification('Error deleting transaction: ' + (error.message || error), 'error');
        } finally {
            if (confirmButton) confirmButton.disabled = false;
            if (loadingSpan) {
                loadingSpan.classList.add('hidden');
            }
            if (textSpan) {
                textSpan.classList.remove('hidden');
            }
        }
    }

    function renderSpinnerRow() {
        if (!DOM.tableBody) return;
        
        // Hide empty state when loading
        if (DOM.emptyState) DOM.emptyState.classList.add('hidden');
        
        DOM.tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="py-6 text-center">
                    <div class="flex justify-center items-center gap-2">
                        <div class="w-6 h-6 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                        <span class="text-gray-500 dark:text-neutral-400">Loading...</span>
                    </div>
                </td>
            </tr>
        `;
    }

    // ENHANCED: Load transactions with new filters
    async function loadTransactions(){
        console.log('Starting loadTransactions...');
        renderSpinnerRow(); 
        
        try {
            // Build params with new filters
            const params = new URLSearchParams({
                page: state.page,
                per_page: state.perPage,
                search: state.query,
                status: state.statusFilter
            });

            // Add month and year filters if they have values
            if (state.monthFilter) params.append('month', state.monthFilter);
            if (state.yearFilter) params.append('year', state.yearFilter);
            
            const url = `${API_ENDPOINTS.DATA}?${params}`;
            console.log('Current state:', state);
            console.log('Fetching URL:', url);
            
            const res = await apiRequest(url);
            
            if (!res.data || !res.data.success) {
                console.error('API returned error:', res.data?.message);
                throw new Error(res.data?.message || 'Failed to load transactions');
            }
            
            const payload = res.data.data;
            const summary = res.data.summary; // NEW: Get summary data
            
            console.log('Payload:', payload);
            console.log('Items count:', payload.data?.length || 0);
            console.log('Summary:', summary);
            
            renderTable(payload.data || []);
            renderPagination(payload);
            updateRevenueSummary(summary); // NEW: Update revenue summary
            
            console.log('Transaction loading completed');
        } catch (err) {
            console.error('Load transactions error:', err);
            showNotification('Error loading transactions: ' + (err.message || err), 'error');
            
            // Show empty state on error
            if (DOM.tableBody) {
                DOM.tableBody.innerHTML = '';
            }
            if (DOM.emptyState) {
                DOM.emptyState.classList.remove('hidden');
            }
        } finally {
            hideLoading();
        }
    }

    function renderTable(items){
        console.log('Rendering table with items:', items.length);
        
        if (!DOM.tableBody) {
            console.error('tableBody element not found!');
            return;
        }
        
        DOM.tableBody.innerHTML = '';

        if (!items || items.length === 0) {
            console.log('No items to display');
            
            // Show empty state if available
            if (DOM.emptyState) {
                DOM.emptyState.classList.remove('hidden');
            } else {
                // Fallback to table row
                DOM.tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-6 text-neutral-500">
                            <div class="flex flex-col items-center">
                                <p class="text-lg font-medium">No transactions found</p>
                                <p class="text-sm text-neutral-400">Try different search or filters</p>
                            </div>
                        </td>
                    </tr>`;
            }
            return;
        }

        // Hide empty state when we have data
        if (DOM.emptyState) {
            DOM.emptyState.classList.add('hidden');
        }

        items.forEach((tx, index) => {
            console.log(`Processing item ${index}:`, tx);
            
            const date = tx.created_at ? formatDate(tx.created_at) : '-';
            const paidAt = tx.paid_at ? formatDate(tx.paid_at) : '-';
            const amount = tx.formatted_amount || (tx.amount ? `Rp ${formatNumber(tx.amount)}` : '-'); // Use formatted amount if available
            const status = tx.status ? String(tx.status).charAt(0).toUpperCase() + String(tx.status).slice(1) : '-';
            const billId = tx.bill_id || tx.id || '-';
            const renterName = tx.renter_name || tx.reciept_name || 'Unknown';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-4 py-3" style="padding-right:0 !important">
                    <div class="form-check style-check flex items-center">
                        <input class="form-check-input rounded border input-form-dark property-checkbox" type="checkbox" value="${escapeHtml(billId)}">
                    </div>
                </td>
                <td class="px-4 py-3" style="padding-left:0 !important">
                    <div>
                        <h6 class="text-base mb-0 fw-medium text-primary-bold">#${escapeHtml(billId)}</h6>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <span class="text-sm text-secondary-light">${escapeHtml(renterName)}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="text-sm text-secondary-light">${escapeHtml(date)}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="text-sm text-secondary-light">${escapeHtml(paidAt)}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="text-sm font-medium text-neutral-900 dark:text-white">${escapeHtml(amount)}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="${getStatusClass(status)} px-4 py-1 rounded-full text-sm font-medium">${escapeHtml(status)}</span>
                </td>
            `;
            DOM.tableBody.appendChild(tr);
        });

        console.log('Table rendered successfully');

        // Attach click listeners for delete buttons
        DOM.tableBody.querySelectorAll('button[title="Delete"]').forEach(btn => {
            btn.onclick = function(){
                const billId = this.getAttribute('data-bill');
                const renterName = this.getAttribute('data-renter');
                if (billId) openDeleteModal(billId, renterName);
            };
        });

        // Attach change listeners for checkboxes
        DOM.tableBody.querySelectorAll('.property-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedTransactions);
        });

        // Update selected state after rendering
        updateSelectedTransactions();
    }

    function renderPagination(data){
        if (!DOM.paginationInfo || !DOM.pageNumbers) return;
        DOM.paginationInfo.textContent = `Showing ${data.from || 0} to ${data.to || 0} of ${data.total || 0} entries`;
        DOM.pageNumbers.innerHTML = '';

        const startPage = Math.max(1, state.page - 2);
        const endPage = Math.min(data.last_page || 1, startPage + 4);

        for (let i = startPage; i <= endPage; i++) {
            const btn = document.createElement('button');
            btn.className = `page-link h-8 w-8 flex items-center justify-center rounded-lg border-0 text-base font-semibold ${i === state.page ? 'bg-primary-600 text-white' : 'bg-neutral-300 dark:bg-neutral-600 text-secondary-light hover:bg-primary-100'}`;
            btn.textContent = i;
            btn.onclick = () => { state.page = i; loadTransactions(); };
            const li = document.createElement('li');
            li.className = 'page-item';
            li.appendChild(btn);
            DOM.pageNumbers.appendChild(li);
        }
    }

    function getStatusClass(status) {
        switch(status) {
            case 'Success':
                return 'bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400';
            case 'Pending':
                return 'bg-info-100 dark:bg-info-600/25 text-info-600 dark:text-info-400';
            case 'Failed':
                return 'bg-warning-100 dark:bg-warning-600/25 text-warning-600 dark:text-warning-400';
            default:
                return 'bg-neutral-100 dark:bg-neutral-600/25 text-neutral-600 dark:text-neutral-400';
        }
    }

    async function checkPaymentStatus(billId){
        try {
            const res = await apiRequest(API_ENDPOINTS.PAYMENT_STATUS(billId));
            if (!res.data || !res.data.success) throw new Error(res.data?.message || 'Failed to check status');
            const bill = res.data.data.bill;
            const transaction = res.data.data.transaction;
            let msg = '';
            let type = 'info';

            switch (bill.status) {
                case 'paid':
                    msg = 'Pembayaran berhasil! Properti sudah disewa.';
                    type = 'success';
                    loadTransactions();
                    break;
                case 'pending':
                    if (transaction && transaction.va_number) {
                        msg = `Payment pending. Transfer to ${String(transaction.bank || '').toUpperCase()}: ${transaction.va_number}`;
                    } else {
                        msg = 'Payment pending. Silakan selesaikan pembayaran.';
                    }
                    type = 'info';
                    break;
                case 'failed':
                    msg = 'Pembayaran gagal. Coba ulangi.';
                    type = 'error';
                    break;
                default:
                    msg = `Status: ${bill.status}`;
                    type = 'info';
            }
            showNotification(msg, type);
        } catch (err) {
            console.error('Check payment status error', err);
            showNotification('Error checking payment status: ' + (err.message || err), 'error');
        }
    }

    window.addTransactionToHistory = function(tx){
        if (!tx) return;
        
        const normalized = {
            bill_id: tx.bill_id ?? tx.id ?? tx.billId ?? null,
            renter_name: tx.renter_name ?? tx.reciept_name ?? tx.renterName ?? '-',
            created_at: tx.created_at ?? tx.createdAt ?? new Date().toISOString(),
            paid_at: tx.paid_at ?? tx.paidAt ?? null,
            amount: tx.amount ?? tx.total ?? 0,
            status: tx.status ?? 'pending'
        };

        if (DOM.tableBody) {
            const tr = document.createElement('tr');
            const date = formatDate(normalized.created_at);
            const paidAt = normalized.paid_at ? formatDate(normalized.paid_at) : '-';
            const amount = `Rp ${formatNumber(normalized.amount)}`;
            const status = normalized.status.charAt(0).toUpperCase() + normalized.status.slice(1);
            const billId = normalized.bill_id ?? '-';
            tr.innerHTML = `
                <td class="px-4 py-3" style="padding-right:0 !important">
                    <div class="form-check style-check flex items-center">
                        <input class="form-check-input rounded border input-form-dark property-checkbox" type="checkbox" value="${escapeHtml(billId)}">
                    </div>
                </td>
                <td class="px-4 py-3" style="padding-left:0 !important"><h6 class="text-base mb-0 fw-medium text-primary-bold">#${escapeHtml(billId)}</h6></td>
                <td class="px-4 py-3"><span class="text-sm text-secondary-light">${escapeHtml(normalized.renter_name)}</span></td>
                <td class="px-4 py-3"><span class="text-sm text-secondary-light">${escapeHtml(date)}</span></td>
                <td class="px-4 py-3"><span class="text-sm text-secondary-light">${escapeHtml(paidAt)}</span></td>
                <td class="px-4 py-3 text-center"><span class="text-sm font-medium">${escapeHtml(amount)}</span></td>
                <td class="px-4 py-3 text-center"><span class="px-4 py-1 rounded-full text-sm font-medium">${escapeHtml(status)}</span></td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <button class="w-8 h-8 bg-red-50 dark:bg-red-600/10 text-red-600 rounded-full inline-flex items-center justify-center hover:bg-red-100 dark:hover:bg-red-600/20" title="Delete" data-bill="${escapeHtml(billId)}" data-renter="${escapeHtml(normalized.renter_name)}">
                            <iconify-icon icon="ph:trash" class="text-sm"></iconify-icon>
                        </button>
                    </div>
                </td>
            `;
            DOM.tableBody.prepend(tr);
            
            tr.querySelectorAll('button[title="Delete"]').forEach(b => {
                b.onclick = () => { 
                    const billId = b.getAttribute('data-bill'); 
                    const renterName = b.getAttribute('data-renter');
                    if (billId) openDeleteModal(billId, renterName); 
                };
            });

            tr.querySelectorAll('.property-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedTransactions);
            });
        }
    };

    function debounce(fn, wait){
        let t;
        return function(...args){
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), wait);
        };
    }

    // ENHANCED: Initialize events with new filters
    function initEvents(){
        DOM.searchInput?.addEventListener('input', debounce(function(){
            state.query = DOM.searchInput.value;
            state.page = 1;
            loadTransactions();
        }, 300));

        DOM.statusFilter?.addEventListener('change', function(){
            state.statusFilter = DOM.statusFilter.value;
            state.page = 1;
            loadTransactions();
        });

        // NEW: Month filter event
        DOM.monthFilter?.addEventListener('change', function(){
            state.monthFilter = DOM.monthFilter.value;
            state.page = 1;
            loadTransactions();
        });

        // NEW: Year filter event
        DOM.yearFilter?.addEventListener('change', function(){
            state.yearFilter = DOM.yearFilter.value;
            state.page = 1;
            loadTransactions();
        });

        DOM.perPageSelect?.addEventListener('change', function(){
            state.perPage = parseInt(DOM.perPageSelect.value);
            state.page = 1;
            loadTransactions();
        });

        // NEW: Clear filters button
        DOM.clearFilters?.addEventListener('click', clearAllFilters);

        // Select All checkbox functionality
        DOM.selectAllCheckbox?.addEventListener('change', function() {
            const checkboxes = DOM.tableBody.querySelectorAll('.property-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedTransactions();
        });

        // Single delete modal events
        DOM.deleteCancel?.addEventListener('click', closeDeleteModal);
        DOM.deleteConfirm?.addEventListener('click', confirmSingleDelete);

        // Close single delete modal when clicking outside
        DOM.deleteBackdrop?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    }

    console.log('Checking DOM elements:');
    console.log('tableBody:', DOM.tableBody);
    console.log('perPageSelect:', DOM.perPageSelect);
    console.log('searchInput:', DOM.searchInput);
    console.log('monthFilter:', DOM.monthFilter); // NEW
    console.log('yearFilter:', DOM.yearFilter); // NEW
    console.log('clearFilters:', DOM.clearFilters); // NEW
    console.log('deleteBackdrop:', DOM.deleteBackdrop);

    initEvents();
    
    setTimeout(() => {
        console.log('Initializing transaction loading...');
        loadTransactions();
    }, 100);

    // ENHANCED: Export enhanced window object
    window.transactionManager = {
        loadTransactions,
        addTransactionToHistory: window.addTransactionToHistory,
        checkPaymentStatus,
        openDeleteModal,
        closeDeleteModal,
        clearAllFilters 
    };
})();