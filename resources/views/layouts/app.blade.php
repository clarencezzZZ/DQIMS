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
            /* Dark mode colors */
            --dark-bg: #121212;
            --dark-surface: #1e1e1e;
            --dark-on-surface: #e0e0e0;
            --dark-border: #444;
            --dark-card-bg: #1e1e1e;
            --dark-navbar-bg: linear-gradient(135deg, #1b5e20 0%, #0d3a12 100%);
            /* Enhanced dark mode colors */
            --dark-accent: #4caf50;
            --dark-card-elevation: #2a2a2a;
            --dark-surface-secondary: #252525;
            --dark-overlay: rgba(0, 0, 0, 0.4);
        }
        
        /* Dark mode theme */
        [data-theme="dark"] {
            --bg-body: var(--dark-bg);
            --bg-surface: var(--dark-surface);
            --color-text: var(--dark-on-surface);
            --color-border: var(--dark-border);
            --card-bg: var(--dark-card-elevation);
            --bs-body-color: #e9ecef;
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
    </style>
    @yield('styles')
</head>
<body>
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark">
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
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text text-muted">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth

    <div class="container-fluid">
        <div class="row">
            @auth
            <div class="col-md-2 d-none d-md-block sidebar p-0">
                <div class="d-flex flex-column">
                    @if(auth()->user()->isFrontDesk() || auth()->user()->isAdmin())
                    <a href="{{ route('front-desk.index') }}" class="nav-link {{ request()->routeIs('front-desk.index') ? 'active' : '' }}">
                        <i class="bi bi-reception-4"></i> Front Desk
                    </a>
                    <a href="{{ route('front-desk.create') }}" class="nav-link {{ request()->routeIs('front-desk.create') ? 'active' : '' }}">
                        <i class="bi bi-plus-circle"></i> New Inquiry
                    </a>
                    <a href="{{ route('front-desk.live-status') }}" class="nav-link {{ request()->routeIs('front-desk.live-status') ? 'active' : '' }}">
                        <i class="bi bi-eye"></i> Live Queue Status
                    </a>
                    @endif
                    
                    @if(auth()->user()->isSectionStaff() || auth()->user()->isAdmin())
                    <a href="{{ route('section.index') }}" class="nav-link {{ request()->routeIs('section.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Section Dashboard
                    </a>
                    @endif
                    
                    @if(auth()->user()->isAdmin())
                    <div class="sidebar-divider"></div>
                    <a href="{{ route('admin.index') }}" class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Admin Dashboard
                    </a>
                    <a href="{{ route('admin.inquiries') }}" class="nav-link {{ request()->routeIs('admin.inquiries') ? 'active' : '' }}">
                        <i class="bi bi-list-check"></i> All Inquiries
                    </a>
                    <a href="{{ route('admin.assessments') }}" class="nav-link {{ request()->routeIs('admin.assessments*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text"></i> Assessments
                    </a>
                    @if(auth()->user()->username === 'admin')
                    <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Users
                    </a>
                    @endif
                    <a href="{{ route('admin.categories') }}" class="nav-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Categories
                    </a>
                    @endif
                    
                    @if(auth()->user()->isSectionStaff() || auth()->user()->isAdmin())
                    <div class="sidebar-divider"></div>
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
        });
    </script>
    @yield('scripts')
</body>
</html>
