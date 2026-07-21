<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Myme Admin')</title>

<link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<link rel="shortcut icon" href="{{ asset('images/favicon.svg') }}" type="image/x-icon">

<style>
/* ── Layout ── */
body { background: #f1f4f9; font-size: 14px; }
#main { background: #f1f4f9; min-height: 100vh; }

/* ── Sidebar ── */
#sidebar { width: 240px; }
#sidebar .sidebar-wrapper {
    background: #ffffff;
    border-right: 1px solid #e5e9f0;
    height: 100vh;
    display: flex;
    flex-direction: column;
}
#sidebar .sidebar-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e5e9f0;
    background: #f0fdf4;
}
#sidebar .sidebar-header img { height: 36px; }
#sidebar .sidebar-header .brand-name {
    font-size: 17px;
    font-weight: 700;
    color: #16a34a;
    letter-spacing: .5px;
}
#sidebar .sidebar-menu { padding: 8px 0; flex: 1; overflow-y: auto; }
#sidebar .menu { padding: 0; margin: 0; list-style: none; }
#sidebar .sidebar-title {
    padding: 12px 20px 4px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: #9ca3af;
}
#sidebar .sidebar-item > .sidebar-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 20px;
    color: #4b5563;
    font-size: 13.5px;
    font-weight: 500;
    border-left: 3px solid transparent;
    transition: all .15s;
    text-decoration: none;
}
#sidebar .sidebar-item > .sidebar-link:hover {
    color: #16a34a;
    background: #f0fdf4;
}
#sidebar .sidebar-item.active > .sidebar-link {
    color: #16a34a;
    background: #dcfce7;
    border-left-color: #16a34a;
    font-weight: 600;
}
#sidebar .sidebar-item svg { width: 16px; height: 16px; flex-shrink: 0; }

/* submenu */
#sidebar .submenu { list-style: none; padding: 0; background: #f9fafb; }
#sidebar .submenu li a {
    display: block;
    padding: 8px 20px 8px 46px;
    color: #6b7280;
    font-size: 13px;
    text-decoration: none;
    transition: color .15s;
}
#sidebar .submenu li a:hover { color: #16a34a; }

/* sidebar toggler */
#sidebar .sidebar-toggler {
    margin: 12px 16px;
    color: #9ca3af;
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
}

/* ── Navbar ── */
.navbar.navbar-header {
    background: #fff;
    border-bottom: 1px solid #e5e9f0;
    padding: 0 20px;
    height: 56px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
}
.navbar .page-heading {
    font-size: 15px;
    font-weight: 600;
    color: #1a2238;
    margin: 0;
}
.navbar .nav-link { color: #4a5568; padding: 6px 10px; }
.navbar .nav-link:hover { color: #16a34a; }
.navbar .avatar img { width: 34px; height: 34px; border-radius: 50%; object-fit: cover; }
.navbar .admin-name { font-size: 13px; font-weight: 600; color: #1a2238; }

/* ── Page content ── */
.main-content { padding: 20px 24px !important; }
.page-title { margin-bottom: 16px; }
.page-title h3 { font-size: 18px; font-weight: 700; color: #1a2238; margin: 0; }
.breadcrumb { margin: 0; font-size: 12px; }
.breadcrumb-item + .breadcrumb-item::before { color: #aaa; }

/* ── Cards ── */
.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 1px 6px rgba(0,0,0,.07);
    margin-bottom: 16px;
}
.card-header {
    background: #fff;
    border-bottom: 1px solid #f0f0f0;
    padding: 12px 16px;
    font-size: 14px;
    font-weight: 600;
    color: #1a2238;
    border-radius: 10px 10px 0 0 !important;
}
.card-body { padding: 14px 16px; }

/* ── Stat cards ── */
.stat-card {
    border-radius: 12px;
    padding: 18px 20px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 4px 14px rgba(0,0,0,.12);
    margin-bottom: 16px;
}
.stat-card .stat-icon {
    width: 52px; height: 52px;
    border-radius: 12px;
    background: rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.stat-card .stat-icon svg { width: 24px; height: 24px; color: #fff; }
.stat-card .stat-label { font-size: 12px; opacity: .85; margin-bottom: 2px; }
.stat-card .stat-value { font-size: 26px; font-weight: 700; line-height: 1; }
.stat-orange { background: linear-gradient(135deg, #16a34a, #15803d); }
.stat-blue   { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.stat-green  { background: linear-gradient(135deg, #10b981, #059669); }
.stat-purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }

/* ── Tables ── */
.table { font-size: 13px; margin: 0; }
.table thead th {
    background: #16a34a;
    color: #fff;
    font-weight: 600;
    font-size: 12px;
    letter-spacing: .4px;
    padding: 10px 12px;
    border: none;
    white-space: nowrap;
}
.table tbody td { padding: 9px 12px; vertical-align: middle; border-color: #f0f3f8; }
.table tbody tr:hover { background: #f8faff; }
.table-responsive { border-radius: 8px; overflow: hidden; }

/* ── Buttons ── */
.btn-sm { padding: 4px 8px; font-size: 12px; border-radius: 6px; }
.btn { border-radius: 7px; font-size: 13px; font-weight: 500; }
.btn-primary { background: #16a34a; border-color: #16a34a; }
.btn-primary:hover { background: #15803d; border-color: #15803d; }

/* ── Alerts ── */
.alert { border: none; border-radius: 8px; font-size: 13px; padding: 10px 16px; }

/* ── Modal ── */
.modal-header { padding: 14px 20px; border-bottom: 1px solid #eee; }
.modal-footer { padding: 12px 20px; border-top: 1px solid #eee; }
.modal-body { padding: 16px 20px; }
.modal-title { font-size: 15px; font-weight: 600; }
.form-label { font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 4px; }
.form-control, .form-select { font-size: 13px; border-radius: 7px; border-color: #d1d5db; padding: 6px 10px; }
.form-control:focus, .form-select:focus { border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,.12); }

/* ── Pagination ── */
.pagination { margin: 0; font-size: 13px; }
.page-link { padding: 5px 10px; color: #16a34a; border-color: #e5e9f0; }
.page-item.active .page-link { background: #16a34a; border-color: #16a34a; color: #fff; }

/* ── Footer ── */
footer { padding: 12px 24px; font-size: 12px; color: #9ca3af; background: transparent; }

/* ── Remove extra whitespace ── */
.section { margin: 0; }
</style>
