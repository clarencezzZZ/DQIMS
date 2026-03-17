@extends('layouts.app')

@section('title', 'Assessment')

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

            @php
                $allDescriptions = json_decode($assessment->request_type, true);
                if (!is_array($allDescriptions)) {
                    $allDescriptions = [$assessment->request_type];
                }

                $namesData = json_decode($assessment->names_detail, true);
                $groupedItems = [];

                if(is_array($namesData)){
                    foreach($namesData as $item){
                        if(isset($item['name']) && $item['name'] !== '${name}' && !empty($item['name'])){
                            $desc = $item['description'] ?? 'GENERAL';
                            $groupedItems[$desc][] = $item;
                        }
                    }
                }
            @endphp

            @foreach($allDescriptions as $description)
                @php
                    $itemsForThisDescription = $groupedItems[$description] ?? [];
                    $totalForThisDescription = 0;
                    foreach($itemsForThisDescription as $item) {
                        $totalForThisDescription += ($item['quantity'] ?? 1) * ($item['amount'] ?? 0);
                    }
                @endphp

                <div class="card shadow printable-assessment-page mb-4">
                    <div class="card-body p-4">
                        <!-- Office Header -->
                        <div class="text-center mb-3">
                            <h5 class="fw-bold mb-1" style="font-size: 11pt;">Republic of the Philippines</h5>
                            <h5 class="fw-bold mb-1" style="font-size: 11pt;">DEPARTMENT OF ENVIRONMENT AND NATURAL RESOURCES</h5>
                            <h6 class="mb-1" style="font-size: 10pt;">OFFICE Regional Office No. 4A (CALABARZON)</h6>
                            <h6 class="mb-1" style="font-size: 10pt;">Office Address: DENR IV-A (CALABARZON) COMPOUND</h6>
                            <h6 class="mb-1" style="font-size: 10pt;">DICOT BUILDING, Mayapa Main Road<br>Along SLEX, Brgy. Mayapa, Calamba City, Laguna</h6>
                        </div>

                        <!-- BILL NUMBER Section - Right -->
                        <div class="mb-3 d-flex">
                            <div class="flex-fill"></div>
                            <div class="print-meta">
                                <div class="meta-row">
                                    <div class="label">BILL NUMBER:</div>
                                    <div class="value">{{ $assessment->bill_number ?? $assessment->assessment_number }}</div>
                                </div>
                                <div class="meta-row">
                                    <div class="label">RESPONSIBILITY CENTER:</div>
                                    <div class="value">{{ $assessment->responsibility_center ?? 'SMD' }}</div>
                                </div>
                                <div class="meta-row">
                                    <div class="label">DATE:</div>
                                    <div class="value">{{ $assessment->assessment_date->format('F d, Y') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- ASSESSMENT FORM - Center -->
                        <div class="text-center mb-3" style="margin-top: 10px; margin-bottom: 10px;">
                            <h4 class="fw-bold" style="font-size: 12pt;">ASSESSMENT FORM</h4><br>
                        </div>

                        <!-- Name/Payee and Address - Left aligned -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td width="15%" class="fw-bold"><span style="display: inline-block; min-width: 120px;">NAME/PAYEE:</span></td>
                                        <td style="text-align: center; text-transform: uppercase; text-align: left;">
                                            <div style="display: inline-block; width: 500px; border-bottom: 1px solid black; text-align: center;">
                                                {{ $assessment->name_payee ?? $assessment->guest_name }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold"><span style="display: inline-block; min-width: 120px;">ADDRESS:</span></td>
                                        <td style="text-align: center; text-transform: uppercase; text-align: left;">
                                            <div style="display: inline-block; width: 500px; border-bottom: 1px solid black; text-align: center;">
                                                {{ $assessment->address }}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Main Table -->
                        <div class="mb-4">
                            <table class="table" style="width:100%; border:2px solid #000; border-collapse:collapse;">
                                <thead>
                                    <tr style="background-color:#e9ecef;">
                                        <th width="15%" style="border:2px solid #000; font-size:12pt; padding:6px; text-align:center;">LEGAL BASIS<br>(DAO/SBC)</th>
                                        <th width="45%" style="border:2px solid #000; font-size:12pt; padding:6px; text-align:center;">DESCRIPTION AND COMPUTATION of Fees<br>and/or Charges Assessed</th>
                                        <th width="10%" style="border:2px solid #000; font-size:12pt; padding:6px; text-align:center;">Quantity</th>
                                        <th width="15%" style="border:2px solid #000; font-size:12pt; padding:6px; text-align:center;">AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td rowspan="{{ count($itemsForThisDescription) + 1 }}" style="border-right:2px solid #000; font-size:12pt; padding:40px; text-align:center; vertical-align:top; white-space: nowrap;">
                                            {{ $assessment->legal_basis ?? '1993-20' }}
                                        </td>
                                        <td style="border-right:2px solid #000; font-size:12pt; padding:6px; vertical-align:top; border-bottom: none !important; background-color: #f8f9fa;">
                                            <strong class="text-uppercase text-success">{{ $description }}</strong>
                                        </td>
                                        <td style="border-right:2px solid #000; font-size:12pt; padding:6px; text-align:center; vertical-align:top; border-bottom: none !important; background-color: #f8f9fa;">&nbsp;</td>
                                        <td style="font-size:12pt; padding:6px; text-align:right; vertical-align:top; border-bottom: none !important; background-color: #f8f9fa;">&nbsp;</td>
                                    </tr>

                                    @foreach($itemsForThisDescription as $item)
                                        <tr>
                                            <td style="border-right:2px solid #000; font-size:12pt; padding:6px; vertical-align:top; border-top: none !important; border-bottom: none !important; padding-left: 25px;">
                                                {{ $item['name'] }}
                                            </td>
                                            <td style="border-right:2px solid #000; font-size:12pt; padding:6px; text-align:center; vertical-align:top; border-top: none !important; border-bottom: none !important;">
                                                {{ $item['quantity'] ?? 1 }}
                                            </td>
                                            <td style="font-size:12pt; padding:6px; text-align:right; vertical-align:top; border-top: none !important; border-bottom: none !important;">
                                                @if(isset($item['amount']) && $item['amount'] > 0)
                                                    {{ number_format($item['amount'], 2) }}
                                                @else
                                                    &nbsp;
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr style="background:#e9ecef;">
                                        <td colspan="3" style="border-top:2px solid #000; border-right:2px solid #000; font-size:12pt; padding:6px; text-align:right; font-weight:bold;">TOTAL:</td>
                                        <td style="border-top:2px solid #000; font-size:12pt; padding:6px; text-align:right; font-weight:bold;">₱{{ number_format($totalForThisDescription, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Signature Section -->
                        <table class="table table-borderless mt-4 pt-3" style="width:100%;">
                            <tr>
                                <td style="width:70%; vertical-align:top;">
                                    <div style="min-height: 120px;">
                                        <p class="fw-bold mb-4" style="font-size: 9pt;">PREPARED BY:</p>
                                        <div style="margin-top: 70px;">
                                            <p class="fw-bold mb-0" style="font-size: 9pt;">STANLEY M. LOTA</p>
                                            <p class="mb-0" style="font-size: 9pt;">SIGNATURE OVER PRINTED NAME</p>
                                            <p class="mb-0" style="font-size: 9pt; margin-top: 10px;">ACCEPTANCE</p>
                                            <p style="font-size: 9pt;">Position/Designation</p>
                                        </div>
                                    </div>
                                </td>
                                <td style="width:50%; vertical-align:top; text-align:left;">
                                    <div style="min-height: 120px;">
                                        <p class="fw-bold mb-4" style="font-size: 9pt;">REVIEWED BY:</p>
                                        <p class="mb-0" style="font-size: 9pt;">FOR</p>
                                        <p class="fw-bold mb-0" style="font-size: 9pt; margin-top: 35px;">ENGR. ERITHA R. LUMAOANG</p>
                                        <p style="font-size: 9pt;">SIGNATURE OVER PRINTED NAME</p>
                                        <p class="mb-0" style="font-size: 9pt; margin-top: 10px;">ASST. DIVISION CHIEF</p>
                                        <p style="font-size: 9pt;">Position/Designation</p>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <!-- Footer - Bottom Left -->
                        <div class="mt-4 pt-2 border-top text-start">
                            <small style="font-size: 7pt;">R4A.FD.042.0001</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    @media print {
        @page {
            size: A4;
            margin: 10mm;
        }
        
        body {
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 10pt;
            line-height: 1.2;
            width: 100%;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        /* Hide EVERYTHING except the assessment print area */
        .navbar, .sidebar, .btn, .no-print, header, footer, nav, .breadcrumb, .alert {
            display: none !important;
        }
        
        .container, .print-container, .row, .col-lg-10 {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            display: block !important;
            border: none !important;
            box-shadow: none !important;
        }
        
        .card { 
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            page-break-after: always !important;
        }

        .card:last-child {
            page-break-after: avoid !important;
        }
        
        .mb-4 {
            margin-bottom: 0 !important;
        }
        
        .card-body {
            padding: 0 !important;
            margin: 0 !important;
        }

        .printable-assessment-page {
            page-break-inside: avoid !important;
        }

        #assessment-print {
            width: 100% !important;
        }
        
        /* Table styling for print */
        .table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-bottom: 10px !important;
        }
        
        .table th, .table td {
            padding: 4px 6px !important;
            border: 1px solid #000 !important;
            font-size: 9pt !important;
        }
        
        .table-borderless td, .table-borderless th {
            border: none !important;
        }
        
        /* Preserve background colors in print */
        thead th, .table-light, [style*="background-color:#e9ecef"], [style*="background-color: #f8f9fa"] {
            background-color: #f0f0f0 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .text-success {
            color: #198754 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Adjust signature section spacing */
        .mt-4 { margin-top: 1rem !important; }
        .pt-3 { padding-top: 0.5rem !important; }
        
        /* Ensure no extra pages */
        html, body {
            height: auto;
            overflow: visible;
        }
    }
    
    /* Screen styles */
    .print-meta {
        width: 320px;
        margin-left: auto;
    }
    .print-meta .meta-row {
        display: flex;
        align-items: center;
        margin-bottom: 6px;
    }
    .print-meta .label {
        width: 180px;
        text-align: right;
        font-weight: bold;
        font-size: 9pt;
    }
    .print-meta .value {
        flex: 1;
        border-bottom: 1px solid #000;
        text-align: center;
        text-transform: uppercase;
        font-size: 9pt;
        padding: 2px 4px;
        min-height: 18px;
        line-height: 18px;
    }
    
    .table-bordered {
        border: 2px solid #000 !important;
    }
    
    .table-bordered th, .table-bordered td {
        border: 1px solid #000 !important;
    }
    
    .fw-bold { font-weight: bold !important; }
    .text-center { text-align: center !important; }
    .text-end { text-align: right !important; }
    
    /* Total row styling */
    tr.table-light {
        background-color: #f8f9fa !important;
    }
</style>
@endsection
