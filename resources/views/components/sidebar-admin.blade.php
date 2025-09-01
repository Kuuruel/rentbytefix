<aside class="sidebar">
    <button type="button" class="sidebar-close-btn !mt-4">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('super-admin.index') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/logo-light.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <!-- Home selalu active, Dashboard tidak pernah active -->
            <style>
                .sidebar-menu li.active>a,
                .sidebar-menu li.active>a .menu-icon,
                .sidebar-menu li.active>a span {
                    background-color: rgb(72 127 255 / var(--tw-bg-opacity));
                    color: #ffffff !important;
                    font-weight: 600;
                }
   
            </style>


            <li class="active">
                <a href="{{ route('super-admin.index') }}">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Home</span>
                </a>
            </li>
            <li class="mt-2 mb-2">
                <a href="{{ route('super-admin.index') }}">
                    <iconify-icon icon="hugeicons:dashboard-square-01" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="dropdown ">
                <a href="javascript:void(0)">
                    <iconify-icon icon="lucide:user-cog" class="menu-icon"></iconify-icon>
                    <span>Users</span>
                </a>
                <ul class="sidebar-submenu">
                    <li class="pb-2">
                        <a href="{{ route('super-admin.index2') }}">
                            <iconify-icon icon="iconoir:add-user" class="menu-icon"></iconify-icon>
                            <span>Manage Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.index4') }}">
                            <iconify-icon icon="arcticons:my-data-manager" class="menu-icon"></iconify-icon>
                            <span>Statistik Users</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('super-admin.index3') }}">
                    <iconify-icon icon="guidance:settings" class="menu-icon"></iconify-icon>
                    <span>Settings Global</span>
                </a>
            </li>

        </ul>
    </div>
</aside>
