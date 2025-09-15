let currentPage = 1;
let currentTab = 'active';
let searchTerm = '';
let priorityFilter = '';
let targetFilter = '';
let selectedNotifications = [];
let selectedTenants = [];
let deleteId = null;
let archiveId = null;
let restoreId = null;

let currentSettings = {
    default_priority: 'Normal',
    default_delivery_methods: ['Dashboard']
};


let saveButtonInitialized = false;


document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM Content Loaded');

    loadSettings();
    loadTenants();
    loadNotifications();
    setupEventListeners();
    loadTenantsFromDatabase();

    if (!saveButtonInitialized) {
        setupSaveButton();
        saveButtonInitialized = true;
    }
});


function loadSettings() {
    console.log('Loading settings...');
    fetch('/admin/notifications/get-settings')
        .then(response => response.json())
        .then(data => {
            console.log('Settings loaded:', data);
            if (data.success) {
                currentSettings = data.data;
                populateSettingsForm(data.data);

                setTimeout(() => {
                    applyDefaultsToCreateForm();
                }, 500);
            }
        })
        .catch(error => console.error('Error loading settings:', error));
}

function applyDefaultsToCreateForm() {
    console.log('Applying defaults to create form:', currentSettings);

    const prioritySelect = document.getElementById('desig');
    if (prioritySelect && currentSettings.default_priority) {
        prioritySelect.value = currentSettings.default_priority;
        console.log('Priority set to:', currentSettings.default_priority);
    }

    if (currentSettings.default_delivery_methods && Array.isArray(currentSettings.default_delivery_methods)) {
        const deliveryCheckboxes = document.querySelectorAll('#dropdown-content input[type="checkbox"]');
        deliveryCheckboxes.forEach(cb => cb.checked = false);

        currentSettings.default_delivery_methods.forEach(method => {
            let checkbox = null;

            if (method === 'Dashboard') {
                checkbox = document.getElementById('dashboard');
            } else if (method === 'Push Notifications') {
                checkbox = document.getElementById('push');
            }

            if (!checkbox) {
                deliveryCheckboxes.forEach(cb => {
                    if (cb.value === method) {
                        checkbox = cb;
                    }
                });
            }

            if (checkbox) {
                checkbox.checked = true;
                console.log('Delivery method checked:', method);
            }
        });

        updateSelection();
    }
}

function populateSettingsForm(settings) {
    console.log('Populating settings form with:', settings);

    const defaultPrioritySelect = document.getElementById('default-priority');
    if (defaultPrioritySelect) {
        defaultPrioritySelect.value = settings.default_priority || 'Normal';
    }

    if (settings.default_delivery_methods && Array.isArray(settings.default_delivery_methods)) {
        const settingsCheckboxes = document.querySelectorAll('#settings-dropdown-content input[type="checkbox"]');
        settingsCheckboxes.forEach(cb => cb.checked = false);

        settings.default_delivery_methods.forEach(method => {
            let checkbox = null;

            if (method === 'Dashboard') {
                checkbox = document.getElementById('settings-dashboard');
            } else if (method === 'Push Notifications') {
                checkbox = document.getElementById('settings-push');
            }

            if (checkbox) {
                checkbox.checked = true;
            }
        });

        updateSettingsSelection();
    }

    const notificationToggle = document.getElementById('notificationToggle');
    const displayNotifications = document.getElementById('displayNotifications');

    if (notificationToggle) {
        notificationToggle.checked = settings.push_enabled || false;
    }
    if (displayNotifications) {
        displayNotifications.value = settings.display_count || 5;
    }

    updateToggleText();
}

function setupSaveButton() {
    console.log('Setting up save button...');

    document.addEventListener('click', function (e) {
        if (e.target.id === 'settingsSaveButton' ||
            (e.target.textContent.trim() === 'Save' &&
                e.target.closest('#notification-password'))) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Save button clicked via delegation');
            handleSaveSettings();
        }
    }, {
        once: false
    });
}

function handleSaveSettings() {
    console.log('handleSaveSettings called');

    if (window.savingInProgress) {
        console.log('Save already in progress, ignoring...');
        return;
    }

    window.savingInProgress = true;

    const formData = {
        default_priority: document.getElementById('default-priority').value,
        default_delivery_methods: getSelectedSettingsDeliveryMethods(),
        push_enabled: document.getElementById('notificationToggle').checked,
        dashboard_display_count: parseInt(document.getElementById('displayNotifications').value)
    };

    console.log('Submitting settings:', formData);

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
            console.log('Settings response:', data);
            if (data.success) {
                currentSettings = data.data;
                applyDefaultsToCreateForm();
                showAlert('Settings successfully updated', 'info');
            } else {
                console.error('Settings update failed:', data.errors);
                showAlert('Failed to update settings', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while updating settings', 'error');
        })
        .finally(() => {
            window.savingInProgress = false;
        });
}

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

