<aside class="sidebar">
    <button type="button" class="sidebar-close-btn !mt-4">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('super-admin.index') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/renbyte-logos.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/renbyte-logos.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/r-logos.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <style>
                .sidebar-menu li.active>a,
                .sidebar-menu li.active>a .menu-icon,
                .sidebar-menu li.active>a span {
                    background-color: #3b82f6;
                    

                    color: #ffffff !important;
                    font-weight: 600;
                    padding: 7px;
                }

                .sidebar-menu li.active>a .menu-icon {
                    margin-right: 0px !important;
                }

                .sidebar-menu li.active>a span {
                    margin-left: 0px !important;
                }
                

                /* Dark modenya we */
                .dark .sidebar-menu li.active>a,
                .dark .sidebar-menu li.active>a .menu-icon,
                .dark .sidebar-menu li.active>a span {
                    background-color: rgb(72 127 255 / var(--tw-bg-opacity));
                    color: #ffffff !important;
                    font-weight: 600;
                }
            </style>

            <li class="active">
                <a href="{{ route('super-admin.index') }}">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span class="">Home</span>
                </a>
            </li>
            <li class="mt-2 mb-2">
                <a href="{{ route('super-admin.index') }}">
                    <iconify-icon icon="hugeicons:dashboard-square-01" class="menu-icon"></iconify-icon>
                    <span class="px-2">Dashboard</span>
                </a>
            </li>
            <li class="dropdown ">
                <a href="javascript:void(0)">
                    <iconify-icon icon="lucide:user-cog" class="menu-icon"></iconify-icon>
                    <span class="px-2">Users</span>
                </a>
                <ul class="sidebar-submenu">
                    <li class="pb-2">
                        <a href="{{ route('super-admin.index2') }}">
                            {{-- <iconify-icon icon="iconoir:add-user" class="menu-icon"></iconify-icon> --}}
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                            <span class="px-2">Manage Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.index4') }}">
                            {{-- <iconify-icon icon="arcticons:my-data-manager" class="menu-icon"></iconify-icon> --}}
                            <i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i>
                            <span class="px-2">Statistik Users</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="dropdown mt-2 mb-6">
                <a href="javascript:void(0)">
                    <iconify-icon icon="guidance:settings" class="menu-icon"></iconify-icon>
                    <span class="px-2">Settings Global</span>
                </a>
                <ul class="sidebar-submenu">
                    <li class="pb-2">
                        <a href="{{ route('super-admin.index9') }}">
                            {{-- <iconify-icon icon="iconoir:add-user" class="menu-icon"></iconify-icon> --}}
                            <i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i>
                            <span class="px-2">Midtrans API Key</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.index7') }}">
                            {{-- <iconify-icon icon="arcticons:my-data-manager" class="menu-icon"></iconify-icon> --}}
                            <i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i>
                            <span class="px-2">Global Notifications</span>
                        </a>
                    </li>
                </ul>
            </li>





           
    </div>
</aside>
