(function () {
    'use strict';

    let tenants = window.tenantsData || [];
    let state = {
        query: '',
        statusFilter: '',
        page: 1,
        perPage: parseInt(document.getElementById('perPageSelect')?.value || '10'),
        selectedToDelete: null,
        isEditing: false
    };

    const API_ENDPOINTS = {
        INDEX: '/tenants',
        STORE: '/tenants',
        UPDATE: (id) => `/tenants/${id}`,
        DELETE: (id) => `/tenants/${id}`
    };

    const DOM = {
        searchInput: document.getElementById('searchInput'),
        statusFilter: document.getElementById('statusFilter'),
        tableBody: document.getElementById('tableBody'),
        paginationInfo: document.getElementById('paginationInfo'),
        perPageSelect: document.getElementById('perPageSelect'),
        prevBtn: document.getElementById('prevBtn'),
        nextBtn: document.getElementById('nextBtn'),
        pageNumbers: document.getElementById('pageNumbers'),
        btnOpenCreate: document.getElementById('btnOpenCreate'),
        selectAll: document.getElementById('selectAll'),
        loadingSpinner: document.getElementById('loadingSpinner'),

        modalBackdrop: document.getElementById('modalBackdrop'),
        modalTitle: document.getElementById('modalTitle'),
        tenantForm: document.getElementById('tenantForm'),
        formId: document.getElementById('formId'),
        formMethod: document.getElementById('formMethod'),
        formName: document.getElementById('formName'),
        formEmail: document.getElementById('formEmail'),
        formPassword: document.getElementById('formPassword'),
        formStatus: document.getElementById('formStatus'),
        formNote: document.getElementById('formNote'),
        formSubmit: document.getElementById('formSubmit'),
        formCancel: document.getElementById('formCancel'),
        closeModalBtn: document.getElementById('closeModalBtn'),
        passwordHint: document.getElementById('passwordHint'),
        errorMessages: document.getElementById('errorMessages'),

        detailsBackdrop: document.getElementById('detailsBackdrop'),
        detailAvatar: document.getElementById('detailAvatar'),
        detailName: document.getElementById('detailName'),
        detailEmail: document.getElementById('detailEmail'),
        detailStatus: document.getElementById('detailStatus'),
        detailJoinDate: document.getElementById('detailJoinDate'),
        detailId: document.getElementById('detailId'),
        detailCreator: document.getElementById('detailCreator'),
        detailNotes: document.getElementById('detailNotes'),
        detailNotesSection: document.getElementById('detailNotesSection'),
        closeDetailsBtn: document.getElementById('closeDetailsBtn'),
        closeDetailsFooterBtn: document.getElementById('closeDetailsFooterBtn'),

        deleteBackdrop: document.getElementById('deleteBackdrop'),
        deleteName: document.getElementById('deleteName'),
        deleteConfirm: document.getElementById('deleteConfirm'),
        deleteCancel: document.getElementById('deleteCancel')
    };

    async function apiRequest(url, options = {}) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const defaultHeaders = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };

            if (csrfToken) {
                defaultHeaders['X-CSRF-TOKEN'] = csrfToken;
            }

            const headers = { ...defaultHeaders, ...options.headers };

            const response = await fetch(url, {
                ...options,
                headers
            });

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('text/html')) {
                console.error('Received HTML response instead of JSON:', response.url);
 
                const htmlText = await response.text();
                console.error('HTML Response:', htmlText.substring(0, 500) + '...');

                if (htmlText.includes('<!DOCTYPE') || htmlText.includes('<html')) {
                    throw new Error('Server returned an error page instead of JSON. Please check the server logs.');
                }
                
                throw new Error('Unexpected response format. Expected JSON but received HTML.');
            }

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP Error: ${response.status} ${response.statusText}`);
            }

            return { response, data };
        } catch (error) {
            console.error('API Request Error:', {
                url,
                method: options.method || 'GET',
                error: error.message,
                stack: error.stack
            });

            throw new Error(`API Request Failed: ${error.message}`);
        }
    }

    function showLoading() {
        if (DOM.loadingSpinner) {
            DOM.loadingSpinner.classList.remove('hidden');
        }
    }

    function hideLoading() {
        if (DOM.loadingSpinner) {
            DOM.loadingSpinner.classList.add('hidden');
        }
    }

    function showNotification(message, type = 'success') {
        document.querySelectorAll('.notification-toast').forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification-toast fixed top-6 left-1/2 transform -translate-x-1/2 z-[60] px-6 py-4 rounded-xl shadow-2xl text-white transition-all duration-500 ease-out ${
            type === 'success' 
                ? 'bg-gradient-to-r from-green-500 to-emerald-500 border-2 border-green-300' 
                : 'bg-gradient-to-r from-red-500 to-rose-500 border-2 border-red-300'
        } backdrop-blur-sm translate-y-[-20px] opacity-0`;
        
        notification.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0">
                    <iconify-icon icon="${type === 'success' ? 'ph:check-circle' : 'ph:warning-circle'}" 
                                  class="text-2xl text-white"></iconify-icon>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-white">${type === 'success' ? 'Success!' : 'Error!'}</p>
                    <p class="text-sm text-white/90">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" 
                        class="flex-shrink-0 text-white/70 hover:text-white text-lg ml-2">
                    &times;
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.remove('opacity-0', 'translate-y-[-20px]');
            notification.classList.add('opacity-100', 'translate-y-0');
        }, 10);

        const timeout = type === 'error' ? 6000 : 4000;
        setTimeout(() => {
            notification.classList.add('opacity-0', 'translate-y-[-20px]');
            setTimeout(() => notification.remove(), 500);
        }, timeout);
    }

    function showErrors(errors) {
        if (!DOM.errorMessages) return;
        
        const errorContainer = DOM.errorMessages;
        let errorHtml = '<div class="text-sm"><strong>Please fix the following errors:</strong><ul class="list-disc list-inside mt-2">';
        
        if (typeof errors === 'object') {
            Object.values(errors).forEach(errorArray => {
                if (Array.isArray(errorArray)) {
                    errorArray.forEach(error => {
                        errorHtml += `<li>${escapeHtml(error)}</li>`;
                    });
                } else {
                    errorHtml += `<li>${escapeHtml(errorArray)}</li>`;
                }
            });
        } else {
            errorHtml += `<li>${escapeHtml(errors)}</li>`;
        }
        
        errorHtml += '</ul></div>';
        errorContainer.innerHTML = errorHtml;
        errorContainer.classList.remove('hidden');
    }

    function hideErrors() {
        if (DOM.errorMessages) {
            DOM.errorMessages.classList.add('hidden');
        }
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        if (isNaN(date)) return dateString;
        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    }

    function getFiltered() {
        const q = state.query.toLowerCase();
        const sf = state.statusFilter;
        return tenants.filter(t => {
            const matchesQuery = !q ||
                (t.name && t.name.toLowerCase().includes(q)) ||
                (t.email && t.email.toLowerCase().includes(q));
            const matchesStatus = !sf || (t.status === sf);
            return matchesQuery && matchesStatus;
        });
    }

    function getTotalPages(filtered) {
        return Math.max(1, Math.ceil(filtered.length / state.perPage));
    }

    function render() {
        const filtered = getFiltered();
        const totalPages = getTotalPages(filtered);

        if (state.page > totalPages) state.page = totalPages;

        const start = (state.page - 1) * state.perPage;
        const paginated = filtered.slice(start, start + state.perPage);

        if (paginated.length === 0) {
            DOM.tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-neutral-500 dark:text-neutral-400">
                        <div class="flex flex-col items-center gap-4">
                            <iconify-icon icon="tabler:users-off" class="text-5xl text-neutral-300 dark:text-neutral-600"></iconify-icon>
                            <div class="text-center">
                                <p class="text-lg font-medium">No tenants found</p>
                                <p class="text-sm text-neutral-400">Try adjusting your search or filter criteria</p>
                            </div>
                        </div>
                    </td>
                </tr>`;
        } else {
            DOM.tableBody.innerHTML = paginated.map((t, idx) => {
                const avatar = t.avatar || '/assets/images/user-list/user-list1.png';
                const status = t.status || 'Active';
                const ownerText = t.user ? `Added by: ${t.user.name}` : '';
                const checkboxId = `tenant-cb-${t.id}`;
                
                return `
                <tr class="transition-all duration-200">
                    <td class="px-4 py-4 align-middle">
                        <div class="flex items-center gap-3">
                            <div class="form-check style-check flex items-center">
                                <input class="form-check-input rounded border border-neutral-400 tbody-checkbox" 
                                       type="checkbox" name="checkbox" id="${checkboxId}" data-id="${t.id}">
                            </div>
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400 min-w-[20px]">${start + idx + 1}</span>
                        </div>
                    </td>
                    <td class="px-4 py-4 align-middle">
                        <span class="text-sm text-neutral-600 dark:text-neutral-300 whitespace-nowrap">${formatDate(t.created_at)}</span>
                    </td>
                    <td class="px-4 py-4 align-middle">
                        <div class="flex items-center gap-3 min-w-0">
                            
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-100 truncate">${escapeHtml(t.name)}</p>
                                ${ownerText ? `<p class="text-xs text-neutral-500 dark:text-neutral-400 truncate mt-0.5">${escapeHtml(ownerText)}</p>` : ''}
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 align-middle">
                        <span class="text-sm text-neutral-700 dark:text-neutral-300 break-all">${escapeHtml(t.email)}</span>
                    </td>
                    <td class="px-4 py-4 text-center align-middle">
                        <span class="${status === 'Active' ? 
                            'bg-emerald-100 dark:bg-emerald-600/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-600/30' : 
                            'bg-neutral-100 dark:bg-neutral-600/20 text-neutral-600 dark:text-neutral-400 border border-neutral-200 dark:border-neutral-600/30'
                        } px-3 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap">
                            ${status}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center align-middle">
                        <div class="flex items-center gap-1.5 justify-center">
                            <button type="button" title="View Details" 
                                    class="bg-blue-50 dark:bg-blue-600/20 hover:bg-blue-100 dark:hover:bg-blue-600/30 text-blue-600 dark:text-blue-400 w-8 h-8 flex justify-center items-center rounded-lg transition-all duration-200 hover:scale-105" 
                                    onclick="viewTenant(${t.id})">
                                <iconify-icon icon="ph:eye" class="text-sm"></iconify-icon>
                            </button>
                            <button type="button" title="Edit Tenant" 
                                    class="bg-amber-50 dark:bg-amber-600/20 hover:bg-amber-100 dark:hover:bg-amber-600/30 text-amber-600 dark:text-amber-400 w-8 h-8 flex justify-center items-center rounded-lg transition-all duration-200 hover:scale-105" 
                                    onclick="editTenant(${t.id})">
                                <iconify-icon icon="ph:pencil-simple" class="text-sm"></iconify-icon>
                            </button>
                            <button type="button" title="Delete Tenant" 
                                    class="bg-red-50 dark:bg-red-600/20 hover:bg-red-100 dark:hover:bg-red-600/30 text-red-600 dark:text-red-400 w-8 h-8 flex justify-center items-center rounded-lg transition-all duration-200 hover:scale-105" 
                                    onclick="confirmDelete(${t.id})">
                                <iconify-icon icon="ph:trash" class="text-sm"></iconify-icon>
                            </button>
                        </div>
                    </td>
                </tr>`; 
            }).join('');
        }

        const total = filtered.length;
        const startCount = total === 0 ? 0 : start + 1;
        const endCount = Math.min(total, start + state.perPage);
        if (DOM.paginationInfo) {
            DOM.paginationInfo.textContent = `Showing ${startCount} to ${endCount} of ${total} entries`;
        }

        if (DOM.prevBtn) DOM.prevBtn.disabled = state.page === 1;
        if (DOM.nextBtn) DOM.nextBtn.disabled = state.page === totalPages;

        renderPageNumbers(totalPages);
    }

    function renderPageNumbers(totalPages) {
        if (!DOM.pageNumbers) return;
        
        let pageHTML = '';
        const maxVisiblePages = 5;
        let startPage = Math.max(1, state.page - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage < maxVisiblePages - 1) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        if (startPage > 1) {
            pageHTML += `
                <li class="page-item">
                    <button class="page-link ${1 === state.page ? 
                        'bg-primary-600 text-white border-primary-600' : 
                        'bg-white dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-600'
                    } font-medium rounded border flex items-center justify-center h-8 w-8 text-sm transition-colors" 
                    onclick="goToPage(1)">
                        1
                    </button>
                </li>`;
            
            if (startPage > 2) {
                pageHTML += `
                    <li class="page-item">
                        <span class="flex items-center justify-center h-8 w-8 text-neutral-400">...</span>
                    </li>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            pageHTML += `
                <li class="page-item">
                    <button class="page-link ${i === state.page ? 
                        'bg-primary-600 text-white border-primary-600' : 
                        'bg-white dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-600'
                    } font-medium rounded border flex items-center justify-center h-8 w-8 text-sm transition-colors" 
                    onclick="goToPage(${i})">
                        ${i}
                    </button>
                </li>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                pageHTML += `
                    <li class="page-item">
                        <span class="flex items-center justify-center h-8 w-8 text-neutral-400">...</span>
                    </li>`;
            }
            
            pageHTML += `
                <li class="page-item">
                    <button class="page-link ${totalPages === state.page ? 
                        'bg-primary-600 text-white border-primary-600' : 
                        'bg-white dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-600'
                    } font-medium rounded border flex items-center justify-center h-8 w-8 text-sm transition-colors" 
                    onclick="goToPage(${totalPages})">
                        ${totalPages}
                    </button>
                </li>`;
        }
        
        DOM.pageNumbers.innerHTML = pageHTML;
    }

    function openCreateModal() {
        state.isEditing = false;
        if (DOM.modalTitle) DOM.modalTitle.textContent = 'Add New Tenant';

        if (DOM.tenantForm) DOM.tenantForm.reset();
        if (DOM.formId) DOM.formId.value = '';
        if (DOM.formMethod) DOM.formMethod.value = 'POST';
        if (DOM.formStatus) DOM.formStatus.value = 'Active';
        if (DOM.formPassword) DOM.formPassword.required = true;
        if (DOM.passwordHint) DOM.passwordHint.textContent = '*';

        const submitText = DOM.formSubmit?.querySelector('.submit-text');
        if (submitText) submitText.textContent = 'Create Tenant';
        
        hideErrors();
        showModal(true);

        setTimeout(() => DOM.formName?.focus(), 100);
    }

    function editTenant(id) {
        const tenant = tenants.find(t => t.id === id);
        if (!tenant) return;
        
        state.isEditing = true;
        if (DOM.modalTitle) DOM.modalTitle.textContent = 'Edit Tenant';

        if (DOM.formId) DOM.formId.value = tenant.id;
        if (DOM.formMethod) DOM.formMethod.value = 'PUT';
        if (DOM.formName) DOM.formName.value = tenant.name || '';
        if (DOM.formEmail) DOM.formEmail.value = tenant.email || '';
        if (DOM.formPassword) {
            DOM.formPassword.value = '';
            DOM.formPassword.required = false;
        }
        if (DOM.formStatus) DOM.formStatus.value = tenant.status || 'Active';
        if (DOM.formNote) DOM.formNote.value = tenant.note || '';
        if (DOM.passwordHint) DOM.passwordHint.textContent = '(leave blank to keep current password)';

        const submitText = DOM.formSubmit?.querySelector('.submit-text');
        if (submitText) submitText.textContent = 'Update Tenant';
        
        hideErrors();
        showModal(true);
        
        setTimeout(() => DOM.formName?.focus(), 100);
    }

    function viewTenant(id) {
        const tenant = tenants.find(t => t.id === id);
        if (!tenant) return;
        
        const avatar = tenant.avatar || '/assets/images/user-list/user-list1.png';
        const status = tenant.status || 'Active';
        const ownerInfo = tenant.user ? 
            `<div class="font-medium">${tenant.user.name}</div><div class="text-xs opacity-75">${tenant.user.email}</div>` : 
            '<div class="text-neutral-500 dark:text-neutral-400">No creator information available</div>';

        if (DOM.detailAvatar) DOM.detailAvatar.src = avatar;
        if (DOM.detailName) DOM.detailName.textContent = tenant.name || '-';
        if (DOM.detailEmail) DOM.detailEmail.textContent = tenant.email || '-';
        
        if (DOM.detailStatus) {
            DOM.detailStatus.innerHTML = `
                <span class="${status === 'Active' ? 
                    'bg-emerald-100 dark:bg-emerald-600/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-600/30' : 
                    'bg-neutral-100 dark:bg-neutral-600/20 text-neutral-600 dark:text-neutral-400 border border-neutral-200 dark:border-neutral-600/30'
                } px-2 py-1 rounded-full text-xs font-semibold">
                    ${status}
                </span>
            `;
        }
        
        if (DOM.detailJoinDate) DOM.detailJoinDate.textContent = formatDate(tenant.created_at);
        if (DOM.detailId) DOM.detailId.textContent = `#${tenant.id}`;
        if (DOM.detailCreator) DOM.detailCreator.innerHTML = ownerInfo;
        
        if (tenant.note && tenant.note.trim()) {
            if (DOM.detailNotes) DOM.detailNotes.textContent = tenant.note;
            if (DOM.detailNotesSection) DOM.detailNotesSection.classList.remove('hidden');
        } else {
            if (DOM.detailNotesSection) DOM.detailNotesSection.classList.add('hidden');
        }
        
        showDetailsModal(true);
    }

    function showModal(visible) {
        if (!DOM.modalBackdrop) return;
        
        if (visible) {
            DOM.modalBackdrop.classList.remove('hidden');
            DOM.modalBackdrop.classList.add('flex');
            document.body.style.overflow = 'hidden';
        } else {
            DOM.modalBackdrop.classList.add('hidden');
            DOM.modalBackdrop.classList.remove('flex');
            document.body.style.overflow = '';
            hideErrors();
        }
    }

    function showDetailsModal(visible) {
        if (!DOM.detailsBackdrop) return;
        
        if (visible) {
            DOM.detailsBackdrop.classList.remove('hidden');
            DOM.detailsBackdrop.classList.add('flex');
            document.body.style.overflow = 'hidden';
        } else {
            DOM.detailsBackdrop.classList.add('hidden');
            DOM.detailsBackdrop.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    function closeModal() {
        showModal(false);
        state.isEditing = false;
    }

    function closeDetailsModal() {
        showDetailsModal(false);
    }

    function confirmDelete(id) {
        const tenant = tenants.find(t => t.id === id);
        if (!tenant) return;
        
        state.selectedToDelete = tenant;
        if (DOM.deleteName) DOM.deleteName.textContent = tenant.name;
        if (DOM.deleteBackdrop) {
            DOM.deleteBackdrop.classList.remove('hidden');
            DOM.deleteBackdrop.classList.add('flex');
        }
    }

    function closeDeleteModal() {
        if (DOM.deleteBackdrop) {
            DOM.deleteBackdrop.classList.add('hidden');
            DOM.deleteBackdrop.classList.remove('flex');
        }
        state.selectedToDelete = null;
    }

    async function deleteTenant() {
        if (!state.selectedToDelete) return;
        
        const deleteBtn = DOM.deleteConfirm;
        const deleteText = deleteBtn?.querySelector('.delete-text');
        const deleteLoading = deleteBtn?.querySelector('.delete-loading');
        
        try {
            if (deleteText) deleteText.classList.add('hidden');
            if (deleteLoading) deleteLoading.classList.remove('hidden');
            if (deleteBtn) deleteBtn.disabled = true;

            const { data } = await apiRequest(API_ENDPOINTS.DELETE(state.selectedToDelete.id), {
                method: 'DELETE'
            });

            if (data.success) {
                const index = tenants.findIndex(t => t.id === state.selectedToDelete.id);
                if (index > -1) tenants.splice(index, 1);
                
                closeDeleteModal();

                const filtered = getFiltered();
                const totalPages = getTotalPages(filtered);
                if (state.page > totalPages) state.page = totalPages;
                
                render();
                showNotification(data.message || 'Tenant deleted successfully');
            } else {
                throw new Error(data.message || 'Failed to delete tenant');
            }
        } catch (error) {
            console.error('Delete error:', error);
            showNotification(error.message || 'Failed to delete tenant', 'error');
        } finally {
            if (deleteText) deleteText.classList.remove('hidden');
            if (deleteLoading) deleteLoading.classList.add('hidden');
            if (deleteBtn) deleteBtn.disabled = false;
        }
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        hideErrors();

        const submitBtn = DOM.formSubmit;
        const submitText = submitBtn?.querySelector('.submit-text');
        const submitLoading = submitBtn?.querySelector('.submit-loading');

        try {
            if (submitText) submitText.classList.add('hidden');
            if (submitLoading) submitLoading.classList.remove('hidden');
            if (submitBtn) submitBtn.disabled = true;

            const formData = {
                name: DOM.formName?.value.trim() || '',
                email: DOM.formEmail?.value.trim() || '',
                status: DOM.formStatus?.value || 'Active',
                note: DOM.formNote?.value.trim() || ''
            };

            if (DOM.formPassword?.value.trim()) {
                formData.password = DOM.formPassword.value;
            }

            const isEdit = state.isEditing;
            const method = isEdit ? 'PUT' : 'POST';
            const url = isEdit ? API_ENDPOINTS.UPDATE(DOM.formId?.value) : API_ENDPOINTS.STORE;

            const { data } = await apiRequest(url, {
                method: method,
                body: JSON.stringify(formData)
            });

            if (data.success) {
                if (isEdit) {
                    const index = tenants.findIndex(t => t.id === parseInt(DOM.formId?.value));
                    if (index > -1) {
                        tenants[index] = data.tenant;
                    }
                } else {
                    tenants.unshift(data.tenant);
                    state.page = 1;
                }

                closeModal();
                render();
                showNotification(data.message || `Tenant ${isEdit ? 'updated' : 'created'} successfully`);
            } else {
                throw new Error(data.message || `Failed to ${isEdit ? 'update' : 'create'} tenant`);
            }
        } catch (error) {
            console.error('Form submit error:', error);

            try {
                const errorData = JSON.parse(error.message);
                if (errorData.errors) {
                    showErrors(errorData.errors);
                    return;
                }
            } catch (parseError) {
            }
            
            showNotification(error.message || `Failed to ${state.isEditing ? 'update' : 'create'} tenant`, 'error');
        } finally {
            if (submitText) submitText.classList.remove('hidden');
            if (submitLoading) submitLoading.classList.add('hidden');
            if (submitBtn) submitBtn.disabled = false;
        }
    }

    function goToPage(page) {
        const filtered = getFiltered();
        const totalPages = getTotalPages(filtered);
        if (page >= 1 && page <= totalPages) {
            state.page = page;
            render();
        }
    }

    function prevPage() {
        if (state.page > 1) {
            state.page--;
            render();
        }
    }

    function nextPage() {
        const totalPages = getTotalPages(getFiltered());
        if (state.page < totalPages) {
            state.page++;
            render();
        }
    }

    function updateFilters() {
        state.query = DOM.searchInput?.value || '';
        state.statusFilter = DOM.statusFilter?.value || '';
        state.page = 1;
        render();
    }

    function updatePerPage() {
        state.perPage = parseInt(DOM.perPageSelect?.value || '10');
        state.page = 1;
        render();
    }

    function toggleSelectAll() {
        const checkboxes = document.querySelectorAll('.tbody-checkbox');
        const isChecked = DOM.selectAll?.checked || false;
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
    }

    function initEventListeners() {
        DOM.tenantForm?.addEventListener('submit', handleFormSubmit);

        DOM.searchInput?.addEventListener('input', debounce(updateFilters, 300));
        DOM.statusFilter?.addEventListener('change', updateFilters);
        DOM.perPageSelect?.addEventListener('change', updatePerPage);

        DOM.prevBtn?.addEventListener('click', prevPage);
        DOM.nextBtn?.addEventListener('click', nextPage);

        DOM.btnOpenCreate?.addEventListener('click', openCreateModal);
        DOM.formCancel?.addEventListener('click', closeModal);
        DOM.closeModalBtn?.addEventListener('click', closeModal);
        DOM.modalBackdrop?.addEventListener('click', (e) => {
            if (e.target === DOM.modalBackdrop) closeModal();
        });

        DOM.closeDetailsBtn?.addEventListener('click', closeDetailsModal);
        DOM.closeDetailsFooterBtn?.addEventListener('click', closeDetailsModal);
        DOM.detailsBackdrop?.addEventListener('click', (e) => {
            if (e.target === DOM.detailsBackdrop) closeDetailsModal();
        });

        DOM.deleteConfirm?.addEventListener('click', deleteTenant);
        DOM.deleteCancel?.addEventListener('click', closeDeleteModal);
        DOM.deleteBackdrop?.addEventListener('click', (e) => {
            if (e.target === DOM.deleteBackdrop) closeDeleteModal();
        });

        DOM.selectAll?.addEventListener('change', toggleSelectAll);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
                closeDetailsModal();
                closeDeleteModal();
            }
        });
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    window.viewTenant = viewTenant;
    window.editTenant = editTenant;
    window.confirmDelete = confirmDelete;
    window.goToPage = goToPage;

    function init() {
        console.log('Initializing Tenant List...');
        console.log('Initial tenants data:', tenants);
        
        initEventListeners();
        hideLoading();
        render();
        
        console.log('Tenant List initialized successfully');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();