function updateTenantDropdown() {
    window.tenants = window.tenantsData;
}

function loadTenantsFromDatabase() {
    console.log('Loading tenants from database...');

    fetch('/admin/notifications/get-tenants')
        .then(response => response.json())
        .then(data => {
            console.log('Tenants loaded:', data);
            if (data.success) {
                window.tenants = data.data;
                console.log('Tenants set to window:', window.tenants);
            } else {
                console.error('Failed to load tenants:', data);
                window.tenants = [];
            }
        })
        .catch(error => {
            console.error('Error loading tenants:', error);
            window.tenants = [];
        });
}

function toggleTenantList() {
    const select = document.getElementById('depart');
    const tenantSelection = document.getElementById('tenantSelection');

    if (select.value === 'specific') {
        tenantSelection.style.display = 'block';

        if (window.tenants && window.tenants.length > 0) {
            renderTenantList(window.tenants);
        } else {
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
        tenantItem.className = `flex items-center p-4 hover:bg-gray-50 rounded-lg cursor-pointer border transition-colors mb-3 ${isSelected ? 'bg-blue-50 border-blue-200' : 'border-gray-200'}`;
        tenantItem.onclick = () => toggleTenantSelection(tenant.id);

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

function toggleTenantSelection(tenantId) {
    const index = selectedTenants.indexOf(tenantId);
    if (index > -1) {
        selectedTenants.splice(index, 1);
    } else {
        selectedTenants.push(tenantId);
    }

    const searchTerm = document.getElementById('tenantSearch').value.toLowerCase();
    const filteredTenants = window.tenants.filter(tenant =>
        tenant.name.toLowerCase().includes(searchTerm) ||
        tenant.email.toLowerCase().includes(searchTerm)
    );
    renderTenantList(filteredTenants);

    console.log('Selected tenants:', selectedTenants);
}

function toggleDropdown() {
    const content = document.getElementById('dropdown-content');
    const chevron = document.querySelector('.chevron');

    content.classList.toggle('hidden');
    if (chevron) chevron.classList.toggle('open');
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

function toggleSettingsDropdown() {
    const content = document.getElementById('settings-dropdown-content');
    if (content) {
        content.classList.toggle('hidden');
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
    const checkboxes = document.querySelectorAll('#settings-dropdown-content input[type="checkbox"]:checked');
    const selectedOptions = Array.from(checkboxes).map(cb => cb.value);

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

function loadNotifications(page = 1) {
    const endpoint = currentTab === 'active' ?
        '/admin/notifications/get-all-notifications' :
        '/admin/notifications/get-archived-notifications';

    const params = new URLSearchParams({
        page: page,
        per_page: 10,
        search: searchTerm,
        priority: priorityFilter,
        target: targetFilter
    });

    console.log('Loading notifications with params:', {
        tab: currentTab,
        page: page,
        search: searchTerm,
        priority: priorityFilter,
        target: targetFilter
    });


    showLoadingState();

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
            showErrorState();
        })
        .finally(() => {
            hideLoadingState();
        });
}

function showLoadingState() {
    const tbodyId = currentTab === 'active' ? 'tableBody' : 'archivedTableBody';
    let tbody = document.querySelector(`#${tbodyId}`);

    if (!tbody) {
        const activeTabContent = document.querySelector(currentTab === 'active' ? '#styled-todoList' : '#styled-recentLead');
        if (activeTabContent) {
            tbody = activeTabContent.querySelector('tbody');
        }
    }

    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-12">
                    <div class="flex flex-col items-center justify-center space-y-3">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                        <p class="text-neutral-500 text-sm">Loading notifications...</p>
                    </div>
                </td>
            </tr>
        `;
    }
}

function hideLoadingState() {

}

function showErrorState() {
    const tbodyId = currentTab === 'active' ? 'tableBody' : 'archivedTableBody';
    let tbody = document.querySelector(`#${tbodyId}`);

    if (!tbody) {
        const activeTabContent = document.querySelector(currentTab === 'active' ? '#styled-todoList' : '#styled-recentLead');
        if (activeTabContent) {
            tbody = activeTabContent.querySelector('tbody');
        }
    }

    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-12">
                    <div class="flex flex-col items-center justify-center space-y-3">
                        <iconify-icon icon="lucide:alert-circle" class="text-4xl text-red-500"></iconify-icon>
                        <p class="text-neutral-500 text-sm">Failed to load notifications</p>
                        <button onclick="loadNotifications()" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                            Try Again
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }
}

function populateTable(notifications) {
    const tbodyId = currentTab === 'active' ? 'tableBody' : 'archivedTableBody';
    const tbody = document.querySelector(`#${tbodyId}`);

    if (!tbody) {
        const activeTabContent = document.querySelector(currentTab === 'active' ? '#styled-todoList' : '#styled-recentLead');
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

function populateTableRows(tbody, notifications) {
    if (notifications.length === 0) {
        tbody.innerHTML = `
         <tr>
    <td colspan="7" class="text-center py-8 text-neutral-500 align-middle">
        <div class="flex flex-col items-center justify-center min-h-[100px]">
            <p class="text-lg font-medium text-neutral-500 ">No notifications found</p>
            <p class="text-sm text-neutral-400 dark:text-neutral-400">There are no notifications to display</p>
        </div>
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

function createTableRow(notif, index) {
    const tr = document.createElement('tr');
    tr.className = 'hover:bg-neutral-50 dark:hover:bg-gray-800 transition-colors';

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

function getActionButtons(notif) {
    if (currentTab === 'active') {
        return `
            <div class="flex items-center justify-center gap-2">
                <button onclick="archiveNotification(${notif.id})" 
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-warning-100 text-warning-600 hover:bg-warning-200 dark:bg-warning-600/25 dark:text-warning-400 transition-colors"
                        title="Archive">
                    <iconify-icon icon="lucide:archive" class="text-sm"></iconify-icon>
                </button>
                <button onclick="deleteNotification(${notif.id})" 
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-danger-100 text-danger-600 hover:bg-danger-200 dark:bg-danger-600/25 dark:text-danger-400 transition-colors"
                        title="Delete Permanently">
                    <iconify-icon icon="lucide:trash-2" class="text-sm"></iconify-icon>
                </button>
            </div>
        `;
    } else {
        return `
            <div class="flex items-center justify-center gap-2">
                <button onclick="restoreNotification(${notif.id})" 
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-success-100 text-success-600 hover:bg-success-200 dark:bg-success-600/25 dark:text-success-400 transition-colors"
                        title="Restore">
                    <iconify-icon icon="lucide:rotate-ccw" class="text-sm"></iconify-icon>
                </button>
                <button onclick="deleteNotification(${notif.id})" 
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-danger-100 text-danger-600 hover:bg-danger-200 dark:bg-danger-600/25 dark:text-danger-400 transition-colors"
                        title="Delete Permanently">
                    <iconify-icon icon="lucide:trash-2" class="text-sm"></iconify-icon>
                </button>
            </div>
        `;
    }
}


function updatePagination(pagination) {
    const paginationInfoId = currentTab === 'active' ? 'paginationInfo' : 'archivedPaginationInfo';
    const pageNumbersId = currentTab === 'active' ? 'pageNumbers' : 'archivedPageNumbers';

    let paginationInfo = document.getElementById(paginationInfoId);
    let pageNumbers = document.getElementById(pageNumbersId);

    if (!paginationInfo || !pageNumbers) {
        const activeTabContent = document.querySelector(currentTab === 'active' ? '#styled-todoList' : '#styled-recentLead');
        if (activeTabContent) {
            if (!paginationInfo) {
                paginationInfo = activeTabContent.querySelector('span[id*="paginationInfo"], span.text-neutral-600');
            }
            if (!pageNumbers) {
                pageNumbers = activeTabContent.querySelector('div[id*="pageNumbers"], div.flex.gap-1');
            }
        }
    }

    if (paginationInfo) {
        paginationInfo.textContent = `Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total} entries`;
    }

    if (pageNumbers) {
        pageNumbers.innerHTML = '';

        if (pagination.current_page > 1) {
            const prevBtn = createPageButton('Previous', pagination.current_page - 1);
            pageNumbers.appendChild(prevBtn);
        }

        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = createPageButton(i, i, i === pagination.current_page);
            pageNumbers.appendChild(pageBtn);
        }

        if (pagination.current_page < pagination.last_page) {
            const nextBtn = createPageButton('Next', pagination.current_page + 1);
            pageNumbers.appendChild(nextBtn);
        }
    }
}

function createPageButton(text, page, isActive = false) {
    const button = document.createElement('button');
    button.textContent = text;
    button.className = `px-3 py-1 rounded-lg text-sm transition-colors ${isActive
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


function setupEventListeners() {
    setupTabSwitching();
    setupSearchInputs();
    setTimeout(() => {
        setupPriorityFilters();
    }, 100);
    setupSelectAllCheckboxes();
    setupDropdownClosers();
}

function setupTabSwitching() {
    document.addEventListener('click', function (e) {
        if (e.target.closest('[data-tabs-target="#styled-todoList"]')) {
            console.log('Switching to active tab');
            currentTab = 'active';
            resetFilters();
            loadNotifications();
        }
        if (e.target.closest('[data-tabs-target="#styled-recentLead"]')) {
            console.log('Switching to archived tab');
            currentTab = 'archived';
            resetFilters();
            loadNotifications();
        }
    });
}

function setupSearchInputs() {
    const activeSearchInput = document.getElementById('searchInput');
    if (activeSearchInput) {
        activeSearchInput.addEventListener('input', function () {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(() => {
                searchTerm = this.value;
                currentPage = 1;
                console.log('Active search changed:', searchTerm);
                loadNotifications();
            }, 500);
        });
    }

    const archivedSearchInput = document.getElementById('archivedSearchInput');
    if (archivedSearchInput) {
        archivedSearchInput.addEventListener('input', function () {
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

function setupPriorityFilters() {
    const activeStatusFilter = document.getElementById('statusFilter');
    if (activeStatusFilter) {
        activeStatusFilter.addEventListener('change', function () {
            priorityFilter = this.value;
            currentPage = 1;
            console.log('Active priority filter changed:', priorityFilter);
            loadNotifications();
        });
    }

    const archivedTargetFilter = document.getElementById('archivedTargetFilter');
    if (archivedTargetFilter) {
        console.log('archivedTargetFilter ditemukan:', archivedTargetFilter);
        archivedTargetFilter.addEventListener('change', function () {
            targetFilter = this.value;
            currentPage = 1;
            console.log('Archived target filter changed:', targetFilter);
            loadNotifications();
        });
    } else {
        console.error('archivedTargetFilter element tidak ditemukan!');
    }
}

function setupSelectAllCheckboxes() {
    const selectAllCheckboxes = document.querySelectorAll('input[id*="selectAll"]');

    selectAllCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const checkboxClass = this.id.includes('archived') ? '.archived-notification-checkbox' : '.notification-checkbox';
            const targetCheckboxes = document.querySelectorAll(checkboxClass);

            targetCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateSelectedNotifications();
        });
    });
}

function setupDropdownClosers() {
    document.addEventListener('click', function (event) {
        const container = document.querySelector('.dropdown-container');
        const content = document.getElementById('dropdown-content');

        if (container && !container.contains(event.target)) {
            if (content) content.classList.add('hidden');
        }

        const settingsContainer = document.querySelector('.dropdown-container-settings');
        const settingsContent = document.getElementById('settings-dropdown-content');

        if (settingsContainer && !settingsContainer.contains(event.target)) {
            if (settingsContent) settingsContent.classList.add('hidden');
        }
    });
}

function resetFilters() {
    searchTerm = '';
    priorityFilter = '';
    targetFilter = '';
    currentPage = 1;

    const searchInputs = document.querySelectorAll('input[placeholder*="Search"]');
    searchInputs.forEach(input => input.value = '');

    const filters = document.querySelectorAll('select[id*="Filter"]');
    filters.forEach(filter => filter.value = '');

    console.log('Filters reset');
}

function updateSelectedNotifications() {
    const checkboxClass = currentTab === 'active' ? '.notification-checkbox' : '.archived-notification-checkbox';
    const selectAllId = currentTab === 'active' ? 'selectAll' : 'archivedSelectAll';

    const checkboxes = document.querySelectorAll(`${checkboxClass}:checked`);
    selectedNotifications = Array.from(checkboxes).map(cb => parseInt(cb.value));

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


function submitNotification() {
    const submitBtn = document.querySelector('button[onclick="submitNotification()"]');
    if (submitBtn.disabled) {
        return;
    }

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
                showAlert('Notification successfully created and sent!', 'success');
                resetCreateForm();
                if (currentTab === 'active') {
                    loadNotifications();
                }
            } else {
                showAlert('Failed to create notification. Please check your input.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while creating the notification.', 'error');
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

function resetCreateForm() {
    document.getElementById('name').value = '';
    document.getElementById('desc').value = '';
    document.getElementById('depart').value = 'all';

    selectedTenants = [];
    const tenantSelection = document.getElementById('tenantSelection');
    if (tenantSelection) {
        tenantSelection.style.display = 'none';
    }

    setTimeout(() => {
        applyDefaultsToCreateForm();
    }, 100);
}

function getSelectedDeliveryMethods() {
    const checkboxes = document.querySelectorAll('#dropdown-content input[type="checkbox"]:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

function getSelectedSettingsDeliveryMethods() {
    const checkboxes = document.querySelectorAll('#settings-dropdown-content input[type="checkbox"]:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}


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

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const content = document.getElementById('deleteModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

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

function closeArchiveModal() {
    const modal = document.getElementById('archiveModal');
    const content = document.getElementById('archiveModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

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

function closeRestoreModal() {
    const modal = document.getElementById('restoreModal');
    const content = document.getElementById('restoreModalContent');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

document.addEventListener('DOMContentLoaded', function () {
    // Delete confirmation
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', () => {
            const confirmBtn = document.getElementById('confirmDeleteBtn');

            confirmBtn.disabled = true;
            confirmBtn.innerHTML = `
                <div class="loading-spinner"></div>
                <span style="margin-left: 8px;">Menghapus...</span>
            `;

            fetch(`/admin/notifications/${deleteId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
    }

    const confirmArchiveBtn = document.getElementById('confirmArchiveBtn');
    if (confirmArchiveBtn) {
        confirmArchiveBtn.addEventListener('click', () => {
            const button = document.getElementById('confirmArchiveBtn');
            const spinner = document.getElementById('archiveLoadingSpinner');
            const buttonText = document.getElementById('archiveButtonText');

            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');
            if (spinner) spinner.classList.remove('hidden');
            if (buttonText) buttonText.textContent = 'Archiving...';

            fetch(`/admin/notifications/${archiveId}/archive`, {
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
                })
                .finally(() => {
                    button.disabled = false;
                    button.classList.remove('opacity-50', 'cursor-not-allowed');
                    if (spinner) spinner.classList.add('hidden');
                    if (buttonText) buttonText.textContent = 'Archive';
                    closeArchiveModal();
                });
        });
    }

    const confirmRestoreBtn = document.getElementById('confirmRestoreBtn');
    if (confirmRestoreBtn) {
        confirmRestoreBtn.addEventListener('click', () => {
            const confirmBtn = document.getElementById('confirmRestoreBtn');

            confirmBtn.disabled = true;
            confirmBtn.innerHTML = `
                <div class="loading-spinner"></div>
                <span style="margin-left: 8px;">Memulihkan...</span>
            `;

            fetch(`/admin/notifications/${restoreId}/restore`, {
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
                })
                .finally(() => {
                    closeRestoreModal();
                });
        });
    }
});

function showAlert(message, type = 'info', duration = 4000) {
    document.querySelectorAll('.notification-toast').forEach(n => n.remove());

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
        },
        warning: {
            bg: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
            shadow: '0 10px 25px rgba(245, 158, 11, 0.3)',
            icon: 'ph:warning-fill'
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
                        ">${type === 'success' ? 'Success!' : type === 'delete' ? 'Deleted!' : type === 'error' ? 'Error!' : type === 'warning' ? 'Warning!' : 'Info!'}</h4>
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

    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
        toast.style.opacity = '1';
    }, 10);

    const progressBar = toast.querySelector('.notification-progress');
    if (progressBar) {
        setTimeout(() => {
            progressBar.style.width = '0%';
        }, 100);
    }

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

document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');

        console.log('URL Params:', activeTab);

        if (activeTab === 'all-notifications') {
            const allNotificationsTab = document.getElementById('change-password-tab');
            console.log('Tab element found:', allNotificationsTab);

            if (allNotificationsTab) {
                allNotificationsTab.click();

                setTimeout(() => {
                    allNotificationsTab.click();
                    console.log('Second click attempt');
                }, 100);

                setTimeout(() => {
                    allNotificationsTab.dispatchEvent(new Event('click', { bubbles: true }));
                    console.log('Force event trigger');
                }, 200);
            }
        }
    }, 10);
});


document.addEventListener('change', function (e) {
    if (e.target.id === 'default-priority') {
        currentSettings.default_priority = e.target.value;
        const createPrioritySelect = document.getElementById('desig');
        if (createPrioritySelect) {
            createPrioritySelect.value = e.target.value;
        }
    }

    if (e.target.closest('#settings-dropdown-content')) {
        setTimeout(() => {
            currentSettings.default_delivery_methods = getSelectedSettingsDeliveryMethods();
            applyDefaultsToCreateForm();
        }, 50);
    }
});


function debugFilterElements() {
    console.log('=== DEBUG FILTER ELEMENTS ===');
    console.log('archivedTargetFilter:', document.getElementById('archivedTargetFilter'));
    console.log('statusFilter:', document.getElementById('statusFilter'));
    console.log('searchInput:', document.getElementById('searchInput'));
    console.log('archivedSearchInput:', document.getElementById('archivedSearchInput'));

    const archivedTab = document.getElementById('styled-recentLead');
    console.log('Archived tab visible:', archivedTab && !archivedTab.classList.contains('hidden'));
}

function checkFilterElements() {
    console.log('Checking filter elements...');
    console.log('archivedTargetFilter:', document.getElementById('archivedTargetFilter'));
    console.log('archivedStatusFilter:', document.getElementById('archivedStatusFilter'));
    console.log('archivedSearchInput:', document.getElementById('archivedSearchInput'));
    console.log('Active statusFilter:', document.getElementById('statusFilter'));
    console.log('Active searchInput:', document.getElementById('searchInput'));
}

setTimeout(debugFilterElements, 2000);
setTimeout(checkFilterElements, 1000);


// HAPUS SEMUA KODE BULK ACTIONS YANG ADA
// GANTI DENGAN KODE SIMPLE INI

// Variabel global
let bulkSelectedIds = [];

// GANTI FUNGSI updateSelectedNotifications YANG SUDAH ADA
function updateSelectedNotifications() {
    // Cari checkbox yang checked berdasarkan tab
    let checkedBoxes;
    let selectAllId;

    if (currentTab === 'active') {
        checkedBoxes = document.querySelectorAll('#styled-todoList input[type="checkbox"]:checked:not(#selectAll)');
        selectAllId = 'selectAll';
    } else {
        checkedBoxes = document.querySelectorAll('#styled-recentLead input[type="checkbox"]:checked:not(#archivedSelectAll)');
        selectAllId = 'archivedSelectAll';
    }

    // Update selectedNotifications (untuk compatibility dengan kode existing)
    selectedNotifications = Array.from(checkedBoxes).map(cb => parseInt(cb.value));

    // Update bulkSelectedIds
    bulkSelectedIds = selectedNotifications;

    // Update select all checkbox
    const selectAllBox = document.getElementById(selectAllId);
    const allBoxes = currentTab === 'active'
        ? document.querySelectorAll('#styled-todoList input[type="checkbox"]:not(#selectAll)')
        : document.querySelectorAll('#styled-recentLead input[type="checkbox"]:not(#archivedSelectAll)');

    if (selectAllBox && allBoxes.length > 0) {
        if (bulkSelectedIds.length === 0) {
            selectAllBox.indeterminate = false;
            selectAllBox.checked = false;
        } else if (bulkSelectedIds.length === allBoxes.length) {
            selectAllBox.indeterminate = false;
            selectAllBox.checked = true;
        } else {
            selectAllBox.indeterminate = true;
        }
    }

    // Show/hide bulk actions
    showBulkActions();
}

// FUNGSI UNTUK SHOW BULK ACTIONS
function showBulkActions() {
    // Hapus bulk action yang sudah ada
    document.querySelectorAll('.bulk-action-bar').forEach(bar => bar.remove());

    if (bulkSelectedIds.length === 0) return;

    // Tentukan container
    let container;
    if (currentTab === 'active') {
        container = document.querySelector('#styled-todoList .card-body');
    } else {
        container = document.querySelector('#styled-recentLead .card-body');
    }

    if (!container) return;

    // Buat bulk action bar
    const bulkBar = document.createElement('div');
    bulkBar.className = 'bulk-action-bar bg-blue-50 border border-blue-300 rounded-lg p-3 mb-4';

    if (currentTab === 'active') {
        bulkBar.innerHTML = `
            <div class="flex justify-between items-center">
                <span class="font-medium text-blue-800">${bulkSelectedIds.length} notifications selected</span>
                <div class="space-x-2">
                    <button onclick="doBulkArchive()" class="rounded-lg bg-warning-100 text-warning-600 hover:bg-warning-200 dark:bg-warning-600/25 dark:text-warning-400 transition-colors px-4 py-2   text-sm">
                        Archive
                    </button>
                    <button onclick="doBulkDelete()" class="rounded-lg bg-danger-100 text-danger-600 hover:bg-danger-200 dark:bg-danger-600/25 dark:text-danger-400 transition-colors px-4 py-2   text-sm">
                        Delete 
                    </button>
                    <button onclick="clearSelection()"  class="rounded-lg bg-neutral-100 text-neutral-600 hover:bg-neutral-200 dark:bg-neutral-600/25 dark:text-neutral-400 transition-colors px-4 py-2   text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        `;
    } else {
        bulkBar.innerHTML = `
            <div class="flex justify-between items-center">
                <span class="font-medium text-green-800">${bulkSelectedIds.length} notifications selected</span>
                <div class="space-x-2">
                    <button onclick="doBulkRestore()" class="rounded-lg bg-success-100 text-success-600 hover:bg-success-200 dark:bg-success-600/25 dark:text-success-400 transition-colors px-4 py-2   text-sm">
                        Restore
                    </button>
                    <button onclick="doBulkDelete()"class="rounded-lg bg-danger-100 text-danger-600 hover:bg-danger-200 dark:bg-danger-600/25 dark:text-danger-400 transition-colors px-4 py-2   text-sm">
                        Delete
                    </button>
                    <button onclick="clearSelection()" class="rounded-lg bg-neutral-100 text-neutral-600 hover:bg-neutral-200 dark:bg-neutral-600/25 dark:text-neutral-400 transition-colors px-4 py-2   text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        `;
    }

    container.insertBefore(bulkBar, container.firstChild);
}


function clearSelection() {
    if (currentTab === 'active') {
        document.querySelectorAll('#styled-todoList input[type="checkbox"]').forEach(cb => cb.checked = false);
    } else {
        document.querySelectorAll('#styled-recentLead input[type="checkbox"]').forEach(cb => cb.checked = false);
    }

    bulkSelectedIds = [];
    selectedNotifications = [];
    showBulkActions();
}
// BULK MODAL FUNCTIONS - MATCHING EXISTING PATTERN
function showBulkArchiveModal() {
    const modal = document.getElementById('bulkArchiveModal');
    const content = document.getElementById('bulkArchiveModalContent');
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeBulkArchiveModal() {
    const modal = document.getElementById('bulkArchiveModal');
    const content = document.getElementById('bulkArchiveModalContent');
    content.classList.add('scale-95', 'opacity-0');
    content.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

function showBulkDeleteModal() {
    const modal = document.getElementById('bulkDeleteModal');
    const content = document.getElementById('bulkDeleteModalContent');
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeBulkDeleteModal() {
    const modal = document.getElementById('bulkDeleteModal');
    const content = document.getElementById('bulkDeleteModalContent');
    content.classList.add('scale-95', 'opacity-0');
    content.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

function showBulkRestoreModal() {
    const modal = document.getElementById('bulkRestoreModal');
    const content = document.getElementById('bulkRestoreModalContent');
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeBulkRestoreModal() {
    const modal = document.getElementById('bulkRestoreModal');
    const content = document.getElementById('bulkRestoreModalContent');
    content.classList.add('scale-95', 'opacity-0');
    content.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

// UPDATED BULK FUNCTIONS
function doBulkArchive() {
    if (bulkSelectedIds.length === 0) {
        alert('Please select notifications first');
        return;
    }
    document.getElementById('bulkArchiveCount').textContent = `${bulkSelectedIds.length} notifications`;
    showBulkArchiveModal();
}

function confirmBulkArchive() {
    const spinner = document.getElementById('bulkArchiveLoadingSpinner');
    const buttonText = document.getElementById('bulkArchiveButtonText');

    spinner.classList.remove('hidden');
    buttonText.textContent = 'Archiving...';

    fetch('/admin/notifications/bulk-archive', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            notification_ids: bulkSelectedIds
        })
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showAlert('Successfully archived', 'success');
                clearSelection();
                loadNotifications();
            } else {
                showAlert('Failed to archive', 'error');
            }
            closeBulkArchiveModal();
        })
        .catch(e => {
            showAlert('Error: ' + e.message, 'error');
            closeBulkArchiveModal();
        })
        .finally(() => {
            spinner.classList.add('hidden');
            buttonText.textContent = 'Archive All';
        });
}

function doBulkDelete() {
    if (bulkSelectedIds.length === 0) {
        alert('Please select notifications first');
        return;
    }
    document.getElementById('bulkDeleteCount').textContent = `${bulkSelectedIds.length} notifications`;
    showBulkDeleteModal();
}

function confirmBulkDelete() {
    fetch('/admin/notifications/bulk-delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            notification_ids: bulkSelectedIds
        })
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showAlert('Successfully deleted', 'success');
                clearSelection();
                loadNotifications();
            } else {
                showAlert('Failed to delete', 'error');
            }
            closeBulkDeleteModal();
        })
        .catch(e => {
            showAlert('Error: ' + e.message, 'error');
            closeBulkDeleteModal();
        });
}

function doBulkRestore() {
    console.log('doBulkRestore called with IDs:', bulkSelectedIds);

    if (bulkSelectedIds.length === 0) {
        alert('Please select notifications first');
        return;
    }

    document.getElementById('bulkRestoreCount').textContent = `${bulkSelectedIds.length} notifications`;
    showBulkRestoreModal();
}

function confirmBulkRestore() {
    closeBulkRestoreModal();

    // Show loading in bulk bar
    const bulkBar = document.querySelector('.bulk-action-bar');
    if (bulkBar) {
        bulkBar.innerHTML = `
            <div class="flex justify-center items-center">
                <span class="text-blue-600">Processing...</span>
            </div>
        `;
    }

    fetch('/admin/notifications/bulk-restore', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            notification_ids: bulkSelectedIds
        })
    })
        .then(response => {
            console.log('Response status:', response.status);
            return response.text();
        })
        .then(responseText => {
            console.log('Raw response text:', responseText);

            if (!responseText || responseText.trim() === '') {
                throw new Error('Server returned empty response');
            }

            if (responseText.trim().startsWith('<!DOCTYPE') || responseText.trim().startsWith('<html')) {
                console.error('Server returned HTML:', responseText);
                throw new Error('Server error - returned HTML instead of JSON');
            }

            let data;
            try {
                data = JSON.parse(responseText);
            } catch (parseError) {
                console.error('JSON parse error:', parseError);
                throw new Error('Response is not valid JSON: ' + responseText.substring(0, 100));
            }

            if (data.success) {
                showAlert('Successfully restored', 'success');
                clearSelection();
                loadNotifications();
            } else {
                showAlert(data.message || 'Failed to restore', 'error');
                showBulkActions();
            }
        })
        .catch(error => {
            console.error('Full error object:', error);
            showAlert('Error: ' + error.message, 'error');
            showBulkActions();
        });
}

document.addEventListener('DOMContentLoaded', function () {
    // Setup select all untuk active tab
    setTimeout(() => {
        const activeSelectAll = document.getElementById('selectAll');
        if (activeSelectAll) {
            activeSelectAll.addEventListener('change', function () {
                const allBoxes = document.querySelectorAll('#styled-todoList input[type="checkbox"]:not(#selectAll)');
                allBoxes.forEach(cb => cb.checked = this.checked);
                updateSelectedNotifications();
            });
        }

        const archivedSelectAll = document.getElementById('archivedSelectAll');
        if (archivedSelectAll) {
            archivedSelectAll.addEventListener('change', function () {
                const allBoxes = document.querySelectorAll('#styled-recentLead input[type="checkbox"]:not(#archivedSelectAll)');
                allBoxes.forEach(cb => cb.checked = this.checked);
                updateSelectedNotifications();
            });
        }
    }, 1000);
});

// UPDATE FUNGSI setupTabSwitching YANG ADA
function setupTabSwitching() {
    document.addEventListener('click', function (e) {
        if (e.target.closest('[data-tabs-target="#styled-todoList"]')) {
            currentTab = 'active';
            resetFilters();
            clearSelection();
            loadNotifications();
        }
        if (e.target.closest('[data-tabs-target="#styled-recentLead"]')) {
            currentTab = 'archived';
            resetFilters();
            clearSelection();
            loadNotifications();
        }
    });
}

console.log('Simple bulk actions loaded (with fixed restore)');