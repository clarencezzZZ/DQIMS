<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>DENR Report- {{ $date_range['start'] }} to {{ $date_range['end'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 16pt;
        }
        
        .header p {
            margin: 3px 0;
            font-size: 9pt;
        }
        
        .report-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #0d6efd;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .stats-row {
            display: table-row;
        }
        
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }
        
        .stat-label {
            font-size: 8pt;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 18pt;
            font-weight: bold;
            color: #0d6efd;
        }
        
        .section-title {
            background-color: #0d6efd;
            color: white;
            padding: 8px 12px;
            font-size: 11pt;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table th {
            background-color: #e9ecef;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #dee2e6;
            font-size: 9pt;
        }
        
        table td {
            padding: 6px 8px;
            border: 1px solid #dee2e6;
            font-size: 9pt;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
        }
        
        .badge-success { background-color: #198754; color: white; }
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-info { background-color: #0dcaf0; color: #000; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 8pt;
            color: #6c757d;
        }
        
        .no-break {
            page-break-inside: avoid;
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
            position: relative;
        }
        
        .bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
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
        .bar-purple { background: linear-gradient(90deg, #6f42c1, #5f37a8); }
        .bar-orange { background: linear-gradient(90deg, #fd7e14, #e8590c); }
        .bar-teal { background: linear-gradient(90deg, #20c997, #17a589); }
    </style>
</head>
<body>
    <div class="header">
        <h2>Republic of the Philippines</h2>
        <p>DEPARTMENT OF ENVIRONMENT AND NATURAL RESOURCES</p>
        <p>Regional Office No. 4A (CALABARZON)</p>
        <h3 style="margin-top: 15px;">Queueing & Inquiry Management System Report</h3>
    </div>
    
    <div class="report-info">
        <strong>Report Period:</strong> {{ \Carbon\Carbon::parse($date_range['start'])->format('F d, Y') }} to {{ \Carbon\Carbon::parse($date_range['end'])->format('F d, Y') }}<br>
        <strong>Date Generated:</strong> {{ \Carbon\Carbon::now()->format('F d, Y h:i A') }}
    </div>
    
    <!-- Overall Statistics -->
    <div class="section-title no-break">Overall Statistics</div>
    <div class="stats-grid no-break">
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-label">Total Inquiries</div>
                <div class="stat-value">{{ $overall_stats['total'] }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Completed</div>
                <div class="stat-value" style="color: #198754;">{{ $overall_stats['completed'] }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Waiting</div>
                <div class="stat-value" style="color: #ffc107;">{{ $overall_stats['waiting'] }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Skipped</div>
                <div class="stat-value" style="color: #dc3545;">{{ $overall_stats['skipped'] }}</div>
            </div>
        </div>
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-label">Serving</div>
                <div class="stat-value" style="color: #0dcaf0;">{{ $overall_stats['serving'] ?? 0 }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Total Assessments</div>
                <div class="stat-value" style="color: #6f42c1;">{{ $assessments_count ?? 0 }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value" style="color: #fd7e14;">₱{{ number_format($total_fees ?? 0, 2) }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Avg Processing Time</div>
                <div class="stat-value" style="color: #20c997; font-size: 14pt;">{{ round($average_processing_time ?? 0) }}<span style="font-size: 9pt;">min</span></div>
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
    
    <div class="chart-container no-break">
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
    <div class="section-title no-break">Category Statistics</div>
    <table class="no-break">
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
                            <span class="badge badge-success">{{ $stats['completed'] }} Completed</span>
                            @if($stats['waiting'] > 0)
                                <span class="badge badge-warning">{{ $stats['waiting'] }} Waiting</span>
                            @endif
                            @if($stats['skipped'] > 0)
                                <span class="badge badge-danger">{{ $stats['skipped'] }} Skipped</span>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    
    <!-- Inquiry Details -->
    <div class="section-title no-break">Inquiry Details</div>
    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="12%">Queue Number</th>
                <th width="20%">Name</th>
                <th width="20%">Category</th>
                <th width="15%">Request Type</th>
                <th width="13%">Status</th>
                <th width="15%">Date</th>
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
                                <span class="badge badge-success">Completed</span>
                                @break
                            @case('waiting')
                                <span class="badge badge-warning">Waiting</span>
                                @break
                            @case('serving')
                                <span class="badge badge-info">Serving</span>
                                @break
                            @case('skipped')
                                <span class="badge badge-danger">Skipped</span>
                                @break
                            @default
                                <span class="badge badge-secondary">{{ ucfirst($inquiry->status) }}</span>
                        @endswitch
                    </td>
                    <td>{{ \Carbon\Carbon::parse($inquiry->date)->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>This is a computer-generated report. No signature is required.</p>
        <p>Generated on {{ \Carbon\Carbon::now()->format('F d, Y \a\t h:i A') }}</p>
    </div>
</body>
</html>
