@extends('layouts.app')

@section('title', 'Admin Dashboard - 3rd Floor')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-speedometer2 text-primary"></i> Admin Dashboard (3rd Floor)</h2>
                    <p class="text-muted mb-0">Manage inquiries, assessments, reports, and categories - User management for main admin only</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.inquiries') }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-plus"></i> Create Assessment
                    </a>
                    <a href="{{ route('reports.index') }}" class="btn btn-info text-white">
                        <i class="bi bi-graph-up"></i> Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Statistics -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Total Inquiries</h6>
                            <h3 class="mb-0 mt-2">{{ $todayStats['total_inquiries'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people" style="font-size: 2rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Waiting</h6>
                            <h3 class="mb-0 mt-2">{{ $todayStats['waiting'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-hourglass-split" style="font-size: 2rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Serving</h6>
                            <h3 class="mb-0 mt-2">{{ $todayStats['serving'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-check" style="font-size: 2rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Completed</h6>
                            <h3 class="mb-0 mt-2">{{ $todayStats['completed'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle" style="font-size: 2rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Skipped</h6>
                            <h3 class="mb-0 mt-2">{{ $todayStats['skipped'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-x-circle" style="font-size: 2rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Categories</h6>
                            <h3 class="mb-0 mt-2">{{ $categories->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-tags" style="font-size: 2rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <a href="{{ route('admin.inquiries') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 hover-card">
                    <div class="card-body text-center">
                        <i class="bi bi-list-check text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 mb-1">All Inquiries</h5>
                        <p class="text-muted mb-0">View and manage all inquiries</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.assessments') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 hover-card">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark-text text-success" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 mb-1">Assessment Form</h5>
                        <p class="text-muted mb-0">Create and view assessments</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('reports.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 hover-card">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up text-info" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 mb-1">Reports</h5>
                        <p class="text-muted mb-0">Generate and export reports</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.categories') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 hover-card">
                    <div class="card-body text-center">
                        <i class="bi bi-tags text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 mb-1">Categories</h5>
                        <p class="text-muted mb-0">Manage service categories</p>
                    </div>
                </div>
            </a>
        </div>
        @if(auth()->user()->username === 'admin')
        <div class="col-md-3">
            <a href="{{ route('admin.users') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100 hover-card">
                    <div class="card-body text-center">
                        <i class="bi bi-people-fill text-warning" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 mb-1">User Management</h5>
                        <p class="text-muted mb-0">Manage system users</p>
                    </div>
                </div>
            </a>
        </div>
        @endif
    </div>

    <!-- Category Status -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-tags"></i> Category Status Overview</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Code</th>
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
                                        <td>
                                            <span class="badge" style="background-color: {{ $category->color }}">&nbsp;</span>
                                            {{ $category->name }}
                                        </td>
                                        <td><code>{{ $category->code }}</code></td>
                                        <td><span class="badge bg-warning">{{ $waiting }}</span></td>
                                        <td><span class="badge bg-info">{{ $serving }}</span></td>
                                        <td><span class="badge bg-success">{{ $completed }}</span></td>
                                        <td><strong>{{ $total }}</strong></td>
                                        <td>
                                            <a href="{{ route('admin.inquiries', ['category' => $category->id]) }}" class="btn btn-sm btn-outline-primary">
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
</div>
@endsection

@section('styles')
<style>
    .hover-card {
        transition: all 0.3s ease;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }
</style>
@endsection
