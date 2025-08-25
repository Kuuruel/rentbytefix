// FIX SEARCH JAVASCRIPT ONLY - GANTI SCRIPT YANG ADA DI BLADE
// REPLACE script section yang ada dengan ini:

document.addEventListener('DOMContentLoaded', function () {
    console.log('Search script loaded');

    const searchInput = document.getElementById('searchInput');
    const tenantGrid = document.getElementById('tenantGrid');
    const paginationInfo = document.getElementById('paginationInfo');
    let searchTimeout;
    let originalTenants = [];

    // Wait for page to fully load
    setTimeout(() => {
        initializeSearch();
    }, 100);

    function initializeSearch() {
        // Store original tenant cards
        if (tenantGrid) {
            const tenantCards = tenantGrid.querySelectorAll('.user-grid-card');
            console.log('Found tenant cards:', tenantCards.length);

            tenantCards.forEach(card => {
                const nameElement = card.querySelector('h6');
                const countryElement = card.querySelector('.text-secondary-light');

                originalTenants.push({
                    element: card,
                    name: nameElement ? nameElement.textContent.toLowerCase() : '',
                    country: countryElement ? countryElement.textContent.toLowerCase() : ''
                });
            });
            console.log('Stored', originalTenants.length, 'tenants');
        }

        // Setup search input listener
        setupSearchListener();
    }

    function setupSearchListener() {
        if (searchInput) {
            // Remove any existing listeners
            searchInput.removeEventListener('input', handleSearchInput);

            // Add new listener
            searchInput.addEventListener('input', handleSearchInput);

            console.log('Search listener attached');
        }
    }

    function handleSearchInput(e) {
        const searchTerm = e.target.value;
        console.log('Search input:', searchTerm);

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch(searchTerm);
        }, 300);
    }

    // Search function
    function performSearch(searchTerm) {
        console.log('Performing search for:', searchTerm);

        if (!originalTenants.length) {
            console.log('No tenants stored, reinitializing...');
            initializeSearch();
            return;
        }

        if (!searchTerm || searchTerm.trim() === '') {
            // Show all tenants
            originalTenants.forEach(tenant => {
                if (tenant.element) {
                    tenant.element.style.display = 'block';
                }
            });
            updatePaginationInfo(originalTenants.length);
            hideNoResults();
            return;
        }

        const term = searchTerm.toLowerCase().trim();
        let visibleCount = 0;

        originalTenants.forEach(tenant => {
            if (tenant.element) {
                const isMatch = tenant.name.includes(term) || tenant.country.includes(term);

                if (isMatch) {
                    tenant.element.style.display = 'block';
                    visibleCount++;
                } else {
                    tenant.element.style.display = 'none';
                }
            }
        });

        console.log('Found', visibleCount, 'matches');
        updatePaginationInfo(visibleCount, searchTerm);

        // Show no results message if needed
        if (visibleCount === 0) {
            showNoResults(searchTerm);
        } else {
            hideNoResults();
        }
    }

    // Update pagination info
    function updatePaginationInfo(count, searchTerm = '') {
        if (paginationInfo) {
            const searchText = searchTerm ? ` for "${searchTerm}"` : '';
            paginationInfo.textContent = `Showing ${count} entries${searchText}`;
        }
    }

    // Show no results message
    function showNoResults(searchTerm) {
        if (!tenantGrid) return;

        hideNoResults(); // Remove existing message first

        const noResultsDiv = document.createElement('div');
        noResultsDiv.id = 'noResultsMessage';
        noResultsDiv.className = 'col-span-full text-center py-8';
        noResultsDiv.innerHTML = `
            <div class="flex flex-col items-center">
                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-lg">No tenants found</p>
                <p class="text-gray-400 dark:text-gray-500 text-sm">No results for "${searchTerm}"</p>
                <button onclick="clearSearch()" class="mt-4 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Clear Search
                </button>
            </div>
        `;

        tenantGrid.appendChild(noResultsDiv);
    }

    // Hide no results message
    function hideNoResults() {
        const existing = document.getElementById('noResultsMessage');
        if (existing) {
            existing.remove();
        }
    }

    // Clear search function - make it global
    window.clearSearch = function () {
        console.log('Clearing search');
        if (searchInput) {
            searchInput.value = '';
            performSearch('');
        }
    };

    // Prevent form submission if search is in a form
    if (searchInput) {
        const searchForm = searchInput.closest('form');
        if (searchForm) {
            searchForm.addEventListener('submit', function (e) {
                e.preventDefault();
                performSearch(searchInput.value);
            });
        }
    }

    console.log('Search initialized successfully');
});

// Dropdown functions (keep existing functionality)
window.toggleDropdown = function (dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    if (dropdown) {
        dropdown.classList.toggle('hidden');

        // Close other dropdowns
        document.querySelectorAll('[id^="dropdown"]').forEach(other => {
            if (other.id !== dropdownId) {
                other.classList.add('hidden');
            }
        });
    }
};

// Close dropdowns when clicking outside
document.addEventListener('click', function (event) {
    if (!event.target.closest('.dropdown')) {
        document.querySelectorAll('[id^="dropdown"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    }
});