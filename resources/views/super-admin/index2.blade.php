@extends('layout.layout')

@php
    $title='Users List';
    $subTitle = 'Users List';
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
                    <form class="navbar-search">
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
                            <!-- Data will be populated by JavaScript -->
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

<!-- Modal Create/Edit -->
<div id="modalBackdrop" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="bg-white dark:bg-neutral-700 rounded-xl w-full max-w-2xl mx-4 shadow-lg z-10 overflow-hidden border border-neutral-200 dark:border-neutral-600">
        <div class="px-6 py-4 border-b border-neutral-200 dark:border-neutral-600 flex justify-between items-center">
            <h3 id="modalTitle" class="text-lg font-semibold text-neutral-900 dark:text-white">Add Tenant</h3>
            <button id="closeModalBtn" class="text-neutral-500 dark:text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 text-xl">&times;</button>
        </div>

        <form id="userForm" class="px-6 py-6 space-y-4">
            <input type="hidden" id="formId" value="">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Name</label>
                    <input id="formName" type="text" required class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Email</label>
                    <input id="formEmail" type="email" required class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">User ID (owner)</label>
                    <input id="formUserId" type="number" class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Join Date</label>
                    <input id="formJoinDate" type="date" required class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm text-neutral-600 dark:text-neutral-400 mb-1 font-medium">Status</label>
                    <select id="formStatus" required class="w-full bg-neutral-50 dark:bg-neutral-600 text-neutral-900 dark:text-white rounded px-3 py-2 border border-neutral-300 dark:border-neutral-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" id="formCancel" class="px-4 py-2 rounded bg-neutral-200 dark:bg-neutral-600 text-sm text-neutral-700 dark:text-neutral-200 hover:bg-neutral-300 dark:hover:bg-neutral-500">Cancel</button>
                <button type="submit" id="formSubmit" class="btn btn-primary px-4 py-2 rounded text-sm">Add Tenant</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delete Confirmation -->
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
                <button id="deleteConfirm" class="px-4 py-2 rounded bg-danger-600 text-white text-sm hover:bg-danger-700">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- ======= SCRIPT (perbaikan penuh, disesuaikan migration tenants) ======= -->
