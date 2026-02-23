@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-graph-up text-info"></i> Reports & Analytics</h2>
                    <p class="text-muted mb-0">Generate and export system reports</p>
                </div>
                <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Report Filters</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.generate') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Report Type</label>
                    <select name="report_type" class="form-select" required>
                        <option value="daily">Daily Report</option>
                        <option value="weekly">Weekly Report</option>
                        <option value="monthly">Monthly Report</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach(\App\Models\Category::where('is_active', true)->get() as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-info text-white">
                        <i class="bi bi-search"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        @php
            $todayStats = [
                'Total Inquiries' => \App\Models\Inquiry::today()->count(),
                'Completed' => \App\Models\Inquiry::today()->where('status', 'completed')->count(),
                'Total Assessments' => \App\Models\Assessment::whereDate('created_at', today())->count(),
                'Total Revenue' => \App\Models\Assessment::whereDate('created_at', today())->sum('fees'),
            ];
        @endphp
        
        @foreach($todayStats as $label => $value)
            <div class="col-md-3">
                <div class="card bg-light shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">{{ $label }} (Today)</h6>
                        <h3 class="mb-0 {{ $label == 'Total Revenue' ? 'text-success' : 'text-primary' }}">
                            @if($label == 'Total Revenue')
                                ₱{{ number_format($value, 2) }}
                            @else
                                {{ $value }}
                            @endif
                        </h3>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Export Options -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-download"></i> Export Data</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="{{ route('reports.export-pdf') }}?date={{ date('Y-m-d') }}" class="btn btn-outline-danger w-100">
                        <i class="bi bi-file-pdf" style="font-size: 2rem;"></i>
                        <h5 class="mt-2">Export as PDF</h5>
                        <small class="text-muted">Download today's report as PDF</small>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ route('reports.export-excel') }}?date={{ date('Y-m-d') }}" class="btn btn-outline-success w-100">
                        <i class="bi bi-file-excel" style="font-size: 2rem;"></i>
                        <h5 class="mt-2">Export as Excel</h5>
                        <small class="text-muted">Download data as Excel spreadsheet</small>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="{{ route('reports.print') }}?date={{ date('Y-m-d') }}" target="_blank" class="btn btn-outline-primary w-100">
                        <i class="bi bi-printer" style="font-size: 2rem;"></i>
                        <h5 class="mt-2">Print Report</h5>
                        <small class="text-muted">Open print-friendly version</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
