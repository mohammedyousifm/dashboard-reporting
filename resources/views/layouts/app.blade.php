@php $isAr = app()->getLocale() === 'ar'; $dir = $isAr ? 'rtl' : 'ltr'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('ui.pg_dashboard')) — {{ __('ui.app_name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if($isAr)
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-w:  260px;
            --primary:    #4f46e5;
            --primary-d:  #3730a3;
            --primary-l:  #6366f1;
            --sidebar-tx: #8b96b5;
            --radius-xl:  18px;
            --radius-lg:  14px;
            --radius-md:  10px;
            --radius-sm:  7px;
            --shadow-xs:  0 1px 2px rgba(15,23,42,.05);
            --shadow-sm:  0 1px 3px rgba(15,23,42,.07), 0 1px 2px rgba(15,23,42,.04);
            --shadow-md:  0 4px 16px rgba(15,23,42,.09), 0 2px 6px rgba(15,23,42,.04);
            --shadow-lg:  0 12px 32px rgba(15,23,42,.11), 0 4px 10px rgba(15,23,42,.06);
        }
        *, *::before, *::after { box-sizing: border-box; }

        body {
            background: #eceef7;
            font-family: 'Cairo', 'Segoe UI', sans-serif;
            font-size: .9rem;
            color: #1e293b;
            min-height: 100vh;
        }

        /* ── Scrollbars ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c8d0e0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ══════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            background: linear-gradient(175deg, #1a2035 0%, #1e2845 55%, #1c2d50 100%);
            min-height: 100vh;
            position: fixed;
            top: 0; {{ $isAr ? 'right:0;left:auto;' : 'left:0;' }}
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform .28s cubic-bezier(.4,0,.2,1);
            display: flex;
            flex-direction: column;
            border-{{ $isAr ? 'left' : 'right' }}: 1px solid rgba(255,255,255,.06);
        }
        .sidebar::-webkit-scrollbar { width: 3px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); }

        /* ── Brand ── */
        .sidebar-brand {
            padding: 1.4rem 1.15rem 1.3rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            flex-shrink: 0;
            background: rgba(0,0,0,.12);
        }
        .sidebar-brand .brand-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(79,70,229,.5);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-brand h5 { color: #f0f4ff; margin: 0; font-weight: 800; font-size: .95rem; line-height: 1.2; }
        .sidebar-brand small { color: #7b8faf; font-size: .72rem; }

        /* ── Section labels ── */
        .sidebar-nav { padding: .5rem 0 1.5rem; flex: 1; }
        .sidebar-section {
            padding: 1.2rem 1.3rem .4rem;
            font-size: .63rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #6b7fa0;
            font-weight: 700;
        }

        /* ── Nav links ── */
        .sidebar-nav .nav-link {
            color: #b0bdd4;
            margin: .12rem .65rem;
            padding: .55rem .9rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: .65rem;
            font-size: .85rem;
            font-weight: 500;
            transition: background .13s, color .13s;
            position: relative;
        }
        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,.09);
            color: #dde5f5;
        }
        .sidebar-nav .nav-link.active {
            background: rgba(99,102,241,.28);
            color: #e2e8ff;
            font-weight: 700;
            border-{{ $isAr ? 'right' : 'left' }}: 3px solid #818cf8;
            padding-{{ $isAr ? 'right' : 'left' }}: calc(.9rem - 3px);
        }
        .sidebar-nav .nav-link i {
            font-size: 1rem; width: 1.15rem; text-align: center; flex-shrink: 0;
            color: #6d7fa0;
            transition: color .13s;
        }
        .sidebar-nav .nav-link:hover i { color: #a8b8d0; }
        .sidebar-nav .nav-link.active i { color: #818cf8; }

        /* ══════════════════════════════════════
           MAIN WRAPPER
        ══════════════════════════════════════ */
        .main-wrapper {
            {{ $isAr ? 'margin-right: var(--sidebar-w); margin-left: 0;' : 'margin-left: var(--sidebar-w);' }}
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* ── Topbar ── */
        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e6eaf3;
            padding: .8rem 1.6rem;
            position: sticky; top: 0;
            z-index: 900;
            display: flex; align-items: center;
            justify-content: space-between;
            gap: 1rem;
            min-height: 64px;
            box-shadow: 0 1px 0 #e6eaf3, 0 2px 10px rgba(15,23,42,.04);
        }
        .topbar-title { font-size: 1rem; font-weight: 700; margin: 0; line-height: 1.2; color: #0f172a; }
        .topbar-sub { font-size: .72rem; color: #64748b; margin-top: 1px; }

        /* ── Page body ── */
        .page-body { padding: 1.5rem 1.6rem; flex: 1; }

        /* ══════════════════════════════════════
           KPI CARDS
        ══════════════════════════════════════ */
        .kpi-card {
            background: #fff;
            border-radius: var(--radius-xl);
            padding: 1.3rem 1.35rem 1.15rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid #e6eaf3;
            position: relative;
            overflow: hidden;
            transition: transform .2s cubic-bezier(.34,1.56,.64,1), box-shadow .2s;
        }
        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: var(--kpi-accent, var(--primary));
            border-radius: var(--radius-xl) var(--radius-xl) 0 0;
        }
        .kpi-card::after {
            content: '';
            position: absolute;
            {{ $isAr ? 'left: -16px; right: auto;' : 'right: -16px; left: auto;' }}
            top: -16px;
            width: 72px; height: 72px;
            background: var(--kpi-accent, var(--primary));
            opacity: .055;
            border-radius: 50%;
        }
        .kpi-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
        .kpi-icon {
            width: 46px; height: 46px;
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
        }
        .kpi-value {
            font-size: 1.9rem; font-weight: 800; line-height: 1.1;
            margin-top: .5rem; letter-spacing: -.02em;
        }
        .kpi-label {
            font-size: .72rem; color: #64748b; margin-top: .2rem;
            font-weight: 600; letter-spacing: .04em; text-transform: uppercase;
        }

        /* ══════════════════════════════════════
           CHART / INFO CARDS
        ══════════════════════════════════════ */
        .chart-card {
            background: #fff;
            border-radius: var(--radius-xl);
            padding: 1.3rem 1.4rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid #e6eaf3;
        }
        .chart-card .card-title {
            font-size: .75rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 1rem;
            padding-bottom: .7rem;
            border-bottom: 1px solid #f1f5f9;
            text-transform: uppercase;
            letter-spacing: .06em;
            display: flex; align-items: center; gap: .35rem;
        }
        .filter-card {
            background: #fff;
            border-radius: var(--radius-xl);
            padding: 1.05rem 1.4rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid #e6eaf3;
            margin-bottom: 1.25rem;
        }

        /* ══════════════════════════════════════
           TABLE CARDS
        ══════════════════════════════════════ */
        .table-card {
            background: #fff;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            border: 1px solid #e6eaf3;
            overflow: hidden;
        }
        .table-card-header {
            padding: .9rem 1.3rem;
            border-bottom: 1px solid #f0f2f9;
            display: flex; align-items: center;
            justify-content: space-between;
            background: linear-gradient(to bottom, #fafbfe 0%, #f6f8fd 100%);
        }
        .table-card-header .tc-title {
            font-size: .75rem; font-weight: 700; color: #475569;
            text-transform: uppercase; letter-spacing: .06em;
            display: flex; align-items: center; gap: .35rem;
        }
        .table-card .table { margin: 0; }
        .table-card .table thead th {
            background: #f6f8fc;
            font-size: .67rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            font-weight: 700;
            border-bottom: 1px solid #eaecf5;
            padding: .7rem 1.1rem;
            white-space: nowrap;
        }
        .table-card .table tbody td {
            padding: .75rem 1.1rem;
            border-bottom: 1px solid #f3f5fb;
            vertical-align: middle;
        }
        .table-card .table tbody tr:last-child td { border-bottom: none; }
        .table-card .table tbody tr:hover td { background: #f4f6ff; }
        .table-card .table tfoot td {
            background: #f6f8fc; font-size: .84rem;
            padding: .72rem 1.1rem; font-weight: 700;
            border-top: 1px solid #eaecf5;
        }

        /* ══════════════════════════════════════
           BADGES
        ══════════════════════════════════════ */
        .badge {
            font-weight: 600; font-size: .68rem;
            padding: .3em .78em; border-radius: 30px; letter-spacing: .01em;
        }
        .status-active      { background: #dcfce7; color: #166534; }
        .status-completed   { background: #dbeafe; color: #1e40af; }
        .status-pending     { background: #fef9c3; color: #854d0e; }
        .status-inactive    { background: #f1f5f9; color: #475569; }
        .status-on_hold     { background: #fce7f3; color: #9d174d; }
        .status-cancelled   { background: #fee2e2; color: #991b1b; }
        .status-in_progress { background: #ede9fe; color: #5b21b6; }
        .status-closed      { background: #f1f5f9; color: #475569; }
        .status-paid        { background: #dcfce7; color: #166534; }
        .status-reimbursed  { background: #e0f2fe; color: #0c4a6e; }

        /* ══════════════════════════════════════
           SUMMARY BOX
        ══════════════════════════════════════ */
        .summary-box {
            border-radius: var(--radius-xl);
            padding: 1.1rem 1.4rem;
            display: flex; align-items: flex-start; gap: 1rem;
            margin-bottom: 1.25rem;
        }
        .summary-box .sb-icon {
            width: 44px; height: 44px;
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; flex-shrink: 0;
        }
        .summary-box .sb-label {
            font-size: .67rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .08em; opacity: .55; margin-bottom: .25rem;
        }
        .summary-box p { margin: 0; font-size: .9rem; line-height: 1.6; }

        /* ══════════════════════════════════════
           EMPTY STATES
        ══════════════════════════════════════ */
        .chart-empty {
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            color: #94a3b8; font-size: .82rem;
            min-height: 140px; gap: .5rem;
        }
        .chart-empty i { font-size: 2.5rem; opacity: .2; }
        .table-empty { text-align: center; color: #94a3b8; padding: 3rem 1rem; }
        .table-empty i { font-size: 2.8rem; opacity: .18; display: block; margin-bottom: .6rem; }

        /* ══════════════════════════════════════
           ALERTS
        ══════════════════════════════════════ */
        .alert {
            border-radius: var(--radius-md);
            border: none;
            font-size: .85rem;
            font-weight: 500;
            box-shadow: var(--shadow-xs);
        }
        .alert-success {
            background: linear-gradient(to right, #dcfce7, #f0fdf4);
            color: #166534;
            border-inline-start: 4px solid #22c55e;
        }

        /* ══════════════════════════════════════
           FORMS
        ══════════════════════════════════════ */
        .form-label { font-size: .8rem; font-weight: 700; color: #374151; margin-bottom: .35rem; }
        .form-control, .form-select {
            font-size: .84rem;
            border: 1.5px solid #dde2ef;
            border-radius: 9px;
            padding: .5rem .85rem;
            background: #fcfcfe;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3.5px rgba(79,70,229,.13);
            background: #fff;
            outline: none;
        }
        .form-control-sm, .form-select-sm { font-size: .8rem; padding: .32rem .65rem; }
        .input-group-text {
            background: #f4f6fb; border-color: #dde2ef;
            border-radius: 9px; font-size: .84rem; font-weight: 600; color: #475569;
        }
        .input-group > .form-control:not(:first-child),
        .input-group > .form-select:not(:first-child) { border-start-start-radius: 0; border-end-start-radius: 0; }
        .input-group > .input-group-text:first-child { border-start-end-radius: 0; border-end-end-radius: 0; }

        /* ══════════════════════════════════════
           BUTTONS
        ══════════════════════════════════════ */
        .btn { font-size: .83rem; font-weight: 600; border-radius: 9px; transition: all .14s; }
        .btn:active { transform: scale(.97) !important; }
        .btn-sm { font-size: .77rem; padding: .3rem .75rem; }
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #5e56e8 100%);
            border-color: #4f46e5;
            box-shadow: 0 2px 8px rgba(79,70,229,.28);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #3730a3 0%, #4f46e5 100%);
            border-color: #3730a3;
            box-shadow: 0 4px 12px rgba(79,70,229,.36);
            transform: translateY(-1px);
        }
        .btn-outline-primary:hover, .btn-outline-secondary:hover,
        .btn-outline-danger:hover, .btn-outline-success:hover,
        .btn-outline-warning:hover { transform: translateY(-1px); box-shadow: var(--shadow-xs); }
        .btn-xs { font-size: .72rem; padding: .22rem .55rem; border-radius: 6px; }

        /* ══════════════════════════════════════
           LANG SWITCH
        ══════════════════════════════════════ */
        .lang-switch {
            display: flex; gap: 3px;
            background: #f0f2f8; border-radius: 9px; padding: 3px;
        }
        .lang-switch a {
            font-size: .72rem; font-weight: 700;
            padding: .24rem .68rem; border-radius: 7px;
            text-decoration: none; color: #64748b;
            transition: all .13s;
        }
        .lang-switch a.active {
            background: var(--primary); color: #fff;
            box-shadow: 0 2px 6px rgba(79,70,229,.3);
        }

        /* ══════════════════════════════════════
           MOBILE / OVERLAY
        ══════════════════════════════════════ */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.48);
            backdrop-filter: blur(2px);
            z-index: 999;
        }
        .sidebar-overlay.show { display: block; }
        @media (max-width: 991.98px) {
            .sidebar { transform: {{ $isAr ? 'translateX(100%)' : 'translateX(-100%)' }}; }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { {{ $isAr ? 'margin-right:0;' : 'margin-left:0;' }} }
            .sidebar-toggle { display: flex !important; }
            .page-body { padding: 1rem; }
        }
        .sidebar-toggle { display: none; }

        /* ══════════════════════════════════════
           PRINT
        ══════════════════════════════════════ */
        @media print {
            .sidebar, .topbar, .no-print, .filter-card { display: none !important; }
            .main-wrapper { margin: 0 !important; }
            .page-body { padding: 0; }
            .table-card, .chart-card { box-shadow: none; border: 1px solid #ddd; }
        }

        /* ══════════════════════════════════════
           RTL TWEAKS
        ══════════════════════════════════════ */
        @if($isAr)
        .table-card .table thead th,
        .table-card .table tbody td { text-align: right; }
        .kpi-card::before { border-radius: 0 var(--radius-xl) var(--radius-xl) 0; }
        @endif
    </style>
    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2">
            <div class="brand-icon"><i class="bi bi-bar-chart-fill text-white"></i></div>
            <div>
                <h5>{{ __('ui.app_name') }}</h5>
                <small>{{ __('ui.app_subtitle') }}</small>
            </div>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="sidebar-section">{{ __('ui.nav_overview') }}</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> {{ __('ui.nav_dashboard') }}
        </a>

        <div class="sidebar-section">{{ __('ui.nav_my_work') }}</div>
        <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
            <i class="bi bi-kanban"></i> {{ __('ui.nav_projects') }}
        </a>
        <a href="{{ route('tasks.index') }}" class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
            <i class="bi bi-check2-square"></i> {{ __('ui.nav_tasks') }}
        </a>
        <a href="{{ route('achievements.index') }}" class="nav-link {{ request()->routeIs('achievements.*') ? 'active' : '' }}">
            <i class="bi bi-trophy"></i> {{ __('ui.nav_achievements') }}
        </a>

        <div class="sidebar-section">{{ __('ui.nav_reports_sec') }}</div>
        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
            <i class="bi bi-file-text"></i> {{ __('ui.nav_all_reports') }}
        </a>
        <a href="{{ route('reports.weekly') }}" class="nav-link {{ request()->routeIs('reports.weekly') ? 'active' : '' }}">
            <i class="bi bi-calendar-week"></i> {{ __('ui.nav_weekly') }}
        </a>
        <a href="{{ route('reports.monthly') }}" class="nav-link {{ request()->routeIs('reports.monthly') ? 'active' : '' }}">
            <i class="bi bi-calendar-month"></i> {{ __('ui.nav_monthly') }}
        </a>
        <a href="{{ route('reports.yearly') }}" class="nav-link {{ request()->routeIs('reports.yearly') ? 'active' : '' }}">
            <i class="bi bi-calendar-range"></i> {{ __('ui.nav_yearly') }}
        </a>

        <div class="sidebar-section">{{ __('ui.nav_payments') }}</div>
        <a href="{{ route('budgets.index') }}" class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
            <i class="bi bi-wallet2"></i> {{ __('ui.nav_budgets') }}
        </a>
        <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> {{ __('ui.nav_expenses') }}
        </a>

        <div class="sidebar-section">{{ __('ui.nav_setup') }}</div>
        <a href="{{ route('companies.index') }}" class="nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i> {{ __('ui.nav_companies') }}
        </a>
    </div>
</nav>

<!-- Main -->
<div class="main-wrapper">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="sidebar-toggle btn btn-sm btn-outline-secondary" id="sidebarToggle">
                <i class="bi bi-list fs-5"></i>
            </button>
            <div>
                <p class="topbar-title">@yield('page-title', __('ui.pg_dashboard'))</p>
                <span class="topbar-sub">@yield('page-subtitle', '')</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @yield('topbar-actions')
            <div class="lang-switch">
                <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
                <a href="{{ route('lang.switch', 'ar') }}" class="{{ app()->getLocale() === 'ar' ? 'active' : '' }}">ع</a>
            </div>
            <div class="text-muted d-none d-lg-block" style="font-size:.72rem">{{ now()->format('D, M d Y') }}</div>
        </div>
    </div>

    <div class="page-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@stack('scripts')
<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
const toggle  = document.getElementById('sidebarToggle');
const isRtl   = document.documentElement.dir === 'rtl';

function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('show'); }
function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('show'); }
toggle.addEventListener('click', () => sidebar.classList.contains('open') ? closeSidebar() : openSidebar());
overlay.addEventListener('click', closeSidebar);

// Close sidebar when clicking a nav link on mobile
document.querySelectorAll('.sidebar .nav-link').forEach(l => l.addEventListener('click', () => {
    if (window.innerWidth < 992) closeSidebar();
}));
</script>
</body>
</html>