<script>
(function () {
    // data dari server (pastikan controller mengirim $tenants)
    const tenants = @json($tenants ?? []);

    let state = {
        query: '',
        statusFilter: '',
        page: 1,
        perPage: 10,
        selectedToDelete: null
    };

    // DOM
    const searchInput = document.getElementById('searchInput');
    const statusFilterEl = document.getElementById('statusFilter');
    const tableBody = document.getElementById('tableBody');
    const paginationInfo = document.getElementById('paginationInfo');
    const perPageSelect = document.getElementById('perPageSelect');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const pageNumbers = document.getElementById('pageNumbers');
    const btnOpenCreate = document.getElementById('btnOpenCreate');
    const selectAll = document.getElementById('selectAll');

    // modal elements
    const modalBackdrop = document.getElementById('modalBackdrop');
    const modalTitle = document.getElementById('modalTitle');
    const userForm = document.getElementById('userForm');
    const formId = document.getElementById('formId');
    const formName = document.getElementById('formName');
    const formEmail = document.getElementById('formEmail');
    const formUserId = document.getElementById('formUserId');
    const formJoinDate = document.getElementById('formJoinDate');
    const formStatus = document.getElementById('formStatus');
    const formSubmit = document.getElementById('formSubmit');
    const formCancel = document.getElementById('formCancel');
    const closeModalBtn = document.getElementById('closeModalBtn');

    // delete modal
    const deleteBackdrop = document.getElementById('deleteBackdrop');
    const deleteName = document.getElementById('deleteName');
    const deleteConfirm = document.getElementById('deleteConfirm');
    const deleteCancel = document.getElementById('deleteCancel');

    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
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

        // render table body
        if (paginated.length === 0) {
            tableBody.innerHTML =
                `<tr>
                    <td colspan="8" class="px-4 py-8 text-center text-neutral-500 dark:text-neutral-400">
                        No tenants found
                    </td>
                </tr>`;
        } else {
            tableBody.innerHTML = paginated.map((t, idx) => {
                const avatar = t.avatar ?? "{{ asset('assets/images/user-list/user-list1.png') }}";
                const status = t.status ?? 'Active';
                return `
                <tr>
                    <td>
                        <div class="flex items-center gap-10">
                            <div class="form-check style-check flex items-center">
                                <input class="form-check-input rounded border border-neutral-400" type="checkbox" name="checkbox" id="SL-${t.id}">
                            </div>
                            ${start + idx + 1}
                        </div>
                    </td>
                    <td>${formatDate(t.created_at)}</td>
                    <td>
                        <div class="flex items-center">
                            <img src="${avatar}" alt="" class="w-10 h-10 rounded-full shrink-0 me-2 overflow-hidden">
                            <div class="grow">
                                <span class="text-base mb-0 font-normal text-secondary-light">${escapeHtml(t.name)}</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="text-base mb-0 font-normal text-secondary-light">${escapeHtml(t.email)}</span></td>
                    <td class="text-center">
                        <span class="${status === 'Active' ? 'bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 border border-success-600' : 'bg-neutral-200 dark:bg-neutral-600 text-neutral-600 border border-neutral-400'} px-6 py-1.5 rounded font-medium text-sm">
                            ${status}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="flex items-center gap-3 justify-center">
                            <button type="button" class="bg-info-100 dark:bg-info-600/25 hover:bg-info-200 text-info-600 dark:text-info-400 font-medium w-10 h-10 flex justify-center items-center rounded-full" onclick="viewTenant(${t.id})">
                                <iconify-icon icon="majesticons:eye-line" class="icon text-xl"></iconify-icon>
                            </button>
                            <button type="button" class="bg-success-100 dark:bg-success-600/25 text-success-600 dark:text-success-400 bg-hover-success-200 font-medium w-10 h-10 flex justify-center items-center rounded-full" onclick="editTenant(${t.id})">
                                <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                            </button>
                            <button type="button" class="bg-danger-100 dark:bg-danger-600/25 hover:bg-danger-200 text-danger-600 dark:text-danger-500 font-medium w-10 h-10 flex justify-center items-center rounded-full" onclick="confirmDelete(${t.id})">
                                <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                            </button>
                        </div>
                    </td>
                </tr>`;
            }).join('');
        }

        const total = filtered.length;
        const startCount = total === 0 ? 0 : start + 1;
        const endCount = Math.min(total, start + state.perPage);
        paginationInfo.textContent = `Showing ${startCount} to ${endCount} of ${total} entries`;

        prevBtn.disabled = state.page === 1;
        nextBtn.disabled = state.page === totalPages;

        renderPageNumbers(totalPages);
    }

    function renderPageNumbers(totalPages) {
        let pageHTML = '';
        const maxVisiblePages = 5;
        let startPage = Math.max(1, state.page - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        if (endPage - startPage < maxVisiblePages - 1) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        for (let i = startPage; i <= endPage; i++) {
            pageHTML += `
                <li class="page-item">
                    <button class="page-link ${i === state.page ? 'bg-primary-600 text-white' : 'bg-neutral-300 dark:bg-neutral-600 text-secondary-light'} font-semibold rounded-lg border-0 flex items-center justify-center h-8 w-8 text-base" onclick="goToPage(${i})">
                        ${i}
                    </button>
                </li>`;
        }
        pageNumbers.innerHTML = pageHTML;
    }

    // modal controls
    function showModal(visible) { modalBackdrop.style.display = visible ? 'flex' : 'none'; }
    function openCreateModal() {
        modalTitle.textContent = 'Add Tenant';
        formSubmit.textContent = 'Add Tenant';
        formId.value = '';
        formName.value = '';
        formEmail.value = '';
        formUserId.value = '';
        formJoinDate.value = '';
        formStatus.value = 'Active';
        showModal(true);
    }

    function editTenant(id) {
        const t = tenants.find(x => x.id === id);
        if (!t) return;
        modalTitle.textContent = 'Edit Tenant';
        formSubmit.textContent = 'Update Tenant';
        formId.value = t.id;
        formName.value = t.name;
        formEmail.value = t.email;
        formUserId.value = t.user_id ?? '';
        // created_at may include time; convert to yyyy-mm-dd for date input
        formJoinDate.value = t.created_at ? new Date(t.created_at).toISOString().substr(0,10) : '';
        formStatus.value = t.status ?? 'Active';
        showModal(true);
    }

    function viewTenant(id) {
        const t = tenants.find(x => x.id === id);
        if (!t) return;
        alert(`Viewing tenant: ${t.name}\nEmail: ${t.email}\nUser ID: ${t.user_id ?? ''}`);
    }

    function closeModal() { showModal(false); }

    // delete
    function confirmDelete(id) {
        const t = tenants.find(x => x.id === id);
        if (!t) return;
        state.selectedToDelete = t;
        deleteName.textContent = t.name;
        deleteBackdrop.style.display = 'flex';
    }

    function deleteUser() {
        if (!state.selectedToDelete) return;
        const index = tenants.findIndex(u => u.id === state.selectedToDelete.id);
        if (index > -1) tenants.splice(index, 1);
        state.selectedToDelete = null;
        deleteBackdrop.style.display = 'none';
        const filtered = getFiltered();
        const totalPages = getTotalPages(filtered);
        if (state.page > totalPages) state.page = totalPages;
        render();
    }

    function cancelDelete() {
        state.selectedToDelete = null;
        deleteBackdrop.style.display = 'none';
    }

    function goToPage(page) { state.page = page; render(); }
    function prevPage() { if (state.page > 1) { state.page--; render(); } }
    function nextPage() {
        const totalPages = getTotalPages(getFiltered());
        if (state.page < totalPages) { state.page++; render(); }
    }

    userForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = formId.value;
        const tenantData = {
            name: formName.value.trim(),
            email: formEmail.value.trim(),
            user_id: formUserId.value ? parseInt(formUserId.value) : null,
            created_at: formJoinDate.value,
            status: formStatus.value
        };
        if (!tenantData.name || !tenantData.email || !tenantData.created_at) {
            alert('Please fill in all required fields.');
            return;
        }
        if (id) {
            const index = tenants.findIndex(u => u.id === parseInt(id));
            if (index > -1) tenants[index] = { ...tenants[index], ...tenantData };
        } else {
            const newId = tenants.length ? Math.max(...tenants.map(u => u.id)) + 1 : 1;
            const newTenant = { id: newId, ...tenantData, avatar: "{{ asset('assets/images/user-list/user-list1.png') }}" };
            tenants.unshift(newTenant);
            state.page = 1;
        }
        closeModal();
        render();
    });

    // events
    searchInput.addEventListener('input', function(e) {
        state.query = e.target.value;
        state.page = 1;
        render();
    });
    statusFilterEl.addEventListener('change', function(e) {
        state.statusFilter = e.target.value;
        state.page = 1;
        render();
    });
    perPageSelect.addEventListener('change', function(e) {
        state.perPage = parseInt(e.target.value);
        state.page = 1;
        render();
    });
    prevBtn.addEventListener('click', prevPage);
    nextBtn.addEventListener('click', nextPage);
    btnOpenCreate.addEventListener('click', openCreateModal);
    formCancel.addEventListener('click', closeModal);
    closeModalBtn.addEventListener('click', closeModal);
    deleteConfirm.addEventListener('click', deleteUser);
    deleteCancel.addEventListener('click', cancelDelete);

    selectAll.addEventListener('change', function(e) {
        const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
        checkboxes.forEach(checkbox => { checkbox.checked = e.target.checked; });
    });

    modalBackdrop.addEventListener('click', function(e) {
        if (e.target === modalBackdrop) closeModal();
    });
    deleteBackdrop.addEventListener('click', function(e) {
        if (e.target === deleteBackdrop) cancelDelete();
    });

    window.viewTenant = viewTenant;
    window.editTenant = editTenant;
    window.confirmDelete = confirmDelete;
    window.goToPage = goToPage;

    // initial render
    render();
})();
</script>
@endsection
