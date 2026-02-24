<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DENR Queueing & Inquiry Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --denr-green: #2e7d32;
            --denr-dark: #1b5e20;
            --denr-light: #4caf50;
        }
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background: linear-gradient(135deg, var(--denr-green) 0%, var(--denr-dark) 100%);
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
            border: none;
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
            background: #000;
            color: #fff;
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
            background-color: #fff;
            border-right: 1px solid #e9ecef;
            box-shadow: 1px 0 5px rgba(0,0,0,0.05);
        }
        .sidebar .nav-link {
            color: #495057;
            padding: 14px 24px;
            border-radius: 0;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.95rem;
            letter-spacing: 0.2px;
        }
        .sidebar .nav-link:hover {
            background-color: #f8f9fa;
            color: var(--denr-green);
            border-left-color: #adb5bd;
        }
        .sidebar .nav-link.active {
            background-color: #e8f5e8;
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
            background-color: #e9ecef;
            margin: 8px 24px;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
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
                    <a href="{{ route('front-desk.index') }}" class="nav-link {{ request()->routeIs('front-desk.*') ? 'active' : '' }}">
                        <i class="bi bi-reception-4"></i> Front Desk
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
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i> Reports
                    </a>
                    <div class="sidebar-divider"></div>
                    <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Users
                    </a>
                    @endif
                    <a href="{{ route('admin.categories') }}" class="nav-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i> Categories
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Set up CSRF token for AJAX requests
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    @yield('scripts')
</body>
</html>
