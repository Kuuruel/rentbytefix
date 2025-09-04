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
        DATA: '/landlord/properties/data',
        BULK_DELETE: '/landlord/properties/bulk-delete',
        RENTALS: '/landlord/rentals',
        PAYMENT_STATUS: (billId) => `/landlord/rentals/${billId}/payment-status`,
        RENTER_DETAILS: (propertyId) => `/landlord/properties/${propertyId}/renter-details`,
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

        bulkDeleteModal: document.getElementById('bulkDeleteModal'),
        bulkDeleteCount: document.getElementById('bulkDeleteCount'),
        bulkDeleteCancel: document.getElementById('bulkDeleteCancel'),
        bulkDeleteConfirm: document.getElementById('bulkDeleteConfirm'),

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

        paymentLinkModal: document.getElementById('paymentLinkModal'),
        closePaymentLinkBtn: document.getElementById('closePaymentLinkBtn'),
        closePaymentLinkFooterBtn: document.getElementById('closePaymentLinkFooterBtn'),
        paymentPropertyName: document.getElementById('paymentPropertyName'),
        paymentRenterName: document.getElementById('paymentRenterName'),
        paymentAmount: document.getElementById('paymentAmount'),
        paymentDueDate: document.getElementById('paymentDueDate'),
        generatedPaymentLink: document.getElementById('generatedPaymentLink'),
        copyPaymentLink: document.getElementById('copyPaymentLink'),
        shareViaWhatsapp: document.getElementById('shareViaWhatsapp'),
        generateAnotherLink: document.getElementById('generateAnotherLink'),

        renterDetailsModal: document.getElementById('renterDetailsModal'),
        closeRenterDetailsBtn: document.getElementById('closeRenterDetailsBtn'),
        closeRenterDetailsFooterBtn: document.getElementById('closeRenterDetailsFooterBtn'),
        renterDetailPropertyName: document.getElementById('renterDetailPropertyName'),
        renterDetailName: document.getElementById('renterDetailName'),
        renterDetailPhone: document.getElementById('renterDetailPhone'),
        renterDetailEmail: document.getElementById('renterDetailEmail'),
        renterDetailAddress: document.getElementById('renterDetailAddress'),
        renterDetailStartDate: document.getElementById('renterDetailStartDate'),
        renterDetailEndDate: document.getElementById('renterDetailEndDate'),
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

    function showBulkDeleteModal() {
        const checkedBoxes = document.querySelectorAll('.property-checkbox:checked');
        const selectedCount = checkedBoxes.length;
        
        if (selectedCount === 0) {
            showNotification('Please select properties to delete', 'error');
            return;
        }
        
        if (DOM.bulkDeleteCount) {
            DOM.bulkDeleteCount.textContent = selectedCount;
        }
        
        if (DOM.bulkDeleteModal) {
            DOM.bulkDeleteModal.classList.remove('hidden');
            DOM.bulkDeleteModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeBulkDeleteModal() {
        if (DOM.bulkDeleteModal) {
            DOM.bulkDeleteModal.classList.add('hidden');
            DOM.bulkDeleteModal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    function showPaymentLinkModal(data) {
        if (DOM.paymentPropertyName) DOM.paymentPropertyName.textContent = data.property_name;
        if (DOM.paymentRenterName) DOM.paymentRenterName.textContent = data.renter_name;
        if (DOM.paymentAmount) DOM.paymentAmount.textContent = data.amount;
        if (DOM.paymentDueDate) DOM.paymentDueDate.textContent = data.due_date;
        if (DOM.generatedPaymentLink) DOM.generatedPaymentLink.value = data.payment_link;
        
        if (DOM.paymentLinkModal) {
            DOM.paymentLinkModal.classList.remove('hidden');
            DOM.paymentLinkModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closePaymentLinkModal() {
        if (DOM.paymentLinkModal) {
            DOM.paymentLinkModal.classList.add('hidden');
            DOM.paymentLinkModal.classList.remove('flex');
            document.body.style.overflow = '';
        }
    }

    function handleCopyPaymentLink() {
        const linkInput = DOM.generatedPaymentLink;
        const copyText = DOM.copyPaymentLink?.querySelector('.copy-text');
        const copySuccess = DOM.copyPaymentLink?.querySelector('.copy-success');
        
        if (linkInput) {
            linkInput.select();
            linkInput.setSelectionRange(0, 99999);
            document.execCommand('copy');
            
            if (copyText && copySuccess) {
                copyText.classList.add('hidden');
                copySuccess.classList.remove('hidden');
                
                setTimeout(() => {
                    copyText.classList.remove('hidden');
                    copySuccess.classList.add('hidden');
                }, 2000);
            }
        }
    }

    function handleWhatsAppShare() {
        const paymentLink = DOM.generatedPaymentLink?.value;
        const propertyName = DOM.paymentPropertyName?.textContent;
        const amount = DOM.paymentAmount?.textContent;
        
        if (paymentLink && propertyName && amount) {
            const message = `Hi! Silakan lakukan pembayaran untuk penyewaan properti "${propertyName}" dengan total ${amount}. Link pembayaran: ${paymentLink}`;
            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        }
    }

    function handleGenerateAnotherLink() {
        closePaymentLinkModal();
        if (DOM.rentForm) DOM.rentForm.reset();
        showRentModal(true);
    }

    function showPaymentModal(paymentData) {
        const existingModal = document.getElementById('paymentModal');
        if (existingModal) {
            existingModal.remove();
        }

        const modal = document.createElement('div');
        modal.id = 'paymentModal';
        modal.className = 'fixed inset-0 z-50 flex items-center justify-center';
        modal.innerHTML = `
            <div class="absolute inset-0 bg-black/60"></div>
            <div class="bg-white dark:bg-neutral-700 rounded-xl mx-4 shadow-xl z-10 overflow-hidden border border-neutral-200 dark:border-neutral-600" 
                 style="width: 32rem !important; max-width: 90vw !important;">
                
                <!-- Header -->
                <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-600 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-neutral-800 dark:to-neutral-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-white flex items-center gap-2">
                            <iconify-icon icon="ph:check-circle" class="text-2xl text-green-600 dark:text-green-400"></iconify-icon>
                            Payment Created
                        </h3>
                        <button id="closePaymentModal" class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 text-xl">&times;</button>
                    </div>
                </div>

                <!-- Body -->
                <div class="px-6 py-6">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-green-100 dark:bg-green-800/30 rounded-full flex items-center justify-center mx-auto mb-4">
                            <iconify-icon icon="ph:currency-circle-dollar" class="text-green-600 dark:text-green-400 text-2xl"></iconify-icon>
                        </div>
                        <h4 class="text-lg font-semibold text-neutral-900 dark:text-white mb-2">Rental Successfully Created!</h4>
                        <p class="text-sm text-neutral-600 dark:text-neutral-300">
                            Please make payment to complete the rental process.
                        </p>
                    </div>

                    <!-- Payment Info -->
                    <div class="bg-neutral-50 dark:bg-neutral-800 rounded-lg p-4 mb-6">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Total Payment</span>
                            <span class="text-lg font-bold text-neutral-900 dark:text-white">${paymentData.amount}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Bill ID</span>
                            <span class="text-sm font-semibold text-neutral-900 dark:text-white">#${paymentData.bill_id}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-3">
                        <button onclick="window.open('${paymentData.payment_link}', '_blank')" 
                                class="w-full px-6 py-3 rounded-lg text-sm bg-blue-600 hover:bg-blue-700 text-white transition-colors font-medium">
                            <iconify-icon icon="ph:credit-card" class="mr-2"></iconify-icon>
                            Pay Now
                        </button>
                        <button onclick="window.propertyManager.checkPaymentStatus(${paymentData.bill_id})" 
                                class="w-full px-6 py-3 rounded-lg text-sm bg-green-600 hover:bg-green-700 text-white transition-colors font-medium">
                            <iconify-icon icon="ph:arrow-clockwise" class="mr-2"></iconify-icon>
                            Check Payment Status
                        </button>
                        <button id="closePaymentModalBtn" 
                                class="w-full px-6 py-3 rounded-lg text-sm bg-neutral-200 dark:bg-neutral-600 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-300 dark:hover:bg-neutral-500 transition-colors font-medium">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';

        document.getElementById('closePaymentModal').onclick = closePaymentModal;
        document.getElementById('closePaymentModalBtn').onclick = closePaymentModal;
        modal.onclick = (e) => {
            if (e.target === modal) closePaymentModal();
        };
    }

    function closePaymentModal() {
        const modal = document.getElementById('paymentModal');
        if (modal) {
            modal.remove();
            document.body.style.overflow = '';
        }
    }

    async function checkPaymentStatus(billId) {
        try {
            const { data } = await apiRequest(API_ENDPOINTS.PAYMENT_STATUS(billId));
            
            if (data.success) {
                const bill = data.data.bill;
                const transaction = data.data.transaction;
                
                let message = '';
                let type = 'info';
                
                switch (bill.status) {
                    case 'paid':
                        message = 'Pembayaran berhasil! Properti sudah disewa.';
                        type = 'success';
                        closePaymentModal();
                        setTimeout(() => {
                            loadProperties();
                            clearAllSelections();
                        }, 1000);
                        break;
                    case 'pending':
                        if (transaction && transaction.va_number) {
                            message = `Payment pending. Transfer to ${transaction.bank.toUpperCase()}: ${transaction.va_number}`;
                        } else {
                            message = 'Payment is still pending. Please complete the payment.';
                        }
                        type = 'info';
                        break;
                    case 'failed':
                        message = 'Payment failed. Please try again.';
                        type = 'error';
                        break;
                    default:
                        message = `Status: ${bill.status}`;
                        type = 'info';
                }
                
                showNotification(message, type);
            } else {
                throw new Error(data.message || 'Failed to check payment status');
            }
        } catch (error) {
            console.error('Error checking payment status:', error);
            showNotification('Error checking payment status: ' + error.message, 'error');
        }
    }

    async function handleBulkDelete() {
        const checkedBoxes = document.querySelectorAll('.property-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(cb => parseInt(cb.value));
        
        if (selectedIds.length === 0) {
            showNotification('No properties selected for deletion', 'error');
            return;
        }

        const confirmBtn = DOM.bulkDeleteConfirm;
        const btnText = confirmBtn?.querySelector('.bulk-delete-text');
        const btnLoading = confirmBtn?.querySelector('.bulk-delete-loading');

        try {
            if (btnText) btnText.classList.add('hidden');
            if (btnLoading) btnLoading.classList.remove('hidden');
            if (confirmBtn) confirmBtn.disabled = true;

            let response, data;
            
            try {
                const idsParam = selectedIds.join(',');
                const result = await apiRequest(`${API_ENDPOINTS.BULK_DELETE}?ids=${idsParam}`, {
                    method: 'DELETE'
                });
                response = result.response;
                data = result.data;
            } catch (deleteError) {
                try {
                    const result = await apiRequest(API_ENDPOINTS.BULK_DELETE, {
                        method: 'DELETE',
                        body: JSON.stringify({ ids: selectedIds })
                    });
                    response = result.response;
                    data = result.data;
                } catch (deleteBodyError) {
                    console.log('Bulk delete not available, deleting individually...');
                    let successCount = 0;
                    let errors = [];
                    
                    for (const id of selectedIds) {
                        try {
                            await apiRequest(API_ENDPOINTS.DELETE(id), {
                                method: 'DELETE'
                            });
                            successCount++;
                        } catch (individualError) {
                            errors.push(`Property ${id}: ${individualError.message}`);
                        }
                    }
                    
                    if (successCount > 0) {
                        closeBulkDeleteModal();
                        loadProperties();
                        clearAllSelections();
                        
                        if (errors.length > 0) {
                            showNotification(`${successCount} properties deleted successfully. ${errors.length} failed.`, 'error');
                            console.error('Individual delete errors:', errors);
                        } else {
                            showNotification(`${successCount} properties deleted successfully!`, 'delete');
                        }
                    } else {
                        throw new Error('Failed to delete any properties');
                    }
                    return;
                }
            }

            if (data.success) {
                closeBulkDeleteModal();
                loadProperties();
                clearAllSelections();
                showNotification(`${selectedIds.length} properties deleted successfully!`, 'delete');
            } else {
                throw new Error(data.message || 'Failed to delete properties');
            }
        } catch (error) {
            console.error('Bulk delete error:', error);
            showNotification('Error deleting properties: ' + error.message, 'error');
        } finally {
            if (btnText) btnText.classList.remove('hidden');
            if (btnLoading) btnLoading.classList.add('hidden');
            if (confirmBtn) confirmBtn.disabled = false;
        }
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

    async function handleRentSubmit(e) {
        e.preventDefault();
        
        const generateBtn = DOM.generatePaymentBtn;
        const btnText = generateBtn?.querySelector('.btn-text');
        const btnLoading = generateBtn?.querySelector('.submit-loading');
        
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
            
            const { data } = await apiRequest(API_ENDPOINTS.RENTALS, {
                method: 'POST',
                body: JSON.stringify(rentData)
            });
            
            if (data.success) {
                closeRentModal();

                if (data.data.payment_link) {
                    showPaymentLinkModal(data.data);
                } else {
                    showPaymentModal(data.data);
                }
                
                loadProperties();
                showNotification('Payment link generated successfully!', 'success');
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

        let actionButtons = '';

        const viewButton = `
            <button onclick="viewProperty(${property.id})" class="w-8 h-8 bg-primary-50 dark:bg-primary-600/10 text-primary-600 dark:text-primary-400 rounded-full inline-flex items-center justify-center">
               <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
            </button>
        `;

        let rentButton = '';
        if (property.status === 'Rented') {
            rentButton = `
                <button onclick="showRenterDetails(${property.id})" class="w-8 h-8 bg-info-100 dark:bg-info-600/25 text-info-600 dark:text-info-400 rounded-full inline-flex items-center justify-center" title="View Renter Details">
                    <iconify-icon icon="ph:user-circle" class="text-sm"></iconify-icon>
                </button>
            `;
        }

        let editDeleteButtons = '';
        if (property.status === 'Available') {
            editDeleteButtons = `
                <button onclick="editProperty(${property.id})" class="w-8 h-8 bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 rounded-full inline-flex items-center justify-center">
                    <iconify-icon icon="lucide:edit" class="text-sm"></iconify-icon>
                </button>
                <button onclick="confirmDelete(${property.id})" class="w-8 h-8 bg-danger-100 dark:bg-danger-600/25 text-danger-600 dark:text-danger-400 rounded-full inline-flex items-center justify-center">
                    <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                </button>
            `;
        } else {
            editDeleteButtons = `
                <button disabled class="w-8 h-8 bg-neutral-100 dark:bg-neutral-600/25 text-neutral-400 rounded-full inline-flex items-center justify-center cursor-not-allowed" title="Cannot edit ${property.status.toLowerCase()} property">
                    <iconify-icon icon="lucide:edit" class="text-sm"></iconify-icon>
                </button>
                <button disabled class="w-8 h-8 bg-neutral-100 dark:bg-neutral-600/25 text-neutral-400 rounded-full inline-flex items-center justify-center cursor-not-allowed" title="Cannot delete ${property.status.toLowerCase()} property">
                    <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                </button>
            `;
        }
        
        actionButtons = viewButton + rentButton + editDeleteButtons;

        row.innerHTML = `
            <td>
                <div class="flex items-center gap-0">
                    <div class="form-check style-check flex items-center">
                        <input class="form-check-input rounded border input-form-dark property-checkbox" type="checkbox" value="${property.id}">
                    </div>
                </div>
            </td>
            <td>
                <div class="flex items-center justify-center gap-3">
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
                <span class="${getStatusClass(property.status)} px-5 py-1 rounded-full text-sm font-medium">
                    ${escapeHtml(property.status)}
                </span>
            </td>
           <td class="text-center">
                <div class="flex items-center justify-center gap-2">
                    ${actionButtons}
                </div>
            </td>
        `;
        DOM.tableBody.appendChild(row);
    });
}


    function getStatusClass(status) {
    switch(status) {
        case 'Available':
            return 'bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400';
        case 'Processing':
            return 'bg-info-100 dark:bg-info-600/25 text-info-600 dark:text-info-400';
        case 'Rented':
            return 'bg-warning-100 dark:bg-warning-600/25 text-warning-600 dark:text-warning-400';
        default:
            return 'bg-neutral-100 dark:bg-neutral-600/25 text-neutral-600 dark:text-neutral-400';
    }
}

    function toggleSelectAll() {
        const checkboxes = document.querySelectorAll('.property-checkbox');
        const isChecked = DOM.selectAll?.checked || false;
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        
        updateSelectActions();
    }

    function updateSelectActions() {
        const checkedBoxes = document.querySelectorAll('.property-checkbox:checked');
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
                            <span id="selectedCount">${selectedCount}</span> property(ies) selected
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="bulkDeleteBtn" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-xs font-medium flex items-center gap-2 transition-colors">
                            <iconify-icon icon="ph:trash" class="text-sm"></iconify-icon>
                            Delete Selected
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
                const selectedCountEl = document.getElementById('selectedCount');
                if (selectedCountEl) {
                    selectedCountEl.textContent = selectedCount;
                }
            }

            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const clearSelectionBtn = document.getElementById('clearSelection');

            if (bulkDeleteBtn) {
                bulkDeleteBtn.onclick = showBulkDeleteModal;
            }
            if (clearSelectionBtn) {
                clearSelectionBtn.onclick = clearAllSelections;
            }

        } else {
            if (bulkActionsContainer) {
                bulkActionsContainer.remove();
            }
        }
    }

    function updateSelectAll() {
        const checkboxes = document.querySelectorAll('.property-checkbox');
        const checkedBoxes = document.querySelectorAll('.property-checkbox:checked');

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

    function clearAllSelections() {
        const checkboxes = document.querySelectorAll('.property-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = false);
        
        if (DOM.selectAll) {
            DOM.selectAll.checked = false;
            DOM.selectAll.indeterminate = false;
        }
        
        updateSelectActions();
    }

    function initSelectEventListeners() {
        DOM.selectAll?.addEventListener('change', toggleSelectAll);

        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('property-checkbox')) {
                updateSelectAll();
            }
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
                status === 'Processing' ? 
                'bg-info-100 dark:bg-info-600/25 text-info-600 dark:text-info-400' : 
                'bg-warning-100 dark:bg-warning-600/25 text-warning-600 dark:text-warning-400'
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

    const rentNowBtn = document.getElementById('rentNowBtn');
    const renterDetailBtn = document.getElementById('renterDetailBtn');
    
    if (status === 'Rented') {
        if (rentNowBtn) rentNowBtn.classList.add('hidden');
        if (renterDetailBtn) {
            renterDetailBtn.classList.remove('hidden');
            renterDetailBtn.onclick = () => {
                closeDetailsModal();
                setTimeout(() => showRenterDetails(id), 100);
            };
        }
    } else if (status === 'Processing') {
        if (rentNowBtn) rentNowBtn.classList.add('hidden');
        if (renterDetailBtn) renterDetailBtn.classList.add('hidden');
    } else {
        if (rentNowBtn) rentNowBtn.classList.remove('hidden');
        if (renterDetailBtn) renterDetailBtn.classList.add('hidden');
    }

    showDetailsModal(true);
};


    function showRenterDetailsModal(visible) {
    if (!DOM.renterDetailsModal) return;
    
    if (visible) {
        DOM.renterDetailsModal.classList.remove('hidden');
        DOM.renterDetailsModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    } else {
        DOM.renterDetailsModal.classList.add('hidden');
        DOM.renterDetailsModal.classList.remove('flex');
        document.body.style.overflow = '';
    }
}

function closeRenterDetailsModal() {
    showRenterDetailsModal(false);
}

window.showRenterDetails = async function(propertyId) {
    try {
        const property = properties.find(p => p.id === propertyId);
        if (!property) {
            showNotification('Property not found', 'error');
            return;
        }

        if (DOM.renterDetailPropertyName) {
            DOM.renterDetailPropertyName.textContent = property.name;
        }

        if (DOM.renterDetailName) DOM.renterDetailName.textContent = 'Loading...';
        if (DOM.renterDetailPhone) DOM.renterDetailPhone.textContent = 'Loading...';
        if (DOM.renterDetailEmail) DOM.renterDetailEmail.textContent = 'Loading...';
        if (DOM.renterDetailAddress) DOM.renterDetailAddress.textContent = 'Loading...';
        if (DOM.renterDetailStartDate) DOM.renterDetailStartDate.textContent = 'Loading...';
        if (DOM.renterDetailEndDate) DOM.renterDetailEndDate.textContent = 'Loading...';

        showRenterDetailsModal(true);

        console.log('Fetching renter details from:', API_ENDPOINTS.RENTER_DETAILS(propertyId));

        const { data } = await apiRequest(API_ENDPOINTS.RENTER_DETAILS(propertyId));

        console.log('Renter details response:', data);
        
        if (data.success && data.data) {
            const renterData = data.data;

            console.log('Renter data structure:', renterData);

            if (DOM.renterDetailName) {
                DOM.renterDetailName.textContent = renterData.renter_name || renterData.name || 'No name provided';
            }
            if (DOM.renterDetailPhone) {
                DOM.renterDetailPhone.textContent = renterData.renter_phone || renterData.phone || 'No phone provided';
            }
            if (DOM.renterDetailEmail) {
                DOM.renterDetailEmail.textContent = renterData.renter_email || renterData.email || 'No email provided';
            }
            if (DOM.renterDetailAddress) {
                DOM.renterDetailAddress.textContent = renterData.renter_address || renterData.address || 'No address provided';
            }
            if (DOM.renterDetailStartDate) {
                const startDate = renterData.start_date || renterData.rental_start || renterData.created_at;
                DOM.renterDetailStartDate.textContent = startDate ? formatDate(startDate) : 'No start date';
            }
            if (DOM.renterDetailEndDate) {
                const endDate = renterData.end_date || renterData.rental_end;
                DOM.renterDetailEndDate.textContent = endDate ? formatDate(endDate) : 'No end date';
            }
        } else {
            console.error('API returned success but no renter data:', data);

            if (data.renter) {
                const renterData = data.renter;
                console.log('Using alternative data structure:', renterData);
                
                if (DOM.renterDetailName) DOM.renterDetailName.textContent = renterData.name || 'No name provided';
                if (DOM.renterDetailPhone) DOM.renterDetailPhone.textContent = renterData.phone || 'No phone provided';
                if (DOM.renterDetailEmail) DOM.renterDetailEmail.textContent = renterData.email || 'No email provided';
                if (DOM.renterDetailAddress) DOM.renterDetailAddress.textContent = renterData.address || 'No address provided';
                if (DOM.renterDetailStartDate) DOM.renterDetailStartDate.textContent = renterData.start_date ? formatDate(renterData.start_date) : 'No start date';
                if (DOM.renterDetailEndDate) DOM.renterDetailEndDate.textContent = renterData.end_date ? formatDate(renterData.end_date) : 'No end date';
            } else {
                throw new Error(data.message || 'No renter data found for this property');
            }
        }
    } catch (error) {
        console.error('Error loading renter details:', error);
        showNotification('Error loading renter details: ' + error.message, 'error');

        const errorMsg = 'Error loading data';
        if (DOM.renterDetailName) DOM.renterDetailName.textContent = errorMsg;
        if (DOM.renterDetailPhone) DOM.renterDetailPhone.textContent = errorMsg;
        if (DOM.renterDetailEmail) DOM.renterDetailEmail.textContent = errorMsg;
        if (DOM.renterDetailAddress) DOM.renterDetailAddress.textContent = errorMsg;
        if (DOM.renterDetailStartDate) DOM.renterDetailStartDate.textContent = errorMsg;
        if (DOM.renterDetailEndDate) DOM.renterDetailEndDate.textContent = errorMsg;
    }
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

        DOM.bulkDeleteCancel?.addEventListener('click', closeBulkDeleteModal);
        DOM.bulkDeleteConfirm?.addEventListener('click', handleBulkDelete);
        DOM.bulkDeleteModal?.addEventListener('click', (e) => {
            if (e.target === DOM.bulkDeleteModal) closeBulkDeleteModal();
        });

        DOM.closeRentBtn?.addEventListener('click', closeRentModal);
        DOM.closeRentFooterBtn?.addEventListener('click', closeRentModal);
        DOM.rentBackdrop?.addEventListener('click', (e) => {
            if (e.target === DOM.rentBackdrop) closeRentModal();
        });
        
        DOM.rentForm?.addEventListener('submit', handleRentSubmit);
        DOM.startDate?.addEventListener('change', calculateEndDate);

        DOM.closePaymentLinkBtn?.addEventListener('click', closePaymentLinkModal);
        DOM.closePaymentLinkFooterBtn?.addEventListener('click', closePaymentLinkModal);
        DOM.copyPaymentLink?.addEventListener('click', handleCopyPaymentLink);
        DOM.shareViaWhatsapp?.addEventListener('click', handleWhatsAppShare);
        DOM.generateAnotherLink?.addEventListener('click', handleGenerateAnotherLink);
        DOM.paymentLinkModal?.addEventListener('click', (e) => {
            if (e.target === DOM.paymentLinkModal) closePaymentLinkModal();
        });

        DOM.closeRenterDetailsBtn?.addEventListener('click', closeRenterDetailsModal);
        DOM.closeRenterDetailsFooterBtn?.addEventListener('click', closeRenterDetailsModal);
        DOM.renterDetailsModal?.addEventListener('click', (e) => {
            if (e.target === DOM.renterDetailsModal) closeRenterDetailsModal();
        });

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
                closeBulkDeleteModal();
                closePaymentLinkModal();
                closePaymentModal();
                closeRenterDetailsModal();
            }
        });

        initSelectEventListeners();

        const today = new Date().toISOString().split('T')[0];
        if (DOM.startDate) {
            DOM.startDate.setAttribute("min", today);
        }
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

    window.propertyManager = {
        checkPaymentStatus: checkPaymentStatus,
        rentNow: window.rentNow,
        rentNowFromDetails: window.rentNowFromDetails,
        viewProperty: window.viewProperty,
        editProperty: window.editProperty,
        confirmDelete: window.confirmDelete
    };

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