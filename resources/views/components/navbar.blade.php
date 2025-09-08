<div class="navbar-header border-b border-neutral-200 dark:border-neutral-600">
    <div class="flex items-center justify-between">
        <div class="col-auto">
            <div class="flex flex-wrap items-center gap-[16px]">
                <button type="button" class="sidebar-toggle">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon non-active"></iconify-icon>
                    <iconify-icon icon="iconoir:arrow-right" class="icon active"></iconify-icon>
                </button>
                <button type="button" class="sidebar-mobile-toggle d-flex !leading-[0]">
                    <iconify-icon icon="heroicons:bars-3-solid" class="icon !text-[30px]"></iconify-icon>
                </button>
                <form class="navbar-search">
                    <input type="text" name="search" placeholder="Search">
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
        </div>
        <div class="col-auto">
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" id="theme-toggle" class="w-10 h-10 bg-neutral-200 dark:bg-neutral-700 dark:text-white rounded-full flex justify-center items-center">
                    <span id="theme-toggle-dark-icon" class="hidden">
                        <i class="ri-sun-line"></i>
                    </span>
                    <span id="theme-toggle-light-icon" class="hidden">
                        <i class="ri-moon-line"></i>
                    </span>
                </button>

                <div class="hidden sm:inline-block">
                    <button data-dropdown-toggle="dropdownInformation" class="has-indicator w-10 h-10 bg-neutral-200 dark:bg-neutral-700 dark:text-white rounded-full flex justify-center items-center" type="button">
                        <img src="{{ asset('assets/images/lang-flag.png') }}" alt="image" class="w-6 h-6 object-cover rounded-full">
                    </button>
                </div>

                <button data-dropdown-toggle="dropdownMessage" class="has-indicator w-10 h-10 bg-neutral-200 dark:bg-neutral-700 rounded-full flex justify-center items-center" type="button">
                    <iconify-icon icon="mage:email" class="text-neutral-900 dark:text-white text-xl"></iconify-icon>
                </button>
                <div id="dropdownMessage" class="z-10 hidden bg-white dark:bg-neutral-700 rounded-2xl overflow-hidden shadow-lg max-w-[394px] w-full">
                    <div class="py-3 px-4 rounded-lg bg-primary-50 dark:bg-primary-600/25 m-4 flex items-center justify-between gap-2">
                        <h6 class="text-lg text-neutral-900 font-semibold mb-0">Message</h6>
                        <span class="w-10 h-10 bg-white dark:bg-neutral-600 text-primary-600 dark:text-white font-bold flex justify-center items-center rounded-full">05</span>
                    </div>
                    <div class="scroll-sm !border-t-0">
                        <div class="max-h-[400px] overflow-y-auto">
                            <a href="javascript:void(0)" class="flex px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 justify-between gap-1">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 relative">
                                        <img class="rounded-full w-11 h-11" src="{{ asset('assets/images/notification/profile-3.png') }}" alt="Joseph image">
                                        <span class="absolute end-[2px] bottom-[2px] w-2.5 h-2.5 bg-success-500 border border-white rounded-full dark:border-gray-600"></span>
                                    </div>
                                    <div>
                                        <h6 class="text-sm fw-semibold mb-1">Kathryn Murphy</h6>
                                        <p class="mb-0 text-sm line-clamp-1">hey! there i'm...</p>
                                    </div>
                                </div>
                                <div class="shrink-0 flex flex-col items-end gap-1">
                                    <span class="text-sm text-neutral-500">12:30 PM</span>
                                    <span class="w-4 h-4 text-xs bg-warning-600 text-white rounded-full flex justify-center items-center">8</span>
                                </div>
                            </a>
                        </div>
                        <div class="text-center py-3 px-4">
                            <a href="javascript:void(0)" class="text-primary-600 dark:text-primary-600 font-semibold hover:underline text-center">See All Message</a>
                        </div>
                    </div>
                </div>

                <button data-dropdown-toggle="dropdownNotification" class="has-indicator w-10 h-10 bg-neutral-200 dark:bg-neutral-700 rounded-full flex justify-center items-center" type="button">
                    <iconify-icon icon="iconoir:bell" class="text-neutral-900 dark:text-white text-xl"></iconify-icon>
                </button>
                <div id="dropdownNotification" class="z-10 hidden bg-white dark:bg-neutral-700 rounded-2xl overflow-hidden shadow-lg max-w-[394px] w-full">
                    <div class="py-3 px-4 rounded-lg bg-primary-50 dark:bg-primary-600/25 m-4 flex items-center justify-between gap-2">
                        <h6 class="text-lg text-neutral-900 font-semibold mb-0">Notification</h6>
                        <span class="w-10 h-10 bg-white dark:bg-neutral-600 text-primary-600 dark:text-white font-bold flex justify-center items-center rounded-full">05</span>
                    </div>
                    <div class="scroll-sm !border-t-0">
                        <div class="max-h-[400px] overflow-y-auto">
                            <a href="javascript:void(0)" class="flex px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 justify-between gap-1">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 relative w-11 h-11 bg-success-200 dark:bg-success-600/25 text-success-600 flex justify-center items-center rounded-full">
                                        <iconify-icon icon="bitcoin-icons:verify-outline" class="text-2xl"></iconify-icon>
                                    </div>
                                    <div>
                                        <h6 class="text-sm fw-semibold mb-1">Congratulations</h6>
                                        <p class="mb-0 text-sm line-clamp-1">Your profile has been Verified.</p>
                                    </div>
                                </div>
                                <div class="shrink-0">
                                    <span class="text-sm text-neutral-500">23 Mins ago</span>
                                </div>
                            </a>
                        </div>
                        <div class="text-center py-3 px-4">
                            <a href="javascript:void(0)" class="text-primary-600 dark:text-primary-600 font-semibold hover:underline text-center">See All Notification</a>
                        </div>
                    </div>
                </div>

                <button data-dropdown-toggle="dropdownProfile" class="flex justify-center items-center rounded-full" type="button">
                    @if(Auth::guard('web')->check())
                        @if(Auth::user()->img)
                            <img src="{{ asset('assets/images/super-admin/' . Auth::user()->img) }}"
                                alt="image"
                                class="w-10 h-10 object-cover rounded-full">
                        @else
                            <img src="{{ asset('assets/images/user.png') }}"
                                alt="image"
                                class="w-10 h-10 object-cover rounded-full">
                        @endif

                    @elseif(Auth::guard('tenant')->check())
                        @if(Auth::guard('tenant')->user()->avatar)
                            <img src="{{ asset('assets/images/tenants/' . Auth::guard('tenant')->user()->avatar) }}"
                                alt="image"
                                class="w-10 h-10 object-cover rounded-full">
                        @else
                            <img src="{{ asset('assets/images/user.png') }}"
                                alt="image"
                                class="w-10 h-10 object-cover rounded-full">
                        @endif

                    @else
                        <img src="{{ asset('assets/images/user.png') }}"
                            alt="image"
                            class="w-10 h-10 object-cover rounded-full">
                    @endif
                </button>

                <div id="dropdownProfile" class="z-10 hidden bg-white dark:bg-neutral-700 rounded-lg shadow-lg dropdown-menu-sm p-3">
                    <div class="py-3 px-4 rounded-lg bg-primary-50 dark:bg-primary-600/25 mb-4 flex items-center justify-between gap-2">
                        <div>
                            <h6 class="text-lg text-neutral-900 font-semibold mb-0">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</h6>
                            <span class="text-neutral-500">{{ Auth::check() ? ucfirst(Auth::user()->role) : 'User' }}</span>
                        </div>
                        <button id="closeProfileDropdown" type="button" class="hover:text-danger-600" aria-label="Tutup">
                            <iconify-icon icon="radix-icons:cross-1" class="icon text-xl"></iconify-icon>
                        </button>
                    </div>

                    <div class="max-h-[400px] overflow-y-auto scroll-sm pe-2">
                        <ul class="flex flex-col">
                            @if(Auth::guard('web')->check())
                                <li>
                                    <a class="text-black px-0 py-2 hover:text-primary-600 flex items-center gap-4"
                                    href="{{ route('viewProfileAdmin', ['id' => Auth::user()->id]) }}">
                                        <iconify-icon icon="solar:user-linear" class="icon text-xl"></iconify-icon>
                                        My Profile
                                    </a>
                                </li>
                            @elseif(Auth::guard('tenant')->check())
                                <li>
                                    <a class="text-black px-0 py-2 hover:text-primary-600 flex items-center gap-4"
                                    href="{{ route('viewProfileTenant', ['id' => Auth::guard('tenant')->user()->id]) }}">
                                        <iconify-icon icon="solar:user-linear" class="icon text-xl"></iconify-icon>
                                        My Profile
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a class="text-black px-0 py-2 hover:text-primary-600 flex items-center gap-4"
                                    href="{{ route('viewProfileAdmin') }}">
                                        <iconify-icon icon="solar:user-linear" class="icon text-xl"></iconify-icon>
                                        My Profile
                                    </a>
                                </li>
                            @endif

                            <li>
                                <a class="text-black px-0 py-2 hover:text-primary-600 flex items-center gap-4" href="{{ route('email') }}">
                                    <iconify-icon icon="tabler:message-check" class="icon text-xl"></iconify-icon> Inbox
                                </a>
                            </li>
                            <li>
                                <a class="text-black px-0 py-2 hover:text-primary-600 flex items-center gap-4" href="{{ route('company') }}">
                                    <iconify-icon icon="icon-park-outline:setting-two" class="icon text-xl"></iconify-icon> Setting
                                </a>
                            </li>

                            <li>
                                <button id="logoutButton" class="text-black px-0 py-2 hover:text-danger-600 flex items-center gap-4 w-full text-left bg-transparent border-0">
                                    <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon> Log Out
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="logoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[50] hidden opacity-0 transition-opacity duration-300 p-4">
    <div id="modalContent" class="bg-white rounded-lg p-4 sm:p-6  max-w-sm mx-4 shadow-xl transform scale-95 transition-transform duration-300">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 sm:w-10 sm:h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                <iconify-icon icon="lucide:log-out" class="text-red-600 text-base sm:text-lg"></iconify-icon>
            </div>
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800 leading-tight">Konfirmasi Logout</h2>
        </div>
        
        <p class="mb-6 text-sm sm:text-base text-gray-600 leading-relaxed">Apakah kamu yakin ingin keluar dari Rentbyte?</p>
        
        <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
            <button 
                id="cancelButton" 
                type="button"
                class="w-full sm:w-auto px-4 py-2 sm:py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 transition-colors text-sm sm:text-base font-medium"
            >
                Batal
            </button>
            
            <button 
                id="confirmLogout" 
                type="button"
                class="w-full sm:w-auto px-4 py-2 sm:py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors text-sm sm:text-base font-medium"
            >
                Ya, Keluar
            </button>
        </div>
    </div>
