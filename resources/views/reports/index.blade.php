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
            <form action="{{ route('reports.generate') }}" method="POST" class="row g-3" id="reportFilterForm">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Report Type</label>
                    <select name="report_type" class="form-select" required onchange="toggleDateFields()">
                        <option value="daily" {{ old('report_type', request('report_type', 'daily')) == 'daily' ? 'selected' : '' }}>Daily Report</option>
                        <option value="weekly" {{ old('report_type', request('report_type')) == 'weekly' ? 'selected' : '' }}>Weekly Report</option>
                        <option value="monthly" {{ old('report_type', request('report_type')) == 'monthly' ? 'selected' : '' }}>Monthly Report</option>
                        <option value="yearly" {{ old('report_type', request('report_type')) == 'yearly' ? 'selected' : '' }}>Yearly Report</option>
                        <option value="custom" {{ old('report_type', request('report_type')) == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    @if(isset($date_range))
                        <input type="date" name="date_from" class="form-control" value="{{ old('date_from', isset($date_range['start']) ? \Carbon\Carbon::parse($date_range['start'])->format('Y-m-d') : '') }}" id="dateFromField" disabled>
                    @else
                        <input type="date" name="date_from" class="form-control" value="{{ old('date_from', request('date_from', date('Y-m-d'))) }}" id="dateFromField" disabled>
                    @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    @if(isset($date_range))
                        <input type="date" name="date_to" class="form-control" value="{{ old('date_to', isset($date_range['end']) ? \Carbon\Carbon::parse($date_range['end'])->format('Y-m-d') : '') }}" id="dateToField" disabled>
                    @else
                        <input type="date" name="date_to" class="form-control" value="{{ old('date_to', request('date_to', date('Y-m-d'))) }}" id="dateToField" disabled>
                    @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="" {{ old('category', request('category')) == '' ? 'selected' : '' }}>All Categories</option>
                        @foreach(\App\Models\Category::where('is_active', true)->get() as $category)
                            <option value="{{ $category->id }}" {{ old('category', request('category')) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="p-2 bg-success text-white rounded text-center fw-bold">
                        <i class="bi bi-check-circle"></i> COMPLETED
                    </div>
                    <input type="hidden" name="status" value="completed">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Section</label>
                    <select name="section" class="form-select">
                        <option value="" {{ old('section', request('section')) == '' ? 'selected' : '' }}>All Sections</option>
                        @foreach(\App\Models\Category::where('is_active', true)->distinct()->pluck('section') as $section)
                            @if($section)
                                <option value="{{ $section }}" {{ old('section', request('section')) == $section ? 'selected' : '' }}>{{ $section }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-info text-white" id="generateReportBtn">
                        <i class="bi bi-search"></i> Generate Report
                    </button>
                    <button type="button" class="btn btn-secondary ms-2" onclick="resetFilters()">
                        <i class="bi bi-arrow-clockwise"></i> Reset Filters
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Add loading indicator -->
    <div id="loadingIndicator" style="display: none;" class="text-center py-4">
        <div class="spinner-border text-info" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Generating report, please wait...</p>
    </div>

    <!-- Report Results Display -->
    @if(isset($overall_stats))
        <div class="alert alert-success mb-4">
            <h5 class="mb-1"><i class="bi bi-check-circle"></i> Report Generated Successfully</h5>
            <p class="mb-0">Showing data from <strong>{{ $date_range['start'] }}</strong> to <strong>{{ $date_range['end'] }}</strong></p>
        </div>
        
        <!-- Enhanced Report Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-block stat-block-primary">
                    <h2 class="stat-value text-primary">{{ $overall_stats['total'] }}</h2>
                    <span class="stat-label">Total Inquiries</span>
                    <i class="bi bi-people"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-block stat-block-success">
                    <h2 class="stat-value text-success">{{ $overall_stats['completed'] }}</h2>
                    <span class="stat-label">Completed</span>
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-block stat-block-info">
                    <h2 class="stat-value text-info">{{ $assessments_count ?? 0 }}</h2>
                    <span class="stat-label">Total Assessments</span>
                    <i class="bi bi-file-earmark-text"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-block stat-block-warning">
                    <h2 class="stat-value text-warning">₱{{ number_format($total_fees ?? 0, 2) }}</h2>
                    <span class="stat-label">Total Revenue</span>
                    <i class="bi bi-currency-peso"></i>
                </div>
            </div>
        </div>

        <!-- Category Statistics Table -->
        <div class="card shadow-sm mb-4">
            <div class="card-header gradient-header-primary text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Category Statistics</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Section</th>
                                <th>Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category_stats as $code => $stats)
                                @if($stats['total'] > 0)  <!-- Only show categories with data -->
                                    <tr>
                                        <td>{{ $stats['name'] }}</td>
                                        <td>{{ $stats['section'] }}</td>
                                        <td><span class="badge bg-success">{{ $stats['completed'] }}</span></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Revenue Statistics Bar Graph -->
        <div class="card shadow-sm mb-4">
            <div class="card-header gradient-header-green text-white">
                <h5 class="mb-0"><i class="bi bi-currency-peso"></i> Revenue Statistics</h5>
            </div>
            <div class="card-body">
                <div class="chart-container-wrapper">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Report Charts -->
        <!-- Bar Chart Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header gradient-header-info text-white">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Report Statistics Overview</h5>
            </div>
            <div class="card-body">
                <div class="chart-container-wrapper">
                    <canvas id="dailyChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Section-wise Statistics -->
        <div class="card shadow-sm mb-4">
            <div class="card-header gradient-header-primary text-white">
                <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Section Statistics</h5>
            </div>
            <div class="card-body">
                <div class="chart-container-wrapper">
                    <canvas id="sectionChart" height="300"></canvas>
                </div>
            </div>
        </div>
    @else
        <!-- Default Stats (Today's stats) -->
        <div class="row mb-4">
            @php
                $todayStatsItems = [
                    ['label' => 'Total Inquiries', 'value' => \App\Models\Inquiry::today()->count(), 'icon' => 'bi-people', 'color' => 'text-primary', 'variant' => 'primary'],
                    ['label' => 'Completed', 'value' => \App\Models\Inquiry::today()->where('status', 'completed')->count(), 'icon' => 'bi-check-circle', 'color' => 'text-success', 'variant' => 'success'],
                    ['label' => 'Total Assessments', 'value' => \App\Models\Assessment::whereDate('created_at', today())->count(), 'icon' => 'bi-file-earmark-text', 'color' => 'text-info', 'variant' => 'info'],
                    ['label' => 'Total Revenue', 'value' => \App\Models\Assessment::whereDate('created_at', today())->sum('fees'), 'icon' => 'bi-currency-peso', 'color' => 'text-warning', 'is_currency' => true, 'variant' => 'warning'],
                ];
            @endphp
            
            @foreach($todayStatsItems as $item)
                <div class="col-md-3">
                    <div class="stat-block stat-block-{{ $item['variant'] }}">
                        <h2 class="stat-value {{ $item['color'] }}">
                            @if(isset($item['is_currency']) && $item['is_currency'])
                                ₱{{ number_format($item['value'], 2) }}
                            @else
                                {{ $item['value'] }}
                            @endif
                        </h2>
                        <span class="stat-label">{{ $item['label'] }} (Today)</span>
                        <i class="bi {{ $item['icon'] }}"></i>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Bar Chart Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header gradient-header-info text-white">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Daily Statistics Overview</h5>
            </div>
            <div class="card-body">
                <div class="chart-container-wrapper">
                    <canvas id="dailyChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Section-wise Statistics -->
        <div class="card shadow-sm mb-4">
            <div class="card-header gradient-header-primary text-white">
                <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Section Statistics</h5>
            </div>
            <div class="card-body">
                <div class="chart-container-wrapper">
                    <canvas id="sectionChart" height="300"></canvas>
                </div>
            </div>
        </div>
    @endif

    <!-- Export Options -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-download"></i> Export Data</h5>
        </div>
        <div class="card-body">
            @if(isset($overall_stats))
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <form action="{{ route('reports.export-pdf') }}" method="GET" target="_blank">
                            <input type="hidden" name="report_type" value="{{ request('report_type', 'daily') }}">
                            <input type="hidden" name="date_from" value="{{ is_string($date_range['start']) ? $date_range['start'] : \Carbon\Carbon::parse($date_range['start'])->format('Y-m-d') }}">
                            <input type="hidden" name="date_to" value="{{ is_string($date_range['end']) ? $date_range['end'] : \Carbon\Carbon::parse($date_range['end'])->format('Y-m-d') }}">
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            <input type="hidden" name="section" value="{{ request('section') }}">
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-outline-danger w-100 export-btn export-btn-pdf" style="cursor: pointer;">
                                <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 2.5rem;"></i>
                                <h5 class="mt-3 fw-bold">Export as PDF</h5>
                                <p class="text-muted small mb-0">Professional PDF Report</p>
                            </button>
                        </form>
                    </div>
                    <div class="col-md-4 mb-3">
                        <form action="{{ route('reports.export-excel') }}" method="POST">
                            @csrf
                            <input type="hidden" name="report_type" value="{{ request('report_type', 'daily') }}">
                            <input type="hidden" name="date_from" value="{{ is_string($date_range['start']) ? $date_range['start'] : \Carbon\Carbon::parse($date_range['start'])->format('Y-m-d') }}">
                            <input type="hidden" name="date_to" value="{{ is_string($date_range['end']) ? $date_range['end'] : \Carbon\Carbon::parse($date_range['end'])->format('Y-m-d') }}">
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            <input type="hidden" name="section" value="{{ request('section') }}">
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-outline-success w-100 export-btn export-btn-excel" style="cursor: pointer;">
                                <i class="bi bi-file-earmark-spreadsheet text-success" style="font-size: 2.5rem;"></i>
                                <h5 class="mt-3 fw-bold">Export as Excel</h5>
                                <p class="text-muted small mb-0">Enhanced Data Spreadsheet</p>
                            </button>
                        </form>
                    </div>
                    <div class="col-md-4 mb-3">
                        <form action="{{ route('reports.print') }}" method="GET" target="_blank">
                            <input type="hidden" name="report_type" value="{{ request('report_type', 'daily') }}">
                            <input type="hidden" name="date_from" value="{{ is_string($date_range['start']) ? $date_range['start'] : \Carbon\Carbon::parse($date_range['start'])->format('Y-m-d') }}">
                            <input type="hidden" name="date_to" value="{{ is_string($date_range['end']) ? $date_range['end'] : \Carbon\Carbon::parse($date_range['end'])->format('Y-m-d') }}">
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            <input type="hidden" name="section" value="{{ request('section') }}">
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-outline-primary w-100 export-btn export-btn-print" style="cursor: pointer;">
                                <i class="bi bi-printer text-primary" style="font-size: 2.5rem;"></i>
                                <h5 class="mt-3 fw-bold">Print Report</h5>
                                <p class="text-muted small mb-0">Print-Friendly Layout</p>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Debug Info (remove in production) -->
                <div class="alert alert-info mt-3" style="font-size: 9pt;">
                    <strong>Debug Info:</strong><br>
                    Report Type: {{ request('report_type', 'daily') }}<br>
                    Date From: {{ is_string($date_range['start']) ? $date_range['start'] : \Carbon\Carbon::parse($date_range['start'])->format('Y-m-d') }}<br>
                    Date To: {{ is_string($date_range['end']) ? $date_range['end'] : \Carbon\Carbon::parse($date_range['end'])->format('Y-m-d') }}<br>
                    Category: {{ request('category') ?? 'All' }}<br>
                    Section: {{ request('section') ?? 'All' }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-download text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3">Please generate a report first to enable export options</p>
                    <button class="btn btn-info text-white mt-2" onclick="document.getElementById('generateReportBtn').click(); return false;">
                        <i class="bi bi-search"></i> Generate Report First
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Helper to get theme-aware colors
function getThemeColors() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    return {
        text: isDark ? '#adb5bd' : '#666',
        grid: isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)',
        border: isDark ? '#2d3238' : 'rgba(0,0,0,0.1)'
    };
}

// Toggle date fields based on report type selection
function toggleDateFields() {
    const reportTypeSelect = document.querySelector('select[name="report_type"]');
    if (!reportTypeSelect) return;
    
    const reportType = reportTypeSelect.value;
    const dateFromField = document.getElementById('dateFromField');
    const dateToField = document.getElementById('dateToField');
    
    if (!dateFromField || !dateToField) return;
    
    if (reportType === 'custom') {
        dateFromField.disabled = false;
        dateToField.disabled = false;
    } else {
        dateFromField.disabled = true;
        dateToField.disabled = true;
        
        // Set dates based on report type
        const today = new Date();
        let startDate = new Date(today);
        let endDate = new Date(today);
        
        switch(reportType) {
            case 'daily':
                // Today
                break;
            case 'weekly':
                // Start of week (Sunday) to end of week (Saturday)
                startDate.setDate(today.getDate() - today.getDay());
                endDate.setDate(today.getDate() + (6 - today.getDay()));
                break;
            case 'monthly':
                // Start of month to end of month
                startDate.setDate(1);
                endDate.setDate(new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate());
                break;
            case 'yearly':
                // Start of year (January 1st) to end of year (December 31st)
                startDate.setMonth(0);
                startDate.setDate(1);
                endDate.setMonth(11);
                endDate.setDate(31);
                break;
        }
        
        // Format dates as YYYY-MM-DD
        const formatDate = (date) => {
            return date.toISOString().split('T')[0];
        };
        
        // Update the date values based on the report type
        // Only update if the report type is not custom (to avoid overriding user-entered dates)
        dateFromField.value = formatDate(startDate);
        dateToField.value = formatDate(endDate);
    }
}

console.log('Chart.js script loaded');

// Check if Chart is available immediately
if (typeof Chart !== 'undefined') {
    console.log('Chart.js is available, version:', Chart.version || 'unknown');
} else {
    console.error('Chart.js is not available');
}

// Bar Elevator Class for handling bar animations
class BarElevator {
    constructor() {
        this.charts = {};
        this.debug = true;
        this.init();
    }
    
    init() {
        if (this.debug) console.log('BarElevator: Initializing...');
        
        // Create charts
        this.createDailyChart();
        this.createSectionChart();
        this.createRevenueChart();
        
        // Set up animation triggers
        this.setupAnimationTriggers();
        
        if (this.debug) console.log('BarElevator: Initialization complete');
    }
    
    createDailyChart() {
        const ctx = document.getElementById('dailyChart');
        if (!ctx) {
            if (this.debug) console.error('BarElevator: dailyChart canvas not found');
            return;
        }
        
        // Prepare data based on whether report results are available
        let labels, countData, revenueData;
        @if(isset($overall_stats))
            labels = ['Total Inquiries', 'Completed', 'Total Assessments', 'Total Revenue'];
            countData = [
                {{ $overall_stats['total'] ?? 0 }},
                {{ $overall_stats['completed'] ?? 0 }},
                {{ $assessments_count ?? 0 }},
                null // Revenue goes to second dataset
            ];
            revenueData = [null, null, null, {{ $total_fees ?? 0 }}];
        @else
            labels = ['Total Inquiries', 'Completed', 'Total Assessments', 'Total Revenue'];
            countData = [
                {{ \App\Models\Inquiry::today()->count() }},
                {{ \App\Models\Inquiry::today()->where('status', 'completed')->count() }},
                {{ \App\Models\Assessment::whereDate('created_at', today())->count() }},
                null
            ];
            revenueData = [null, null, null, {{ \App\Models\Assessment::whereDate('created_at', today())->sum('fees') ?: 0 }}];
        @endif
        
        try {
            this.charts.daily = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Volume (Counts)',
                            data: countData,
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.7)',    // Total Inquiries
                                'rgba(40, 167, 69, 0.7)',     // Completed
                                'rgba(23, 162, 184, 0.7)',    // Total Assessments
                                'rgba(0, 0, 0, 0)'            // Hidden
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(40, 167, 69, 1)',
                                'rgba(23, 162, 184, 1)',
                                'rgba(0, 0, 0, 0)'
                            ],
                            borderWidth: 2,
                            borderRadius: 8,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Revenue (₱)',
                            data: revenueData,
                            backgroundColor: 'rgba(255, 193, 7, 0.7)',
                            borderColor: 'rgba(255, 193, 7, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: getThemeColors().text,
                                usePointStyle: true,
                                padding: 20,
                                font: { size: 12, weight: '600' }
                            }
                        },
                        tooltip: {
                            backgroundColor: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1d21' : 'rgba(255, 255, 255, 0.9)',
                            titleColor: document.documentElement.getAttribute('data-theme') === 'dark' ? '#fff' : '#333',
                            bodyColor: document.documentElement.getAttribute('data-theme') === 'dark' ? '#adb5bd' : '#666',
                            borderColor: getThemeColors().border,
                            borderWidth: 1,
                            padding: 12,
                            boxPadding: 6,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed.y !== null) {
                                        if (context.datasetIndex === 1 || context.label === 'Total Revenue') {
                                            label += '₱' + context.parsed.y.toLocaleString(undefined, {minimumFractionDigits: 2});
                                        } else {
                                            label += context.parsed.y.toLocaleString();
                                        }
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Volume Count',
                                color: getThemeColors().text,
                                font: { weight: 'bold' }
                            },
                            grid: {
                                color: getThemeColors().grid,
                                drawBorder: false
                            },
                            ticks: {
                                color: getThemeColors().text,
                                font: { size: 11 }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Revenue (₱)',
                                color: '#ffc107',
                                font: { weight: 'bold' }
                            },
                            grid: {
                                drawOnChartArea: false, // only want the grid lines for one axis
                            },
                            ticks: {
                                color: '#ffc107',
                                font: { size: 11 },
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: getThemeColors().text,
                                font: { size: 11, weight: '500' }
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    plugins: [{
                        id: 'barElevator',
                        afterDraw: (chart) => {
                            if (chart.elevateBars) {
                                this.elevateChartBars(chart);
                                chart.elevateBars = false;
                            }
                        }
                    }]
                }
            });
            
            if (this.debug) console.log('BarElevator: Daily chart created');
            
            setTimeout(() => {
                this.elevateChartBars(this.charts.daily);
            }, 300);
            
        } catch (error) {
            if (this.debug) console.error('BarElevator: Error creating daily chart:', error);
        }
    }
    
    createSectionChart() {
        const ctx = document.getElementById('sectionChart');
        if (!ctx) {
            if (this.debug) console.error('BarElevator: sectionChart canvas not found');
            return;
        }
        
        // Prepare section data based on whether report results are available
        let sectionLabels = [];
        let sectionData = [];
        @if(isset($category_stats))
            // Use report data for sections
            @php
                $sectionTotals = [];
                foreach($category_stats as $code => $stats) {
                    if($stats['total'] > 0) {  // Only include sections with data
                        $sectionName = $stats['section'];
                        if(!isset($sectionTotals[$sectionName])) {
                            $sectionTotals[$sectionName] = 0;
                        }
                        $sectionTotals[$sectionName] += $stats['total'];
                    }
                }
            @endphp
            
            @foreach($sectionTotals as $sectionName => $total)
                sectionLabels.push('{{ $sectionName }}');
                sectionData.push({{ $total }});
            @endforeach
        @else
            // Use default data with full section names (without 'SECTION' word)
            sectionLabels = ['AGGREGATE AND CORRECTION', 'ORIGINAL AND OTHER SURVEYS', 'SURVEYS AND CONTROL', 'LAND EVALUATION'];
            sectionData = [2, 1, 1, 0];
        @endif
        
        const sectionChartData = {
            labels: sectionLabels.length > 0 ? sectionLabels : ['No Data'],
            datasets: [{
                label: 'Inquiries per Section',
                data: sectionData.length > 0 ? sectionData : [0],
                backgroundColor: [
                    'rgba(231, 76, 60, 0.7)',
                    'rgba(52, 152, 219, 0.7)',
                    'rgba(46, 204, 113, 0.7)',
                    'rgba(155, 89, 182, 0.7)',
                    'rgba(241, 196, 15, 0.7)',
                    'rgba(142, 68, 173, 0.7)',
                    'rgba(230, 126, 34, 0.7)',
                    'rgba(26, 188, 156, 0.7)'
                ],
                borderColor: [
                    'rgba(231, 76, 60, 1)',
                    'rgba(52, 152, 219, 1)',
                    'rgba(46, 204, 113, 1)',
                    'rgba(155, 89, 182, 1)',
                    'rgba(241, 196, 15, 1)',
                    'rgba(142, 68, 173, 1)',
                    'rgba(230, 126, 34, 1)',
                    'rgba(26, 188, 156, 1)'
                ],
                borderWidth: 1
            }]
        };
        
        try {
            this.charts.section = new Chart(ctx, {
                type: 'bar',
                data: sectionChartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Hide for sections as labels are on X axis
                        },
                        tooltip: {
                            backgroundColor: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1d21' : 'rgba(255, 255, 255, 0.9)',
                            titleColor: document.documentElement.getAttribute('data-theme') === 'dark' ? '#fff' : '#333',
                            bodyColor: document.documentElement.getAttribute('data-theme') === 'dark' ? '#adb5bd' : '#666',
                            borderColor: getThemeColors().border,
                            borderWidth: 1,
                            padding: 12,
                            boxPadding: 6,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    return 'Total inquiries: ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: getThemeColors().grid,
                                drawBorder: false
                            },
                            ticks: {
                                color: getThemeColors().text,
                                precision: 0
                            },
                            title: {
                                display: true,
                                text: 'Number of Inquiries',
                                color: getThemeColors().text,
                                font: { weight: 'bold' }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: getThemeColors().text,
                                font: { size: 10, weight: '500' },
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                    borderRadius: 8,
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    // Custom plugin for bar elevation
                    plugins: [{
                        id: 'barElevator',
                        afterDraw: (chart) => {
                            if (chart.elevateBars) {
                                this.elevateChartBars(chart);
                                chart.elevateBars = false;
                            }
                        }
                    }]
                }
            });
            
            if (this.debug) console.log('BarElevator: Section chart created');
            
            // Trigger initial elevation
            setTimeout(() => {
                this.elevateChartBars(this.charts.section);
            }, 600);
            
        } catch (error) {
            if (this.debug) console.error('BarElevator: Error creating section chart:', error);
        }
    }
    
    createRevenueChart() {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) {
            if (this.debug) console.log('BarElevator: revenueChart canvas not found, skipping creation.');
            return;
        }
        
        // Prepare revenue data based on whether report results are available
        let labels = [];
        let data = [];
        @if(isset($assessments))
            // Group assessments by date to show revenue trend
            @php
                $revenueByDate = [];
                foreach($assessments as $assessment) {
                    $date = \Carbon\Carbon::parse($assessment->assessment_date)->format('M j');
                    if(!isset($revenueByDate[$date])) {
                        $revenueByDate[$date] = 0;
                    }
                    $revenueByDate[$date] += $assessment->fees;
                }
            @endphp
            
            @foreach($revenueByDate as $date => $revenue)
                labels.push('{{ $date }}');
                data.push({{ $revenue }});
            @endforeach
        @else
            // Use default data
            labels = ['Jan 1', 'Jan 2', 'Jan 3', 'Jan 4', 'Jan 5'];
            data = [0, 0, 0, 0, 0];
        @endif
        
        const revenueData = {
            labels: labels.length > 0 ? labels : ['No Data'],
            datasets: [{
                label: 'Daily Revenue (₱)',
                data: data.length > 0 ? data : [0],
                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: 'rgba(40, 167, 69, 0.9)',
            }]
        };
        
        try {
            this.charts.revenue = new Chart(ctx, {
                type: 'bar',
                data: revenueData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: getThemeColors().text,
                                usePointStyle: true,
                                font: { weight: 'bold' }
                            }
                        },
                        tooltip: {
                            backgroundColor: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1a1d21' : 'rgba(255, 255, 255, 0.9)',
                            titleColor: document.documentElement.getAttribute('data-theme') === 'dark' ? '#fff' : '#333',
                            bodyColor: document.documentElement.getAttribute('data-theme') === 'dark' ? '#adb5bd' : '#666',
                            borderColor: getThemeColors().border,
                            borderWidth: 1,
                            padding: 12,
                            boxPadding: 6,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: ₱' + context.parsed.y.toLocaleString(undefined, {minimumFractionDigits: 2});
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: getThemeColors().grid,
                                drawBorder: false
                            },
                            ticks: {
                                color: getThemeColors().text,
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            },
                            title: {
                                display: true,
                                text: 'Revenue (Philippine Peso)',
                                color: getThemeColors().text,
                                font: { weight: 'bold' }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: getThemeColors().text,
                                font: { weight: '500' }
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    // Custom plugin for bar elevation
                    plugins: [{
                        id: 'barElevator',
                        afterDraw: (chart) => {
                            if (chart.elevateBars) {
                                this.elevateChartBars(chart);
                                chart.elevateBars = false;
                            }
                        }
                    }]
                }
            });
            
            if (this.debug) console.log('BarElevator: Revenue chart created');
            
            // Trigger initial elevation
            setTimeout(() => {
                this.elevateChartBars(this.charts.revenue);
            }, 900);
            
        } catch (error) {
            if (this.debug) console.error('BarElevator: Error creating revenue chart:', error);
        }
    }
    
    elevateChartBars(chart) {
        if (!chart) return;
        
        if (this.debug) console.log('BarElevator: Elevating bars for chart');
        
        // Get all bar elements
        const meta = chart.getDatasetMeta(0);
        if (!meta.data) return;
        
        // Apply elevation animation to each bar with staggered timing for uniform bottom-to-top effect
        meta.data.forEach((bar, index) => {
            if (bar && bar.element) {
                // Calculate stagger delay based on index for uniform sequence
                const delay = index * 150; // 150ms delay between each bar
                
                // Add elevation class to the bar element after delay
                setTimeout(() => {
                    // Apply a transformation to make the bar grow from the bottom
                    const originalHeight = bar.height;
                    bar.height = 0; // Start with zero height
                    
                    // Animate the height increase
                    let start;
                    const duration = 800;
                    
                    function step(timestamp) {
                        if (!start) start = timestamp;
                        const progress = Math.min((timestamp - start) / duration, 1);
                        
                        // Ease-out function for smooth animation
                        const easedProgress = 1 - Math.pow(1 - progress, 2);
                        bar.height = originalHeight * easedProgress;
                        
                        chart.render();
                        
                        if (progress < 1) {
                            window.requestAnimationFrame(step);
                        }
                    }
                    
                    window.requestAnimationFrame(step);
                }, delay);
            }
        });
    }
    
    setupAnimationTriggers() {
        // Page load trigger
        if (this.debug) console.log('BarElevator: Setting up animation triggers');
        
        // Simulate dropdown actions (you can connect this to actual dropdown events)
        this.setupDropdownTriggers();
        
        // Set up periodic refresh trigger
        setInterval(() => {
            if (this.debug) console.log('BarElevator: Periodic refresh trigger');
            this.triggerAllElevations();
        }, 30000); // Every 30 seconds
    }
    
    setupDropdownTriggers() {
        // This would be connected to actual dropdown change events
        // For now, we'll simulate it
        document.addEventListener('click', (e) => {
            // Simulate dropdown action trigger
            if (e.target.closest('.dropdown') || e.target.closest('select')) {
                if (this.debug) console.log('BarElevator: Dropdown action detected');
                this.triggerAllElevations();
            }
        });
    }
    
    triggerAllElevations() {
        if (this.debug) console.log('BarElevator: Triggering all bar elevations');
        
        Object.values(this.charts).forEach(chart => {
            if (chart) {
                // Update theme colors before rendering
                const colors = getThemeColors();
                chart.options.scales.y.ticks.color = colors.text;
                chart.options.scales.y.grid.color = colors.grid;
                chart.options.scales.x.ticks.color = colors.text;
                
                chart.elevateBars = true;
                chart.update();
            }
        });
    }
    
    // Public method to trigger elevation manually
    elevateAll() {
        this.triggerAllElevations();
    }

    setupThemeObserver() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'data-theme') {
                    if (this.debug) console.log('BarElevator: Theme change detected, updating charts');
                    this.triggerAllElevations();
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing BarElevator');
        
    // Initialize the bar elevator system
    window.barElevator = new BarElevator();
    window.barElevator.setupThemeObserver();
        
    // Make it globally accessible for manual triggering
    window.triggerBarElevation = () => {
        if (window.barElevator) {
            window.barElevator.elevateAll();
        }
    };
        
    console.log('BarElevator initialized and ready');
});
    
