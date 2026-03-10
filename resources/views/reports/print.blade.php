<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Report - DENR Queueing System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: A4;
                margin: 15mm;
            }
            
            body {
                font-size: 9pt;
                line-height: 1.4;
            }
            
            .no-print {
                display: none !important;
            }
            
            .card {
                box-shadow: none !important;
                border: none !important;
            }
            
            .badge {
                padding: 3px 8px;
                font-size: 8pt;
            }
            
            table th, table td {
                padding: 4px 6px !important;
                font-size: 8pt !important;
            }
            
            h2 { font-size: 14pt !important; }
            h3 { font-size: 12pt !important; }
            h4 { font-size: 11pt !important; }
            h5 { font-size: 10pt !important; }
            h6 { font-size: 9pt !important; }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .report-info {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #0d6efd;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .stat-label {
            font-size: 8pt;
            text-transform: uppercase;
            opacity: 0.9;
        }
        
        .stat-value {
            font-size: 24pt;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .section-title {
            background-color: #0d6efd;
            color: white;
            padding: 8px 12px;
            font-size: 10pt;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        table th {
            background-color: #e9ecef !important;
            font-weight: bold !important;
        }
        
        .badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        /* Bar Chart Styles */
        .chart-container {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        
        .chart-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .bar-chart {
            width: 100%;
        }
        
        .bar-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .bar-label {
            display: table-cell;
            width: 30%;
            vertical-align: middle;
            font-size: 8pt;
            font-weight: bold;
            color: #495057;
            padding-right: 10px;
            text-align: right;
        }
        
        .bar-wrapper {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
        }
        
        .bar-background {
            width: 100%;
            height: 25px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .bar-fill {
            height: 100%;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 8px;
            color: white;
            font-size: 8pt;
            font-weight: bold;
        }
        
        .bar-value {
            display: inline-block;
            margin-left: 10px;
            font-size: 9pt;
            font-weight: bold;
            color: #495057;
            min-width: 40px;
            text-align: left;
        }
        
        /* Bar Colors */
        .bar-primary { background: linear-gradient(90deg, #0d6efd, #0b5ed7); }
        .bar-success { background: linear-gradient(90deg, #198754, #157347); }
        .bar-warning { background: linear-gradient(90deg, #ffc107, #ffca2c); }
        .bar-info { background: linear-gradient(90deg, #0dcaf0, #0aacd8); }
        .bar-danger { background: linear-gradient(90deg, #dc3545, #bb2d3b); }
    </style>
</head>
<body>
    <!-- Print Button -->
    <button class="btn btn-primary btn-lg print-button no-print" onclick="window.print()">
        <i class="bi bi-printer"></i> Print Report
    </button>
    
    <!-- Header -->
    <div class="header">
        <h2>Republic of the Philippines</h2>
        <p>DEPARTMENT OF ENVIRONMENT AND NATURAL RESOURCES</p>
        <p>Regional Office No. 4A (CALABARZON)</p>
        <h3 style="margin-top: 15px;">Queueing & Inquiry Management System Report</h3>
    </div>
    
    <!-- Report Info -->
    <div class="report-info">
        <strong>Report Period:</strong> {{ \Carbon\Carbon::parse($date_range['start'])->format('F d, Y') }} to {{ \Carbon\Carbon::parse($date_range['end'])->format('F d, Y') }}<br>
        <strong>Generated:</strong> {{ \Carbon\Carbon::now()->format('F d, Y h:i A') }}
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Total Inquiries</div>
                <div class="stat-value">{{ $overall_stats['total'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #198754 0%, #146c43 100%);">
                <div class="stat-label">Completed</div>
                <div class="stat-value">{{ $overall_stats['completed'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%); color: #000;">
                <div class="stat-label">Waiting</div>
                <div class="stat-value">{{ $overall_stats['waiting'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);">
                <div class="stat-label">Skipped</div>
                <div class="stat-value">{{ $overall_stats['skipped'] }}</div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);">
                <div class="stat-label">Total Assessments</div>
                <div class="stat-value">{{ $assessments_count ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #fd7e14 0%, #e8590c 100%);">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">₱{{ number_format($total_fees ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #20c997 0%, #17a589 100%);">
                <div class="stat-label">Avg Processing Time</div>
                <div class="stat-value">{{ round($average_processing_time ?? 0) }}<small>min</small></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);">
                <div class="stat-label">Serving</div>
                <div class="stat-value">{{ $overall_stats['serving'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    
    <!-- Status Distribution Bar Chart -->
    @php
        $maxStatus = max([
            $overall_stats['total'] ?? 1,
            $overall_stats['completed'] ?? 1,
            $overall_stats['waiting'] ?? 1,
            $overall_stats['serving'] ?? 1,
            $overall_stats['skipped'] ?? 1
        ]);
    @endphp
    
    <div class="chart-container">
        <div class="chart-title">📊 Inquiry Status Distribution</div>
        <div class="bar-chart">
            <div class="bar-row">
                <div class="bar-label">Total Inquiries</div>
                <div class="bar-wrapper">
                    <div class="bar-background">
                        <div class="bar-fill bar-primary" style="width: {{ $overall_stats['total'] > 0 ? ($overall_stats['total'] / $maxStatus * 100) : 0 }}%;">
                            {{ $overall_stats['total'] }}
                        </div>
                    </div>
                </div>
                <div class="bar-value">{{ $overall_stats['total'] }}</div>
            </div>
            
            <div class="bar-row">
                <div class="bar-label">Completed</div>
                <div class="bar-wrapper">
                    <div class="bar-background">
                        <div class="bar-fill bar-success" style="width: {{ $overall_stats['total'] > 0 ? ($overall_stats['completed'] / $maxStatus * 100) : 0 }}%;">
                            {{ $overall_stats['completed'] }}
                        </div>
                    </div>
                </div>
                <div class="bar-value">{{ $overall_stats['completed'] }}</div>
            </div>
            
            <div class="bar-row">
                <div class="bar-label">Waiting</div>
                <div class="bar-wrapper">
                    <div class="bar-background">
                        <div class="bar-fill bar-warning" style="width: {{ $overall_stats['total'] > 0 ? ($overall_stats['waiting'] / $maxStatus * 100) : 0 }}%;">
                            {{ $overall_stats['waiting'] }}
                        </div>
                    </div>
                </div>
                <div class="bar-value">{{ $overall_stats['waiting'] }}</div>
            </div>
            
            <div class="bar-row">
                <div class="bar-label">Serving</div>
                <div class="bar-wrapper">
                    <div class="bar-background">
                        <div class="bar-fill bar-info" style="width: {{ $overall_stats['total'] > 0 ? (($overall_stats['serving'] ?? 0) / $maxStatus * 100) : 0 }}%;">
                            {{ $overall_stats['serving'] ?? 0 }}
                        </div>
                    </div>
                </div>
                <div class="bar-value">{{ $overall_stats['serving'] ?? 0 }}</div>
            </div>
            
            <div class="bar-row">
                <div class="bar-label">Skipped</div>
                <div class="bar-wrapper">
                    <div class="bar-background">
                        <div class="bar-fill bar-danger" style="width: {{ $overall_stats['total'] > 0 ? ($overall_stats['skipped'] / $maxStatus * 100) : 0 }}%;">
                            {{ $overall_stats['skipped'] }}
                        </div>
                    </div>
                </div>
                <div class="bar-value">{{ $overall_stats['skipped'] }}</div>
            </div>
        </div>
    </div>
    
    <!-- Category Statistics -->
    <div class="section-title">Category Statistics</div>
    <table class="table table-bordered mb-4">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="30%">Category</th>
                <th width="25%">Section</th>
                <th width="15%">Total</th>
                <th width="25%">Status Breakdown</th>
            </tr>
        </thead>
        <tbody>
            @php $counter= 1; @endphp
            @foreach($category_stats as $code => $stats)
                @if($stats['total'] > 0)
                    <tr>
                        <td>{{ $counter++ }}</td>
                        <td><strong>{{ $stats['name'] }}</strong></td>
                        <td>{{ $stats['section'] }}</td>
                        <td><strong>{{ $stats['total'] }}</strong></td>
                        <td>
                            <span class="badge bg-success">{{ $stats['completed'] }} Completed</span>
                            @if($stats['waiting'] > 0)
                                <span class="badge bg-warning">{{ $stats['waiting'] }} Waiting</span>
                            @endif
                            @if($stats['skipped'] > 0)
                                <span class="badge bg-danger">{{ $stats['skipped'] }} Skipped</span>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    
    <!-- Inquiry Details -->
    <div class="section-title">Inquiry Details</div>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="12%">Queue Number</th>
                <th width="20%">Name</th>
                <th width="20%">Category</th>
                <th width="13%">Request Type</th>
                <th width="10%">Status</th>
                <th width="20%">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inquiries as $index => $inquiry)
                <tr>
                    <td>{{ $index +1 }}</td>
                    <td>{{ $inquiry->queue_number }}</td>
                    <td>{{ $inquiry->name }}</td>
                    <td>{{ $inquiry->category->name ?? 'N/A' }}</td>
                    <td>{{ $inquiry->request_type }}</td>
                    <td>
                        @switch($inquiry->status)
                            @case('completed')
                                <span class="badge bg-success">Completed</span>
                                @break
                            @case('waiting')
                                <span class="badge bg-warning">Waiting</span>
                                @break
                            @case('serving')
                                <span class="badge bg-info">Serving</span>
                                @break
                            @case('skipped')
                                <span class="badge bg-danger">Skipped</span>
                                @break
                            @default
                                <span class="badge bg-secondary">{{ ucfirst($inquiry->status) }}</span>
                        @endswitch
                    </td>
                    <td>{{ \Carbon\Carbon::parse($inquiry->date)->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="text-center mt-4 pt-3 border-top no-print">
        <p class="text-muted small">This is a computer-generated report. No signature is required.</p>
        <p class="text-muted small">Generated on {{ \Carbon\Carbon::now()->format('F d, Y \a\t h:i A') }}</p>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-trigger print dialog when page loads (optional)
        // window.addEventListener('load', function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // });
    </script>
</body>
</html>
