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
        <div class="col-md-2">
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
        <div class="col-md-2">
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
        <div class="col-md-2">
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
        <div class="col-md-2">
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
        <div class="col-md-2">
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
        <div class="col-md-2">
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

    <!-- Main Modules Section - Enhanced with Data Cards -->
    <div class="row mb-4">
        <!-- Assessments Card -->
        <div class="col-lg-6 col-md-6 mb-3">
            <a href="{{ route('admin.assessments') }}" class="text-decoration-none">
                <div class="card module-card hover-card h-100 border-0 shadow-sm">
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
                        
                        <!-- Assessment Statistics Cards -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="bg-light rounded p-2 text-center">
                                    <h6 class="mb-0 text-success fw-bold">{{ \App\Models\Assessment::whereDate('created_at', today())->count() }}</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">Today</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded p-2 text-center">
                                    <h6 class="mb-0 text-primary fw-bold">{{ \App\Models\Assessment::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">This Week</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="bg-light rounded p-2 text-center">
                                    <h6 class="mb-0 text-info fw-bold">{{ \App\Models\Assessment::whereMonth('created_at', now()->month)->count() }}</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">This Month</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded p-2 text-center">
                                    <h6 class="mb-0 text-warning fw-bold">{{ number_format(\App\Models\Assessment::whereDate('created_at', today())->sum('fees'), 2) }}</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">Today's Fees (₱)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <div class="bg-light rounded p-2 text-center">
                                    <h6 class="mb-0 text-danger fw-bold">{{ number_format(\App\Models\Assessment::sum('fees'), 2) }}</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">Total Revenue (₱)</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="d-flex gap-2">
                            <span class="badge bg-success flex-fill text-center py-2" style="font-size: 0.8rem; cursor: pointer;" onclick="event.stopPropagation(); window.location='{{ route('admin.assessments.create-direct') }}'">
                                <i class="bi bi-plus-circle"></i> Quick Create
                            </span>
                            <span class="badge bg-info flex-fill text-center py-2" style="font-size: 0.8rem; cursor: pointer;" onclick="event.stopPropagation(); window.location='{{ route('admin.assessments') }}'">
                                <i class="bi bi-eye"></i> View All
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
        <!-- Reports Card -->
        <div class="col-lg-6 col-md-6 mb-3">
            <a href="{{ route('reports.index') }}" class="text-decoration-none">
                <div class="card module-card hover-card h-100 border-0 shadow-sm">
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
                        
                        <!-- Report Statistics Cards -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="bg-light rounded p-2 text-center">
                                    <h6 class="mb-0 text-success fw-bold">{{ \App\Models\Inquiry::today()->completed()->count() }}</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">Completed Today</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded p-2 text-center">
                                    <h6 class="mb-0 text-primary fw-bold">{{ number_format(\App\Models\Assessment::sum('fees'), 2) }}</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">Total Fees (₱)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="bg-light rounded p-2 text-center">
                                    <h6 class="mb-0 text-info fw-bold">{{ \App\Models\Category::active()->count() }}</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">Active Categories</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded p-2 text-center">
                                    <h6 class="mb-0 text-warning fw-bold">{{ \App\Models\User::where('is_active', true)->count() }}</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">Active Users</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- This Month Bar Chart - Same height as stat cards -->
                        <div class="mb-3">
                            <div class="bg-light rounded p-3 text-center" style="height: 120px; display: flex; align-items: center; justify-content: center;">
                                <canvas id="monthlyChart" style="max-height: 100px; width: 100%;"></canvas>
                            </div>
                        </div>
                        
                        <!-- Quick Graph Access -->
                        <div class="d-flex gap-2">
                            <span class="badge bg-info flex-fill text-center py-2" style="font-size: 0.8rem; cursor: pointer;" onclick="event.stopPropagation(); window.location='{{ route('reports.index', ['report_type' => 'daily']) }}'">
                                <i class="bi bi-bar-chart"></i> Daily
                            </span>
                            <span class="badge bg-primary flex-fill text-center py-2" style="font-size: 0.8rem; cursor: pointer;" onclick="event.stopPropagation(); window.location='{{ route('reports.index', ['report_type' => 'monthly']) }}'">
                                <i class="bi bi-pie-chart"></i> Monthly
                            </span>
                            <span class="badge bg-success flex-fill text-center py-2" style="font-size: 0.8rem; cursor: pointer;" onclick="event.stopPropagation(); window.location='{{ route('reports.index', ['report_type' => 'yearly']) }}'">
                                <i class="bi bi-graph-up"></i> Yearly
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    @if(auth()->user()->username === 'admin')
    <!-- Admin Only Modules -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <a href="{{ route('admin.inquiries') }}" class="text-decoration-none">
                <div class="card module-card hover-card h-100 border-0 shadow-sm">
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
        
        <div class="col-lg-4 col-md-6 mb-3">
            <a href="{{ route('admin.categories') }}" class="text-decoration-none">
                <div class="card module-card hover-card h-100 border-0 shadow-sm">
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
            <a href="{{ route('admin.users') }}" class="text-decoration-none">
                <div class="card module-card hover-card h-100 border-0 shadow-sm">
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
    
    /* Module Card Styling */
    .module-card {
        background: white;
        position: relative;
        overflow: hidden;
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
        transform: scale(1.05);
    }
    
    .module-icon-wrapper i {
        font-size: 1.8rem;
        color: white;
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
    new Chart(ctx, {
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
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 8
                        }
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
});
</script>
@endsection