// Trigger animations when the page is shown again (e.g., after clicking sidebar)
window.addEventListener('pageshow', function() {
    // Small timeout to ensure DOM is fully loaded
    setTimeout(() => {
        if (window.barElevator) {
            // Trigger animations again
            window.barElevator.elevateAll();
        }
    }, 100);
});
</script>
@endsection

@section('styles')
<style>
    .card {
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .card-header {
        font-weight: 600;
        letter-spacing: 0.5px;
        border-bottom: none;
        padding: 1rem 1.25rem;
    }

    .export-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-width: 2px;
        border-radius: 15px;
        padding: 1.5rem 1rem;
        background: white;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    [data-theme="dark"] .export-btn {
        background: var(--dark-surface) !important;
        border-color: var(--dark-border) !important;
    }
    
    .export-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .export-btn i {
        transition: transform 0.3s ease;
    }
    
    .export-btn:hover i {
        transform: scale(1.2);
    }
    
    .export-btn-pdf:hover { background-color: #fff5f5; border-color: #dc3545; color: #dc3545; }
    .export-btn-excel:hover { background-color: #f6fff9; border-color: #198754; color: #198754; }
    .export-btn-print:hover { background-color: #f0f7ff; border-color: #0d6efd; color: #0d6efd; }

    [data-theme="dark"] .export-btn-pdf:hover { background-color: rgba(220, 53, 69, 0.1) !important; }
    [data-theme="dark"] .export-btn-excel:hover { background-color: rgba(25, 135, 84, 0.1) !important; }
    [data-theme="dark"] .export-btn-print:hover { background-color: rgba(13, 110, 253, 0.1) !important; }

    /* Professional Stat Blocks */
    .stat-block {
        background: white;
        border-radius: 12px;
        padding: 24px 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

    .stat-block:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1);
    }

    /* Color Variants */
    .stat-block-primary { border-left-color: #0d6efd; background: linear-gradient(to right, rgba(13, 110, 253, 0.02), white); }
    .stat-block-success { border-left-color: #198754; background: linear-gradient(to right, rgba(25, 135, 84, 0.02), white); }
    .stat-block-info { border-left-color: #0dcaf0; background: linear-gradient(to right, rgba(13, 202, 240, 0.02), white); }
    .stat-block-warning { border-left-color: #ffc107; background: linear-gradient(to right, rgba(255, 193, 7, 0.02), white); }

    [data-theme="dark"] .stat-block-primary { background: linear-gradient(to right, rgba(13, 110, 253, 0.05), var(--dark-surface-secondary)) !important; }
    [data-theme="dark"] .stat-block-success { background: linear-gradient(to right, rgba(25, 135, 84, 0.05), var(--dark-surface-secondary)) !important; }
    [data-theme="dark"] .stat-block-info { background: linear-gradient(to right, rgba(13, 202, 240, 0.05), var(--dark-surface-secondary)) !important; }
    [data-theme="dark"] .stat-block-warning { background: linear-gradient(to right, rgba(255, 193, 7, 0.05), var(--dark-surface-secondary)) !important; }

    .stat-block i {
        position: absolute;
        right: 15px;
        bottom: 15px;
        font-size: 2.2rem;
        opacity: 0.8;
        transition: all 0.4s ease;
        z-index: 1;
    }

    [data-theme="dark"] .stat-block i {
        opacity: 0.9;
        filter: brightness(1.2);
    }

    .stat-block:hover i {
        opacity: 1;
        transform: scale(1.2) rotate(-5deg);
    }

    /* Fixed Dark Mode Text Visibility */
    [data-theme="dark"] .text-muted { color: #adb5bd !important; }
    [data-theme="dark"] .form-label { color: var(--dark-on-surface) !important; }
    [data-theme="dark"] .card { 
        background-color: var(--dark-surface) !important; 
        border: 1px solid var(--dark-border) !important; 
    }
    [data-theme="dark"] .card-body { 
        background-color: var(--dark-surface) !important; 
    }
    [data-theme="dark"] .table { color: var(--dark-on-surface) !important; border-color: var(--dark-border) !important; }
    [data-theme="dark"] .table-light { background-color: var(--dark-surface-secondary) !important; color: var(--dark-on-surface) !important; }
    [data-theme="dark"] .form-control, [data-theme="dark"] .form-select { background-color: var(--dark-surface-secondary) !important; border-color: var(--dark-border) !important; color: var(--dark-on-surface) !important; }
    
    /* Ensure chart background is also dark */
    [data-theme="dark"] .chart-container-wrapper {
        background-color: var(--dark-surface-secondary) !important;
        border-color: var(--dark-border) !important;
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

    .gradient-header-green {
        background: linear-gradient(135deg, var(--denr-green) 0%, var(--denr-dark) 100%) !important;
    }

    .gradient-header-info {
        background: linear-gradient(135deg, #17a2b8 0%, #0dcaf0 100%) !important;
    }

    .gradient-header-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
    }
    
    /* Chart container styling */
    canvas {
        width: 100% !important;
        height: 300px !important;
    }
</style>
@endsection