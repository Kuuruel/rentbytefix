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
        formCountry: document.getElementById('formCountry'),
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
        detailCountry: document.getElementById('detailCountry'),
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

            console.log('Making API request:', { url, method: options.method || 'GET', headers });

            const response = await fetch(url, {
                ...options,
                headers
            });

            console.log('Response status:', response.status, response.statusText);

            const contentType = response.headers.get('content-type');
            console.log('Response content-type:', contentType);

            if (contentType && contentType.includes('text/html')) {
                console.error('Received HTML response instead of JSON:', response.url);

                const htmlText = await response.text();
                console.error('HTML Response:', htmlText.substring(0, 500) + '...');

                if (htmlText.includes('<!DOCTYPE') || htmlText.includes('<html')) {
                    throw new Error('Server returned an error page instead of JSON. Please check the server logs.');
                }

                throw new Error('Unexpected response format. Expected JSON but received HTML.');
            }

            let data;
            try {
                const responseText = await response.text();
                console.log('Raw response:', responseText);
                data = responseText ? JSON.parse(responseText) : {};
            } catch (parseError) {
                console.error('Failed to parse JSON response:', parseError);
                throw new Error(`Invalid JSON response from server: ${parseError.message}`);
            }

            if (!response.ok) {
                console.error('API Error Response:', data);
                const errorMessage = data.message || `HTTP Error: ${response.status} ${response.statusText}`;

                if (data.errors) {
                    const error = new Error(errorMessage);
                    error.validationErrors = data.errors;
                    throw error;
                }
                
                throw new Error(errorMessage);
            }

            return { response, data };
        } catch (error) {
            console.error('API Request Error:', {
                url,
                method: options.method || 'GET',
                error: error.message,
                validationErrors: error.validationErrors,
                stack: error.stack
            });

            throw error;
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
        notification.className = 'notification-toast';
        notification.style.cssText = `
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
            delete: {
                bg: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                shadow: '0 10px 25px rgba(239, 68, 68, 0.3)',
                icon: 'ph:trash-fill'
            },
            error: {
                bg: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                shadow: '0 10px 25px rgba(245, 158, 11, 0.3)',
                icon: 'ph:warning-circle-fill'
            }
        };

        const config = colors[type] || colors.success;

        notification.innerHTML = `
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
                            ">${type === 'success' ? 'Success!' : type === 'delete' ? 'Deleted!' : 'Error!'}</h4>
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
                            <iconify-icon icon="ph:x" style="font-size: 14px;"></iconify-icon>
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

        document.body.appendChild(notification);
        console.log('Notification added to DOM:', notification);

        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        }, 10);

        const progressBar = notification.querySelector('.notification-progress');
        if (progressBar) {
            setTimeout(() => {
                progressBar.style.width = '0%';
            }, 100);
        }

        const timeout = type === 'error' ? 6000 : 4500;
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.transform = 'translateX(100%)';
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 400);
            }
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

        if (state.page > totalPages && totalPages > 0) {
            state.page = totalPages;
        }
        if (state.page < 1) {
            state.page = 1;
        }

        const start = (state.page - 1) * state.perPage;
        const paginated = filtered.slice(start, start + state.perPage);

        const endIndex = Math.min(start + state.perPage, filtered.length);
        const showingStart = filtered.length === 0 ? 0 : start + 1;
        const showingEnd = filtered.length === 0 ? 0 : endIndex;

        if (DOM.paginationInfo) {
            DOM.paginationInfo.textContent = `Showing ${showingStart} to ${showingEnd} of ${filtered.length} entries`;
        }

        if (DOM.prevBtn) {
            DOM.prevBtn.disabled = state.page <= 1;
            DOM.prevBtn.classList.toggle('opacity-50', state.page <= 1);
            DOM.prevBtn.classList.toggle('cursor-not-allowed', state.page <= 1);
        }

        if (DOM.nextBtn) {
            DOM.nextBtn.disabled = state.page >= totalPages;
            DOM.nextBtn.classList.toggle('opacity-50', state.page >= totalPages);
            DOM.nextBtn.classList.toggle('cursor-not-allowed', state.page >= totalPages);
        }

        renderPageNumbers(totalPages);

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
            const country = t.country || '-';
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
                            <p class="text-xs text-neutral-500 dark:text-neutral-400 truncate mt-0.5">${escapeHtml(country)}</p>
                            ${ownerText ? `<p class="text-xs text-neutral-500 dark:text-neutral-400 truncate mt-0.5">${escapeHtml(ownerText)}</p>` : ''}
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 align-middle">
                    <span class="text-sm text-neutral-700 dark:text-neutral-300 break-all">${escapeHtml(t.email)}</span>
                </td>
                <td class="px-4 py-4 text-center align-middle">
                    <span class="${status === 'Active' ? 
                        'bg-success-100 dark:bg-emerald-600/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-600/30' : 
                        'bg-neutral-100 dark:bg-neutral-600/20 text-neutral-600 dark:text-neutral-400 border border-neutral-200 dark:border-neutral-600/30'
                    } px-3 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap">
                        ${status}
                    </span>
                </td>
                <td class="px-4 py-4 text-center align-middle">
                    <div class="flex items-center gap-1.5 justify-center">
                        <button type="button" title="View Details" 
                                class="bg-info-100 dark:bg-blue-600/20 hover:bg-blue-100 dark:hover:bg-blue-600/30 text-blue-600 dark:text-blue-400 w-8 h-8 flex justify-center items-center rounded-lg transition-all duration-200 hover:scale-105" 
                                onclick="viewTenant(${t.id})">
                            <iconify-icon icon="ph:eye" class="text-sm"></iconify-icon>
                        </button>
                        <button type="button" title="Edit Tenant" 
                                class="bg-warning-100 dark:bg-amber-600/20 hover:bg-amber-100 dark:hover:bg-amber-600/30 text-amber-600 dark:text-amber-400 w-8 h-8 flex justify-center items-center rounded-lg transition-all duration-200 hover:scale-105" 
                                onclick="editTenant(${t.id})">
                            <iconify-icon icon="ph:pencil-simple" class="text-sm"></iconify-icon>
                        </button>
                        <button type="button" title="Delete Tenant" 
                                class="bg-danger-100 dark:bg-red-600/20 hover:bg-red-100 dark:hover:bg-red-600/30 text-red-600 dark:text-red-400 w-8 h-8 flex justify-center items-center rounded-lg transition-all duration-200 hover:scale-105" 
                                onclick="confirmDelete(${t.id})">
                            <iconify-icon icon="ph:trash" class="text-sm"></iconify-icon>
                        </button>
                    </div>
                </td>
            </tr>`; 
        }).join('');
    }
}

    function renderPageNumbers(totalPages) {
        if (!DOM.pageNumbers) return;

        if (totalPages <= 1) {
            DOM.pageNumbers.innerHTML = '';
            return;
        }

        const delta = 2;
        const rangeStart = Math.max(1, state.page - delta);
        const rangeEnd = Math.min(totalPages, state.page + delta);

        let pageHTML = '';

        pageHTML += `
            <li class="page-item">
                <button class="page-link ${state.page <= 1 ?
                'bg-neutral-200 dark:bg-neutral-600 text-neutral-400 dark:text-neutral-500 cursor-not-allowed' :
                'bg-white dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 border border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-600'
            } font-medium rounded flex items-center justify-center h-8 w-8 text-sm transition-colors" 
                ${state.page <= 1 ? 'disabled' : ''} title="First page">
                    &laquo;&laquo;
                </button>
            </li>
        `;

        pageHTML += `
            <li class="page-item">
                <button class="page-link ${state.page <= 1 ?
                'bg-neutral-200 dark:bg-neutral-600 text-neutral-400 dark:text-neutral-500 cursor-not-allowed' :
                'bg-white dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 border border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-600'
            } font-medium rounded flex items-center justify-center h-8 w-8 text-sm transition-colors" 
                ${state.page <= 1 ? 'disabled' : ''} title="Previous page">
                    &laquo;
                </button>
            </li>
        `;

        if (rangeStart > 1) {
            if (rangeStart > 2) {
                pageHTML += `
                    <li class="page-item">
                        <button class="page-link bg-white dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 border border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-600 font-medium rounded flex items-center justify-center h-8 w-8 text-sm transition-colors">
                            1
                        </button>
                    </li>
                    <li class="page-item">
                        <span class="page-link bg-transparent text-neutral-500 dark:text-neutral-400 flex items-center justify-center h-8 w-8 text-sm">
                            ...
                        </span>
                    </li>
                `;
            } else {
                pageHTML += `
                    <li class="page-item">
                        <button class="page-link bg-white dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 border border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-600 font-medium rounded flex items-center justify-center h-8 w-8 text-sm transition-colors">
                            1
                        </button>
                    </li>
                `;
            }
        }

        for (let i = rangeStart; i <= rangeEnd; i++) {
            const isActive = i === state.page;
            pageHTML += `
                <li class="page-item">
                    <button class="page-link ${isActive ?
                    'bg-primary-600 text-white border border-primary-600' :
                    'bg-white dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 border border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-600'
                } font-medium rounded flex items-center justify-center h-8 w-8 text-sm transition-colors" 
                    ${isActive ? 'disabled' : ''} data-page="${i}">
                        ${i}
                    </button>
                </li>
            `;
        }

        if (rangeEnd < totalPages) {
            if (rangeEnd < totalPages - 1) {
                pageHTML += `
                    <li class="page-item">
                        <span class="page-link bg-transparent text-neutral-500 dark:text-neutral-400 flex items-center justify-center h-8 w-8 text-sm">
                            ...
                        </span>
                    </li>
                    <li class="page-item">
                        <button class="page-link bg-white dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 border border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-600 font-medium rounded flex items-center justify-center h-8 w-8 text-sm transition-colors" 
                        data-page="${totalPages}">
                            ${totalPages}
                        </button>
                    </li>
                `;
            } else {
                pageHTML += `
                    <li class="page-item">
                        <button class="page-link bg-white dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 border border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-600 font-medium rounded flex items-center justify-center h-8 w-8 text-sm transition-colors" 
                        data-page="${totalPages}">
                            ${totalPages}
                        </button>
                    </li>
                `;
            }
        }

        pageHTML += `
            <li class="page-item">
                <button class="page-link ${state.page >= totalPages ?
                'bg-neutral-200 dark:bg-neutral-600 text-neutral-400 dark:text-neutral-500 cursor-not-allowed' :
                'bg-white dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 border border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-600'
            } font-medium rounded flex items-center justify-center h-8 w-8 text-sm transition-colors" 
                ${state.page >= totalPages ? 'disabled' : ''} title="Next page" id="nextPageBtn">
                    &raquo;
                </button>
            </li>
        `;

        pageHTML += `
            <li class="page-item">
                <button class="page-link ${state.page >= totalPages ?
                'bg-neutral-200 dark:bg-neutral-600 text-neutral-400 dark:text-neutral-500 cursor-not-allowed' :
                'bg-white dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 border border-neutral-300 dark:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-600'
            } font-medium rounded flex items-center justify-center h-8 w-8 text-sm transition-colors" 
                ${state.page >= totalPages ? 'disabled' : ''} title="Last page" id="lastPageBtn">
                    &raquo;&raquo;
                </button>
            </li>
        `;

        DOM.pageNumbers.innerHTML = pageHTML;

        addPaginationEventListeners(totalPages);

    }

    function addPaginationEventListeners(totalPages) {
        const firstPageBtn = DOM.pageNumbers.querySelector('button[title="First page"]');
        if (firstPageBtn && !firstPageBtn.disabled) {
            firstPageBtn.addEventListener('click', () => goToPage(1));
        }

        const prevPageBtn = DOM.pageNumbers.querySelector('button[title="Previous page"]');
        if (prevPageBtn && !prevPageBtn.disabled) {
            prevPageBtn.addEventListener('click', () => prevPage());
        }

        const pageNumberBtns = DOM.pageNumbers.querySelectorAll('button[data-page]');
        pageNumberBtns.forEach(btn => {
            if (!btn.disabled) {
                const pageNum = parseInt(btn.dataset.page);
                btn.addEventListener('click', () => goToPage(pageNum));
            }
        });

        const nextPageBtn = DOM.pageNumbers.querySelector('#nextPageBtn');
        if (nextPageBtn && !nextPageBtn.disabled) {
            nextPageBtn.addEventListener('click', () => nextPage());
        }

        const lastPageBtn = DOM.pageNumbers.querySelector('#lastPageBtn');
        if (lastPageBtn && !lastPageBtn.disabled) {
            lastPageBtn.addEventListener('click', () => goToPage(totalPages));
        }
    }

    function initEventListeners() {
        DOM.tenantForm?.addEventListener('submit', handleFormSubmit);

        DOM.searchInput?.addEventListener('input', debounce(updateFilters, 300));
        DOM.statusFilter?.addEventListener('change', updateFilters);
        DOM.perPageSelect?.addEventListener('change', updatePerPage);

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

        initSelectEventListeners();

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
                closeDetailsModal();
                closeDeleteModal();
            }
        });

        document.getElementById('bulkDeleteCancel')?.addEventListener('click', hideBulkDeleteModal);
        document.getElementById('bulkDeleteConfirm')?.addEventListener('click', bulkDeleteTenants);
        document.getElementById('bulkDeleteModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'bulkDeleteModal') hideBulkDeleteModal();
        });

        document.getElementById('bulkStatusCancel')?.addEventListener('click', hideBulkStatusModal);
        document.getElementById('bulkStatusConfirm')?.addEventListener('click', bulkToggleStatus);
        document.getElementById('bulkStatusModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'bulkStatusModal') hideBulkStatusModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                hideBulkDeleteModal();
                hideBulkStatusModal();
            }
        });
    }

    function goToPage(page) {
        const filtered = getFiltered();
        const totalPages = getTotalPages(filtered);

        if (isNaN(page) || page < 1 || page > totalPages) {
            const input = document.querySelector('#pageNumbers input[type="number"]');
            if (input) input.value = state.page;
            return;
        }

        if (page >= 1 && page <= totalPages) {
            state.page = page;
            render();
        }
    }

    function openCreateModal() {
        state.isEditing = false;
        if (DOM.modalTitle) DOM.modalTitle.textContent = 'Add New Tenant';

        if (DOM.tenantForm) DOM.tenantForm.reset();
        if (DOM.formId) DOM.formId.value = '';
        if (DOM.formMethod) DOM.formMethod.value = 'POST';
        if (DOM.formStatus) DOM.formStatus.value = 'Active';
        if (DOM.formCountry) DOM.formCountry.value = '';
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
        if (DOM.formCountry) DOM.formCountry.value = tenant.country || '';
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
        const country = tenant.country || '-';
        const ownerInfo = tenant.user ?
            `<div class="font-medium">${tenant.user.name}</div><div class="text-xs opacity-75">${tenant.user.email}</div>` :
            '<div class="text-neutral-500 dark:text-neutral-400">No creator information available</div>';

        if (DOM.detailAvatar) DOM.detailAvatar.src = avatar;
        if (DOM.detailName) DOM.detailName.textContent = tenant.name || '-';
        if (DOM.detailEmail) DOM.detailEmail.textContent = tenant.email || '-';

        if (DOM.detailStatus) {
            DOM.detailStatus.innerHTML = `
              <span class="${status === 'Active'
                    ? 'bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 px-8 py-1.5 rounded-full font-medium text-sm'
                    : 'bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:text-danger-400 px-8 py-1.5 rounded-full font-medium text-sm'
                }">
        ${status}
    </span>
            `;
        }

        if (DOM.detailJoinDate) DOM.detailJoinDate.textContent = formatDate(tenant.created_at);
        if (DOM.detailId) DOM.detailId.textContent = `#${tenant.id}`;
        if (DOM.detailCountry) DOM.detailCountry.textContent = country;
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
                const tenantName = state.selectedToDelete.name;
                const index = tenants.findIndex(t => t.id === state.selectedToDelete.id);
                if (index > -1) tenants.splice(index, 1);

                closeDeleteModal();

                const filtered = getFiltered();
                const totalPages = getTotalPages(filtered);
                if (state.page > totalPages) state.page = totalPages;

                render();
                showNotification(`${tenantName} has been permanently removed from the system`, 'delete');
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
                country: DOM.formCountry?.value || '',
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
                const tenantName = formData.name;

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

                if (isEdit) {
                    showNotification(`${tenantName}'s profile has been successfully updated with the latest information`, 'success');
                } else {
                    showNotification(`Welcome ${tenantName}! New tenant account has been created and activated`, 'success');
                }
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
        updateSelectActions();
    }

    function updateSelectAll() {
        const checkboxes = document.querySelectorAll('.tbody-checkbox');
        const checkedBoxes = document.querySelectorAll('.tbody-checkbox:checked');

        if (DOM.selectAll) {
            if (checkboxes.length === 0) {
                DOM.selectAll.checked = false;
                DOM.selectAll.indeterminate = false;
            } else if (checkedBoxes.length === checkboxes.length) {
                DOM.selectAll.checked = true;
                DOM.selectAll.indeterminate = false;
            } else if (checkedBoxes.length > 0) {
                DOM.selectAll.checked = false;
                DOM.selectAll.indeterminate = true;
            } else {
                DOM.selectAll.checked = false;
                DOM.selectAll.indeterminate = false;
            }
        }
        updateSelectActions();
    }

    function updateSelectActions() {
        const checkedBoxes = document.querySelectorAll('.tbody-checkbox:checked');
        const selectedCount = checkedBoxes.length;

        let bulkActionsContainer = document.getElementById('bulkActionsContainer');

        if (selectedCount > 0) {
            if (!bulkActionsContainer) {
                bulkActionsContainer = document.createElement('div');
                bulkActionsContainer.id = 'bulkActionsContainer';
                bulkActionsContainer.className = 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4';
                bulkActionsContainer.innerHTML = `
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <iconify-icon icon="ph:check-square" class="text-blue-600 dark:text-blue-400 text-xl"></iconify-icon>
                        <span class="text-sm font-medium text-blue-900 dark:text-blue-100">
                            <span id="selectedCount">${selectedCount}</span> tenant(s) selected
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="bulkDeleteBtn" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-xs font-medium flex items-center gap-2 transition-colors">
                            <iconify-icon icon="ph:trash" class="text-sm"></iconify-icon>
                            Delete Selected
                        </button>
                        <button id="bulkStatusBtn" class="bg-green-600 hover:bg-green-700 text-black dark:text-white px-3 py-2 rounded-lg text-xs font-medium flex items-center gap-2 transition-colors">
                            <iconify-icon icon="ph:toggle-left" class="text-sm"></iconify-icon>
                            Toggle Status
                        </button>
                        <button id="clearSelection" class="bg-gray-500 hover:bg-gray-600 text-black dark:text-white px-3 py-2 rounded-lg text-xs font-medium flex items-center gap-2 transition-colors">
                            <iconify-icon icon="ph:x" class="text-sm"></iconify-icon>
                            Clear
                        </button>
                    </div>
                </div>
            `;

                const tableContainer = document.querySelector('.table-responsive');
                if (tableContainer) {
                    tableContainer.parentNode.insertBefore(bulkActionsContainer, tableContainer);
                }
            } else {
                document.getElementById('selectedCount').textContent = selectedCount;
            }

            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const bulkStatusBtn = document.getElementById('bulkStatusBtn');
            const clearSelectionBtn = document.getElementById('clearSelection');

            bulkDeleteBtn.onclick = showBulkDeleteModal;
            bulkStatusBtn.onclick = showBulkStatusModal;
            clearSelectionBtn.onclick = clearAllSelections;

        } else {
            if (bulkActionsContainer) {
                bulkActionsContainer.remove();
            }
        }
    }

    function getSelectedTenantIds() {
        const checkedBoxes = document.querySelectorAll('.tbody-checkbox:checked');
        return Array.from(checkedBoxes).map(checkbox => parseInt(checkbox.dataset.id));
    }

    function clearAllSelections() {
        const checkboxes = document.querySelectorAll('.tbody-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = false);
        if (DOM.selectAll) {
            DOM.selectAll.checked = false;
            DOM.selectAll.indeterminate = false;
        }
        updateSelectActions();
    }

    function showBulkDeleteModal() {
        const selectedIds = getSelectedTenantIds();
        if (selectedIds.length === 0) return;

        document.getElementById('bulkDeleteCount').textContent = selectedIds.length;
        document.getElementById('bulkDeleteModal').classList.remove('hidden');
        document.getElementById('bulkDeleteModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function hideBulkDeleteModal() {
        document.getElementById('bulkDeleteModal').classList.add('hidden');
        document.getElementById('bulkDeleteModal').classList.remove('flex');
        document.body.style.overflow = '';
    }

    function showBulkStatusModal() {
        const selectedIds = getSelectedTenantIds();
        if (selectedIds.length === 0) return;

        const selectedTenants = tenants.filter(t => selectedIds.includes(t.id));
        const activeCount = selectedTenants.filter(t => t.status === 'Active').length;
        const newStatus = activeCount >= selectedIds.length / 2 ? 'Inactive' : 'Active';

        document.getElementById('bulkStatusCount').textContent = selectedIds.length;
        document.getElementById('bulkNewStatus').textContent = newStatus;

        const description = newStatus === 'Active'
            ? 'Selected tenants will be activated and gain access to the system.'
            : 'Selected tenants will be deactivated and lose access to the system.';
        document.getElementById('statusChangeDescription').textContent = description;

        document.getElementById('bulkStatusModal').classList.remove('hidden');
        document.getElementById('bulkStatusModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function hideBulkStatusModal() {
        document.getElementById('bulkStatusModal').classList.add('hidden');
        document.getElementById('bulkStatusModal').classList.remove('flex');
        document.body.style.overflow = '';
    }

    async function bulkDeleteTenants() {
        const selectedIds = getSelectedTenantIds();
        if (selectedIds.length === 0) return;

        const deleteBtn = document.getElementById('bulkDeleteConfirm');
        const deleteText = deleteBtn?.querySelector('.bulk-delete-text');
        const deleteLoading = deleteBtn?.querySelector('.bulk-delete-loading');

        try {
            if (deleteText) deleteText.classList.add('hidden');
            if (deleteLoading) deleteLoading.classList.remove('hidden');
            if (deleteBtn) deleteBtn.disabled = true;

            const deletePromises = selectedIds.map(async (id) => {
                try {
                    const { data } = await apiRequest(API_ENDPOINTS.DELETE(id), { method: 'DELETE' });
                    return { id, success: true, data };
                } catch (error) {
                    console.error(`Failed to delete tenant ${id}:`, error);
                    return { id, success: false, error: error.message };
                }
            });

            const results = await Promise.all(deletePromises);
            const successful = results.filter(r => r.success);
            const failed = results.filter(r => !r.success);

            successful.forEach(result => {
                const index = tenants.findIndex(t => t.id === result.id);
                if (index > -1) tenants.splice(index, 1);
            });

            hideBulkDeleteModal();
            clearAllSelections();

            const filtered = getFiltered();
            const totalPages = getTotalPages(filtered);
            if (state.page > totalPages) state.page = totalPages;

            render();

            if (failed.length === 0) {
                showNotification(`Successfully deleted ${successful.length} tenant(s)`, 'delete');
            } else {
                showNotification(`Deleted ${successful.length} tenant(s), ${failed.length} failed`, 'error');
            }

        } catch (error) {
            console.error('Bulk delete error:', error);
            showNotification('Failed to delete selected tenants', 'error');
        } finally {
            if (deleteText) deleteText.classList.remove('hidden');
            if (deleteLoading) deleteLoading.classList.add('hidden');
            if (deleteBtn) deleteBtn.disabled = false;
        }
    }

    async function bulkToggleStatus() {
        const selectedIds = getSelectedTenantIds();
        if (selectedIds.length === 0) return;

        const selectedTenants = tenants.filter(t => selectedIds.includes(t.id));
        const activeCount = selectedTenants.filter(t => t.status === 'Active').length;
        const newStatus = activeCount >= selectedIds.length / 2 ? 'Inactive' : 'Active';

        const statusBtn = document.getElementById('bulkStatusConfirm');
        const statusText = statusBtn?.querySelector('.bulk-status-text');
        const statusLoading = statusBtn?.querySelector('.bulk-status-loading');

        try {
            if (statusText) statusText.classList.add('hidden');
            if (statusLoading) statusLoading.classList.remove('hidden');
            if (statusBtn) statusBtn.disabled = true;

            const updatePromises = selectedIds.map(async (id) => {
                try {
                    const tenant = tenants.find(t => t.id === id);
                    if (!tenant) return { id, success: false, error: 'Tenant not found' };

                    const formData = {
                        name: tenant.name,
                        email: tenant.email,
                        country: tenant.country || '',
                        status: newStatus,
                        note: tenant.note || ''
                    };

                    const { data } = await apiRequest(API_ENDPOINTS.UPDATE(id), {
                        method: 'PUT',
                        body: JSON.stringify(formData)
                    });
                    return { id, success: true, data };
                } catch (error) {
                    console.error(`Failed to update tenant ${id}:`, error);
                    return { id, success: false, error: error.message };
                }
            });

            const results = await Promise.all(updatePromises);
            const successful = results.filter(r => r.success);
            const failed = results.filter(r => !r.success);

            successful.forEach(result => {
                const tenantIndex = tenants.findIndex(t => t.id === result.id);
                if (tenantIndex > -1) {
                    tenants[tenantIndex].status = newStatus;
                }
            });

            hideBulkStatusModal();
            clearAllSelections();
            render();

            if (failed.length === 0) {
                showNotification(`Successfully updated status for ${successful.length} tenant(s)`, 'success');
            } else {
                showNotification(`Updated ${successful.length} tenant(s), ${failed.length} failed`, 'error');
            }

        } catch (error) {
            console.error('Bulk status update error:', error);
            showNotification('Failed to update selected tenants', 'error');
        } finally {
            if (statusText) statusText.classList.remove('hidden');
            if (statusLoading) statusLoading.classList.add('hidden');
            if (statusBtn) statusBtn.disabled = false;
        }
    }

    function initSelectEventListeners() {
        DOM.selectAll?.addEventListener('change', toggleSelectAll);

        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('tbody-checkbox')) {
                updateSelectAll();
            }
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

        initSelectEventListeners();

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
                closeDetailsModal();
                closeDeleteModal();
            }
        });

        document.getElementById('bulkDeleteCancel')?.addEventListener('click', hideBulkDeleteModal);
        document.getElementById('bulkDeleteConfirm')?.addEventListener('click', bulkDeleteTenants);
        document.getElementById('bulkDeleteModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'bulkDeleteModal') hideBulkDeleteModal();
        });

        document.getElementById('bulkStatusCancel')?.addEventListener('click', hideBulkStatusModal);
        document.getElementById('bulkStatusConfirm')?.addEventListener('click', bulkToggleStatus);
        document.getElementById('bulkStatusModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'bulkStatusModal') hideBulkStatusModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                hideBulkDeleteModal();
                hideBulkStatusModal();
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
