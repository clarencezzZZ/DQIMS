@extends('layouts.app')

@section('title', 'Admin Dashboard - 3rd Floor')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-speedometer2" style="color: var(--denr-green);"></i> Admin Dashboard (3rd Floor)</h2>
                    @if(auth()->user()->username === 'admin')
                    <p class="text-muted mb-0">Manage inquiries, assessments, reports, and categories - User management for main admin only</p>
                    @else
                    <p class="text-muted mb-0">Access assessments and reports</p>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    @if(auth()->user()->username === 'admin')
                    <a href="{{ route('admin.inquiries') }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-plus"></i> Create Assessment
                    </a>
                    @endif
                    <a href="{{ route('reports.index') }}" class="btn" style="background-color: var(--denr-green); color: white;">
                        <i class="bi bi-graph-up"></i> Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Statistics - Enhanced Cards -->
    <div class="row mb-4">
        <div class="col-md">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, var(--denr-green) 0%, var(--denr-dark) 100%); color: white; min-height: 94.98px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0 opacity-75">Total Inquiries</h6>
                            <h3 class="mb-0 mt-2 display-6">{{ $todayStats['total_inquiries'] }}</h3>
                        </div>
                        <div class="align-self-center opacity-50">
                            <i class="bi bi-people" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: #000; min-height: 94.98px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0 opacity-75">Waiting</h6>
                            <h3 class="mb-0 mt-2 display-6">{{ $todayStats['waiting'] }}</h3>
                        </div>
                        <div class="align-self-center opacity-50">
                            <i class="bi bi-hourglass-split" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #17a2b8 0%, #0dcaf0 100%); color: white; min-height: 94.98px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0 opacity-75">Serving</h6>
                            <h3 class="mb-0 mt-2 display-6">{{ $todayStats['serving'] }}</h3>
                        </div>
                        <div class="align-self-center opacity-50">
                            <i class="bi bi-person-check" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; min-height: 94.98px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0 opacity-75">Completed</h6>
                            <h3 class="mb-0 mt-2 display-6">{{ $todayStats['completed'] }}</h3>
                        </div>
                        <div class="align-self-center opacity-50">
                            <i class="bi bi-check-circle" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; min-height: 94.98px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0 opacity-75">Skipped</h6>
                            <h3 class="mb-0 mt-2 display-6">{{ $todayStats['skipped'] }}</h3>
                        </div>
                        <div class="align-self-center opacity-50">
                            <i class="bi bi-x-circle" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(auth()->user()->username === 'admin')
        <div class="col-md">
            <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #343a40 0%, #212529 100%); color: white; min-height: 94.98px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0 opacity-75">Categories</h6>
                            <h3 class="mb-0 mt-2 display-6">{{ $categories->count() }}</h3>
                        </div>
                        <div class="align-self-center opacity-50">
                            <i class="bi bi-tags" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Module Access Management -->
    @if(auth()->user()->username === 'admin')
    <div class="row mb-4">
        <div class="col-12">
            <div class="toggle-wrapper shadow-sm">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle p-2 shadow-sm me-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-shield-lock text-primary" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Module Access Control</h5>
                            <p class="text-muted mb-0 small">Enable or disable restricted system modules</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3 flex-wrap">
                        @php $isEnabled = $moduleSettings['restricted_access']->is_enabled ?? false; @endphp
                        <div class="module-toggle-btn {{ $isEnabled ? 'enabled' : 'disabled' }}" id="restrictedToggleBtn">
                            <div class="form-check form-switch p-0 m-0 d-flex align-items-center gap-2">
                                <label class="form-check-label order-1 fw-bold" for="toggle_restricted" id="toggleLabel">
                                    <i class="bi {{ $isEnabled ? 'bi-unlock-fill text-success' : 'bi-lock-fill text-danger' }} me-1"></i>
                                    {{ $isEnabled ? 'Access Enabled' : 'Access Restricted' }}
                                </label>
                                <input class="form-check-input order-2 ms-0 module-toggle" type="checkbox" role="switch" 
                                       id="toggle_restricted" data-module="restricted_access" {{ $isEnabled ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Modules Section - Enhanced with Data Cards -->
    <div class="row mb-4">
        <!-- Assessments Card -->
        <div class="col-lg-6 col-md-6 mb-3">
            @php $isAssessmentsEnabled = $moduleSettings['assessments']->is_enabled ?? true; @endphp
            <a href="{{ $isAssessmentsEnabled ? route('admin.assessments') : 'javascript:void(0)' }}" class="text-decoration-none">
                <div class="card module-card hover-card h-100 border-0 shadow-sm {{ !$isAssessmentsEnabled ? 'disabled-module' : '' }}">
                    @if(!$isAssessmentsEnabled)
                    <div class="module-lock-badge"><i class="bi bi-lock-fill"></i> Restricted</div>
                    @endif
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="module-icon-wrapper flex-shrink-0">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="card-title mb-0" style="color: var(--denr-dark); font-weight: 600;">Assessments</h5>
                                    <p class="text-muted mb-0 small">Manage assessment forms</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Assessment Statistics Blocks -->
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="stat-block">
                                    <h6 class="stat-value text-success">{{ \App\Models\Assessment::whereDate('created_at', today())->count() }}</h6>
                                    <span class="stat-label">Today</span>
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-block">
                                    <h6 class="stat-value text-primary">{{ \App\Models\Assessment::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}</h6>
                                    <span class="stat-label">This Week</span>
                                    <i class="bi bi-calendar-range"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-block">
                                    <h6 class="stat-value text-info">{{ \App\Models\Assessment::whereMonth('created_at', now()->month)->count() }}</h6>
                                    <span class="stat-label">This Month</span>
                                    <i class="bi bi-calendar3"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-block">
                                    <h6 class="stat-value text-warning">₱{{ number_format(\App\Models\Assessment::whereDate('created_at', today())->sum('fees'), 2) }}</h6>
                                    <span class="stat-label">Today's Fees</span>
                                    <i class="bi bi-cash-coin"></i>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="stat-block" style="background: linear-gradient(to right, #fff5f5, #f8f9fa);">
                                    <h6 class="stat-value text-danger" style="font-size: 1.5rem;">₱{{ number_format(\App\Models\Assessment::sum('fees'), 2) }}</h6>
                                    <span class="stat-label">Total Revenue</span>
                                    <i class="bi bi-bank"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ $isAssessmentsEnabled ? route('admin.assessments.create-direct') : 'javascript:void(0)' }}" class="action-badge bg-success text-white" onclick="event.stopPropagation();">
                                    <i class="bi bi-plus-lg"></i> Quick Create
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ $isAssessmentsEnabled ? route('admin.assessments') : 'javascript:void(0)' }}" class="action-badge bg-info text-white" onclick="event.stopPropagation();">
                                    <i class="bi bi-list-ul"></i> View All
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Reports Card -->
        <div class="col-lg-6 col-md-6 mb-3">
            @php $isReportsEnabled = $moduleSettings['reports']->is_enabled ?? true; @endphp
            <a href="{{ $isReportsEnabled ? route('reports.index') : 'javascript:void(0)' }}" class="text-decoration-none">
                <div class="card module-card hover-card h-100 border-0 shadow-sm {{ !$isReportsEnabled ? 'disabled-module' : '' }}">
                    @if(!$isReportsEnabled)
                    <div class="module-lock-badge"><i class="bi bi-lock-fill"></i> Restricted</div>
                    @endif
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="module-icon-wrapper flex-shrink-0" style="background: linear-gradient(135deg, #17a2b8, #0dcaf0);">
                                    <i class="bi bi-graph-up"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="card-title mb-0" style="color: var(--denr-dark); font-weight: 600;">Reports & Analytics</h5>
                                    <p class="text-muted mb-0 small">Charts & graphs</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Report Statistics Blocks -->
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="stat-block">
                                    <h6 class="stat-value text-success">{{ \App\Models\Inquiry::today()->completed()->count() }}</h6>
                                    <span class="stat-label">Completed Today</span>
                                    <i class="bi bi-check2-all"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-block">
                                    <h6 class="stat-value text-primary">₱{{ number_format(\App\Models\Assessment::sum('fees'), 2) }}</h6>
                                    <span class="stat-label">Total Fees</span>
                                    <i class="bi bi-wallet2"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-block">
                                    <h6 class="stat-value text-info">{{ \App\Models\Category::active()->count() }}</h6>
                                    <span class="stat-label">Active Categories</span>
                                    <i class="bi bi-grid-3x3-gap"></i>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-block">
                                    <h6 class="stat-value text-warning">{{ \App\Models\User::where('is_active', true)->count() }}</h6>
                                    <span class="stat-label">Active Users</span>
                                    <i class="bi bi-people"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- This Month Bar Chart - Enhanced Container -->
                        <div class="mb-4">
                            <div class="chart-container-wrapper">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-activity text-info me-2"></i>
                                    <span class="small fw-bold text-muted text-uppercase">Weekly Activity Trend</span>
                                </div>
                                <div style="height: 120px;">
                                    <canvas id="monthlyChart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Graph Access -->
                        <div class="row g-2">
                            <div class="col-4">
                                <a href="{{ $isReportsEnabled ? route('reports.index', ['report_type' => 'daily']) : 'javascript:void(0)' }}" class="action-badge bg-info text-white" onclick="event.stopPropagation();">
                                    <i class="bi bi-calendar-event"></i> Daily
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ $isReportsEnabled ? route('reports.index', ['report_type' => 'monthly']) : 'javascript:void(0)' }}" class="action-badge bg-primary text-white" onclick="event.stopPropagation();">
                                    <i class="bi bi-calendar-month"></i> Monthly
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ $isReportsEnabled ? route('reports.index', ['report_type' => 'yearly']) : 'javascript:void(0)' }}" class="action-badge bg-success text-white" onclick="event.stopPropagation();">
                                    <i class="bi bi-calendar-check"></i> Yearly
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    @if(auth()->user()->username === 'admin')
    <!-- Admin Only Modules -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3 position-relative">
            <div id="badge-inquiries-anim" class="status-badge-animated text-success">
                <i class="bi bi-unlock-fill me-1"></i> Access Restored
            </div>
            <div id="badge-inquiries-lock-anim" class="status-badge-animated text-danger">
                <i class="bi bi-lock-fill me-1"></i> Module Locked
            </div>
            @php $isRestrictedEnabled = $moduleSettings['restricted_access']->is_enabled ?? false; @endphp
            <a href="{{ $isRestrictedEnabled ? route('admin.inquiries') : 'javascript:void(0)' }}" class="text-decoration-none module-card-link" id="card-link-inquiries">
                <div class="card module-card hover-card h-100 border-0 shadow-sm {{ !$isRestrictedEnabled ? 'disabled-module' : '' }}" id="module-card-inquiries">
                    <div class="module-lock-badge {{ $isRestrictedEnabled ? 'd-none' : '' }}" id="lock-badge-inquiries"><i class="bi bi-lock-fill"></i> Restricted</div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="module-icon-wrapper flex-shrink-0" style="background: linear-gradient(135deg, var(--denr-green), #4caf50);">
                                <i class="bi bi-list-check"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-1" style="color: var(--denr-dark); font-weight: 600;">All Inquiries</h5>
                                <p class="card-text text-muted mb-2 small">View and manage all inquiry records</p>
                                <div class="module-link">
                                    <span class="badge" style="background-color: var(--denr-green); font-size: 0.75rem;">
                                        <i class="bi bi-arrow-right"></i> Access Module
                                    </span>
                                </div>
                            </div>
                            <div class="module-stats flex-shrink-0 text-end">
                                <small class="text-muted d-block">Total</small>
                                <strong class="text-primary">{{ \App\Models\Inquiry::count() }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-3 position-relative">
            <div id="badge-categories-anim" class="status-badge-animated text-success">
                <i class="bi bi-unlock-fill me-1"></i> Access Restored
            </div>
            <div id="badge-categories-lock-anim" class="status-badge-animated text-danger">
                <i class="bi bi-lock-fill me-1"></i> Module Locked
            </div>
            <a href="{{ $isRestrictedEnabled ? route('admin.categories') : 'javascript:void(0)' }}" class="text-decoration-none module-card-link" id="card-link-categories">
                <div class="card module-card hover-card h-100 border-0 shadow-sm {{ !$isRestrictedEnabled ? 'disabled-module' : '' }}" id="module-card-categories">
                    <div class="module-lock-badge {{ $isRestrictedEnabled ? 'd-none' : '' }}" id="lock-badge-categories"><i class="bi bi-lock-fill"></i> Restricted</div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="module-icon-wrapper flex-shrink-0" style="background: linear-gradient(135deg, #6f42c1, #e83e8c);">
                                <i class="bi bi-tags"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-1" style="color: var(--denr-dark); font-weight: 600;">Categories</h5>
                                <p class="card-text text-muted mb-2 small">Manage service categories and codes</p>
                                <div class="module-link">
                                    <span class="badge" style="background-color: #6f42c1; font-size: 0.75rem;">
                                        <i class="bi bi-arrow-right"></i> Access Module
                                    </span>
                                </div>
                            </div>
                            <div class="module-stats flex-shrink-0 text-end">
                                <small class="text-muted d-block">Active</small>
                                <strong class="text-purple">{{ $categories->count() }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-3">
            @php $isUsersEnabled = $moduleSettings['users']->is_enabled ?? true; @endphp
            <a href="{{ $isUsersEnabled ? route('admin.users') : 'javascript:void(0)' }}" class="text-decoration-none">
                <div class="card module-card hover-card h-100 border-0 shadow-sm {{ !$isUsersEnabled ? 'disabled-module' : '' }}">
                    @if(!$isUsersEnabled)
                    <div class="module-lock-badge"><i class="bi bi-lock-fill"></i> Restricted</div>
                    @endif
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="module-icon-wrapper flex-shrink-0" style="background: linear-gradient(135deg, #fd7e14, #ffc107);">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-1" style="color: var(--denr-dark); font-weight: 600;">User Management</h5>
                                <p class="card-text text-muted mb-2 small">Manage system users and permissions</p>
                                <div class="module-link">
                                    <span class="badge" style="background-color: #fd7e14; font-size: 0.75rem;">
                                        <i class="bi bi-arrow-right"></i> Access Module
                                    </span>
                                </div>
                            </div>
                            <div class="module-stats flex-shrink-0 text-end">
                                <small class="text-muted d-block">Users</small>
                                <strong class="text-warning">{{ \App\Models\User::count() }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    @endif

    @if(auth()->user()->username === 'admin')
    <!-- Category Status - Enhanced Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header" style="background: linear-gradient(135deg, var(--denr-green) 0%, var(--denr-dark) 100%); color: white;">
                    <h5 class="mb-0"><i class="bi bi-tags"></i> Category Status Overview</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Section</th>
                                    <th>Waiting</th>
                                    <th>Serving</th>
                                    <th>Completed</th>
                                    <th>Total Today</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                    @php
                                        $waiting = \App\Models\Inquiry::today()->where('category_id', $category->id)->where('status', 'waiting')->count();
                                        $serving = \App\Models\Inquiry::today()->where('category_id', $category->id)->where('status', 'serving')->count();
                                        $completed = \App\Models\Inquiry::today()->where('category_id', $category->id)->where('status', 'completed')->count();
                                        $total = \App\Models\Inquiry::today()->where('category_id', $category->id)->count();
                                    @endphp
                                    <tr>
                                        <td>{{ $category->name }}</td>
                                        <td><span class="badge" style="background-color: {{ $category->color }}; color: {{ $category->contrast_color }};">{{ $category->section }}</span></td>
                                        <td><span class="badge bg-warning">{{ $waiting }}</span></td>
                                        <td><span class="badge bg-info">{{ $serving }}</span></td>
                                        <td><span class="badge bg-success">{{ $completed }}</span></td>
                                        <td><strong>{{ $total }}</strong></td>
                                        <td>
                                            <a href="{{ route('admin.inquiries', ['category' => $category->id]) }}" class="btn btn-sm" style="background-color: var(--denr-green); color: white;">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Access Notification Overlay -->
    <div class="access-blur-overlay" id="accessBlurOverlay"></div>
    <div class="access-notify" id="accessNotify">
        <div class="access-notify-icon" id="accessNotifyIcon">
            <i class="bi bi-unlock-fill"></i>
        </div>
        <h2 class="access-notify-title" id="accessNotifyTitle">Access Restored</h2>
        <p class="access-notify-text" id="accessNotifyText">You can now access all restricted modules.</p>
        <div class="progress mt-4" style="height: 6px; border-radius: 3px; background: rgba(0,0,0,0.05);">
            <div class="progress-bar" id="accessNotifyProgress" style="width: 0%; transition: width 1.5s linear; background-color: currentColor;"></div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .hover-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 12px;
        overflow: hidden;
        background: white;
    }
    
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.12) !important;
    }
    
    .display-6 {
        font-weight: 700;
    }
    
    .card {
        border-radius: 12px;
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
        font-weight: 600;
    }
    
    .module-card {
        background: white;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05) !important;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    [data-theme="dark"] .module-card {
        background: var(--dark-surface) !important;
        border: 1px solid var(--dark-border) !important;
    }

    [data-theme="dark"] .module-card .card-body {
        background: var(--dark-surface) !important;
    }

    /* Professional Table Styles for Dark Mode */
    [data-theme="dark"] .table {
        color: var(--dark-on-surface) !important;
        border-color: var(--dark-border) !important;
    }

    [data-theme="dark"] .table thead th {
        background-color: var(--dark-surface-secondary) !important;
        color: var(--dark-on-surface) !important;
        border-bottom: 2px solid var(--dark-border) !important;
    }

    [data-theme="dark"] .table td {
        background-color: var(--dark-surface) !important;
        border-color: var(--dark-border) !important;
    }

    [data-theme="dark"] .table-hover tbody tr:hover td {
        background-color: var(--dark-surface-secondary) !important;
    }

    .module-card.disabled-module {
        opacity: 0.6;
        filter: grayscale(1);
        cursor: not-allowed;
        pointer-events: none;
        background: #e9ecef !important;
        border: 2px solid #dee2e6 !important;
        box-shadow: none !important;
    }

    .module-card::after {
        content: '\F471'; /* bi-lock-fill */
        font-family: 'bootstrap-icons';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0);
        font-size: 8rem;
        color: rgba(0,0,0,0.1);
        z-index: 10;
        opacity: 0;
        transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    .module-card.disabled-module::after {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }

    .module-card.access-restored-flash {
        animation: accessRestoredFlash 1s ease-out;
    }

    @keyframes accessRestoredFlash {
        0% { box-shadow: 0 0 0 0 rgba(46, 125, 50, 0.4); }
        70% { box-shadow: 0 0 0 20px rgba(46, 125, 50, 0); }
        100% { box-shadow: 0 0 0 0 rgba(46, 125, 50, 0); }
    }

    .status-badge-animated {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        padding: 10px 20px;
        border-radius: 30px;
        background: white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        z-index: 100;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        pointer-events: none;
        opacity: 0;
        display: none;
    }

    .status-badge-animated.show {
        display: block;
        animation: badgePop 1.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes badgePop {
        0% { transform: translate(-50%, -50%) scale(0.5); opacity: 0; }
        20% { transform: translate(-50%, -50%) scale(1.1); opacity: 1; }
        80% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
        100% { transform: translate(-50%, -150%) scale(0.8); opacity: 0; }
    }

    .module-lock-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #6c757d;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 6px;
        z-index: 20;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }

    .toggle-wrapper {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px 20px;
        border: 1px solid rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }

    [data-theme="dark"] .toggle-wrapper {
        background: var(--dark-surface) !important;
        border-color: var(--dark-border) !important;
    }

    [data-theme="dark"] .toggle-wrapper h5 {
        color: var(--dark-on-surface) !important;
    }

    [data-theme="dark"] .bg-white.rounded-circle {
        background-color: var(--dark-surface-secondary) !important;
        border: 1px solid var(--dark-border) !important;
    }

    /* Fixed Dark Mode Text Visibility for Stats and Filters */
    [data-theme="dark"] .text-muted { color: #adb5bd !important; }
    [data-theme="dark"] .card-title { color: var(--dark-on-surface) !important; }
    [data-theme="dark"] .stat-label { color: #adb5bd !important; }
    [data-theme="dark"] .form-label { color: var(--dark-on-surface) !important; }

    [data-theme="dark"] .form-control,
    [data-theme="dark"] .form-select {
        background-color: var(--dark-surface-secondary) !important;
        border-color: var(--dark-border) !important;
        color: var(--dark-on-surface) !important;
    }

    [data-theme="dark"] .form-control:focus,
    [data-theme="dark"] .form-select:focus {
        background-color: var(--dark-surface-secondary) !important;
        border-color: var(--denr-green) !important;
        box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.25) !important;
    }

    .module-toggle-btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .module-toggle-btn.enabled {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }

    .module-toggle-btn.disabled {
        background-color: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }

    .module-toggle-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    .module-toggle-btn .form-check-input {
        cursor: pointer;
        width: 2.5em;
        height: 1.25em;
        margin-top: 0;
    }
    
    .module-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, var(--denr-green), var(--denr-dark));
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .module-card:hover::before {
        opacity: 1;
    }
    
    .module-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--denr-green), var(--denr-light));
        box-shadow: 0 4px 15px rgba(46, 125, 50, 0.2);
        transition: all 0.3s ease;
    }
    
    .module-card:hover .module-icon-wrapper {
        transform: scale(1.05) rotate(3deg);
    }
    
    .module-icon-wrapper i {
        font-size: 1.8rem;
        color: white;
    }

    /* Professional Stat Blocks */
    .stat-block {
        background: white;
        border-radius: 12px;
        padding: 24px 20px;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0,0,0,0.08);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        border-left: 5px solid #dee2e6; /* Default accent */
    }

    [data-theme="dark"] .stat-block {
        background: var(--dark-surface-secondary) !important;
        border-color: var(--dark-border) !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2) !important;
    }

    .stat-block:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1);
    }

    /* Color Variants */
    .stat-block-primary { border-left-color: #0d6efd; background: linear-gradient(to right, rgba(13, 110, 253, 0.02), white); }
    .stat-block-success { border-left-color: #198754; background: linear-gradient(to right, rgba(25, 135, 84, 0.02), white); }
    .stat-block-info { border-left-color: #0dcaf0; background: linear-gradient(to right, rgba(13, 202, 240, 0.02), white); }
    .stat-block-warning { border-left-color: #ffc107; background: linear-gradient(to right, rgba(255, 193, 7, 0.02), white); }

    .stat-block i {
        position: absolute;
        right: 15px;
        bottom: 15px;
        font-size: 2.2rem;
        opacity: 0.8;
        transition: all 0.4s ease;
        z-index: 1;
    }

    .stat-block:hover i {
        opacity: 1;
        transform: scale(1.2) rotate(-5deg);
    }

    /* Stat block specific icon colors for better visibility */
    .stat-block .bi-calendar-check { color: #2e7d32; }
    .stat-block .bi-calendar-range { color: #0d6efd; }
    .stat-block .bi-calendar3 { color: #0dcaf0; }
    .stat-block .bi-cash-coin { color: #ffc107; }
    .stat-block .bi-bank { color: #dc3545; }
    .stat-block .bi-check2-all { color: #198754; }
    .stat-block .bi-wallet2 { color: #0d6efd; }
    .stat-block .bi-grid-3x3-gap { color: #0dcaf0; }
    .stat-block .bi-people { color: #ffc107; }

    /* Dark mode adjustments for icons */
    [data-theme="dark"] .stat-block i {
        opacity: 0.9;
        filter: brightness(1.2);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 6px;
        line-height: 1;
        letter-spacing: -0.5px;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        font-weight: 700;
    }

    /* Action Badges */
    .action-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 15px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
        text-decoration: none !important;
    }

    .action-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        filter: brightness(1.1);
    }

    .action-badge i {
        font-size: 1.1rem;
    }

    .chart-container-wrapper {
        background: white;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid rgba(0,0,0,0.05);
    }

    [data-theme="dark"] .chart-container-wrapper {
        background: var(--dark-surface-secondary) !important;
        border-color: var(--dark-border) !important;
    }

    /* Access Notification Overlay */
    .access-notify {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.8);
        background: rgba(255, 255, 255, 0.98);
        padding: 40px 60px;
        border-radius: 24px;
        box-shadow: 0 25px 80px rgba(0,0,0,0.3);
        z-index: 11000;
        text-align: center;
        opacity: 0;
        pointer-events: none;
        display: none;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .access-notify.show {
        display: block;
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    .access-notify-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex; 
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        font-size: 3.5rem;
        animation: iconBounce 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite alternate;
    }

    .access-notify-success { background: #e8f5e9; color: #2e7d32; }
    .access-notify-danger { background: #ffebee; color: #c62828; }

    @keyframes iconBounce {
        from { transform: translateY(0); }
        to { transform: translateY(-10px); }
    }

    .access-notify-title {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 10px;
        letter-spacing: -0.5px;
    }

    .access-notify-text {
        font-size: 1.1rem;
        color: #6c757d;
        font-weight: 500;
    }

    /* Screen Blur */
    .access-blur-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.4);
        backdrop-filter: blur(4px);
        z-index: 10900;
        opacity: 0;
        display: none;
        transition: opacity 0.4s ease;
    }

    .access-blur-overlay.show {
        display: block;
        opacity: 1;
    }
    
    .module-stats {
        min-width: 60px;
    }
    
    .module-stats strong {
        font-size: 1.2rem;
        font-weight: 700;
    }
    
    .module-link .badge {
        transition: all 0.2s ease;
        padding: 0.5em 0.8em;
    }
    
    .module-card:hover .module-link .badge {
        transform: translateX(3px);
    }
    
    /* Gradient backgrounds */
    [style*="linear-gradient"] {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .display-6 {
            font-size: 1.5rem;
        }
        
        .module-icon-wrapper {
            width: 50px !important;
            height: 50px !important;
        }
        
        .module-icon-wrapper i {
            font-size: 1.5rem !important;
        }
        
        .module-stats {
            min-width: 50px;
        }
        
        .module-stats strong {
            font-size: 1rem;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get this month's data - last 7 days
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    
    // Use data from controller
    const labels = {!! json_encode($weeklyData['labels']) !!};
    const data = {!! json_encode($weeklyData['data']) !!};
    
    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(23, 162, 184, 0.8)');
    gradient.addColorStop(1, 'rgba(23, 162, 184, 0.2)');
    
    // Create bar chart
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'This Week',
                data: data,
                backgroundColor: gradient,
                borderColor: 'rgba(23, 162, 184, 1)',
                borderWidth: 1,
                borderRadius: 4,
                barThickness: 16
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' inquiries';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        font: {
                            size: 9
                        },
                        color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#adb5bd' : '#666'
                    },
                    grid: {
                        color: document.documentElement.getAttribute('data-theme') === 'dark' ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 8
                        },
                        color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#adb5bd' : '#666'
                    },
                    grid: {
                        display: false
                    }
                }
            },
            layout: {
                padding: {
                    top: 5,
                    bottom: 5,
                    left: 0,
                    right: 0
                }
            }
        }
    });

    // Listen for theme changes to update chart colors
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'data-theme') {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                chart.options.scales.y.ticks.color = isDark ? '#adb5bd' : '#666';
                chart.options.scales.y.grid.color = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
                chart.options.scales.x.ticks.color = isDark ? '#adb5bd' : '#666';
                chart.update();
            }
        });
    });
    observer.observe(document.documentElement, { attributes: true });

    // Module Toggle Logic
    const toggles = document.querySelectorAll('.module-toggle');
    const accessNotify = document.getElementById('accessNotify');
    const accessBlurOverlay = document.getElementById('accessBlurOverlay');
    const accessNotifyIcon = document.getElementById('accessNotifyIcon');
    const accessNotifyTitle = document.getElementById('accessNotifyTitle');
    const accessNotifyText = document.getElementById('accessNotifyText');
    const accessNotifyProgress = document.getElementById('accessNotifyProgress');

    function showAccessAnimation(isEnabled) {
        // Reset progress bar
        accessNotifyProgress.style.transition = 'none';
        accessNotifyProgress.style.width = '0%';
        
        // Update content
        if (isEnabled) {
            accessNotifyIcon.className = 'access-notify-icon access-notify-success';
            accessNotifyIcon.innerHTML = '<i class="bi bi-unlock-fill"></i>';
            accessNotifyTitle.innerText = 'Access Restored';
            accessNotifyTitle.style.color = '#2e7d32';
            accessNotifyText.innerText = 'You can now access all restricted modules.';
            accessNotifyProgress.style.backgroundColor = '#2e7d32';
        } else {
            accessNotifyIcon.className = 'access-notify-icon access-notify-danger';
            accessNotifyIcon.innerHTML = '<i class="bi bi-lock-fill"></i>';
            accessNotifyTitle.innerText = 'Access Restricted';
            accessNotifyTitle.style.color = '#c62828';
            accessNotifyText.innerText = 'Restricted modules have been locked.';
            accessNotifyProgress.style.backgroundColor = '#c62828';
        }

        // Show overlay and notification
        accessBlurOverlay.classList.add('show');
        accessNotify.classList.add('show');

        // Start progress bar
        setTimeout(() => {
            accessNotifyProgress.style.transition = 'width 1.5s linear';
            accessNotifyProgress.style.width = '100%';
        }, 50);

        // Hide after animation
        setTimeout(() => {
            accessNotify.classList.remove('show');
            accessBlurOverlay.classList.remove('show');
        }, 1800);
    }

    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const moduleKey = this.dataset.module;
            const isEnabled = this.checked;
            const btn = document.getElementById('restrictedToggleBtn');
            const label = document.getElementById('toggleLabel');
            
            // Elements to toggle
            const sidebarInquiries = document.getElementById('sidebar-inquiries');
            const sidebarCategories = document.getElementById('sidebar-categories');
            const sidebarFdHome = document.getElementById('sidebar-fd-home');
            const sidebarFdCreate = document.getElementById('sidebar-fd-create');
            const sidebarFdLive = document.getElementById('sidebar-fd-live');
            const cardInquiries = document.getElementById('module-card-inquiries');
            const cardCategories = document.getElementById('module-card-categories');
            const badgeInquiries = document.getElementById('lock-badge-inquiries');
            const badgeCategories = document.getElementById('lock-badge-categories');
            const linkInquiries = document.getElementById('card-link-inquiries');
            const linkCategories = document.getElementById('card-link-categories');
            
            // Animation badges
            const animEnableInq = document.getElementById('badge-inquiries-anim');
            const animEnableCat = document.getElementById('badge-categories-anim');
            const animLockInq = document.getElementById('badge-inquiries-lock-anim');
            const animLockCat = document.getElementById('badge-categories-lock-anim');
            
            // --- OPTIMISTIC UI: Update visual state immediately (ZERO LAG) ---
            
            // 1. Show the professional full-screen animation
            showAccessAnimation(isEnabled);

            if (isEnabled) {
                if (btn) btn.className = 'module-toggle-btn enabled';
                if (label) label.innerHTML = '<i class="bi bi-unlock-fill text-success me-1"></i> Access Enabled';
                
                // Sidebar
                if (sidebarInquiries) sidebarInquiries.classList.remove('disabled-link');
                if (sidebarCategories) sidebarCategories.classList.remove('disabled-link');
                if (sidebarFdHome) {
                    sidebarFdHome.classList.remove('disabled-link');
                    sidebarFdHome.href = '{{ route('front-desk.index') }}';
                }
                if (sidebarFdCreate) {
                    sidebarFdCreate.classList.remove('disabled-link');
                    sidebarFdCreate.href = '{{ route('front-desk.create') }}';
                }
                if (sidebarFdLive) {
                    sidebarFdLive.classList.remove('disabled-link');
                    sidebarFdLive.href = '{{ route('front-desk.live-status') }}';
                }
                
                // Dashboard Cards
                [cardInquiries, cardCategories].forEach(card => {
                    if (card) {
                        card.classList.remove('disabled-module');
                        card.classList.add('access-restored-flash');
                        setTimeout(() => card.classList.remove('access-restored-flash'), 1000);
                    }
                });
                [badgeInquiries, badgeCategories].forEach(badge => { if (badge) badge.classList.add('d-none'); });
                
                // Restore Links
                if (linkInquiries) linkInquiries.href = '{{ route('admin.inquiries') }}';
                if (linkCategories) linkCategories.href = '{{ route('admin.categories') }}';
                
                // Show "Access Restored" badge animation on the cards
                [animEnableInq, animEnableCat].forEach(anim => {
                    if (anim) {
                        anim.classList.add('show');
                        setTimeout(() => anim.classList.remove('show'), 1500);
                    }
                });
            } else {
                if (btn) btn.className = 'module-toggle-btn disabled';
                if (label) label.innerHTML = '<i class="bi bi-lock-fill text-danger me-1"></i> Access Restricted';
                
                // Sidebar
                if (sidebarInquiries) sidebarInquiries.classList.add('disabled-link');
                if (sidebarCategories) sidebarCategories.classList.add('disabled-link');
                if (sidebarFdHome) {
                    sidebarFdHome.classList.add('disabled-link');
                    sidebarFdHome.href = 'javascript:void(0)';
                }
                if (sidebarFdCreate) {
                    sidebarFdCreate.classList.add('disabled-link');
                    sidebarFdCreate.href = 'javascript:void(0)';
                }
                if (sidebarFdLive) {
                    sidebarFdLive.classList.add('disabled-link');
                    sidebarFdLive.href = 'javascript:void(0)';
                }
                
                // Dashboard Cards
                [cardInquiries, cardCategories].forEach(card => { if (card) card.classList.add('disabled-module'); });
                [badgeInquiries, badgeCategories].forEach(badge => { if (badge) badge.classList.remove('d-none'); });
                
                // Disable Links
                if (linkInquiries) linkInquiries.href = 'javascript:void(0)';
                if (linkCategories) linkCategories.href = 'javascript:void(0)';
                
                // Show "Module Locked" badge animation on the cards
                [animLockInq, animLockCat].forEach(anim => {
                    if (anim) {
                        anim.classList.add('show');
                        setTimeout(() => anim.classList.remove('show'), 1500);
                    }
                });
            }
            
            // --- BACKGROUND PERSISTENCE ---
            axios.post('{{ route('admin.modules.toggle') }}', {
                module_key: moduleKey,
                is_enabled: isEnabled
            })
            .then(response => {
                // Background update complete, no need to show another toast
                // as the high-end animation already provided feedback
                console.log('Access state persisted:', response.data);
            })
            .catch(error => {
                console.error('Error toggling module:', error);
                // Revert on failure
                this.checked = !isEnabled;
                window.location.reload(); 
            });
        });
    });
});
</script>
@endsection
