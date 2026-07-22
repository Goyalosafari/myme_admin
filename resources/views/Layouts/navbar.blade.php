<nav class="navbar navbar-header navbar-expand navbar-light">
    <a class="sidebar-toggler me-3" href="#" style="color:#4a5568;">
        <i data-feather="menu"></i>
    </a>

    <span class="page-heading">
        @yield('page-title', 'Dashboard')
    </span>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav d-flex align-items-center ms-auto gap-1">

            {{-- Notifications --}}
            <li class="nav-item dropdown">
                <a href="#" data-bs-toggle="dropdown" class="nav-link position-relative" title="Notifications">
                    <i data-feather="bell"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end" style="min-width:260px;">
                    <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-600" style="font-size:13px;">Notifications</span>
                    </div>
                    <div class="px-3 py-3 text-muted text-center" style="font-size:12px;">No new notifications</div>
                </div>
            </li>

            {{-- Admin dropdown --}}
            <li class="nav-item dropdown">
                <a href="#" data-bs-toggle="dropdown" class="nav-link d-flex align-items-center gap-2">
                    <div style="width:34px;height:34px;border-radius:50%;background:#16a34a;color:#fff;
                                display:flex;align-items:center;justify-content:center;
                                font-size:13px;font-weight:700;flex-shrink:0;">
                        {{ strtoupper(substr(config('admin.name', 'A'), 0, 1)) }}
                    </div>
                    <span class="admin-name d-none d-md-inline">{{ config('admin.name', 'Admin') }}</span>
                    <i data-feather="chevron-down" style="width:14px;height:14px;"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end" style="min-width:180px;">
                    <div class="px-3 py-2 border-bottom">
                        <div style="font-size:13px; font-weight:600; color:#1a2238;">{{ config('admin.name', 'Admin') }}</div>
                        <div style="font-size:11px; color:#9ca3af;">{{ config('admin.email') }}</div>
                    </div>
                    <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('change-password') }}">
                        <i data-feather="key" style="width:14px;height:14px;"></i> Change Password
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger">
                            <i data-feather="log-out" style="width:14px;height:14px;"></i> Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
