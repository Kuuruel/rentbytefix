<aside class="sidebar">
    <button type="button" class="sidebar-close-btn !mt-4">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('landlord.index') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/rentbyte-logo.png') }}" alt="site logo" class="light-logo">
            <img src="{{ asset('assets/images/rentbyte-logo.png') }}" alt="site logo" class="dark-logo">
            <img src="{{ asset('assets/images/r-logos.png') }}" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            <li class="dropdown">
                <a href="javascript:void(0)">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                    <span>Dashboard</span>
                </a>
                <ul class="sidebar-submenu">
                    <li>
                        <a href="{{ route('landlord.index') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Statistic</a>
                    </li>
                    <!-- <li>
                        <a href="{{ route('landlord.index2') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> CRM</a>
                    </li> -->
                    <li>
                        <a href="{{ route('landlord.index3') }}"><i class="ri-circle-fill circle-icon text-warning-600 w-auto"></i> Properties Management</a>
                    </li>
                    <li>
                        <a href="{{ route('landlord.index4') }}"><i class="ri-circle-fill circle-icon text-primary-600 w-auto"></i> Transaction History</a>
                    </li>
                    <!-- <li>
                        <a href="{{ route('landlord.index5') }}"><i class="ri-circle-fill circle-icon text-success-600 w-auto"></i> Investment</a>
                    </li>
                    <li>
                        <a href="{{ route('landlord.index6') }}"><i class="ri-circle-fill circle-icon text-purple-600 w-auto"></i> LMS / Learning System</a>
                    </li>
                    <li>
                        <a href="{{ route('landlord.index7') }}"><i class="ri-circle-fill circle-icon text-info-600 w-auto"></i> NFT & Gaming</a>
                    </li>
                    <li>
                        <a href="{{ route('landlord.index8') }}"><i class="ri-circle-fill circle-icon text-danger-600 w-auto"></i> Medical</a>
                    </li>
                    <li>
                        <a href="{{ route('landlord.index9') }}"><i class="ri-circle-fill circle-icon text-purple-600 w-auto"></i> Analytics</a>
                    </li> -->
                </ul>
            </li>
        </ul>
    </div>
</aside>