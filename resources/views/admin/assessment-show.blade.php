@extends('layouts.app')

@section('title', 'View Assessment')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
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

            <!-- Assessment Card -->
            <div class="card shadow" id="assessment-print">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0"><i class="bi bi-tree"></i> DENR Assessment Form</h4>
                    <small>Department of Environment and Natural Resources</small>
                </div>
                <div class="card-body p-4">
                    <!-- Assessment Number -->
                    <div class="text-center mb-4">
                        <h6 class="text-muted mb-2">Assessment Number</h6>
                        <h2 class="text-success fw-bold">{{ $assessment->assessment_number }}</h2>
                    </div>

                    <hr class="my-4">

                    <!-- Guest Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-success mb-3"><i class="bi bi-person"></i> Guest Information</h6>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted" width="40%">Guest Name:</td>
                                    <td class="fw-bold">{{ $assessment->guest_name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Queue Number:</td>
                                    <td><code>{{ $assessment->queue_number }}</code></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted" width="40%">Category/Request Type:</td>
                                    <td>
                                        @if($assessment->category)
                                            <span class="badge" style="background-color: {{ $assessment->category->color }}">
                                                {{ $assessment->category->name }}
                                            </span>
                                        @else
                                            @if($assessment->request_type)
                                                <span class="badge bg-info">{{ $assessment->request_type }}</span>
                                            @else
                                                N/A
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Assessment Date:</td>
                                    <td>{{ $assessment->assessment_date->format('F d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Fees Section -->
                    <div class="bg-light p-4 rounded mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-success mb-0"><i class="bi bi-calculator"></i> Assessment Fees</h6>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <h3 class="text-success fw-bold mb-0">₱{{ number_format($assessment->fees, 2) }}</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Names with Quantity & Amount -->
                    @if($assessment->names_detail && $assessment->names_detail !== '[]')
                        <div class="mb-4">
                            <h6 class="fw-bold text-success mb-3"><i class="bi bi-people"></i> Names with Quantity & Amount</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Quantity</th>
                                            <th>Amount (₱)</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $namesData = json_decode($assessment->names_detail, true);
                                        @endphp
                                        @if(is_array($namesData))
                                            @foreach($namesData as $item)
                                                @if(isset($item['name']) && $item['name'] !== '${name}' && !empty($item['name']))
                                                    <tr>
                                                        <td>{{ $item['name'] }}</td>
                                                        <td>{{ $item['quantity'] ?? 1 }}</td>
                                                        <td>{{ number_format($item['amount'] ?? 0, 2) }}</td>
                                                        <td>{{ number_format(($item['quantity'] ?? 1) * ($item['amount'] ?? 0), 2) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                    </tbody>
                                    @if($assessment->fees > 0)
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="3" class="text-end">Total:</th>
                                                <th>₱{{ number_format($assessment->fees, 2) }}</th>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Remarks -->
                    @if($assessment->remarks)
                        <div class="mb-4">
                            <h6 class="fw-bold text-success mb-2"><i class="bi bi-chat-text"></i> Remarks</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $assessment->remarks }}
                            </div>
                        </div>
                    @endif

                    <hr class="my-4">

                    <!-- Processing Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="text-muted">Processed By:</td>
                                    <td class="fw-bold">{{ $assessment->processedBy->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Officer of the Day:</td>
                                    <td class="fw-bold">
                                        @if($assessment->custom_officer_name)
                                            {{ $assessment->custom_officer_name }}
                                        @elseif($assessment->officer_of_day && is_numeric($assessment->officer_of_day))
                                            {{ $assessment->officerOfDay ? $assessment->officerOfDay->name : 'N/A' }}
                                        @else
                                            {{ $assessment->officer_of_day ?? 'N/A' }}
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <small class="text-muted">Generated on {{ $assessment->created_at->format('F d, Y h:i A') }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center bg-light">
                    <small class="text-muted">This is an official assessment form from DENR. Please keep this for your records.</small>
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
        }
        .navbar, .sidebar, .btn, .no-print {
            display: none !important;
        }
        .card {
            border: 2px solid #28a745;
            box-shadow: none !important;
        }
        .container {
            max-width: 100%;
        }
    }
</style>
@endsection
