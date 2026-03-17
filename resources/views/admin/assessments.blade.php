@extends('layouts.app')

@section('title', 'Assessment Forms')

@section('styles')
<style>
    .total-amount-wrapper {
        display: inline-block;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .total-updated {
        transform: scale(1.1);
        color: var(--denr-green);
        text-shadow: 0 0 10px rgba(46, 125, 50, 0.2);
    }
    .amount-input, .quantity-input {
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .amount-input:focus, .quantity-input:focus {
        border-color: var(--denr-green);
        box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.1);
    }

    /* Professional Dark Mode Overrides */
    [data-theme="dark"] .card {
        background-color: var(--dark-surface) !important;
        border-color: var(--dark-border) !important;
    }

    [data-theme="dark"] .card-body {
        background-color: var(--dark-surface) !important;
    }

    [data-theme="dark"] .modal-content {
        background-color: var(--dark-surface) !important;
        border-color: var(--dark-border) !important;
        color: var(--dark-on-surface) !important;
    }

    [data-theme="dark"] .modal-header {
        border-bottom-color: var(--dark-border) !important;
    }

    [data-theme="dark"] .modal-footer {
        border-top-color: var(--dark-border) !important;
    }

    [data-theme="dark"] .form-control, 
    [data-theme="dark"] .form-select,
    [data-theme="dark"] .form-control.bg-light {
        background-color: var(--dark-surface-secondary) !important;
        border-color: var(--dark-border) !important;
        color: var(--dark-on-surface) !important;
    }

    [data-theme="dark"] .table {
        color: var(--dark-on-surface) !important;
    }

    [data-theme="dark"] .table thead.table-light {
        background-color: var(--dark-surface-secondary) !important;
    }

    [data-theme="dark"] .table thead th {
        background-color: var(--dark-surface-secondary) !important;
        color: var(--dark-on-surface) !important;
        border-bottom-color: var(--dark-border) !important;
    }

    [data-theme="dark"] .table td {
        border-bottom-color: var(--dark-border) !important;
    }

    [data-theme="dark"] .text-muted {
        color: #adb5bd !important;
    }

    /* Professional Pagination Dark Mode */
    [data-theme="dark"] .card-footer {
        background-color: var(--dark-surface-secondary) !important;
        border-top: 1px solid var(--dark-border) !important;
    }

    [data-theme="dark"] .pagination {
        background-color: var(--dark-surface-secondary) !important;
        border-color: var(--dark-border) !important;
    }

    [data-theme="dark"] .pagination .page-link {
        background-color: transparent !important;
        color: #adb5bd !important;
    }

    [data-theme="dark"] .pagination .page-item.active .page-link {
        background-color: var(--denr-green) !important;
        border-color: var(--denr-green) !important;
        color: white !important;
    }

    [data-theme="dark"] .pagination .page-item.disabled .page-link {
        color: #495057 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-file-earmark-text text-success"></i> Assessment Forms</h2>
                    <p class="text-muted mb-0">View and manage all assessment forms</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#newRequestModal">
                        <i class="bi bi-plus-lg"></i> New Request
                    </button>
                    <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- New Request Modal -->
    <div class="modal fade" id="newRequestModal" tabindex="-1" aria-labelledby="newRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="newRequestModalLabel"><i class="bi bi-file-earmark-plus"></i> New Assessment Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.assessments.store-direct') }}" method="POST" id="assessmentForm">
                    @csrf
                    <div class="modal-body">
                        <!-- Header Info -->
                        <div class="row g-3 mb-4">
                            <!-- Assessment Number (Auto-generated) -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Assessment Number</label>
                                <input type="text" id="assessment_number" class="form-control bg-light" readonly placeholder="Will be generated by system" disabled>

                            </div>
                            <!-- Responsibility Center -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Responsibility Center</label>
                                <input type="text" name="responsibility_center" class="form-control" value="SMD" required>
                            </div>
                            <!-- Date -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Date</label>
                                <input type="date" name="assessment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <!-- Payee Info -->
                        <div class="row g-3 mb-4">
                            <!-- Full Name -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Name/Payee</label>
                                <input type="text" name="guest_name" class="form-control" placeholder="Enter full name" required>
                            </div>
                            <!-- Address -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Address</label>
                                <input type="text" name="address" class="form-control" placeholder="Enter address" required>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Description</label>
                                <select name="description_type" class="form-select" required>
                                    <option value="">-- Select Description --</option>
                                    <option value="Cadastral Cost">Cadastral Cost</option>
                                    <option value="Certification: A&D Status">Certification: A&D Status</option>
                                    <option value="Certification: Cadastral Map">Certification: Cadastral Map</option>
                                    <option value="Certification Cancellation Of Approved Plan">Certification Cancellation Of Approved Plan</option>
                                    <option value="Certification GPPC">Certification GPPC</option>
                                    <option value="Certification Lot Data Computation">Certification Lot Data Computation</option>
                                    <option value="Certification Lot Status">Certification Lot Status</option>
                                    <option value="Certification Rejection Order">Certification Rejection Order</option>
                                    <option value="Certification Survey Plan">Certification Survey Plan</option>
                                    <option value="Certification: Technical Description">Certification: Technical Description</option>
                                    <option value="GE Credit">GE Credit</option>
                                    <option value="Verification Fee">Verification Fee</option>
                                    <option value="Inspection Fee">Inspection Fee</option>
                                </select>
                            </div>
                        </div>

                        <!-- Names/Item -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-bold mb-0">Names/Item</label>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addNameRow()">
                                    <i class="bi bi-plus-lg"></i> Add Item
                                </button>
                            </div>
                            <div id="namesContainer">
                                <!-- Dynamic rows will be added here -->
                            </div>
                            <div class="text-end mt-3">
                                <h5 class="mb-0">Total: ₱<span id="totalAmount" class="total-amount-wrapper">0.00</span></h5>
                                <input type="hidden" name="fees" id="feesInput" value="0">
                            </div>
                        </div>

                        <!-- Officer incharge Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Officer incharge</label>
                            <select name="officer_in_charge" id="officerSelect" class="form-select" required>
                                <option value="">-- Select Officer incharge --</option>
                                <option value="lota">Mr. Stanley M. Lota</option>
                            </select>
                        </div>

                        <!-- Remarks -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Remarks/Notes</label>
                            <textarea name="remarks" class="form-control" rows="2" placeholder="Enter additional notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Save Assessment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.assessments') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Description/Type</label>
                    <select name="category" class="form-select">
                        <option value="">All Types</option>
                        <option value="Cadastral Cost" {{ request('category') == 'Cadastral Cost' ? 'selected' : '' }}>Cadastral Cost</option>
                        <option value="Certification: A&D Status" {{ request('category') == 'Certification: A&D Status' ? 'selected' : '' }}>Certification: A&D Status</option>
                        <option value="Certification: Cadastral Map" {{ request('category') == 'Certification: Cadastral Map' ? 'selected' : '' }}>Certification: Cadastral Map</option>
                        <option value="Certification Cancellation Of Approved Plan" {{ request('category') == 'Certification Cancellation Of Approved Plan' ? 'selected' : '' }}>Certification Cancellation Of Approved Plan</option>
                        <option value="Certification GPPC" {{ request('category') == 'Certification GPPC' ? 'selected' : '' }}>Certification GPPC</option>
                        <option value="Certification Lot Data Computation" {{ request('category') == 'Certification Lot Data Computation' ? 'selected' : '' }}>Certification Lot Data Computation</option>
                        <option value="Certification Lot Status" {{ request('category') == 'Certification Lot Status' ? 'selected' : '' }}>Certification Lot Status</option>
                        <option value="Certification Rejection Order" {{ request('category') == 'Certification Rejection Order' ? 'selected' : '' }}>Certification Rejection Order</option>
                        <option value="Certification Survey Plan" {{ request('category') == 'Certification Survey Plan' ? 'selected' : '' }}>Certification Survey Plan</option>
                        <option value="Certification: Technical Description" {{ request('category') == 'Certification: Technical Description' ? 'selected' : '' }}>Certification: Technical Description</option>
                        <option value="GE Credit" {{ request('category') == 'GE Credit' ? 'selected' : '' }}>GE Credit</option>
                        <option value="Verification Fee" {{ request('category') == 'Verification Fee' ? 'selected' : '' }}>Verification Fee</option>
                        <option value="Inspection Fee" {{ request('category') == 'Inspection Fee' ? 'selected' : '' }}>Inspection Fee</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill"><i class="bi bi-search"></i> Filter</button>
                        <a href="{{ route('admin.assessments') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Assessments Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Assessment Number</th>
                            <th>Date</th>
                            <th>Full Name</th>
                            <th>Address</th>
                            <th>Category</th>
                            <th>Reference</th>
                            <th>Total</th>
                            <th>Processed By</th>
                            <th>Officer in Charge</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assessments as $assessment)
                            <tr>
                                <td><span class="badge bg-success fs-6">{{ $assessment->assessment_number }}</span></td>
                                <td>{{ $assessment->assessment_date->format('M d, Y') }}</td>
                                <td>{{ $assessment->guest_name }}</td>
                                <td>{{ $assessment->address ?? 'N/A' }}</td>
                                <td>
                                    @if($assessment->category)
                                        <span class="badge" style="background-color: {{ $assessment->category->color }}">
                                            {{ $assessment->category->code }}
                                        </span>
                                    @else
                                        @if($assessment->request_type)
                                            <span class="badge bg-info">{{ $assessment->request_type }}</span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $assessment->remarks ?? $assessment->reference ?? 'N/A' }}</td>
                                <td><strong>₱{{ number_format($assessment->fees, 2) }}</strong></td>
                                <td>{{ $assessment->processedBy->name ?? 'N/A' }}</td>
                                <td>
                                    @if($assessment->custom_officer_name)
                                        {{ $assessment->custom_officer_name }}
                                    @elseif($assessment->officer_in_charge === 'lota')
                                        Mr. Stanley M. Lota
                                    @elseif($assessment->officer_in_charge && is_numeric($assessment->officer_in_charge))
                                        {{ $assessment->officerInCharge ? $assessment->officerInCharge->name : 'N/A' }}
                                    @else
                                        {{ $assessment->officer_in_charge ?? 'N/A' }}
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.assessments.edit', $assessment) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-file-earmark-x" style="font-size: 3rem;"></i>
                                    <p class="mt-3">No assessments found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($assessments->hasPages())
            <div class="card-footer border-0 py-4">
                <div class="d-flex flex-column align-items-center justify-content-center">
                    {{ $assessments->links('vendor.pagination.custom') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Show success alert if session has success message
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1e1e1e' : '#fff',
            color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#e0e0e0' : '#545454'
        });
    @endif

    // Show error alert if session has error message
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ session('error') }}",
            background: document.documentElement.getAttribute('data-theme') === 'dark' ? '#1e1e1e' : '#fff',
            color: document.documentElement.getAttribute('data-theme') === 'dark' ? '#e0e0e0' : '#545454'
        });
    @endif

    // Generate temporary assessment number for UX purposes
    // The server will generate the actual unique number (yearly reset format: YYYY-MM-NNNN)
    function generateAssessmentNumber() {
        const year = new Date().getFullYear();
        const month = String(new Date().getMonth() + 1).padStart(2, '0');
        // Get last assessment number for this year and increment (for display only)
        fetch('/admin/assessments/last-number/' + year + '/' + month)
            .then(response => response.json())
            .then(data => {
                const nextNumber = data.last_number + 1;
                const formattedNumber = String(nextNumber).padStart(4, '0');
                document.getElementById('assessment_number').value = `${year}-${month}-${formattedNumber}`;
            })
            .catch(error => {
                // Fallback to timestamp-based approach
                const timestamp = String(new Date().getTime()).slice(-4);
                document.getElementById('assessment_number').value = `${year}-${month}-${timestamp}`;
            });
    }

    // Initialize assessment number when modal opens
    document.getElementById('newRequestModal').addEventListener('show.bs.modal', function () {
        generateAssessmentNumber();
        // Add first name row by default
        if (document.getElementById('namesContainer').children.length === 0) {
            addNameRow();
        }
    });

    // Add new name row
    function addNameRow() {
        const container = document.getElementById('namesContainer');
        const rowId = 'nameRow_' + Date.now();
        
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 align-items-end';
        row.id = rowId;
        row.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="names[]" class="form-control" placeholder="Enter item name" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="quantities[]" class="form-control quantity-input" placeholder="Qty" value="1" min="1" oninput="calculateTotal()">
            </div>
            <div class="col-md-4">
                <input type="number" name="amounts[]" class="form-control amount-input" placeholder="Amount" step="0.01" min="0" oninput="calculateTotal()">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeNameRow('${rowId}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        
        container.appendChild(row);
    }

    // Remove name row
    function removeNameRow(rowId) {
        const row = document.getElementById(rowId);
        if (row) {
            row.remove();
            calculateTotal();
        }
    }

    // Calculate total amount with animation
    let currentTotal = 0;
    function calculateTotal() {
        let total = 0;
        const amounts = document.querySelectorAll('.amount-input');
        const quantities = document.querySelectorAll('.quantity-input');
        
        amounts.forEach((amount, index) => {
            const qty = quantities[index] ? parseFloat(quantities[index].value) || 0 : 0;
            const amt = parseFloat(amount.value) || 0;
            total += (qty * amt);
        });
        
        const totalElement = document.getElementById('totalAmount');
        const feesInput = document.getElementById('feesInput');
        
        // Add visual feedback class
        totalElement.classList.add('total-updated');
        setTimeout(() => totalElement.classList.remove('total-updated'), 300);

        // Animate count-up/down
        animateValue('totalAmount', currentTotal, total, 400);
        currentTotal = total;
        feesInput.value = total.toFixed(2);
    }

    // Professional count animation
    function animateValue(id, start, end, duration) {
        if (start === end) return;
        const obj = document.getElementById(id);
        const range = end - start;
        let current = start;
        const increment = end > start ? Math.max(range / (duration / 16), 0.01) : Math.min(range / (duration / 16), -0.01);
        const startTime = new Date().getTime();
        const endTime = startTime + duration;

        function step() {
            const now = new Date().getTime();
            const remaining = Math.max((endTime - now) / duration, 0);
            const value = end - (remaining * range);
            
            obj.textContent = value.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            
            if (now < endTime) {
                requestAnimationFrame(step);
            } else {
                obj.textContent = end.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
        }
        
        requestAnimationFrame(step);
    }



    // Make functions globally accessible
    window.addNameRow = addNameRow;
    window.removeNameRow = removeNameRow;
    window.calculateTotal = calculateTotal;
</script>
@endsection
