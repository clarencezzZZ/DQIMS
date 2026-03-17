<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DENR Queueing & Inquiry Management System')</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <style>
        :root {
            --denr-green: #2e7d32;
            --denr-dark: #1b5e20;
            --denr-light: #4caf50;
            /* Dark mode colors - Professional Palette */
            --dark-bg: #0f1114;
            --dark-surface: #1a1d21;
            --dark-surface-secondary: #22262b;
            --dark-on-surface: #f1f3f5;
            --dark-border: #2d3238;
            --dark-card-bg: #1a1d21;
            --dark-navbar-bg: linear-gradient(135deg, #1b5e20 0%, #0d3a12 100%);
            /* Enhanced dark mode accent colors */
            --dark-accent: #4caf50;
            --dark-card-elevation: #1e2227;
            --dark-overlay: rgba(0, 0, 0, 0.6);
        }
        
        /* Dark mode theme */
        [data-theme="dark"] {
            --bg-body: var(--dark-bg);
            --bg-surface: var(--dark-surface);
            --color-text: var(--dark-on-surface);
            --color-border: var(--dark-border);
            --card-bg: var(--dark-card-elevation);
            --bs-body-color: #f1f3f5;
            --bs-body-bg: var(--dark-bg);
        }
        body {
            background-color: var(--bg-body, #f5f5f5);
            color: var(--color-text, #212529);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* Text color adjustments for dark mode */
        .text-muted {
            color: var(--color-text, #6c757d) !important;
        }
        
        .text-dark {
            color: var(--color-text, #212529) !important;
        }
        
        .text-light {
            color: var(--color-text, #f8f9fa) !important;
        }
        
        [data-theme="dark"] .text-muted {
            color: #adb5bd !important;
        }
        
        [data-theme="dark"] .text-dark {
            color: #e9ecef !important;
        }
        
        [data-theme="dark"] .text-light {
            color: #f8f9fa !important;
        }
        
        [data-theme="dark"] .text-white {
            color: #e9ecef !important;
        }
        
        /* Table text colors for dark mode */
        [data-theme="dark"] table {
            color: var(--color-text, #e9ecef) !important;
        }
        
        [data-theme="dark"] .table {
            --bs-table-color: var(--color-text, #e9ecef);
            --bs-table-bg: var(--bg-surface, #212529);
            --bs-table-border-color: var(--color-border, #444);
        }
        
        /* Badge text colors for dark mode */
        [data-theme="dark"] .badge {
            color: #fff;
        }
        
        /* General text adjustments for dark mode */
        [data-theme="dark"] {
            --bs-body-color: #e9ecef;
        }
        
        [data-theme="dark"] h1,
        [data-theme="dark"] h2,
        [data-theme="dark"] h3,
        [data-theme="dark"] h4,
        [data-theme="dark"] h5,
        [data-theme="dark"] h6 {
            color: #f8f9fa;
        }
        
        [data-theme="dark"] p {
            color: #e9ecef;
        }
        
        [data-theme="dark"] small {
            color: #adb5bd;
        }
        
        [data-theme="dark"] label {
            color: #dee2e6;
        }
        
        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select {
            background-color: var(--bg-surface, #2d2d2d);
            border-color: var(--color-border, #444);
            color: var(--color-text, #e9ecef);
        }
        
        [data-theme="dark"] .form-control:focus,
        [data-theme="dark"] .form-select:focus {
            background-color: var(--bg-surface, #2d2d2d);
            border-color: var(--denr-green);
            color: var(--color-text, #e9ecef);
        }
        
        /* Card styling for dark mode */
        [data-theme="dark"] .card {
            background-color: var(--card-bg, #2d2d2d);
            border: 1px solid var(--color-border, #444);
            box-shadow: 0 4px 12px var(--dark-overlay);
        }
        
        [data-theme="dark"] .card-header {
            background: linear-gradient(135deg, var(--denr-green) 0%, var(--denr-dark) 100%) !important;
            color: white;
            border-bottom: 1px solid var(--color-border, #444);
        }
        
        [data-theme="dark"] .card-body {
            background-color: var(--dark-surface-secondary);
            color: var(--color-text, #e9ecef);
        }
        
        [data-theme="dark"] .card-footer {
            background-color: var(--dark-surface-secondary);
            border-top: 1px solid var(--color-border, #444);
            color: var(--color-text, #e9ecef);
        }
        
        [data-theme="dark"] .card-header {
            background-color: var(--denr-dark) !important;
            color: white;
        }
        
        [data-theme="dark"] .card-body {
            color: var(--color-text, #e9ecef);
        }
        
        /* Alert styling for dark mode */
        [data-theme="dark"] .alert {
            color: #e9ecef;
            border: 1px solid var(--color-border, #444);
        }
        
        [data-theme="dark"] .alert-success {
            background-color: #1d3a1f;
            border-color: #28a745;
        }
        
        [data-theme="dark"] .alert-danger {
            background-color: #3a1d1d;
            border-color: #dc3545;
        }
        
        /* Button styling for dark mode */
        [data-theme="dark"] .btn {
            color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        [data-theme="dark"] .btn:not(.btn-primary):not(.btn-outline-secondary) {
            background-color: #343a40;
            border-color: #444;
        }
        
        [data-theme="dark"] .btn:not(.btn-primary):not(.btn-outline-secondary):hover {
            background-color: #495057;
            border-color: #555;
        }
        
        [data-theme="dark"] .btn-outline-secondary {
            color: #dee2e6;
            border-color: #444;
            background-color: transparent;
        }
        
        [data-theme="dark"] .btn-outline-secondary:hover {
            background-color: #495057;
            border-color: #555;
            color: #fff;
        }
        
        [data-theme="dark"] .btn-primary {
            background-color: var(--denr-green);
            border-color: var(--denr-green);
        }
        
        [data-theme="dark"] .btn-primary:hover {
            background-color: var(--denr-dark);
            border-color: var(--denr-dark);
        }
        
        /* Table styling for dark mode */
        [data-theme="dark"] .table {
            --bs-table-color: var(--color-text, #e9ecef);
            --bs-table-bg: var(--card-bg, #2d2d2d);
            --bs-table-border-color: var(--color-border, #444);
            --bs-table-striped-bg: rgba(255, 255, 255, 0.05);
            --bs-table-hover-bg: rgba(255, 255, 255, 0.1);
        }
        
        [data-theme="dark"] .table > :not(caption) > * > * {
            border-bottom-color: var(--color-border, #444);
        }
        
        [data-theme="dark"] .table-bordered > :not(caption) > * {
            border-color: var(--color-border, #444);
        }
        
        [data-theme="dark"] .table-responsive {
            border-color: var(--color-border, #444);
        }
        
        [data-theme="dark"] .table-light {
            background-color: #343a40 !important;
            color: #e9ecef;
        }
        
        [data-theme="dark"] .table-dark {
            background-color: #212529;
            color: #f8f9fa;
        }
        
        /* Sidebar styling for dark mode */
        [data-theme="dark"] .sidebar {
            background: linear-gradient(to bottom, var(--bg-surface, #1e1e1e) 0%, var(--dark-surface-secondary, #252525) 100%);
            border-right: 1px solid var(--color-border, #444);
            box-shadow: 3px 0 10px var(--dark-overlay);
        }
        
        /* Pagination SVG fix */
        nav svg {
            max-height: 1.25rem;
            display: inline;
        }
        
        .pagination {
            margin-bottom: 0;
        }

        [data-theme="dark"] .sidebar .nav-link {
            color: var(--color-text, #adb5bd);
            border-left: 3px solid transparent;
        }
        
        [data-theme="dark"] .sidebar .nav-link:hover {
            background-color: rgba(76, 175, 80, 0.15);
            color: var(--denr-light);
            border-left-color: var(--denr-light);
        }
        
        [data-theme="dark"] .sidebar .nav-link.active {
            background-color: rgba(76, 175, 80, 0.2);
            color: var(--denr-light);
            border-left: 3px solid var(--denr-light);
        }
        
        [data-theme="dark"] .sidebar-divider {
            background-color: var(--color-border, #444);
        }
        
        /* Badge styling for dark mode */
        [data-theme="dark"] .badge.bg-warning {
            color: #212529;
        }
        
        [data-theme="dark"] .badge.bg-light {
            color: #212529;
            background-color: #495057 !important;
        }
        
        /* Status badges for dark mode */
        [data-theme="dark"] .status-waiting { background-color: #e0a800 !important; color: #212529 !important; }
        [data-theme="dark"] .status-serving { background-color: #17a2b8 !important; color: #fff !important; }
        [data-theme="dark"] .status-completed { background-color: #28a745 !important; color: #fff !important; }
        [data-theme="dark"] .status-skipped { background-color: #dc3545 !important; color: #fff !important; }
        .navbar {
            background: linear-gradient(135deg, var(--denr-green) 0%, var(--denr-dark) 100%);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        [data-theme="dark"] .navbar {
            background: var(--dark-navbar-bg, linear-gradient(135deg, var(--denr-green) 0%, var(--denr-dark) 100%));
        }
        .navbar-brand {
            font-weight: 600;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-logo {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }
        .card {
            background-color: var(--card-bg, #ffffff);
            border: 1px solid var(--color-border, rgba(0,0,0,0.1));
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: var(--denr-green);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        .btn-primary {
            background-color: var(--denr-green);
            border-color: var(--denr-green);
        }
        .btn-primary:hover {
            background-color: var(--denr-dark);
            border-color: var(--denr-dark);
        }
        .queue-number {
            font-size: 3rem;
            font-weight: bold;
            color: var(--denr-green);
        }
        .category-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        .status-waiting { background-color: #ffc107; color: #000; }
        .status-serving { background-color: #17a2b8; color: #fff; }
        .status-completed { background-color: #28a745; color: #fff; }
        .status-skipped { background-color: #dc3545; color: #fff; }
        
        .monitor-display {
            background: var(--dark-bg, #000);
            color: var(--dark-on-surface, #fff);
            min-height: 100vh;
        }
        .now-serving {
            font-size: 8rem;
            font-weight: bold;
            text-align: center;
            padding: 20px;
        }
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: var(--bg-surface, #fff);
            border-right: 1px solid var(--color-border, #e9ecef);
            box-shadow: 1px 0 5px rgba(0,0,0,0.05);
        }
        .sidebar .nav-link {
            color: var(--color-text, #495057);
            padding: 14px 24px;
            border-radius: 0;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.95rem;
            letter-spacing: 0.2px;
        }
        .sidebar .nav-link:hover {
            background-color: var(--bg-surface, #f8f9fa);
            color: var(--denr-green);
            border-left-color: var(--color-border, #adb5bd);
        }
        .sidebar .nav-link.active {
            background-color: var(--bg-surface, #e8f5e8);
            color: var(--denr-green);
            border-left: 3px solid var(--denr-green);
            font-weight: 600;
        }
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 12px;
            font-size: 1.1em;
        }
        .sidebar-divider {
            height: 1px;
            background-color: var(--color-border, #e9ecef);
            margin: 8px 24px;
        }
        
        /* Dark mode toggle button */
        #darkModeToggle {
            transition: all 0.3s ease;
        }
        
        #darkModeToggle:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            transform: scale(1.05);
        }
        
        [data-theme="dark"] #darkModeToggle:hover {
            background-color: rgba(255, 255, 255, 0.2) !important;
        }
        /* Logout Overlay Animation */
        #logoutOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            color: white;
            backdrop-filter: blur(8px);
            transition: all 0.5s ease;
        }
        
        .logout-spinner {
            width: 80px;
            height: 80px;
            border: 6px solid rgba(255, 255, 255, 0.1);
            border-top: 6px solid var(--denr-light);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        
        .logout-text {
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 1px;
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.7; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.05); }
        }

        /* Welcome Animation Overlay */
        #welcomeOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(46, 125, 50, 1) 0%, rgba(10, 47, 18, 1) 100%);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            color: white;
            backdrop-filter: blur(15px);
            opacity: 0;
            transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-app-content {
            transition: opacity 0.8s ease;
        }

        .welcome-loading .main-app-content {
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }

        .welcome-content {
            text-align: center;
            transform: translateY(30px);
            transition: transform 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .welcome-icon {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            animation: bounceIn 1s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .welcome-icon i {
            font-size: 4rem;
            color: var(--denr-green);
        }

        .welcome-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            text-shadow: 0 4px 10px rgba(0,0,0,0.2);
            letter-spacing: -1px;
        }

        .welcome-name {
            font-size: 2rem;
            font-weight: 400;
            opacity: 0.9;
            margin-bottom: 40px;
        }

        .welcome-loader {
            width: 200px;
            height: 4px;
            background: rgba(255,255,255,0.1);
            border-radius: 2px;
            margin: 0 auto;
            overflow: hidden;
            position: relative;
        }

        .welcome-progress {
            width: 0%;
            height: 100%;
            background: white;
            position: absolute;
            left: 0;
            top: 0;
            box-shadow: 0 0 15px white;
            transition: width 2.5s cubic-bezier(0.1, 0.5, 0.5, 1);
        }

        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); opacity: 1; }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); }
        }

        /* Restricted Module Styling */
        .nav-link.disabled-link {
            color: #adb5bd !important;
            opacity: 0.6;
            cursor: not-allowed !important;
            pointer-events: none !important;
            background-color: transparent !important;
            border-left-color: transparent !important;
        }
        
        .nav-link.disabled-link i {
            color: #adb5bd !important;
        }

        [data-theme="dark"] .nav-link.disabled-link {
            color: #495057 !important;
            opacity: 0.4;
        }

        /* Modal styling for dark mode */
        [data-theme="dark"] .modal-content {
            background-color: var(--dark-card-elevation);
            color: var(--color-text);
            border: 1px solid var(--color-border);
        }
        [data-theme="dark"] .modal-header {
            border-bottom: 1px solid var(--color-border);
        }
        [data-theme="dark"] .modal-footer {
            border-top: 1px solid var(--color-border);
        }
    </style>
    @yield('styles')
</head>
<body class="{{ session('login_welcome') ? 'welcome-loading' : '' }}">
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark main-app-content">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('images/denrlogo.webp') }}" alt="DENR Logo" class="navbar-logo"> DENR DQIMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Dark Mode Toggle -->
                    <li class="nav-item me-2">
                        <button class="btn btn-outline-light rounded-circle p-1" id="darkModeToggle" aria-label="Toggle dark mode" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-flower1"></i>
                        </button>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            @if(auth()->user()->profile_picture && file_exists(public_path('uploads/profiles/' . auth()->user()->profile_picture)))
                                <img src="{{ asset('uploads/profiles/' . auth()->user()->profile_picture) }}" 
                                     alt="{{ auth()->user()->name }}" 
                                     class="rounded-circle" 
                                     style="width: 32px; height: 32px; object-fit: cover; margin-right: 8px;">
                            @else
                                <i class="bi bi-person-circle" style="margin-right: 4px;"></i>
                            @endif
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="min-width: 200px;">
                            <li>
                                <div class="dropdown-item d-flex align-items-center">
                                    @if(auth()->user()->profile_picture && file_exists(public_path('uploads/profiles/' . auth()->user()->profile_picture)))
                                        <img src="{{ asset('uploads/profiles/' . auth()->user()->profile_picture) }}" 
                                             alt="{{ auth()->user()->name }}" 
                                             class="rounded-circle me-2" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2" 
                                             style="width: 40px; height: 40px; font-size: 1.2rem;">
                                            <i class="bi bi-person-circle"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <span class="fw-bold d-block">{{ auth()->user()->name }}</span>
                                        <small class="text-muted">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</small>
                                    </div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                    <i class="bi bi-person-gear"></i> Edit Profile
                                </a>
                            </li>
                            <li>
                                <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                                    @csrf
                                </form>
                                <a href="javascript:void(0)" class="dropdown-item text-danger" id="logout-btn">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth

    <div class="container-fluid main-app-content">
        <div class="row">
            @auth
            <div class="col-md-2 d-none d-md-block sidebar p-0 main-app-content">
                <div class="d-flex flex-column">
                    @php 
                        $restrictedEnabled = \App\Models\ModuleSetting::where('module_key', 'restricted_access')->value('is_enabled') ?? false;
                    @endphp

                    @if(auth()->user()->isFrontDesk() && auth()->user()->username !== 'admin')
                    <a href="{{ $restrictedEnabled ? route('front-desk.index') : 'javascript:void(0)' }}" id="sidebar-fd-home" class="nav-link {{ request()->routeIs('front-desk.index') ? 'active' : '' }} {{ !$restrictedEnabled ? 'disabled-link' : '' }}">
                        <i class="bi bi-reception-4"></i> Front Desk
                    </a>
                    <a href="{{ $restrictedEnabled ? route('front-desk.create') : 'javascript:void(0)' }}" id="sidebar-fd-create" class="nav-link {{ request()->routeIs('front-desk.create') ? 'active' : '' }} {{ !$restrictedEnabled ? 'disabled-link' : '' }}">
                        <i class="bi bi-plus-circle"></i> New Inquiry
                    </a>
                    <a href="{{ $restrictedEnabled ? route('front-desk.live-status') : 'javascript:void(0)' }}" id="sidebar-fd-live" class="nav-link {{ request()->routeIs('front-desk.live-status') ? 'active' : '' }} {{ !$restrictedEnabled ? 'disabled-link' : '' }}">
                        <i class="bi bi-eye"></i> Live Queue Status
                    </a>
                    @endif
                    
                    @if(auth()->user()->isSectionOfficer() && auth()->user()->username !== 'admin')
                    <a href="{{ route('section.index') }}" class="nav-link {{ request()->routeIs('section.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Section Dashboard
                    </a>
                    @endif
                    
                    @if(auth()->user()->isAdmin())
                    <div class="sidebar-divider"></div>
                    @if(auth()->user()->username === 'admin')
                    <a href="{{ route('admin.index') }}" class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Admin Dashboard
                    </a>
                    
                    <a href="{{ route('admin.inquiries') }}" id="sidebar-inquiries" class="nav-link {{ request()->routeIs('admin.inquiries') ? 'active' : '' }} {{ !$restrictedEnabled ? 'disabled-link' : '' }}">
                        <i class="bi bi-list-check"></i> All Inquiries
                    </a>
                    <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Users
                    </a>
                    <a href="{{ route('admin.categories') }}" id="sidebar-categories" class="nav-link {{ request()->routeIs('admin.categories') ? 'active' : '' }} {{ !$restrictedEnabled ? 'disabled-link' : '' }}">
                        <i class="bi bi-tags"></i> Categories
                    </a>
                    @else
                    <!-- Admin Front Desk Role -->
                    <a href="{{ route('admin.index') }}" class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Admin Dashboard
                    </a>
                    @endif
                    <a href="{{ route('admin.assessments') }}" class="nav-link {{ request()->routeIs('admin.assessments*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text"></i> Assessments
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Reports
                    </a>
                    @endif
                </div>
            </div>
            <div class="col-md-10 ms-sm-auto px-md-4 py-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </div>
            @else
            <div class="col-12">
                @yield('content')
            </div>
            @endauth
        </div>
    </div>

    @auth
    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="logoutConfirmModalLabel">
                        <i class="bi bi-box-arrow-right me-2"></i> Confirm Logout
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-circle text-danger" style="font-size: 3.5rem;"></i>
                    </div>
                    <h5 class="fw-bold">Are you sure you want to logout?</h5>
                    <p class="text-muted mb-0">You will be redirected to the login page.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4" id="confirmLogoutBtn">
                        <i class="bi bi-box-arrow-right me-1"></i> Yes, Logout
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Loading Overlay -->
    <div id="logoutOverlay">
        <div class="logout-spinner"></div>
        <div class="logout-text">Logging out...</div>
        <div class="mt-3 opacity-50 small">Closing secure session</div>
    </div>

    <!-- Welcome Login Overlay -->
    @if(session('login_welcome'))
    <div id="welcomeOverlay" style="display: flex; opacity: 1;">
        <div class="welcome-content">
            <div class="welcome-icon">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <h1 class="welcome-title">WELCOME BACK</h1>
            <p class="welcome-name">{{ auth()->user()->name ?? auth()->user()->username }}</p>
            <div class="welcome-loader">
                <div class="welcome-progress" id="welcomeProgress"></div>
            </div>
            <p class="mt-3 small opacity-50 text-uppercase tracking-wider">Preparing your dashboard</p>
        </div>
    </div>
    @endif
    @endauth

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script>
        // Set up CSRF token for AJAX requests
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Dark mode toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const sunflowerIcon = darkModeToggle.querySelector('i');
            const body = document.body;
            
            // Check for saved theme preference
            const savedTheme = localStorage.getItem('theme');
            
            // Initialize theme based on saved preference or system preference
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                enableDarkMode();
            } else {
                enableLightMode();
            }
            
            // Toggle button click handler
            darkModeToggle.addEventListener('click', function() {
                if (document.documentElement.getAttribute('data-theme') === 'dark') {
                    enableLightMode();
                } else {
                    enableDarkMode();
                }
            });
            
            function enableDarkMode() {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                // Change icon to flower without petals (dark mode active)
                sunflowerIcon.classList.remove('bi-flower1');
                sunflowerIcon.classList.add('bi-flower2');
                
                // Update button accessibility
                darkModeToggle.setAttribute('aria-label', 'Switch to light mode');
            }
            
            function enableLightMode() {
                document.documentElement.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
                // Change icon to flower with petals (light mode active)
                sunflowerIcon.classList.remove('bi-flower2');
                sunflowerIcon.classList.add('bi-flower1');
                
                // Update button accessibility
                darkModeToggle.setAttribute('aria-label', 'Switch to dark mode');
            }
            
            // Logout logic
            const logoutBtn = document.getElementById('logout-btn');
            const logoutConfirmModal = new bootstrap.Modal(document.getElementById('logoutConfirmModal'));
            const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');
            const logoutOverlay = document.getElementById('logoutOverlay');
            const logoutForm = document.getElementById('logout-form');
            
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    logoutConfirmModal.show();
                });
            }
            
            if (confirmLogoutBtn) {
                confirmLogoutBtn.addEventListener('click', function() {
                    // Hide the confirmation modal
                    logoutConfirmModal.hide();
                    
                    // Show the logout overlay with animation
                    logoutOverlay.style.display = 'flex';
                    logoutOverlay.style.opacity = '0';
                    setTimeout(() => {
                        logoutOverlay.style.opacity = '1';
                    }, 10);
                    
                    // Delay submission slightly to allow user to see the animation
                    setTimeout(() => {
                        logoutForm.submit();
                    }, 200);
                });
            }

            // Welcome Animation Logic
            const welcomeOverlay = document.getElementById('welcomeOverlay');
            if (welcomeOverlay) {
                const progress = document.getElementById('welcomeProgress');
                const content = welcomeOverlay.querySelector('.welcome-content');
                
                // Content should already be display: flex and opacity: 1 from inline style
                setTimeout(() => {
                    if (content) content.style.transform = 'translateY(0)';
                    if (progress) progress.style.width = '100%';
                }, 100);

                // Hide after 2.2 seconds (reduced slightly for snappier feel)
                setTimeout(() => {
                    welcomeOverlay.style.opacity = '0';
                    
                    // Reveal the dashboard slightly before the overlay is fully gone
                    setTimeout(() => {
                        document.body.classList.remove('welcome-loading');
                    }, 200);

                    setTimeout(() => {
                        welcomeOverlay.style.display = 'none';
                    }, 600);
                }, 2200);
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
