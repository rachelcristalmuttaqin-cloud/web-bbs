<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin HES') - System BBS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --sidebar-w: 260px;
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --primary-light: #ede9fe;
            --surface: #ffffff;
            --bg: #f1f5f9;
            --border: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --sidebar-bg: #0f172a;
            --sidebar-text: #94a3b8;
            --sidebar-active: #4f46e5;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            left: 0; top: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            display: flex; align-items: center; gap: 0.75rem;
        }
        .sidebar-brand-icon {
            width: 40px; height: 40px; min-width: 40px;
            background: linear-gradient(135deg, #4f46e5, #2563eb);
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.15rem;
            box-shadow: 0 4px 12px rgba(79,70,229,0.4);
        }
        .sidebar-brand-text { line-height: 1.2; }
        .sidebar-brand-title { font-size: 0.925rem; font-weight: 700; color: #f1f5f9; }
        .sidebar-brand-sub { font-size: 0.72rem; color: #64748b; margin-top: 0.15rem; }

        .sidebar-nav { flex: 1; padding: 1rem 0.75rem; overflow-y: auto; }
        .sidebar-section { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.09em; color: #475569; padding: 0.75rem 0.75rem 0.4rem; }
        .sidebar-item {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.65rem 0.875rem;
            border-radius: 10px;
            text-decoration: none;
            color: var(--sidebar-text);
            font-size: 0.875rem; font-weight: 500;
            transition: all 0.18s ease;
            margin-bottom: 0.2rem;
            white-space: nowrap;
        }
        .sidebar-item:hover { background: rgba(255,255,255,0.07); color: #e2e8f0; }
        .sidebar-item.active { background: rgba(79,70,229,0.2); color: #a5b4fc; font-weight: 600; }
        .sidebar-item.active .nav-icon { color: #818cf8; }
        .nav-icon { width: 20px; height: 20px; min-width: 20px; opacity: 0.8; }
        .sidebar-item:hover .nav-icon { opacity: 1; }

        .sidebar-footer {
            padding: 1rem 0.75rem;
            border-top: 1px solid rgba(255,255,255,0.07);
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 10px;
            background: rgba(255,255,255,0.05);
        }
        .sidebar-avatar {
            width: 36px; height: 36px; min-width: 36px;
            background: linear-gradient(135deg, #818cf8, #4f46e5);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.875rem; color: #fff;
        }
        .sidebar-user-info { flex: 1; min-width: 0; }
        .sidebar-user-name { font-size: 0.825rem; font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-role { font-size: 0.7rem; color: #64748b; }
        .btn-logout-icon {
            background: none; border: none; color: #475569; cursor: pointer;
            padding: 0.35rem; border-radius: 6px; transition: all 0.18s;
            display: flex; align-items: center;
        }
        .btn-logout-icon:hover { background: rgba(239,68,68,0.15); color: #f87171; }

        /* ── TOPBAR ── */
        .topbar {
            position: fixed;
            top: 0; left: var(--sidebar-w); right: 0;
            height: 60px;
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.75rem;
            z-index: 90;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .topbar-title { font-size: 1rem; font-weight: 700; color: #0f172a; }
        .topbar-right { display: flex; align-items: center; gap: 0.75rem; }
        .topbar-badge {
            background: #ede9fe; color: #5b21b6;
            padding: 0.3rem 0.75rem; border-radius: 20px;
            font-size: 0.75rem; font-weight: 700;
        }

        /* ── MAIN ── */
        .main-content {
            margin-left: var(--sidebar-w);
            padding-top: 60px;
            flex: 1;
            min-height: 100vh;
        }
        .content-body { padding: 1.75rem 2rem; overflow-x: hidden; }

        /* ── CARDS ── */
        .card { background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem; box-shadow: 0 1px 4px rgba(0,0,0,0.03); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
        .card-title { font-size: 1rem; font-weight: 700; color: #0f172a; }

        /* ── ALERTS ── */
        .alert { padding: 0.875rem 1.1rem; border-radius: 12px; margin-bottom: 1.25rem; font-size: 0.875rem; font-weight: 500; display: flex; align-items: center; gap: 0.6rem; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-info { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }

        /* ── TABLES ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        th, td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid #f1f5f9; }
        th { background: #f8fafc; color: #64748b; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #fafafa; }

        /* ── BADGES ── */
        .badge { padding: 0.2rem 0.55rem; border-radius: 12px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
        .badge-safe { background: #dcfce7; color: #15803d; }
        .badge-at_risk { background: #fee2e2; color: #b91c1c; }
        .badge-rendah { background: #e0f2fe; color: #0369a1; }
        .badge-sedang { background: #fef3c7; color: #b45309; }
        .badge-tinggi { background: #fecaca; color: #991b1b; }
        .badge-pending { background: #fef9c3; color: #854d0e; }
        .badge-active { background: #dcfce7; color: #15803d; }
        .badge-rejected { background: #fee2e2; color: #b91c1c; }
        .badge-identifier { display: inline-flex; align-items: center; background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; padding: 0.3rem 0.65rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.02em; text-transform: none; white-space: nowrap; }

        /* ── BUTTONS ── */
        .btn { padding: 0.5rem 1.1rem; border-radius: 9px; font-size: 0.875rem; font-weight: 600; cursor: pointer; border: none; transition: all 0.18s; display: inline-flex; align-items: center; gap: 0.4rem; text-decoration: none; }
        .btn-primary { background: var(--primary); color: #fff; box-shadow: 0 4px 12px rgba(79,70,229,0.25); }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-success { background: #059669; color: #fff; }
        .btn-success:hover { background: #047857; }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-danger:hover { background: #dc2626; }
        .btn-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.8rem; }
        .btn-link { background: none; color: var(--primary); padding: 0; font-weight: 600; }
        .btn-link:hover { text-decoration: underline; }

        /* ── FORM ── */
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 0.4rem; }
        .form-control { width: 100%; padding: 0.65rem 0.875rem; border: 1px solid #cbd5e1; border-radius: 9px; font-size: 0.9rem; outline: none; font-family: inherit; }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229,0.12); }
        textarea.form-control { resize: vertical; min-height: 80px; }

        /* ── STATS ── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .stat-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 1.1rem 1.25rem; display: flex; align-items: center; gap: 0.875rem; }
        .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; }

        /* ── MOBILE ── */
        .sidebar-toggle { display: none; background: none; border: none; cursor: pointer; padding: 0.25rem; }
        .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; }

        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .topbar { left: 0; }
            .sidebar-toggle { display: flex; }
            .overlay.show { display: block; }
            .content-body { padding: 1rem; }
        }
        @media print {
            .sidebar, .topbar { display: none; }
            .main-content { margin: 0; padding: 0; }
        }

        /* Visual refresh: Admin HES */
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --bg: #f4f7fb;
            --border: #e5eaf2;
            --sidebar-bg: #101a2b;
        }
        body {
            background:
                radial-gradient(ellipse at 88% 2%, rgba(37,99,235,.075), transparent 28rem),
                radial-gradient(ellipse at 8% 88%, rgba(20,184,166,.055), transparent 30rem),
                var(--bg);
        }
        .sidebar {
            background: linear-gradient(165deg, #101a2b 0%, #14233a 55%, #102d3b 100%);
            box-shadow: 14px 0 40px rgba(15,23,42,.12);
        }
        .sidebar-brand { padding: 1.55rem 1.25rem 1.4rem; }
        .sidebar-brand-icon {
            background: linear-gradient(145deg, #38bdf8, #2563eb 60%, #4f46e5);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 14px;
            box-shadow: 0 8px 22px rgba(37,99,235,.32);
        }
        .sidebar-item {
            border: 1px solid transparent;
            border-radius: 12px;
            padding: .72rem .875rem;
        }
        .sidebar-item.active {
            color: #eff6ff;
            background: linear-gradient(100deg, rgba(37,99,235,.34), rgba(20,184,166,.12));
            border-color: rgba(125,211,252,.15);
            box-shadow: inset 3px 0 #38bdf8;
        }
        .sidebar-item.active .nav-icon { color: #7dd3fc; }
        .sidebar-footer { border-color: rgba(255,255,255,.1); }
        .sidebar-user { background: rgba(255,255,255,.065); border: 1px solid rgba(255,255,255,.07); }
        .sidebar-avatar { background: linear-gradient(145deg, #14b8a6, #2563eb); }
        .topbar {
            height: 72px;
            background: rgba(255,255,255,.88);
            backdrop-filter: blur(16px);
            border-bottom-color: rgba(226,232,240,.85);
            box-shadow: 0 5px 24px rgba(15,23,42,.045);
        }
        .topbar-title { letter-spacing: -.025em; }
        .topbar-badge { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
        .main-content { padding-top: 72px; }
        .content-body { max-width: 1680px; margin: 0 auto; padding: 2rem 2.25rem; }
        .card {
            border-color: rgba(226,232,240,.9);
            border-radius: 20px;
            box-shadow: 0 8px 28px rgba(15,23,42,.045), 0 1px 2px rgba(15,23,42,.025);
        }
        .card-header { gap: 1rem; }
        .card-title { letter-spacing: -.02em; }
        .stats-grid { gap: 1.1rem; }
        .stat-card {
            border-radius: 18px;
            box-shadow: 0 5px 18px rgba(15,23,42,.035);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 26px rgba(15,23,42,.075); }
        .table-wrap { border: 1px solid #edf1f6; border-radius: 14px; }
        table { font-size: .84rem; }
        th { background: #f5f8fc; color: #52627a; padding-top: .9rem; padding-bottom: .9rem; }
        td { color: #334155; }
        tr:hover td { background: #f7faff; }
        .btn { border-radius: 11px; transition: transform .18s ease, box-shadow .18s ease, background .18s ease; }
        .btn-primary { background: linear-gradient(135deg, #2563eb, #1d4ed8); box-shadow: 0 5px 14px rgba(37,99,235,.2); }
        .btn-primary:hover { box-shadow: 0 8px 18px rgba(37,99,235,.28); }
        .btn-success { background: linear-gradient(135deg, #0f9f8f, #0f766e); box-shadow: 0 5px 14px rgba(15,118,110,.17); }
        .btn-success:hover { background: linear-gradient(135deg, #0f8d80, #0d6b64); transform: translateY(-1px); }
        .form-control {
            border-color: #d9e1ec;
            border-radius: 11px;
            background: #fff;
            transition: border-color .18s ease, box-shadow .18s ease;
        }
        .form-control:focus { border-color: #60a5fa; box-shadow: 0 0 0 4px rgba(59,130,246,.12); }
        .alert { border-radius: 14px; box-shadow: 0 4px 14px rgba(15,23,42,.035); }
        .badge { letter-spacing: .025em; }
        @media (max-width: 900px) {
            .content-body { padding: 1.25rem; }
        }
        @media (max-width: 640px) {
            .topbar { height: 64px; padding: 0 .9rem; }
            .main-content { padding-top: 64px; }
            .topbar-title { font-size: .9rem; }
            .topbar-badge { font-size: .68rem; padding: .25rem .5rem; }
            .content-body { padding: .85rem; }
            .card { border-radius: 16px; padding: 1rem; }
            .card-header { align-items: flex-start; flex-wrap: wrap; }
            .stats-grid { grid-template-columns: 1fr 1fr; gap: .65rem; }
            .stat-card { padding: .85rem; }
            .stat-icon { width: 38px; height: 38px; min-width: 38px; }
        }
        @media print {
            .content-body { max-width: none; padding: 0; }
            .card { box-shadow: none; border-color: #ddd; }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- OVERLAY mobile -->
    <div class="overlay" id="overlay" onclick="closeSidebar()"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">🛡️</div>
            <div class="sidebar-brand-text">
                <div class="sidebar-brand-title">BBS C4.5</div>
                <div class="sidebar-brand-sub">Behavior Based Safety</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-section">Utama</div>
            <a href="{{ route('admin.index') }}" class="sidebar-item {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>

            <div class="sidebar-section">Manajemen</div>
            <a href="{{ route('admin.karyawan.index') }}" class="sidebar-item {{ request()->routeIs('admin.karyawan.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Data Karyawan PT
            </a>
            <a href="{{ route('admin.verifikasi') }}" class="sidebar-item {{ request()->routeIs('admin.verifikasi') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m22 11-4 4-2-2"/></svg>
                Verifikasi Karyawan
            </a>
            <a href="{{ route('admin.observasi.index') }}" class="sidebar-item {{ request()->routeIs('admin.observasi.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Data Observasi BBS
            </a>
            <a href="{{ route('admin.master.index') }}" class="sidebar-item {{ request()->routeIs('admin.master.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2z"/><path d="M7 7h.01"/></svg>
                Master Data
            </a>

            <div class="sidebar-section">Analitik</div>
            <a href="{{ route('admin.c45.index') }}" class="sidebar-item {{ request()->routeIs('admin.c45.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Modul C4.5
            </a>
            <a href="{{ route('admin.laporan.index') }}" class="sidebar-item {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Laporan & Ekspor
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                    <div class="sidebar-user-role">Admin HES</div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout-icon" title="Keluar">
                        <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- TOPBAR -->
    <header class="topbar">
        <div style="display:flex;align-items:center;gap:0.875rem;">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <svg width="22" height="22" fill="none" stroke="#334155" viewBox="0 0 24 24" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="topbar-right">
            <span class="topbar-badge">⚙️ Admin HES</span>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="content-body">
            @if(session('success'))
                <div class="alert alert-success">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">❌ {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">❌ {{ $errors->first() }}</div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('overlay').classList.toggle('show');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('overlay').classList.remove('show');
        }
    </script>
    @yield('scripts')
</body>
</html>
