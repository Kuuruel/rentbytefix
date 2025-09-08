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
                <button type="button" id="theme-toggle"
                    class="w-10 h-10 bg-neutral-200 dark:bg-neutral-700 dark:text-white rounded-full flex justify-center items-center">
                    <span id="theme-toggle-dark-icon" class="hidden">
                        <i class="ri-sun-line"></i>
                    </span>
                    <span id="theme-toggle-light-icon" class="hidden">
                        <i class="ri-moon-line"></i>
                    </span>
                </button>

                {{-- <button data-dropdown-toggle="dropdownMessage"
                    class="has-indicator w-10 h-10 bg-neutral-200 dark:bg-neutral-700 rounded-full flex justify-center items-center"
                    type="button">
                    <iconify-icon icon="mage:email" class="text-neutral-900 dark:text-white text-xl"></iconify-icon>
                </button> --}}
                <div id="dropdownMessage"
                    class="z-10 hidden bg-white dark:bg-neutral-700 rounded-2xl overflow-hidden shadow-lg max-w-[394px] w-full">
                    <div
                        class="py-3 px-4 rounded-lg bg-primary-50 dark:bg-primary-600/25 m-4 flex items-center justify-between gap-2">
                        <h6 class="text-lg text-neutral-900 font-semibold mb-0">Message</h6>
                        <span
                            class="w-10 h-10 bg-white dark:bg-neutral-600 text-primary-600 dark:text-white font-bold flex justify-center items-center rounded-full">05</span>
                    </div>
                    <div class="scroll-sm !border-t-0">
                        <div class="max-h-[400px] overflow-y-auto">
                            <a href="javascript:void(0)"
                                class="flex px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600 justify-between gap-1">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 relative">
                                        <img class="rounded-full w-11 h-11"
                                            src="{{ asset('assets/images/notification/profile-3.png') }}"
                                            alt="Joseph image">
                                        <span
                                            class="absolute end-[2px] bottom-[2px] w-2.5 h-2.5 bg-success-500 border border-white rounded-full dark:border-gray-600"></span>
                                    </div>
                                    <div>
                                        <h6 class="text-sm fw-semibold mb-1">Kathryn Murphy</h6>
                                        <p class="mb-0 text-sm line-clamp-1">hey! there i'm...</p>
                                    </div>
                                </div>
                                <div class="shrink-0 flex flex-col items-end gap-1">
                                    <span class="text-sm text-neutral-500">12:30 PM</span>
                                    <span
                                        class="w-4 h-4 text-xs bg-warning-600 text-white rounded-full flex justify-center items-center">8</span>
                                </div>
                            </a>
                        </div>
                        <div class="text-center py-3 px-4">
                            <a href="javascript:void(0)"
                                class="text-primary-600 dark:text-primary-600 font-semibold hover:underline text-center">See
                                All Message</a>
                        </div>
                    </div>
                </div>

                <button data-dropdown-toggle="dropdownNotification" id="notificationButton"
                    class="has-indicator w-10 h-10 bg-neutral-200 dark:bg-neutral-700 rounded-full flex justify-center items-center relative"
                    type="button">
                    <iconify-icon icon="iconoir:bell" class="text-neutral-900 dark:text-white text-xl"></iconify-icon>
                    <span id="notificationBadge"
                        class="absolute -top-1 -right-1 w-5 h-5 bg-danger-600 text-white rounded-full text-xs flex items-center justify-center hidden">0</span>
                </button>
                <div id="dropdownNotification"
                    class="z-10 hidden bg-white dark:bg-neutral-700 rounded-2xl overflow-hidden shadow-lg max-w-[394px] w-full">
                    <div
                        class="py-3 px-4 rounded-lg bg-primary-50 dark:bg-primary-600/25 m-4 flex items-center justify-between gap-2">
                        <h6 class="text-lg text-neutral-900 dark:text-neutral-200 font-semibold mb-0">Notification</h6>
                        <span id="notificationCount"
                            class="w-10 h-10 bg-white dark:bg-neutral-600 text-primary-600 dark:text-white font-bold flex justify-center items-center rounded-full">0</span>
                    </div>
                    <div class="scroll-sm !border-t-0">
                        <div class="max-h-[400px] overflow-y-auto" id="notificationList">
                            <div class="text-center py-4" id="notificationLoading">
                                <span class="text-sm text-neutral-500">Loading...</span>
                            </div>
                        </div>
                        <div class="text-center py-3 px-4">
                            <a href="/super-admin/index7?tab=all-notifications"
                                class="text-primary-600 dark:text-primary-600 font-semibold hover:underline text-center">
                                See All Notifications
                            </a>
                        </div>
                    </div>
                </div>

                <button data-dropdown-toggle="dropdownProfile" class="flex justify-center items-center rounded-full"
                    type="button">
                    @if (Auth::check() && Auth::user()->img)
                        <img src="{{ asset('assets/images/super-admin/' . Auth::user()->img) }}" alt="image"
                            class="w-10 h-10 object-fit-cover rounded-full">
                    @else
                        <img src="{{ asset('assets/images/user.png') }}" alt="image"
                            class="w-10 h-10 object-fit-cover rounded-full">
                    @endif
                </button>
                <div id="dropdownProfile"
                    class="z-10 hidden bg-white dark:bg-neutral-700 rounded-lg shadow-lg dropdown-menu-sm p-3">
                    <div
                        class="py-3 px-4 rounded-lg bg-primary-50 dark:bg-primary-600/25 mb-4 flex items-center justify-between gap-2">
                        <div>
                            <h6 class="text-lg text-neutral-900 font-semibold mb-0">
                                {{ Auth::check() ? Auth::user()->name : 'Guest' }}</h6>
                            <span
                                class="text-neutral-500">{{ Auth::check() ? ucfirst(Auth::user()->role) : 'User' }}</span>
                        </div>
                        <button id="closeProfileDropdown" type="button" class="hover:text-danger-600" aria-label="Tutup">
                            <iconify-icon icon="radix-icons:cross-1" class="icon text-xl"></iconify-icon>
                        </button>
                    </div>

                    <div class="max-h-[400px] overflow-y-auto scroll-sm pe-2">
                        <ul class="flex flex-col">
                            <li>
                                <a class="text-black px-0 py-2 hover:text-primary-600 flex items-center gap-4" href="{{ route('viewProfile') }}">
                                    <iconify-icon icon="solar:user-linear" class="icon text-xl"></iconify-icon>  My Profile
                                </a>
                            </li>
                            <li>
                                <a class="text-black px-0 py-2 hover:text-primary-600 flex items-center gap-4"
                                    href="{{ route('email') }}">
                                    <iconify-icon icon="tabler:message-check" class="icon text-xl"></iconify-icon>
                                    Inbox
                                </a>
                            </li>
                            <li>
                                <a class="text-black px-0 py-2 hover:text-primary-600 flex items-center gap-4"
                                    href="{{ route('company') }}">
                                    <iconify-icon icon="icon-park-outline:setting-two"
                                        class="icon text-xl"></iconify-icon> Setting
                                </a>
                            </li>

                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit"
                                        class="text-black px-0 py-2 hover:text-danger-600 flex items-center gap-4 w-full text-left">
                                        <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon> Log Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Navbar Notification Functions
    // Tambahkan ini ke layout utama atau navbar blade

    // Load notifications untuk dropdown navbar
    function loadNavbarNotifications() {
        const notificationList = document.getElementById('notificationList');
        const notificationCount = document.getElementById('notificationCount');
        const notificationBadge = document.getElementById('notificationBadge');
        const loadingElement = document.getElementById('notificationLoading');

        if (loadingElement) {
            loadingElement.style.display = 'block';
        }

        fetch('/admin/notifications/get-notifications')
            .then(response => response.json())
            .then(data => {
                if (loadingElement) {
                    loadingElement.style.display = 'none';
                }

                if (data.notifications && notificationList) {
                    populateNavbarNotifications(data.notifications);
                }

                // Update counters
                if (notificationCount) {
                    notificationCount.textContent = data.total_count || 0;
                }

                if (notificationBadge) {
                    if (data.unread_count > 0) {
                        notificationBadge.textContent = data.unread_count;
                        notificationBadge.classList.remove('hidden');
                    } else {
                        notificationBadge.classList.add('hidden');
                    }
                }
            })
            .catch(error => {
                console.error('Error loading navbar notifications:', error);
                if (loadingElement) {
                    loadingElement.style.display = 'none';
                }
                if (notificationList) {
                    notificationList.innerHTML =
                        '<div class="text-center py-4"><span class="text-sm text-red-500">Error loading notifications</span></div>';
                }
            });
    }

    // Populate navbar notification dropdown
    function populateNavbarNotifications(notifications) {
        const notificationList = document.getElementById('notificationList');

        if (!notificationList) return;

        if (notifications.length === 0) {
            notificationList.innerHTML = `
            <div class="text-center py-8">
                <iconify-icon icon="lucide:bell-off" class="text-4xl text-gray-400 mb-2"></iconify-icon>
                <p class="text-sm text-gray-500">No notifications</p>
            </div>
        `;
            return;
        }

        notificationList.innerHTML = '';

        notifications.forEach(notification => {
            const notificationItem = createNavbarNotificationItem(notification);
            notificationList.appendChild(notificationItem);
        });
    }

    // Create navbar notification item
    function createNavbarNotificationItem(notification) {
        const item = document.createElement('div');

        // Warna berdasarkan prioritas
        const priorityColors = getPriorityColor(notification.priority);

        // Background untuk status dibaca/belum dibaca
        const backgroundClass = !notification.is_read ? 'bg-gray-100 dark:bg-gray-800' : 'bg-white dark:bg-neutral-700';

        item.className =
            `px-4 py-3 hover:bg-gray-200 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-100 dark:border-gray-600 last:border-b-0 transition-all duration-200 ${backgroundClass}`;

        item.onclick = () => markNotificationAsRead(notification.id);

        item.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 mt-1 relative">
                <div class="w-6 h-6 rounded-full flex items-center justify-center">
                    <div class="w-3 h-3 rounded-full ${priorityColors.icon}"></div>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <h6 class="text-sm font-semibold ${
                        !notification.is_read ? 'text-gray-900 dark:text-white' : 'text-gray-700 dark:text-gray-300'
                    } truncate">
                        ${notification.title}
                        ${
                            !notification.is_read
                                ? '<span class="ml-2 inline-block w-2 h-2 bg-blue-500 rounded-full"></span>'
                                : ''
                        }
                    </h6>
                    ${
                        !notification.is_read
                            ? '<span class="text-xs bg-blue-100 text-primary-600 dark:bg-blue-900 dark:text-blue-200 px-2 py-1 rounded-full font-medium flex-shrink-0">NEW</span>'
                            : ''
                    }
                </div>
                <p class="text-xs ${
                    !notification.is_read
                        ? 'text-gray-700 dark:text-gray-200 font-medium'
                        : 'text-gray-600 dark:text-gray-400'
                } line-clamp-2 mb-2">
                    ${notification.message}
                </p>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400">${notification.created_at}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs px-2 py-1 rounded-full ${notification.priority_badge}">
                            ${notification.priority}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    `;

        return item;
    }

    // Get priority icon
    // function getPriorityIcon(priority) {
    //     switch (priority) {
    //         case 'Critical':
    //             return 'lucide:alert-triangle';
    //         case 'Important':
    //             return 'lucide:info';
    //         case 'Normal':
    //         default:
    //             return 'lucide:bell';
    //     }
    // }

    // Get priority color
    function getPriorityColor(priority) {
        switch (priority) {
            case 'Critical':
                return {
                    bg: 'bg-danger-100', // Background merah muda untuk lingkaran besar
                        icon: 'bg-danger-500' // Lingkaran kecil merah
                };
            case 'Important':
                return {
                    bg: 'bg-warning-100', // Background kuning muda untuk lingkaran besar
                        icon: 'bg-warning-500' // Lingkaran kecil kuning
                };
            case 'Normal':
            default:
                return {
                    bg: 'bg-primary-100', // Background biru muda untuk lingkaran besar
                        icon: 'bg-primary-500' // Lingkaran kecil biru
                };
        }
    }

    // Mark notification as read
    function markNotificationAsRead(notificationId) {
        // Update UI immediately untuk feedback instant
        const notificationElement = event.target.closest('div[class*="px-4 py-3"]');
        if (notificationElement) {
            notificationElement.className = notificationElement.className.replace('bg-blue-100 dark:bg-blue-900/40',
                'bg-white dark:bg-neutral-700');
        }

        fetch(`/admin/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload navbar notifications untuk update badge count dan status
                    loadNavbarNotifications();
                } else {
                    // Kalau gagal, kembalikan ke state semula
                    if (notificationElement) {
                        notificationElement.className = notificationElement.className.replace(
                            'bg-white dark:bg-neutral-700', 'bg-blue-100 dark:bg-blue-900/40');
                    }
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
                // Kalau error, kembalikan ke state semula
                if (notificationElement) {
                    notificationElement.className = notificationElement.className.replace(
                        'bg-white dark:bg-neutral-700', 'bg-blue-100 dark:bg-blue-900/40');
                }
            });
    }

    // Real-time notification dengan polling yang lebih cepat
    function startNotificationAutoRefresh() {
        // Polling setiap 5 detik untuk notifikasi real-time
        setInterval(() => {
            loadNavbarNotifications();
        }, 5000); // 5 seconds
    }

    // Instant notification check untuk notifikasi baru
    function checkForNewNotifications() {
        loadNavbarNotifications();
    }

    // WebSocket atau Server-Sent Events listener (jika tersedia)
    function initializeRealTimeNotifications() {
        // Jika menggunakan WebSocket
        if (typeof window.Echo !== 'undefined') {
            window.Echo.channel('notifications')
                .listen('NewNotification', (e) => {
                    checkForNewNotifications();
                });
        }

        // Atau menggunakan EventSource untuk Server-Sent Events
        if (typeof EventSource !== 'undefined') {
            const eventSource = new EventSource('/admin/notifications/stream');
            eventSource.onmessage = function(event) {
                checkForNewNotifications();
            };
        }
    }

    // Initialize navbar notifications saat halaman load
    document.addEventListener('DOMContentLoaded', function() {
        // Load initial notifications
        loadNavbarNotifications();

        // Start auto refresh dengan interval yang lebih cepat
        startNotificationAutoRefresh();

        // Initialize real-time notifications
        initializeRealTimeNotifications();

        // Refresh notifications ketika dropdown dibuka
        const notificationButton = document.getElementById('notificationButton');
        if (notificationButton) {
            notificationButton.addEventListener('click', function() {
                checkForNewNotifications();
            });
        }

        // Refresh ketika window mendapat focus (user kembali ke tab)
        window.addEventListener('focus', function() {
            checkForNewNotifications();
        });

        // Refresh ketika ada aktivitas mouse/keyboard (user aktif)
        let lastActivity = Date.now();
        document.addEventListener('mousemove', function() {
            const now = Date.now();
            if (now - lastActivity > 10000) { // 10 detik sejak aktivitas terakhir
                checkForNewNotifications();
                lastActivity = now;
            }
        });
    });
</script>
