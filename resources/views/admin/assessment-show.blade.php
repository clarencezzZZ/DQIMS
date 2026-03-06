@extends('layouts.app')

@section('title', 'View Assessment')

@section('content')
<div class="container print-container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                <div>
                    <h2 class="mb-1"><i class="bi bi-file-earmark-text text-success"></i> Assessment Details</h2>
                    <p class="text-muted mb-0">View assessment information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.assessments') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('admin.assessments.edit', $assessment) }}" class="btn btn-outline-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-primary">
                        <i class="bi bi-printer"></i> Print
                    </button>
                </div>
            </div>

            <!-- Assessment Form -->
            <div class="card shadow" id="assessment-print">
                <div class="card-body p-4">
                    <!-- Office Header - Top Center -->
                    <div class="text-center mb-3">
                        <h5 class="fw-bold mb-1" style="font-size: 11pt;">Republic of the Philippines</h5>
                        <h5 class="fw-bold mb-1" style="font-size: 11pt;">DEPARTMENT OF ENVIRONMENT AND NATURAL RESOURCES</h5>
                        <h6 class="mb-1" style="font-size: 10pt;">OFFICE Regional Office No. 4A (CALABARZON)</h6>
                        <h6 class="mb-1" style="font-size: 10pt;">Office Address: DENR IV-A (CALABARZON) COMPOUND</h6>
                        <h6 class="mb-1" style="font-size: 10pt;">DICOT BUILDING, Mayapa Main Road<br>Along SLEX, Brgy. Mayapa, Calamba City, Laguna</h6>
                    </div>

                    <!-- BILL NUMBER Section - Right -->
                    <div class="row mb-3">
                        <div class="col-6"></div>
                        <div class="col-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="50%" class="fw-bold"><span style="display: inline-block; min-width: 200px;">BILL NUMBER:</span></td>
                                    <td style="text-decoration: underline; text-transform: uppercase;">{{ $assessment->bill_number ?? $assessment->assessment_number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold"><span style="display: inline-block; min-width: 200px;">RESPONSIBILITY CENTER:</span></td>
                                    <td style="text-decoration: underline; text-transform: uppercase;">{{ $assessment->responsibility_center ?? 'SMD' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold"><span style="display: inline-block; min-width: 200px;">DATE:</span></td>
                                    <td style="text-decoration: underline; text-transform: uppercase;">{{ $assessment->assessment_date->format('F d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- ASSESSMENT FORM - Center -->
                    <div class="text-center mb-3">
                        <h4 class="fw-bold" style="font-size: 12pt; text-decoration: underline;">ASSESSMENT FORM</h4>
                    </div>

                    <!-- Name/Payee and Address - Left aligned -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="15%" class="fw-bold"><span style="display: inline-block; min-width: 120px;">NAME/PAYEE:</span></td>
                                    <td style="text-decoration: underline; text-transform: uppercase;">{{ $assessment->guest_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold"><span style="display: inline-block; min-width: 120px;">ADDRESS:</span></td>
                                    <td style="text-decoration: underline; text-transform: uppercase;">{{ $assessment->address ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Main Table -->
                    <div class="mb-4">
                        <table class="table table-bordered" style="border: 2px solid #000; border-collapse: collapse;">
                            <thead class="table-light">
                                <tr style="background-color: #e9ecef !important; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                                    <th width="12%" class="text-center align-middle fw-bold" style="font-size: 9pt; border: 2px solid #000; padding: 6px;">LEGAL BASIS<br>(DAO/SBC)</th>
                                    <th width="48%" class="text-center align-middle fw-bold" style="font-size: 9pt; border: 2px solid #000; padding: 6px;">DESCRIPTION AND COMPUTATION of Fees<br>and/or Charges Assessed</th>
                                    <th width="10%" class="text-center align-middle fw-bold" style="font-size: 9pt; border: 2px solid #000; padding: 6px;">Quantity</th>
                                    <th width="15%" class="text-center align-middle fw-bold" style="font-size: 9pt; border: 2px solid #000; padding: 6px;">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $namesData = json_decode($assessment->names_detail, true);
                                    $hasNames = is_array($namesData) && count($namesData) > 0;
                                    $categoryName = $assessment->category ? strtoupper($assessment->category->name) : strtoupper($assessment->request_type ?? 'Verification Fee');
                                @endphp
                                
                                @if($hasNames)
                                    <tr>
                                        <td rowspan="{{ count($namesData) + 2 }}" class="align-middle text-center" style="font-size: 9pt; border: 1px solid #000; padding: 4px;">{{ $assessment->legal_basis ?? '1993-20' }}</td>
                                        <td style="font-size: 9pt; border: 1px solid #000; padding: 4px; font-weight: bold;">{{ $categoryName }}</td>
                                        <td class="text-center" style="font-size: 9pt; border: 1px solid #000; padding: 4px;"></td>
                                        <td class="text-end" style="font-size: 9pt; border: 1px solid #000; padding: 4px;">-</td>
                                    </tr>
                                    @foreach($namesData as $index => $item)
                                        @if(isset($item['name']) && $item['name'] !== '${name}' && !empty($item['name']))
                                            <tr>
                                                <td style="font-size: 9pt; border: 1px solid #000; padding: 4px;">{{ $item['name'] }}</td>
                                                <td class="text-center" style="font-size: 9pt; border: 1px solid #000; padding: 4px;">{{ $item['quantity'] ?? 1 }}</td>
                                                <td class="text-end" style="font-size: 9pt; border: 1px solid #000; padding: 4px;">{{ number_format($item['amount'] ?? 0, 2) }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr>
                                        <td rowspan="2" class="align-middle text-center" style="font-size: 9pt; border: 1px solid #000; padding: 4px;">{{ $assessment->legal_basis ?? '1993-20' }}</td>
                                        <td style="font-size: 9pt; border: 1px solid #000; padding: 4px; font-weight: bold;">{{ $categoryName }}</td>
                                        <td class="text-center" style="font-size: 9pt; border: 1px solid #000; padding: 4px;"></td>
                                        <td class="text-end" style="font-size: 9pt; border: 1px solid #000; padding: 4px;">₱{{ number_format($assessment->fees, 2) }}</td>
                                    </tr>
                                @endif
                                
                                <!-- Total Row -->
                                <tr class="table-light" style="background-color: #e9ecef !important; -webkit-print-color-adjust: exact; print-color-adjust: exact;">
                                    <td colspan="2" class="text-end fw-bold" style="font-size: 9pt; border: 2px solid #000; padding: 6px;">TOTAL:</td>
                                    <td colspan="2" class="text-end fw-bold" style="font-size: 9pt; border: 2px solid #000; padding: 6px;">₱{{ number_format($assessment->fees, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Remarks/Notes -->
                    @if($assessment->remarks)
                        <div class="mb-3">
                            <strong style="font-size: 9pt;">Remarks:</strong>
                            <p style="font-size: 9pt;">{{ $assessment->remarks }}</p>
                        </div>
                    @endif

                    <!-- Signature Section -->
                    <div class="row mt-4 pt-3">
                        <div class="col-md-6">
                            <div class="text-center" style="min-height: 100px;">
                                <p class="fw-bold mb-4" style="font-size: 9pt;">PREPARED BY:</p>
                                <div style="margin-top: 40px;">
                                    <p class="fw-bold mb-0" style="font-size: 9pt;">STANLEY M. LOTA</p>
                                    <p class="mb-0" style="font-size: 9pt;">SIGNATURE OVER PRINTED NAME</p>
                                    <p class="mb-0" style="font-size: 9pt; margin-top: 10px;">ACCEPTANCE</p>
                                    <p style="font-size: 9pt;">Position/Designation</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center" style="min-height: 100px;">
                                <p class="fw-bold mb-4" style="font-size: 9pt;">REVIEWED BY:</p>
                                <div style="margin-top: 40px;">
                                    <p class="mb-0 text-center" style="font-size: 9pt; min-height: 18px;">FOR</p>
                                    <p class="fw-bold mb-0" style="font-size: 9pt; margin-top: 5px;">ENGR. ERITHA R. LUMAOANG</p>
                                    <p style="font-size: 9pt;">SIGNATURE OVER PRINTED NAME</p>
                                    <p class="mb-0" style="font-size: 9pt; margin-top: 10px;">ASST. DIVISION CHIEF</p>
                                    <p style="font-size: 9pt;">Position/Designation</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer - Bottom Left -->
                    <div class="mt-4 pt-2 border-top text-start">
                        <small style="font-size: 8pt;">R4A.FD.042.0001</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    @media print {
        body {
            background: white;
            margin: 0;
            padding: 0;
            font-size: 10pt;
            line-height: 1.3;
            width: 210mm; /* A4 width */
        }
        
        .navbar, .sidebar, .btn, .no-print {
            display: none !important;
        }
        
        .container, .print-container {
            max-width: 100%;
            margin: 0;
            padding: 0;
        }
        
        .row {
            margin: 0;
        }
        
        .col-lg-10 {
            max-width: 100%;
            flex: 0 0 100%;
        }
        
        #assessment-print {
            page-break-inside: avoid;
            page-break-after: auto;
            border: none;
            box-shadow: none !important;
            margin: 0;
            padding: 10px;
            min-height: 297mm; /* A4 height */
            display: flex;
            flex-direction: column;
        }
        
        .card {
            border: none;
            box-shadow: none !important;
        }
        
        .card-body {
            padding: 10px;
            page-break-inside: avoid;
        }
        
        h4, h5, h6 {
            margin-top: 2px;
            margin-bottom: 2px;
            page-break-after: avoid;
        }
        
        h5 {
            font-size: 11pt !important;
        }
        
        h6 {
            font-size: 10pt !important;
        }
        
        .table {
            page-break-inside: avoid;
            font-size: 9pt !important;
        }
        
        .table th, .table td {
            padding: 4px;
            font-size: 9pt !important;
            page-break-inside: avoid;
        }
        
        /* Preserve exact layout */
        .mb-3 {
            margin-bottom: 0.75rem !important;
        }
        
        /* Ensure single page */
        html, body {
            height: 297mm;
            overflow: hidden;
        }
    }
    
    /* Screen styles */
    #assessment-print {
        min-height: 297mm;
        display: flex;
        flex-direction: column;
    }
    
    .table-bordered {
        border: 2px solid #000 !important;
    }
    
    .table-bordered th, .table-bordered td {
        border: 1px solid #000 !important;
    }
    
    .fw-bold {
        font-weight: bold !important;
    }
    
    .text-center {
        text-align: center !important;
    }
    
    .text-end {
        text-align: right !important;
    }
    
    /* Signature area */
    [style*="min-height: 120px"] {
        min-height: 100px !important;
    }
    
    [style*="margin-top: 50px"] {
        margin-top: 40px !important;
    }
    
    /* Ensure table headers are bold */
    thead th {
        font-weight: bold !important;
    }
    
    /* Preserve spacing */
    .mt-4 {
        margin-top: 1.5rem !important;
    }
    
    .pt-3 {
        padding-top: 1rem !important;
    }
    
    thead th {
        background-color: #f8f9fa !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    /* Total row styling */
    tr.table-light {
        background-color: #f8f9fa !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    /* Prevent breaking within rows */
    tr {
        page-break-inside: avoid;
    }
    
    /* Adjust spacing */
    .mb-3 {
        margin-bottom: 0.75rem !important;
    }
    
    .mb-4 {
        margin-bottom: 1rem !important;
    }
    
    .mt-4 {
        margin-top: 1rem !important;
    }
    
    .pt-3 {
        padding-top: 0.75rem !important;
    }
    
    p {
        margin: 0;
        font-size: 9pt !important;
    }
    
    /* Page setup */
    @page {
        size: A4;
        margin: 10mm;
    }
    
    /* Screen-only styles */
    .no-print {
        display: block;
    }
</style>
@endsection
