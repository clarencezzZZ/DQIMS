@extends('layouts.app')

@section('title', 'Assessment Forms')

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
                            <!-- Bill Number (Auto-generated) -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Bill Number</label>
                                <input type="text" name="bill_number" id="bill_number" class="form-control bg-light" readonly>
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

                        <!-- Legal Basis (Fixed) -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Legal Basis (DAO/SBC)</label>
                                <input type="text" name="legal_basis" class="form-control bg-light" value="1993-20" readonly>
                            </div>
                            <!-- Description -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Description</label>
                                <select name="description_type" class="form-select" required>
                                    <option value="">-- Select Description --</option>
                                    <option value="Cadastral Cost">Cadastral Cost</option>
                                    <option value="Certification: A&D Status">Certification: A&D Status</option>
                                    <option value="Certification: Cadastral Map">Certification: Cadastral Map</option>
                                    <option value="Certification Cancellation Of Approved Plan">Certification Cancellation Of Approved Plan</option>
                                    <option value="Certification GPP">Certification GPP</option>
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

                        <!-- Names with Quantity and Amount -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-bold mb-0">Names with Quantity & Amount</label>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addNameRow()">
                                    <i class="bi bi-plus-lg"></i> Add Name
                                </button>
                            </div>
                            <div id="namesContainer">
                                <!-- Dynamic rows will be added here -->
                            </div>
                            <div class="text-end mt-3">
                                <h5 class="mb-0">Total: ₱<span id="totalAmount">0.00</span></h5>
                                <input type="hidden" name="fees" id="feesInput" value="0">
                            </div>
                        </div>

                        <!-- Officer of the Day Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Officer of the Day</label>
                            <select name="officer_of_day" id="officerSelect" class="form-select" required>
                                <option value="">-- Select Officer of the Day --</option>
                                <option value="{{ $lotaOfficer->id ?? '' }}">Mr. Stanly M. Lota</option>
                                <option value="other">Other</option>
                            </select>
                            <div id="newOfficerInput" class="mt-2" style="display: none;">
                                <label class="form-label">Enter New Officer Name</label>
                                <input type="text" name="new_officer_name" id="newOfficerName" class="form-control" placeholder="Enter officer name">
                            </div>
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
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
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
                                <td>{{ $assessment->reference ?? 'N/A' }}</td>
                                <td><strong>₱{{ number_format($assessment->fees, 2) }}</strong></td>
                                <td>{{ $assessment->processedBy->name ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="confirmDelete({{ $assessment->id }})" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
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
            <div class="card-footer">
                {{ $assessments->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Event Logs Section -->
<div class="card shadow-sm mt-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Event Logs</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="15%">Date & Time</th>
                        <th width="15%">User</th>
                        <th width="10%">Action</th>
                        <th width="15%">Assessment Number</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventLogs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                            <td>{{ $log->user->name ?? 'Unknown User' }}</td>
                            <td>
                                @if($log->action === 'deleted')
                                    <span class="badge bg-danger">{{ ucfirst($log->action) }}</span>
                                @elseif($log->action === 'updated')
                                    <span class="badge bg-warning text-dark">{{ ucfirst($log->action) }}</span>
                                @else
                                    <span class="badge bg-info">{{ ucfirst($log->action) }}</span>
                                @endif
                            </td>
                            <td>{{ $log->assessment_number }}</td>
                            <td>{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No event logs found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Generate next bill number
    function generateBillNumber() {
        const year = new Date().getFullYear();
        const month = String(new Date().getMonth() + 1).padStart(2, '0');
        // Get last assessment bill number and increment
        // For now, generate based on timestamp to ensure uniqueness
        const random = Math.floor(Math.random() * 9000) + 1000;
        return `${year}-${month}-${random}`;
    }

    // Initialize bill number when modal opens
    document.getElementById('newRequestModal').addEventListener('show.bs.modal', function () {
        document.getElementById('bill_number').value = generateBillNumber();
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
                <input type="text" name="names[]" class="form-control" placeholder="Enter name" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="quantities[]" class="form-control quantity-input" placeholder="Qty" value="1" min="1" onchange="calculateTotal()">
            </div>
            <div class="col-md-4">
                <input type="number" name="amounts[]" class="form-control amount-input" placeholder="Amount" step="0.01" min="0" onchange="calculateTotal()">
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

    // Calculate total amount
    function calculateTotal() {
        let total = 0;
        const amounts = document.querySelectorAll('.amount-input');
        const quantities = document.querySelectorAll('.quantity-input');
        
        amounts.forEach((amount, index) => {
            const qty = quantities[index] ? parseInt(quantities[index].value) || 1 : 1;
            const amt = parseFloat(amount.value) || 0;
            total += (qty * amt);
        });
        
        document.getElementById('totalAmount').textContent = total.toFixed(2);
        document.getElementById('feesInput').value = total.toFixed(2);
    }

    // Toggle new officer input based on selection
    document.getElementById('officerSelect')?.addEventListener('change', function() {
        const newOfficerInput = document.getElementById('newOfficerInput');
        if (this.value === 'other') {
            newOfficerInput.style.display = 'block';
            document.getElementById('newOfficerName').required = true;
        } else {
            newOfficerInput.style.display = 'none';
            document.getElementById('newOfficerName').required = false;
        }
    });

    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this assessment? This action cannot be undone.')) {
            // Create a form and submit it to delete the assessment
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/assessments/` + id;
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const tokenField = document.createElement('input');
            tokenField.type = 'hidden';
            tokenField.name = '_token';
            tokenField.value = csrfToken;
            form.appendChild(tokenField);
            
            // Add method spoofing for DELETE
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Make functions globally accessible
    window.addNameRow = addNameRow;
    window.removeNameRow = removeNameRow;
    window.calculateTotal = calculateTotal;
</script>
@endsection