</div>

<form id="logoutForm" method="POST" action="{{ route('logout') }}" class="hidden">
    @csrf
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const logoutButton = document.getElementById('logoutButton');
    const logoutModal = document.getElementById('logoutModal');
    const modalContent = document.getElementById('modalContent');
    const cancelButton = document.getElementById('cancelButton');
    const confirmLogout = document.getElementById('confirmLogout');
    const logoutForm = document.getElementById('logoutForm');
    const dropdownProfile = document.getElementById('dropdownProfile');
    const closeProfileBtn = document.getElementById('closeProfileDropdown');
    const profileToggleBtn = document.querySelector('[data-dropdown-toggle="dropdownProfile"]');

    function openModal() {
        logoutModal.classList.remove('hidden');
        logoutModal.offsetHeight;
        logoutModal.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');

        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        logoutModal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');

        document.body.style.overflow = '';

        setTimeout(() => {
            logoutModal.classList.add('hidden');
        }, 300);
    }

    if (closeProfileBtn && dropdownProfile) {
        closeProfileBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdownProfile.classList.add('hidden');
        if (profileToggleBtn) profileToggleBtn.setAttribute('aria-expanded', 'false');
        });
    }

    document.addEventListener('click', function(e) {
        if (!dropdownProfile.classList.contains('hidden')) {
        if (!dropdownProfile.contains(e.target) && !profileToggleBtn.contains(e.target)) {
            dropdownProfile.classList.add('hidden');
            if (profileToggleBtn) profileToggleBtn.setAttribute('aria-expanded', 'false');
        }
        }
    });

    if (logoutButton) {
        logoutButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openModal();
        });
    }
    
    if (cancelButton) {
        cancelButton.addEventListener('click', closeModal);
    }

    if (logoutModal) {
        logoutModal.addEventListener('click', function(e) {
            if (e.target === logoutModal) {
                closeModal();
            }
        });
    }

    if (confirmLogout) {
        confirmLogout.addEventListener('click', function() {
            logoutForm.submit();
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !logoutModal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
</script>

<style>
    .transition-opacity {
        transition-property: opacity;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    .transition-transform {
        transition-property: transform;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    .transition-colors {
        transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 150ms;
    }

    .duration-300 {
        transition-duration: 300ms;
    }
</style>