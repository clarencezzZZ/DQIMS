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
                        <input type="date" name="date_from" class="form-control" value="{{ old('date_from', $date_range['start']) }}" id="dateFromField" disabled>
                    @else
                        <input type="date" name="date_from" class="form-control" value="{{ old('date_from', request('date_from', date('Y-m-d'))) }}" id="dateFromField" disabled>
                    @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    @if(isset($date_range))
                        <input type="date" name="date_to" class="form-control" value="{{ old('date_to', $date_range['end']) }}" id="dateToField" disabled>
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
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="" {{ old('status', request('status')) == '' ? 'selected' : '' }}>All Status</option>
                        <option value="waiting" {{ old('status', request('status')) == 'waiting' ? 'selected' : '' }}>Waiting</option>
                        <option value="serving" {{ old('status', request('status')) == 'serving' ? 'selected' : '' }}>Serving</option>
                        <option value="completed" {{ old('status', request('status')) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="skipped" {{ old('status', request('status')) == 'skipped' ? 'selected' : '' }}>Skipped</option>
                        <option value="forwarded" {{ old('status', request('status')) == 'forwarded' ? 'selected' : '' }}>Forwarded</option>
                    </select>
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
                    <button type="submit" class="btn btn-info text-white">
                        <i class="bi bi-search"></i> Generate Report
                    </button>
                    <button type="button" class="btn btn-secondary ms-2" onclick="resetFilters()">
                        <i class="bi bi-arrow-clockwise"></i> Reset Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Results Display -->
    @if(isset($overall_stats))
        <div class="alert alert-success mb-4">
            <h5 class="mb-1"><i class="bi bi-check-circle"></i> Report Generated Successfully</h5>
            <p class="mb-0">Showing data from <strong>{{ $date_range['start'] }}</strong> to <strong>{{ $date_range['end'] }}</strong></p>
        </div>
        
        <!-- Report Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 mb-1">Total Inquiries</h6>
                        <h3 class="mb-0">{{ $overall_stats['total'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 mb-1">Waiting</h6>
                        <h3 class="mb-0">{{ $overall_stats['waiting'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 mb-1">Serving</h6>
                        <h3 class="mb-0">{{ $overall_stats['serving'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 mb-1">Completed</h6>
                        <h3 class="mb-0">{{ $overall_stats['completed'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 mb-1">Skipped</h6>
                        <h3 class="mb-0">{{ $overall_stats['skipped'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 mb-1">Revenue</h6>
                        <h3 class="mb-0">₱{{ number_format($total_fees ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Statistics Table -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Category Statistics</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Section</th>
                                <th>Waiting</th>
                                <th>Serving</th>
                                <th>Completed</th>
                                <th>Skipped</th>
                                <th>Forwarded</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category_stats as $code => $stats)
                                @if($stats['total'] > 0)  <!-- Only show categories with data -->
                                    <tr>
                                        <td>{{ $stats['name'] }}</td>
                                        <td>{{ $stats['section'] }}</td>
                                        <td><span class="badge bg-warning">{{ $stats['waiting'] }}</span></td>
                                        <td><span class="badge bg-info">{{ $stats['serving'] }}</span></td>
                                        <td><span class="badge bg-success">{{ $stats['completed'] }}</span></td>
                                        <td><span class="badge bg-danger">{{ $stats['skipped'] }}</span></td>
                                        <td><span class="badge bg-secondary">{{ $stats['forwarded'] }}</span></td>
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
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-currency-dollar"></i> Revenue Statistics</h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="300"></canvas>
            </div>
        </div>

        <!-- Report Charts -->
        <!-- Bar Chart Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Report Statistics Overview</h5>
            </div>
            <div class="card-body">
                <canvas id="dailyChart" height="300"></canvas>
            </div>
        </div>

        <!-- Section-wise Statistics -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Section Statistics</h5>
            </div>
            <div class="card-body">
                <canvas id="sectionChart" height="300"></canvas>
            </div>
        </div>
    @else
        <!-- Default Stats (Today's stats) -->
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

        <!-- Bar Chart Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Daily Statistics Overview</h5>
            </div>
            <div class="card-body">
                <canvas id="dailyChart" height="300"></canvas>
            </div>
        </div>

        <!-- Section-wise Statistics -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Section Statistics</h5>
            </div>
            <div class="card-body">
                <canvas id="sectionChart" height="300"></canvas>
            </div>
        </div>
    @endif

    <!-- Export Options -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-download"></i> Export Data</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="#" onclick="exportReport('pdf')" class="btn btn-outline-danger w-100">
                        <i class="bi bi-file-pdf" style="font-size: 2rem;"></i>
                        <h5 class="mt-2">Export as PDF</h5>
                        <small class="text-muted">Download report as PDF</small>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="#" onclick="exportReport('excel')" class="btn btn-outline-success w-100">
                        <i class="bi bi-file-excel" style="font-size: 2rem;"></i>
                        <h5 class="mt-2">Export as Excel</h5>
                        <small class="text-muted">Download data as Excel spreadsheet</small>
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="#" onclick="exportReport('print')" target="_blank" class="btn btn-outline-primary w-100">
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

@section('scripts')
<script src="{{ asset('js/chart.js') }}"></script>
<script>
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

// Reset filters to default values
function resetFilters() {
    const form = document.getElementById('reportFilterForm');
    
    // Reset the form to default values
    form.reset();
    
    // Set report type back to daily (first option)
    const reportTypeSelect = form.querySelector('select[name="report_type"]');
    if (reportTypeSelect) {
        reportTypeSelect.value = 'daily';
    }
    
    // Reset and disable date fields to today's date
    const dateFromField = document.getElementById('dateFromField');
    const dateToField = document.getElementById('dateToField');
    
    if (dateFromField) {
        dateFromField.disabled = true;
        dateFromField.value = '{{ date('Y-m-d') }}';
    }
    
    if (dateToField) {
        dateToField.disabled = true;
        dateToField.value = '{{ date('Y-m-d') }}';
    }
    
    // Reset all select fields to their first option (empty/default)
    const selectFields = form.querySelectorAll('select');
    selectFields.forEach(select => {
        if (select.name !== 'report_type') { // Don't reset report_type as we already set it
            select.selectedIndex = 0;
        }
    });
    
    // Small delay to ensure DOM updates before triggering toggle
    setTimeout(() => {
        // Trigger the toggle function to set proper date values based on report type
        toggleDateFields();
        console.log('Filters reset to default values');
        
        // Reload the page to show default daily stats instead of report results
        window.location.href = '/reports';
    }, 10);
}
    
// Initialize date fields on page load
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for the form values to be populated from old() helper
    setTimeout(() => {
        // Check if we're displaying report results
        const hasReportResults = document.querySelector('.alert-success') !== null;
        
        // If we have report results, it means we just generated a report, so preserve the submitted dates
        // Otherwise, apply the date range logic based on report type
        if (!hasReportResults) {
            toggleDateFields();
        } else {
            // If we have report results, just ensure the fields are properly enabled/disabled
            // based on the selected report type, but don't recalculate the dates
            const reportType = document.querySelector('select[name="report_type"]').value;
            const dateFromField = document.getElementById('dateFromField');
            const dateToField = document.getElementById('dateToField');
            
            if (reportType === 'custom' && dateFromField && dateToField) {
                dateFromField.disabled = false;
                dateToField.disabled = false;
            } else if (dateFromField && dateToField) {
                dateFromField.disabled = true;
                dateToField.disabled = true;
            }
            // Preserve the dates from the form submission by not recalculating them
        }
    }, 100);
});

// Handle form submission to preserve dates when submitting
document.getElementById('reportFilterForm').addEventListener('submit', function(e) {
    // Only update dates if report type is not custom (to preserve user-entered custom dates)
    const reportTypeSelect = this.querySelector('select[name="report_type"]');
    const reportType = reportTypeSelect.value;
    
    if (reportType !== 'custom') {
        // For non-custom reports, ensure the dates are set according to report type
        const dateFromField = document.getElementById('dateFromField');
        const dateToField = document.getElementById('dateToField');
        
        if (dateFromField && dateToField) {
            // Update date fields based on report type, but only if they're disabled
            // (meaning they weren't manually entered by the user)
            if (dateFromField.disabled && dateToField.disabled) {
                toggleDateFields();
            }
        }
    }
    // Allow normal form submission
    // The dates will be preserved in the form submission
});

// Export report in different formats
function exportReport(format) {
    const formData = new FormData(document.getElementById('reportFilterForm'));
    const params = new URLSearchParams();
        
    // Collect all form values
    for (let [key, value] of formData.entries()) {
        if (value) {
            params.append(key, value);
        }
    }
        
    // Add CSRF token
    params.append('_token', document.querySelector('input[name="_token"]').value);
        
    // Build URL based on format
    let url;
    switch(format) {
        case 'pdf':
            url = '{{ route("reports.export-pdf") }}?' + params.toString();
            break;
        case 'excel':
            url = '{{ route("reports.export-excel") }}?' + params.toString();
            break;
        case 'print':
            url = '{{ route("reports.print") }}?' + params.toString();
            break;
        default:
            url = '{{ route("reports.export-pdf") }}?' + params.toString();
    }
        
    // Open the export URL in a new tab/window
    window.open(url, '_blank');
}

console.log('Chart.js script loaded');

// Check if Chart is available immediately
if (typeof Chart !== 'undefined') {
    console.log('Chart.js is available, version:', Chart.version);
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
        let labels, data;
        @if(isset($overall_stats))
            // Use report data - adjust to show Total Inquiries, Completed, Total Assessments, Total Revenue
            labels = ['Total Inquiries', 'Completed', 'Total Assessments', 'Total Revenue'];
            data = [
                {{ $overall_stats['total'] ?? 0 }},
                {{ $overall_stats['completed'] ?? 0 }},
                {{ $assessments_count ?? 0 }},
                {{ $total_fees ?? 0 }}
            ];
        @else
            // Use default today's data
            labels = ['Total Inquiries', 'Completed', 'Total Assessments', 'Total Revenue'];
            data = [
                {{ \App\Models\Inquiry::today()->count() }},
                {{ \App\Models\Inquiry::today()->where('status', 'completed')->count() }},
                {{ \App\Models\Assessment::whereDate('created_at', today())->count() }},
                {{ \App\Models\Assessment::whereDate('created_at', today())->sum('fees') ?: 0 }}
            ];
        @endif
        
        const dailyData = {
            labels: labels,
            datasets: [{
                label: @if(isset($overall_stats)) 'Report Statistics' @else 'Today\'s Statistics' @endif,
                data: data,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.6)',    // Total Inquiries
                    'rgba(40, 167, 69, 0.6)',     // Completed
                    'rgba(23, 162, 184, 0.6)',    // Total Assessments
                    'rgba(255, 193, 7, 0.6)'      // Total Revenue
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',      // Total Inquiries
                    'rgba(40, 167, 69, 1)',       // Completed
                    'rgba(23, 162, 184, 1)',      // Total Assessments
                    'rgba(255, 193, 7, 1)'        // Total Revenue
                ],
                borderWidth: 1
            }]
        };
        
        try {
            this.charts.daily = new Chart(ctx, {
                type: 'bar',
                data: dailyData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                callback: function(value, index) {
                                    // Format as currency if it's the revenue value (last in the data array)
                                    if (labels[index] === 'Total Revenue') {
                                        return '₱' + value.toLocaleString();
                                    }
                                    return value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
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
            
            if (this.debug) console.log('BarElevator: Daily chart created');
            
            // Trigger initial elevation
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
            // Use default data
            sectionLabels = ['ACS', 'OOSS', 'SCS', 'LES'];
            sectionData = [2, 1, 1, 0];
        @endif
        
        const sectionChartData = {
            labels: sectionLabels.length > 0 ? sectionLabels : ['No Data'],
            datasets: [{
                label: 'Inquiries per Section',
                data: sectionData.length > 0 ? sectionData : [0],
                backgroundColor: [
                    'rgba(231, 76, 60, 0.6)',
                    'rgba(52, 152, 219, 0.6)',
                    'rgba(46, 204, 113, 0.6)',
                    'rgba(155, 89, 182, 0.6)',
                    'rgba(241, 196, 15, 0.6)',
                    'rgba(142, 68, 173, 0.6)',
                    'rgba(230, 126, 34, 0.6)',
                    'rgba(26, 188, 156, 0.6)'
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
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
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
            if (this.debug) console.error('BarElevator: revenueChart canvas not found');
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
                backgroundColor: 'rgba(40, 167, 69, 0.6)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1
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
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
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
                chart.elevateBars = true;
                chart.update();
            }
        });
    }
    
    // Public method to trigger elevation manually
    elevateAll() {
        this.triggerAllElevations();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing BarElevator');
        
    // Initialize the bar elevator system
    window.barElevator = new BarElevator();
        
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
    }
    
    /* Individual Bar Grow Animation */
    .bar-grow {
        animation: barGrow 0.8s ease-out forwards;
    }
    
    @keyframes barGrow {
        0% {
            height: 0;
            opacity: 0.5;
        }
        100% {
            height: 100%;
            opacity: 1;
        }
    }
    
    /* Bar Elevation Animation */
    .bar-elevate {
        animation: barElevate 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
    
    @keyframes barElevate {
        0% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-15px);
        }
        100% {
            transform: translateY(0);
        }
    }
    
    /* Chart container styling */
    canvas {
        width: 100% !important;
        height: 300px !important;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-header h5 {
            font-size: 1.1rem;
        }
        
        canvas {
            height: 250px !important;
        }
    }
</style>
@endsection