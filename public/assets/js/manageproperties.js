(function () {
    'use strict';

    let properties = window.propertiesData || [];
    let state = {
        query: '',
        statusFilter: '',
        page: 1,
        perPage: parseInt(document.getElementById('perPageSelect')?.value || '10'),
        selectedToDelete: null,
        isEditing: false
    };

    const API_ENDPOINTS = {
        INDEX: '/landlord/properties',
        STORE: '/landlord/properties',
        UPDATE: (id) => `/landlord/properties/${id}`,
        DELETE: (id) => `/landlord/properties/${id}`,
        DATA: '/landlord/properties/data'
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
        propertyForm: document.getElementById('propertyForm'),
        formId: document.getElementById('formId'),
        formMethod: document.getElementById('formMethod'),
        formName: document.getElementById('formName'),
        formType: document.getElementById('formType'),
        formAddress: document.getElementById('formAddress'),
        formPrice: document.getElementById('formPrice'),
        formRentType: document.getElementById('formRentType'),
        formStatus: document.getElementById('formStatus'),
        formSubmit: document.getElementById('formSubmit'),
        formCancel: document.getElementById('formCancel'),
        closeModalBtn: document.getElementById('closeModalBtn'),
        errorMessages: document.getElementById('errorMessages'),

        detailsBackdrop: document.getElementById('detailsBackdrop'),
        closeDetailsBtn: document.getElementById('closeDetailsBtn'),
        closeDetailsFooterBtn: document.getElementById('closeDetailsFooterBtn'),
        detailAvatar: document.getElementById('detailAvatar'),
        detailName: document.getElementById('detailName'),
        detailStatus: document.getElementById('detailStatus'),
        detailId: document.getElementById('detailId'),
        detailType: document.getElementById('detailType'),
        detailAddress: document.getElementById('detailAddress'),
        detailPrice: document.getElementById('detailPrice'),
        detailRentType: document.getElementById('detailRentType'),
        detailCreatedAt: document.getElementById('detailCreatedAt'),

        deleteBackdrop: document.getElementById('deleteBackdrop'),
        deleteName: document.getElementById('deleteName'),
        deleteCancel: document.getElementById('deleteCancel'),
        deleteConfirm: document.getElementById('deleteConfirm'),

        rentBackdrop: document.getElementById('rentNow'),
        closeRentBtn: document.getElementById('closeRentBtn'),
        closeRentFooterBtn: document.getElementById('closeRentFooterBtn'),
        
        rentPropertyName: document.getElementById('rentPropertyName'),
        rentPropertyPrice: document.getElementById('rentPropertyPrice'),
        rentPropertyId: document.getElementById('rentPropertyId'),
        
        renterName: document.getElementById('renterName'),
        renterPhone: document.getElementById('renterPhone'),
        renterEmail: document.getElementById('renterEmail'),
        renterAddress: document.getElementById('renterAddress'),
        startDate: document.getElementById('startDate'),
        endDate: document.getElementById('endDate'),
        
        rentForm: document.getElementById('rentForm'),
        generatePaymentBtn: document.getElementById('generatePaymentBtn'),
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
                throw new Error('Server returned an error page instead of JSON. Please check the server logs.');
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
                error: error.message
            });
            throw error;
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
        DOM.errorMessages.innerHTML = errorHtml;
        DOM.errorMessages.classList.remove('hidden');
    }

    function hideErrors() {
        if (DOM.errorMessages) {
            DOM.errorMessages.classList.add('hidden');
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    }

    function formatNumber(number) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        }).format(number);
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function showRentModal(visible) {
        if (!DOM.rentBackdrop) return;
        
        if (visible) {
            DOM.rentBackdrop.classList.remove('hidden');
            DOM.rentBackdrop.classList.add('flex');
            document.body.style.overflow = 'hidden';
        } else {
            DOM.rentBackdrop.classList.add('hidden');
            DOM.rentBackdrop.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    function closeRentModal() {
        showRentModal(false);
    }

    window.rentNow = function(id) {
        console.log('Rent Now clicked for ID:', id);
        
        const property = properties.find(p => p.id === id);
        if (!property) {
            console.error('Property not found:', id);
            return;
        }

        console.log('Property found:', property);

        if (DOM.rentPropertyName) {
            DOM.rentPropertyName.textContent = property.name || '-';
        }
        
        if (DOM.rentPropertyPrice) {
            const rentType = property.rent_type;
            let periodText = '';
            
            if (rentType === 'Monthly') {
                periodText = '/Month';
            } else if (rentType === 'Yearly') {
                periodText = '/Year';
            }
            
            DOM.rentPropertyPrice.textContent = `Rp ${formatNumber(property.price)}${periodText}`;
        }
        
        if (DOM.rentPropertyId) {
            DOM.rentPropertyId.value = property.id;
        }
        
        window.currentRentType = property.rent_type;
        
        if (DOM.renterName) DOM.renterName.value = '';
        if (DOM.renterPhone) DOM.renterPhone.value = '';
        if (DOM.renterEmail) DOM.renterEmail.value = '';
        if (DOM.renterAddress) DOM.renterAddress.value = '';
        if (DOM.startDate) DOM.startDate.value = '';
        if (DOM.endDate) DOM.endDate.value = '';
        
        console.log('Showing rent modal...');
        showRentModal(true);
        
        setTimeout(() => DOM.renterName?.focus(), 100);
    };

    window.rentNowFromDetails = function() {
        const propertyId = DOM.detailId?.textContent?.replace('#', '');
        if (propertyId) {
            closeDetailsModal();
            setTimeout(() => {
                window.rentNow(parseInt(propertyId));
            }, 100);
        }
    };

    function calculateEndDate() {
        const startDateInput = DOM.startDate;
        const endDateInput = DOM.endDate;
        const rentType = window.currentRentType;
        
        if (!startDateInput || !endDateInput || !startDateInput.value) {
            if (endDateInput) endDateInput.value = '';
            return;
        }
        
        const startDate = new Date(startDateInput.value);
        let endDate = new Date(startDate);
        
        if (rentType === 'Monthly') {
            endDate.setMonth(endDate.getMonth() + 1);
        } else if (rentType === 'Yearly') {
            endDate.setFullYear(endDate.getFullYear() + 1);
        }
        
        const formattedEndDate = endDate.toISOString().split('T')[0];
        endDateInput.value = formattedEndDate;
    }

    const today = new Date().toISOString().split('T')[0];
    DOM.startDate.setAttribute("min", today);


    async function handleRentSubmit(e) {
        e.preventDefault();
        
        const generateBtn = DOM.generatePaymentBtn;
        const btnText = generateBtn?.querySelector('.btn-text');
        const btnLoading = generateBtn?.querySelector('.btn-loading');
        
        try {
            if (btnText) btnText.classList.add('hidden');
            if (btnLoading) btnLoading.classList.remove('hidden');
            if (generateBtn) generateBtn.disabled = true;
            
            const rentData = {
                property_id: DOM.rentPropertyId?.value,
                renter_name: DOM.renterName?.value.trim(),
                renter_phone: DOM.renterPhone?.value.trim(),
                renter_email: DOM.renterEmail?.value.trim(),
                renter_address: DOM.renterAddress?.value.trim(),
                start_date: DOM.startDate?.value,
                end_date: DOM.endDate?.value
            };
            
            const { data } = await apiRequest('/landlord/rentals', {
                method: 'POST',
                body: JSON.stringify(rentData)
            });
            
            if (data.success) {
                closeRentModal();
                showNotification('Rental created successfully! Payment has been generated.', 'success');
                
                loadProperties();
                
                if (data.payment_url) {
                    window.open(data.payment_url, '_blank');
                }
            } else {
                throw new Error(data.message || 'Failed to create rental');
            }
            
        } catch (error) {
            console.error('Rent submit error:', error);
            showNotification('Error creating rental: ' + error.message, 'error');
        } finally {
            if (btnText) btnText.classList.remove('hidden');
            if (btnLoading) btnLoading.classList.add('hidden');
            if (generateBtn) generateBtn.disabled = false;
        }
    }

    async function loadProperties() {
        renderSpinnerRow(); 

        try {
            const params = new URLSearchParams({
                page: state.page,
                per_page: state.perPage,
                search: state.query,
                status: state.statusFilter
            });

            const { data } = await apiRequest(`${API_ENDPOINTS.DATA}?${params}`);
            
            if (data.success) {
                properties = data.data.data;
                renderTable(data.data.data);
                renderPagination(data.data);
            } else {
                throw new Error(data.message || 'Failed to load properties');
            }
        } catch (error) {
            console.error('Error loading properties:', error);
            showNotification('Error loading properties: ' + error.message, 'error');
        } finally {
            hideLoading();
        }
    }

    function renderTable(properties) {
        if (!DOM.tableBody) return;

        DOM.tableBody.innerHTML = '';

        if (properties.length === 0) {
            DOM.tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4 text-neutral-500">
                        <div class="flex flex-col items-center gap-4">
                            <div class="text-center">
                                <p class="text-lg font-medium">No properties found</p>
                                <p class="text-sm text-neutral-400">Try changing your search or filters</p>
                            </div>
                        </div>
                    </td>
                </tr>`;
            return;
        }

        properties.forEach(property => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div class="flex items-center gap-0">
                        <div class="form-check style-check flex items-center">
                            <input class="form-check-input rounded border input-form-dark" type="checkbox" value="${property.id}">
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex items-center gap-3">
                        <div>
                            <h6 class="text-base mb-0 fw-medium text-primary-bold">${escapeHtml(property.name)}</h6>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="px-5 py-1 rounded-full text-sm font-medium">
                        ${escapeHtml(property.type)}
                    </span>
                </td>
               <td>
                    <span class="text-sm text-secondary-light truncate block" style="max-width: 200px;">
                        ${escapeHtml(property.address)}
                    </span>
                </td>
                <td>
                    <span class="text-sm font-medium text-neutral-900 dark:text-white">
                        Rp ${formatNumber(property.price)}
                    </span>
                </td>
                <td>
                    <span class="bg-info-100 dark:bg-info-600/25 text-info-600 dark:text-info-400 px-3 py-1 rounded-full text-xs font-medium">
                        ${escapeHtml(property.rent_type)}
                    </span>
                </td>
                <td class="text-center">
                    <span class="${property.status === 'Available' ? 'bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400' : 'bg-warning-100 dark:bg-warning-600/25 text-warning-600 dark:text-warning-400'} px-5 py-1 rounded-full text-sm font-medium">
                        ${escapeHtml(property.status)}
                    </span>
                </td>
                <td class="text-center">
                    <div class="flex items-center justify-center gap-2">
                        <button onclick="viewProperty(${property.id})" class="w-8 h-8 bg-primary-50 dark:bg-primary-600/10 text-primary-600 dark:text-primary-400 rounded-full inline-flex items-center justify-center">
                           <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                        </button>
                        <button onclick="editProperty(${property.id})" class="w-8 h-8 bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 rounded-full inline-flex items-center justify-center">
                            <iconify-icon icon="lucide:edit" class="text-sm"></iconify-icon>
                        </button>
                        <button onclick="confirmDelete(${property.id})" class="w-8 h-8 bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:text-danger-400 rounded-full inline-flex items-center justify-center">
                         <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                        </button>
                    </div>
                </td>
            `;
            DOM.tableBody.appendChild(row);
        });
    }

    function renderPagination(data) {
        if (!DOM.paginationInfo || !DOM.pageNumbers) return;

        DOM.paginationInfo.textContent = `Showing ${data.from || 0} to ${data.to || 0} of ${data.total} entries`;

        DOM.pageNumbers.innerHTML = '';
        
        const startPage = Math.max(1, state.page - 2);
        const endPage = Math.min(data.last_page, startPage + 4);

        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `page-link h-8 w-8 flex items-center justify-center rounded-lg border-0 text-base font-semibold ${
                i === state.page 
                    ? 'bg-primary-600 text-white' 
                    : 'bg-neutral-300 dark:bg-neutral-600 text-secondary-light hover:bg-primary-100'
            }`;
            pageBtn.textContent = i;
            pageBtn.onclick = () => {
                state.page = i;
                loadProperties();
            };
            
            const li = document.createElement('li');
            li.className = 'page-item';
            li.appendChild(pageBtn);
            DOM.pageNumbers.appendChild(li);
        }
    }

    function openCreateModal() {
        state.isEditing = false;
        if (DOM.modalTitle) DOM.modalTitle.textContent = 'Add Property';

        if (DOM.propertyForm) DOM.propertyForm.reset();
        if (DOM.formId) DOM.formId.value = '';
        if (DOM.formMethod) DOM.formMethod.value = 'POST';
        if (DOM.formStatus) DOM.formStatus.value = 'Available';

        const submitText = DOM.formSubmit?.querySelector('.submit-text');
        if (submitText) submitText.textContent = 'Add Property';
        
        hideErrors();
        showModal(true);

        setTimeout(() => DOM.formName?.focus(), 100);
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

    function closeModal() {
        showModal(false);
        state.isEditing = false;
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

    function closeDetailsModal() {
        showDetailsModal(false);
    }

    function showDeleteModal(visible) {
        if (!DOM.deleteBackdrop) return;
        
        if (visible) {
            DOM.deleteBackdrop.classList.remove('hidden');
            DOM.deleteBackdrop.classList.add('flex');
            document.body.style.overflow = 'hidden';
        } else {
            DOM.deleteBackdrop.classList.add('hidden');
            DOM.deleteBackdrop.classList.remove('flex');
            document.body.style.overflow = '';
            state.selectedToDelete = null;
        }
    }

    function closeDeleteModal() {
        showDeleteModal(false);
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
                type: DOM.formType?.value.trim() || '',
                address: DOM.formAddress?.value.trim() || '',
                price: parseFloat(DOM.formPrice?.value) || 0,
                rent_type: DOM.formRentType?.value || '',
                status: DOM.formStatus?.value || 'Available'
            };

            const isEdit = state.isEditing;
            const method = isEdit ? 'PUT' : 'POST';
            const url = isEdit ? API_ENDPOINTS.UPDATE(DOM.formId?.value) : API_ENDPOINTS.STORE;

            const { data } = await apiRequest(url, {
                method: method,
                body: JSON.stringify(formData)
            });

            if (data.success) {
                closeModal();
                loadProperties();
                showNotification(data.message || `Property ${isEdit ? 'updated' : 'added'} successfully!`, 'success');
            } else {
                throw new Error(data.message || `Failed to ${isEdit ? 'update' : 'add'} property`);
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
            
            showNotification(error.message || `Failed to ${state.isEditing ? 'update' : 'add'} property`, 'error');
        } finally {
            if (submitText) submitText.classList.remove('hidden');
            if (submitLoading) submitLoading.classList.add('hidden');
            if (submitBtn) submitBtn.disabled = false;
        }
    }

    async function handleDelete() {
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
                closeDeleteModal();
                loadProperties();
                showNotification(data.message || 'Property deleted successfully!', 'delete');
            } else {
                throw new Error(data.message || 'Failed to delete property');
            }
        } catch (error) {
            console.error('Error deleting property:', error);
            showNotification('Error deleting property: ' + error.message, 'error');
        } finally {
            if (deleteText) deleteText.classList.remove('hidden');
            if (deleteLoading) deleteLoading.classList.add('hidden');
            if (deleteBtn) deleteBtn.disabled = false;
        }
    }

    window.viewProperty = function(id) {
        const property = properties.find(p => p.id === id);
        if (!property) return;

        const status = property.status || 'Available';

        if (DOM.detailName) DOM.detailName.textContent = property.name || '-';
        
        if (DOM.detailStatus) {
            DOM.detailStatus.innerHTML = `
                <span class="${status === 'Available' ? 
                    'bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400' : 
                    'bg-red-100 text-red-700 border border-red-200'
                } px-5 py-1 rounded-full text-xs font-semibold">
                    ${status.toUpperCase()}
                </span>
            `;
        }

        if (DOM.detailId) DOM.detailId.textContent = `#${property.id}`;
        if (DOM.detailType) DOM.detailType.textContent = property.type || '-';
        if (DOM.detailAddress) DOM.detailAddress.textContent = property.address || '-';
        if (DOM.detailPrice) DOM.detailPrice.textContent = `Rp ${formatNumber(property.price)}`;
        if (DOM.detailRentType) DOM.detailRentType.textContent = property.rent_type || '-';
        if (DOM.detailCreatedAt) DOM.detailCreatedAt.textContent = formatDate(property.created_at);

        showDetailsModal(true);
    };

    window.editProperty = async function(id) {
        try {
            const { data } = await apiRequest(`${API_ENDPOINTS.INDEX}/${id}`);
            if (data.success) {
                const property = data.data;
                
                state.isEditing = true;
                if (DOM.modalTitle) DOM.modalTitle.textContent = 'Edit Property';
                if (DOM.formId) DOM.formId.value = property.id;
                if (DOM.formMethod) DOM.formMethod.value = 'PUT';
                if (DOM.formName) DOM.formName.value = property.name;
                if (DOM.formType) DOM.formType.value = property.type;
                if (DOM.formAddress) DOM.formAddress.value = property.address;
                if (DOM.formPrice) DOM.formPrice.value = property.price;
                if (DOM.formRentType) DOM.formRentType.value = property.rent_type;
                if (DOM.formStatus) DOM.formStatus.value = property.status;

                const submitText = DOM.formSubmit?.querySelector('.submit-text');
                if (submitText) submitText.textContent = 'Update Property';
                
                hideErrors();
                showModal(true);
            }
        } catch (error) {
            console.error('Error loading property:', error);
            showNotification('Error loading property details', 'error');
        }
    };

    window.confirmDelete = function(id) {
        const property = properties.find(p => p.id === id);
        if (!property) return;

        state.selectedToDelete = property;
        if (DOM.deleteName) DOM.deleteName.textContent = property.name;
        showDeleteModal(true);
    };

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

    function initEventListeners() {
        DOM.propertyForm?.addEventListener('submit', handleFormSubmit);
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

        DOM.deleteCancel?.addEventListener('click', closeDeleteModal);
        DOM.deleteConfirm?.addEventListener('click', handleDelete);
        DOM.deleteBackdrop?.addEventListener('click', (e) => {
            if (e.target === DOM.deleteBackdrop) closeDeleteModal();
        });

        DOM.closeRentBtn?.addEventListener('click', closeRentModal);
        DOM.closeRentFooterBtn?.addEventListener('click', closeRentModal);
        DOM.rentBackdrop?.addEventListener('click', (e) => {
            if (e.target === DOM.rentBackdrop) closeRentModal();
        });
        
        DOM.rentForm?.addEventListener('submit', handleRentSubmit);
        
        DOM.startDate?.addEventListener('change', calculateEndDate);

        DOM.searchInput?.addEventListener('input', debounce(() => {
            state.query = DOM.searchInput.value;
            state.page = 1;
            loadProperties();
        }, 300));

        DOM.statusFilter?.addEventListener('change', () => {
            state.statusFilter = DOM.statusFilter.value;
            state.page = 1;
            loadProperties();
        });

        DOM.perPageSelect?.addEventListener('change', () => {
            state.perPage = parseInt(DOM.perPageSelect.value);
            state.page = 1;
            loadProperties();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
                closeDetailsModal();
                closeDeleteModal();
                closeRentModal();
            }
        });
    }

    function renderSpinnerRow() {
        if (!DOM.tableBody) return;
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

    function init() {
        console.log('Initializing Property Management...');
        
        initEventListeners();
        loadProperties();
        
        console.log('Property Management initialized successfully');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();