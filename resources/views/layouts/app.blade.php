<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'HealthCare' }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons (Remix Icon) -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <!-- ChartJS for stats -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --bg-primary: #f8fafc;
            --bg-sidebar: rgba(255, 255, 255, 0.35);
            --text-sidebar: #475569;
            --text-sidebar-active: #ffffff;
            --accent-color: #0f766e; /* CoachPro Teal */
            --accent-hover: #0d9488;
            --card-bg: #ffffff;
            --card-border: rgba(226, 232, 240, 0.8);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: 
                radial-gradient(circle at 10% 20%, rgba(204, 251, 241, 0.6) 0%, transparent 40%),
                radial-gradient(circle at 90% 10%, rgba(224, 242, 254, 0.6) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(243, 232, 255, 0.5) 0%, transparent 50%),
                #f1f5f9;
            background-attachment: fixed;
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            overflow-x: hidden;
        }

        /* Outer Dashboard Glass Wrapper */
        .app-wrapper {
            display: flex;
            width: 100%;
            max-width: 1440px;
            height: calc(100vh - 48px);
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-radius: 32px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            margin: auto;
        }

        /* Sidebar - Integrated Inside Wrapper */
        .sidebar {
            width: 280px;
            background-color: var(--bg-sidebar);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-right: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            height: 100%;
            position: static;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 28px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-primary);
            border-bottom: 1px solid rgba(15, 118, 110, 0.08);
        }

        .sidebar-brand i {
            font-size: 24px;
            color: var(--accent-color);
        }

        .sidebar-brand h1 {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: var(--text-primary);
        }

        .sidebar-menu {
            list-style: none;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex-grow: 1;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(15, 118, 110, 0.1) transparent;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(15, 118, 110, 0.15);
            border-radius: 10px;
        }

        .sidebar-menu-divider {
            padding: 16px 16px 4px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--text-secondary);
            letter-spacing: 1px;
            opacity: 0.8;
        }

        .sidebar-menu-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: var(--text-sidebar);
            text-decoration: none;
            border-radius: 16px;
            font-weight: 700;
            font-size: 13.5px;
            transition: all 0.25s ease;
        }

        .sidebar-menu-item a i {
            font-size: 18px;
            color: var(--text-sidebar);
            transition: color 0.2s ease;
        }

        .sidebar-menu-item a:hover {
            background-color: rgba(15, 118, 110, 0.05);
            color: var(--accent-color);
        }

        .sidebar-menu-item a:hover i {
            color: var(--accent-color);
        }

        /* Active State - Teal-to-Blue Gradient Pill */
        .sidebar-menu-item.active a {
            background: linear-gradient(135deg, #0f766e 0%, #0891b2 100%);
            color: var(--text-sidebar-active);
            box-shadow: 0 8px 20px rgba(15, 118, 110, 0.2);
        }

        .sidebar-menu-item.active a i {
            color: #ffffff !important;
        }

        .sidebar-footer {
            padding: 20px 24px;
            border-top: 1px solid rgba(15, 118, 110, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0f766e 0%, #0891b2 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
            box-shadow: 0 4px 12px rgba(15, 118, 110, 0.2);
        }

        .user-details h3 {
            font-size: 13.5px;
            color: var(--text-primary);
            font-weight: 700;
        }

        .user-details span {
            font-size: 10px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        /* Main Content Container - Integrated Inside Wrapper */
        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
            margin-left: 0;
            width: auto;
            padding: 0;
            overflow: hidden;
        }

        /* Header Panel */
        header {
            background-color: rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 20px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: none;
            margin-bottom: 0;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-title i {
            font-size: 24px;
            color: var(--accent-color);
        }

        .header-title h2 {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-primary);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-logout {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 20px;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            color: var(--danger);
            background-color: rgba(239, 68, 68, 0.08);
        }

        /* Content Body - Scrollable content area inside glass wrapper */
        .content-body {
            padding: 32px;
            flex-grow: 1;
            overflow-y: auto;
        }

        /* Grid layouts */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        /* Card styles - Neumorphic / claymorphic white cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02), inset 0 1px 0 rgba(255, 255, 255, 0.6);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 35px rgba(15, 118, 110, 0.04), 0 4px 10px rgba(0, 0, 0, 0.02);
        }

        .stat-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-info h4 {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .stat-info p {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-primary);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .stat-icon.teal { background-color: rgba(15, 118, 110, 0.1); color: var(--accent-color); }
        .stat-icon.red { background-color: rgba(239, 68, 68, 0.08); color: var(--danger); }
        .stat-icon.blue { background-color: rgba(59, 130, 246, 0.08); color: var(--info); }
        .stat-icon.orange { background-color: rgba(245, 158, 11, 0.08); color: var(--warning); }

        /* Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 20px;
            border: 1px solid var(--card-border);
            margin-top: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.01);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            background-color: #ffffff;
        }

        .table th {
            background-color: #f8fafc;
            padding: 16px 24px;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-secondary);
            border-bottom: 1px solid var(--card-border);
        }

        .table td {
            padding: 16px 24px;
            border-bottom: 1px solid var(--card-border);
            font-size: 14px;
            color: var(--text-primary);
            transition: background-color 0.2s;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover td {
            background-color: #f8fafc;
        }

        .table th:not(:last-child),
        .table td:not(:last-child) {
            border-right: 1px solid var(--card-border);
        }

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success { background-color: rgba(16, 185, 129, 0.1); color: var(--success); }
        .badge-warning { background-color: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .badge-danger { background-color: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .badge-info { background-color: rgba(59, 130, 246, 0.1); color: var(--info); }

        /* Pill Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 9999px; /* Pill buttons */
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: all 0.25s ease;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
        }

        .btn:hover {
            transform: translateY(-1.5px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-color) 0%, #0891b2 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(15, 118, 110, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);
            box-shadow: 0 6px 16px rgba(15, 118, 110, 0.35);
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: var(--text-secondary);
            border: 1px solid var(--card-border);
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
            color: var(--text-primary);
        }

        .btn-danger {
            background-color: rgba(239, 68, 68, 0.08);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.1);
        }

        .btn-danger:hover {
            background-color: var(--danger);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        .btn-sm {
            padding: 6px 16px;
            font-size: 11.5px;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 12px 18px;
            border: 1px solid var(--card-border);
            background-color: #ffffff;
            border-radius: 14px;
            font-size: 13.5px;
            color: var(--text-primary);
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.15);
        }

        /* Alert block */
        .alert {
            padding: 16px 24px;
            border-radius: 18px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.01);
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.08);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.15);
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.08);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.15);
        }

        /* Custom Pagination */
        nav[role="navigation"] {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(10px);
            padding: 12px 20px;
            border-radius: 16px;
            border: 1px solid var(--card-border);
            margin-top: 16px;
        }

        nav[role="navigation"] svg {
            width: 16px;
            height: 16px;
            display: inline-block;
            vertical-align: middle;
        }

        nav[role="navigation"] > div:first-child {
            display: none !important;
        }

        nav[role="navigation"] > div:last-child {
            display: flex !important;
            width: 100%;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        nav[role="navigation"] .flex.justify-between.flex-1 {
            display: flex !important;
            align-items: center;
            gap: 8px;
        }

        nav[role="navigation"] a,
        nav[role="navigation"] span[aria-current="page"] > span,
        nav[role="navigation"] span[aria-disabled="true"] > span,
        nav[role="navigation"] div.flex > span {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            padding: 8px 14px !important;
            border-radius: 10px !important;
            border: 1px solid var(--card-border) !important;
            text-decoration: none !important;
            color: var(--text-primary) !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            background-color: #ffffff !important;
            transition: all 0.2s ease !important;
            margin: 0 2px !important;
        }

        nav[role="navigation"] a:hover {
            background-color: var(--accent-color) !important;
            color: #ffffff !important;
            border-color: var(--accent-color) !important;
            box-shadow: 0 4px 10px rgba(15, 118, 110, 0.2) !important;
        }

        nav[role="navigation"] span[aria-current="page"] > span {
            background-color: var(--accent-color) !important;
            color: #ffffff !important;
            border-color: var(--accent-color) !important;
            box-shadow: 0 4px 10px rgba(15, 118, 110, 0.2) !important;
        }

        nav[role="navigation"] span[aria-disabled="true"] > span {
            color: var(--text-secondary) !important;
            background-color: rgba(248, 250, 252, 0.4) !important;
            cursor: not-allowed !important;
            opacity: 0.6 !important;
        }

        /* Mobile Responsive for Wrapper & Sidebar Drawer */
        @media (max-width: 992px) {
            body {
                display: block !important;
                padding: 0 !important;
                height: auto !important;
                min-height: 100vh !important;
                width: 100% !important;
                max-width: 100vw !important;
                overflow-x: hidden !important;
            }
            .app-wrapper {
                display: block !important;
                border-radius: 0;
                margin: 0;
                min-height: 100vh;
                height: auto !important;
                width: 100% !important;
                max-width: 100vw !important;
                background: none !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
                border: none !important;
                box-shadow: none !important;
                overflow: visible !important;
            }
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                height: 100vh !important;
                width: 280px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1100;
                background-color: #ffffff !important;
                border-right: 1px solid rgba(0, 0, 0, 0.1);
            }
            .sidebar.active {
                transform: translateX(0);
                box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(4px);
                z-index: 1050;
            }
            .sidebar.active + .sidebar-overlay {
                display: block;
            }
            .main-container {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                overflow: visible !important;
            }
            .content-body {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 20px;
                height: auto !important;
                overflow: visible !important;
            }
            .menu-toggle {
                display: block !important;
            }
            .header-actions span {
                display: none !important; /* Hide date on tablet/mobile to prevent horizontal overflow */
            }
            /* Override any inline grid template columns to stack vertically on mobile */
            div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
                gap: 20px !important;
            }
            /* Prevent grid items from expanding beyond their boundaries (enables table scrollbar) */
            .stats-grid > *,
            div[style*="grid-template-columns"] > * {
                min-width: 0 !important;
                max-width: 100% !important;
            }
            .card {
                max-width: 100% !important;
            }
            .table-responsive {
                max-width: 100% !important;
                width: 100% !important;
                overflow-x: auto !important;
            }
            header {
                padding: 16px 20px !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }

        /* Extra mobile responsive for devices under 576px (Galaxy Z Fold, iPhone SE, etc.) */
        @media (max-width: 576px) {
            .card {
                padding: 16px !important;
                border-radius: 16px !important;
            }
            .content-body {
                padding: 12px !important;
            }
            .stats-grid {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
            }
            .header-title h2 {
                max-width: 140px !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                white-space: nowrap !important;
                font-size: 14px !important;
            }
            .alert {
                padding: 12px 16px !important;
                border-radius: 14px !important;
                font-size: 13px !important;
            }
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-primary);
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar Layout -->
        <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="ri-heart-pulse-fill"></i>
            <h1>HealthCare</h1>
        </div>
        
        <ul class="sidebar-menu">
            <li class="sidebar-menu-item {{ Request::is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}"><i class="ri-dashboard-fill"></i> Dashboard</a>
            </li>

            <!-- Admin Menus -->
            @if(auth()->user()->isAdmin())
                <li class="sidebar-menu-item {{ Request::is('admin/users*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}"><i class="ri-user-settings-fill"></i> Kelola User</a>
                </li>
                <li class="sidebar-menu-item {{ Request::is('admin/villages*') ? 'active' : '' }}">
                    <a href="{{ route('admin.villages.index') }}"><i class="ri-map-pin-range-fill"></i> Kelola Wilayah</a>
                </li>
                <li class="sidebar-menu-item {{ Request::is('admin/disease-types*') ? 'active' : '' }}">
                    <a href="{{ route('admin.disease-types.index') }}"><i class="ri-virus-line"></i> Kelola Penyakit</a>
                </li>
                <li class="sidebar-menu-item {{ Request::is('admin/vaccines*') ? 'active' : '' }}">
                    <a href="{{ route('admin.vaccines.index') }}"><i class="ri-heart-add-fill"></i> Kelola Vaksin</a>
                </li>
                <li class="sidebar-menu-item {{ Request::is('admin/restock-approvals*') ? 'active' : '' }}">
                    <a href="{{ route('admin.restock.index') }}"><i class="ri-survey-fill"></i> Approval Restock</a>
                </li>
                <li class="sidebar-menu-item {{ Request::is('admin/logs*') ? 'active' : '' }}">
                    <a href="{{ route('admin.logs') }}"><i class="ri-history-fill"></i> Log Aktivitas</a>
                </li>
            @endif

            <!-- Apoteker / Medicine Menus -->
            @if(auth()->user()->isApoteker() || auth()->user()->isAdmin())
                <li class="sidebar-menu-divider" style="padding: 12px 16px 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: rgba(255,255,255,0.2);">Apotek</li>
                <li class="sidebar-menu-item {{ Request::is('apotek/medicines*') ? 'active' : '' }}">
                    <a href="{{ route('apotek.medicines.index') }}"><i class="ri-capsule-fill"></i> Stok Obat</a>
                </li>
                                <li class="sidebar-menu-item {{ Request::is('apotek/transactions/create') ? 'active' : '' }}">
                    <a href="{{ route('apotek.transactions.create') }}"><i class="ri-add-circle-fill"></i> Input Transaksi</a>
                </li>
                <li class="sidebar-menu-item {{ Request::is('apotek/transactions') || (Request::is('apotek/transactions/*') && !Request::is('apotek/transactions/create')) ? 'active' : '' }}">
                    <a href="{{ route('apotek.transactions.index') }}"><i class="ri-history-fill"></i> Log Transaksi Obat</a>
                </li>
                <li class="sidebar-menu-item {{ Request::is('apotek/suppliers*') ? 'active' : '' }}">
                    <a href="{{ route('apotek.suppliers.index') }}"><i class="ri-building-2-fill"></i> Data Supplier</a>
                </li>
        </li>
                @if(auth()->user()->isApoteker())
                    <li class="sidebar-menu-item {{ Request::is('apotek/restock-requests*') ? 'active' : '' }}">
                        <a href="{{ route('apotek.restock-requests.index') }}"><i class="ri-file-list-3-fill"></i> Permintaan Restock</a>
                    </li>
                @endif
                <li class="sidebar-menu-item {{ Request::is('apotek/reports*') ? 'active' : '' }}">
                    <a href="{{ route('apotek.reports') }}"><i class="ri-file-chart-fill"></i> Laporan Stok</a>
                </li>
            @endif

            <!-- Petugas Medis / Disease Menus -->
            @if(auth()->user()->isPetugasMedis() || auth()->user()->isAdmin())
                <li class="sidebar-menu-divider" style="padding: 12px 16px 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: rgba(255,255,255,0.2);">Pelaporan Penyakit</li>
                <li class="sidebar-menu-item {{ Request::is('kesehatan/reports*') ? 'active' : '' }}">
                    <a href="{{ route('kesehatan.reports.index') }}"><i class="ri-virus-fill"></i> Kasus Penyakit</a>
                </li>
                @if(auth()->user()->isPetugasMedis())
                    <li class="sidebar-menu-item {{ Request::is('kesehatan/verification*') ? 'active' : '' }}">
                        <a href="{{ route('kesehatan.verification.index') }}"><i class="ri-checkbox-circle-fill"></i> Verifikasi Kasus</a>
                    </li>
                @endif
                <li class="sidebar-menu-item {{ Request::is('kesehatan/disease-map*') ? 'active' : '' }}">
                    <a href="{{ route('kesehatan.map') }}"><i class="ri-map-pin-2-fill"></i> Peta Sebaran</a>
                </li>
            @endif



            <!-- Imunisasi Menus -->
            @if(auth()->user()->isPetugasMedis() || auth()->user()->isAdmin())
                <li class="sidebar-menu-divider" style="padding: 12px 16px 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: rgba(255,255,255,0.2);">Imunisasi</li>
                <li class="sidebar-menu-item {{ Request::is('imunisasi/children*') ? 'active' : '' }}">
                    <a href="{{ route('imunisasi.children.index') }}"><i class="ri-parent-fill"></i> Data Anak</a>
                </li>
                <li class="sidebar-menu-item {{ Request::is('imunisasi/schedules*') ? 'active' : '' }}">
                    <a href="{{ route('imunisasi.schedules.index') }}"><i class="ri-calendar-event-fill"></i> Jadwal & Catat</a>
                </li>
                <li class="sidebar-menu-item {{ Request::is('imunisasi/reminders*') ? 'active' : '' }}">
                    <a href="{{ route('imunisasi.reminders.index') }}"><i class="ri-notification-3-fill"></i> Reminder Imunisasi</a>
                </li>
                <li class="sidebar-menu-item {{ Request::is('dokter/consultations*') ? 'active' : '' }}">
                    <a href="{{ route('dokter.consultations.index') }}"><i class="ri-heart-pulse-fill"></i> Keluhan KIPI (Vaksin)</a>
                </li>
            @endif

            <!-- Laporan Eksekutif Menus (Admin/Kepala) -->
            @if(auth()->user()->isAdmin())
                <li class="sidebar-menu-divider" style="padding: 12px 16px 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: rgba(255,255,255,0.2);">Laporan Eksekutif</li>
                <li class="sidebar-menu-item {{ Request::is('kepala/reports*') ? 'active' : '' }}">
                    <a href="{{ route('kepala.reports.index') }}"><i class="ri-printer-fill"></i> Cetak Laporan</a>
                </li>
            @endif

            <!-- Warga Menus -->
            @if(auth()->user()->isWarga())
                <li class="sidebar-menu-divider" style="padding: 12px 16px 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: rgba(255,255,255,0.2);">Warga</li>
                <li class="sidebar-menu-item {{ Request::is('warga/my-children*') ? 'active' : '' }}">
                    <a href="{{ route('warga.children.index') }}"><i class="ri-user-heart-fill"></i> Anak Saya</a>
                </li>
                <li class="sidebar-menu-item {{ Request::is('warga/medicines*') ? 'active' : '' }}">
                    <a href="{{ route('warga.medicines.index') }}"><i class="ri-capsule-fill"></i> Daftar Obat</a>
                </li>
                <li class="sidebar-menu-item {{ Request::is('warga/complaints*') ? 'active' : '' }}">
                    <a href="{{ route('warga.complaints.index') }}"><i class="ri-heart-pulse-fill"></i> Keluhan Imunisasi</a>
                </li>
            @endif

            <!-- Layanan AI Kesehatan (All Roles) -->
            <li class="sidebar-menu-divider" style="padding: 12px 16px 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: rgba(255,255,255,0.2);">Layanan AI & Informasi</li>
            <li class="sidebar-menu-item {{ Request::is('ai-chat*') ? 'active' : '' }}">
                <a href="{{ route('ai-chat.index') }}"><i class="ri-robot-fill"></i> Chat AI Kesehatan</a>
            </li>
            <li class="sidebar-menu-item {{ Request::is('statistik-bps*') ? 'active' : '' }}">
                <a href="{{ route('bps.index') }}"><i class="ri-database-2-fill"></i> Statistik BPS</a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="user-details">
                    <h3>{{ auth()->user()->name }}</h3>
                    <span>{{ auth()->user()->role }}</span>
                </div>
            </div>
        </div>
    </aside>
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Main Content Panel -->
    <div class="main-container">
        <header>
            <div class="header-title">
                <button class="menu-toggle" id="menu-toggle"><i class="ri-menu-2-line"></i></button>
                <i class="ri-shield-user-fill"></i>
                <h2>@yield('header-title', 'Dashboard')</h2>
            </div>
            
            <div class="header-actions">
                <span style="font-size: 14px; font-weight: 500; color: var(--text-secondary);">
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
                
                <!-- Logout Form -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout" title="Keluar"><i class="ri-logout-box-r-line"></i></button>
                </form>
            </div>
        </header>

        <main class="content-body">
            <!-- Alert Notifications -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="ri-checkbox-circle-fill" style="font-size: 20px;"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="ri-error-warning-fill" style="font-size: 20px;"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

    <!-- Responsive toggle script & Sidebar Scroll Persistence -->
    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
            });
        }

        // Keep sidebar scroll position across reloads
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarMenu = document.querySelector('.sidebar-menu');
            if (sidebarMenu) {
                const scrollPos = sessionStorage.getItem('sidebarScrollPosition');
                const activeItem = sidebarMenu.querySelector('.sidebar-menu-item.active');
                
                if (scrollPos) {
                    sidebarMenu.scrollTop = parseInt(scrollPos, 10);
                } else if (activeItem) {
                    activeItem.scrollIntoView({ block: 'nearest' });
                }

                sidebarMenu.addEventListener('scroll', function() {
                    sessionStorage.setItem('sidebarScrollPosition', sidebarMenu.scrollTop);
                });
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
