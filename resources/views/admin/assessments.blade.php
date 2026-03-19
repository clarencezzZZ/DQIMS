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

    /* Fix Modal Scrolling and Save Button Visibility */
    #newRequestModal.modal .modal-dialog {
        max-height: 90vh;
        margin-top: 5vh;
        margin-bottom: 5vh;
    }
    #newRequestModal.modal .modal-content {
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }
    #newRequestModal.modal #assessmentForm {
        display: flex;
        flex-direction: column;
        flex: 1;
        overflow: hidden;
    }
    #newRequestModal.modal .modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }
    #newRequestModal.modal .modal-footer {
        flex-shrink: 0;
        background: white;
        z-index: 10;
        border-top: 1px solid #dee2e6;
        padding: 15px 20px;
    }
    [data-theme="dark"] #newRequestModal.modal .modal-footer {
        background: var(--dark-surface);
        border-top-color: var(--dark-border);
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
                    @if(auth()->user()->username === 'admin')
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#manageTypesModal">
                        <i class="bi bi-gear"></i> Manage Descriptions
                    </button>
                    @endif
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
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
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
                                <select id="description-select" name="description_type[]" multiple required>
                                    @foreach($assessmentTypes as $type)
                                        <option value="{{ $type->name }}" data-amount="{{ $type->default_amount }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Names/Item -->
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3">Names/Item (Grouped by Description)</label>
                            <div id="namesContainer">
                                <!-- Dynamic sections will be added here -->
                                <div class="text-center py-4 text-muted border rounded bg-light empty-names-message">
                                    <i class="bi bi-info-circle fs-4"></i>
                                    <p class="mb-0">Please select one or more descriptions above to add items.</p>
                                </div>
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
                        @foreach($assessmentTypes as $type)
                            <option value="{{ $type->name }}" {{ request('category') == $type->name ? 'selected' : '' }}>{{ $type->name }}</option>
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
    @if(auth()->user()->username === 'admin')
    <!-- Manage Assessment Types Modal -->
    <div class="modal fade" id="manageTypesModal" tabindex="-1" aria-labelledby="manageTypesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="manageTypesModalLabel"><i class="bi bi-gear"></i> Manage Assessment Descriptions</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Add New Type Form -->
                    <form id="addTypeForm" class="row g-3 mb-4 p-3 border rounded bg-light">
                        <div class="col-md-7">
                            <label class="form-label fw-bold">Description Name</label>
                            <input type="text" id="new_type_name" class="form-control" placeholder="e.g., Example Fee" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Default Price</label>
                            <input type="number" id="new_type_amount" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100"><i class="bi bi-plus-lg"></i> Add</button>
                        </div>
                    </form>

                    <!-- Types List -->
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" id="typesTable">
                            <thead>
                                <tr>
                                    <th>Description Name</th>
                                    <th>Default Price</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assessmentTypes as $type)
                                <tr data-id="{{ $type->id }}">
                                    <td class="type-name">{{ $type->name }}</td>
                                    <td class="type-amount">₱{{ number_format($type->default_amount, 2) }}</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-warning edit-type-btn" data-id="{{ $type->id }}" data-name="{{ $type->name }}" data-amount="{{ $type->default_amount }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-type-btn" data-id="{{ $type->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Type Modal -->
    <div class="modal fade" id="editTypeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header bg-warning">
                    <h6 class="modal-title"><i class="bi bi-pencil"></i> Edit Description</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateTypeForm">
                    <input type="hidden" id="edit_type_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Name</label>
                            <input type="text" id="edit_type_name" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small">Price</label>
                            <input type="number" id="edit_type_amount" class="form-control form-control-sm" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="button" class="btn btn-xs btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-xs btn-warning">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
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
        // Clear previous dynamic sections if any
        document.getElementById('namesContainer').innerHTML = `
            <div class="text-center py-4 text-muted border rounded bg-light empty-names-message">
                <i class="bi bi-info-circle fs-4"></i>
                <p class="mb-0">Please select one or more descriptions above to add items.</p>
            </div>
        `;
        if (window.descriptionSelect) {
            window.descriptionSelect.clear();
        }
    });

    // Add description section
    function addDescriptionSection(description) {
        const container = document.getElementById('namesContainer');
        const emptyMessage = container.querySelector('.empty-names-message');
        if (emptyMessage) emptyMessage.remove();

        const sectionId = 'section_' + description.replace(/[^a-zA-Z0-9]/g, '_');
        
        if (document.getElementById(sectionId)) return;

        const section = document.createElement('div');
        section.className = 'description-section mb-4 p-3 border rounded shadow-sm position-relative';
        section.id = sectionId;
        section.style.backgroundColor = document.documentElement.getAttribute('data-theme') === 'dark' ? 'var(--dark-surface-secondary)' : '#ffffff';
        
        if (description === 'Verification Fee') {
            section.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-success fw-bold">
                        <i class="bi bi-calculator-fill me-2"></i>${description} (Calculated)
                    </h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-success rounded-pill shadow-sm" onclick="addVerificationItem()">
                            <i class="bi bi-plus-lg"></i> Add Item
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeDescriptionSection('${description}')">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <div id="vItemsContainer" class="p-0">
                    <!-- Verification items will be added here -->
                </div>
            `;
            container.appendChild(section);
            addVerificationItem(); // Add first item automatically
        } else {
            section.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-success fw-bold">
                        <i class="bi bi-tag-fill me-2"></i>${description}
                    </h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-success rounded-pill shadow-sm" onclick="addNameRow('${description}')">
                            <i class="bi bi-plus-lg"></i> Add Item
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger border-0" onclick="removeDescriptionSection('${description}')">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="items-container" id="items_${sectionId}">
                    <!-- Item rows for this description -->
                </div>
            `;
            container.appendChild(section);
            // Add first row automatically for standard descriptions
            addNameRow(description);
        }
    }

    // Calculation logic for Verification Fee
    function updateVerificationCalc(element) {
        const itemBlock = element.closest('.verification-item');
        if (!itemBlock) return;

        const corners = parseFloat(itemBlock.querySelector('.v-corners-input').value) || 0;
        const lots = parseFloat(itemBlock.querySelector('.v-lots-input').value) || 0;
        const plans = parseFloat(itemBlock.querySelector('.v-plans-input').value) || 0;
        const extraInput = itemBlock.querySelector('.v-extra-input');
        const extraPlans = parseFloat(extraInput ? extraInput.value : 0) || 0;

        const cornersTotal = corners * 0.5;
        const lotsX5Total = lots * 5;
        const lotsPlus5Total = lots > 0 ? lots + 5 : 0;
        const plansTotal = plans * 6;

        itemBlock.querySelector('.v-corners-total').value = cornersTotal.toFixed(2);
        itemBlock.querySelector('.v-lots-x5-total').value = lotsX5Total.toFixed(2);
        itemBlock.querySelector('.v-lots-plus5-total').value = lotsPlus5Total.toFixed(2);
        itemBlock.querySelector('.v-plans-total').value = plansTotal.toFixed(2);

        const extraContainer = itemBlock.querySelector('.v-extra-plans-container');
        let extraTotal = 0;
        if (plans >= 30) {
            extraContainer.style.display = 'block';
            extraTotal = extraPlans * 6;
            itemBlock.querySelector('.v-extra-total').value = extraTotal.toFixed(2);
        } else {
            extraContainer.style.display = 'none';
            itemBlock.querySelector('.v-extra-total').value = "0.00";
            if (extraInput) extraInput.value = "";
        }

        // Inspection Fee Calculation
        let inspectionTotal = 0;
        const inspectionCheckbox = itemBlock.querySelector('.v-inspection-checkbox');
        const inspectionContainer = itemBlock.querySelector('.v-inspection-container');
        if (inspectionCheckbox && inspectionCheckbox.checked) {
            inspectionContainer.style.display = 'block';
            const iQty = parseFloat(itemBlock.querySelector('.v-inspection-qty').value) || 0;
            const iAmt = parseFloat(itemBlock.querySelector('.v-inspection-amt').value) || 0;
            inspectionTotal = iQty * iAmt;
        } else {
            if (inspectionContainer) inspectionContainer.style.display = 'none';
        }

        const itemSubtotal = cornersTotal + lotsX5Total + lotsPlus5Total + plansTotal + extraTotal + inspectionTotal;
        itemBlock.querySelector('.v-item-subtotal').value = itemSubtotal.toFixed(2);

        calculateTotal();
    }

    function addVerificationItem() {
        const container = document.getElementById('vItemsContainer');
        if (!container) return;
        
        const itemId = 'vItem_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
        
        const itemBlock = document.createElement('div');
        itemBlock.className = 'verification-item mb-4 p-3 border rounded bg-light position-relative shadow-sm';
        itemBlock.id = itemId;
        itemBlock.innerHTML = `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="removeVerificationItem('${itemId}')"></button>
            
            <!-- Inspection Fee Toggle (Moved to Top) -->
            <div class="mb-2">
                <div class="form-check">
                    <input type="hidden" name="items[Verification Fee][has_inspection][]" class="v-inspection-hidden" value="0">
                    <input class="form-check-input v-inspection-checkbox" type="checkbox" onchange="this.previousElementSibling.value = this.checked ? '1' : '0'; updateVerificationCalc(this)">
                    <label class="form-check-label fw-bold small text-primary">Include Inspection Fee (Optional)</label>
                </div>
            </div>

            <div class="v-inspection-container bg-white border p-2 rounded mb-2 shadow-sm" style="display: none;">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="small fw-bold">Inspection Qty</label>
                        <input type="number" name="items[Verification Fee][inspection_qty][]" class="form-control form-control-sm v-inspection-qty" value="1" min="1" oninput="updateVerificationCalc(this)">
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold">Inspection Amount</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="items[Verification Fee][inspection_amt][]" class="form-control form-control-sm v-inspection-amt" step="0.01" min="0" oninput="updateVerificationCalc(this)">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-2 mb-2">
                <div class="col-md-8">
                    <label class="form-label fw-bold small text-success mb-1">Item Name</label>
                    <input type="text" name="items[Verification Fee][name][]" class="form-control form-control-sm" placeholder="Enter item name (e.g., Verification of Lot 1)" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-success mb-1">Quantity</label>
                    <input type="number" name="items[Verification Fee][qty][]" class="form-control form-control-sm quantity-input" value="1" min="1" oninput="updateVerificationCalc(this)">
                </div>
            </div>

            <div class="calculation-block p-2 border rounded bg-white shadow-sm">
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-5">
                        <label class="small fw-bold mb-0"># Number of Corners</label>
                        <input type="number" class="form-control form-control-sm v-corners-input" placeholder="Enter count" oninput="updateVerificationCalc(this)">
                    </div>
                    <div class="col-md-2 text-center small text-muted">x 0.5 =</div>
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0">₱</span>
                            <input type="text" class="form-control form-control-sm bg-light border-start-0 v-corners-total" readonly value="0.00">
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-5">
                        <label class="small fw-bold mb-0"># Number of Lots</label>
                        <input type="number" class="form-control form-control-sm v-lots-input" placeholder="Enter count" oninput="updateVerificationCalc(this)">
                    </div>
                    <div class="col-md-2 text-center small text-muted">x 5 =</div>
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0">₱</span>
                            <input type="text" class="form-control form-control-sm bg-light border-start-0 v-lots-x5-total" readonly value="0.00">
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-5 text-end small fw-bold text-muted">Automatic Calculation: # Lots</div>
                    <div class="col-md-2 text-center small text-muted">+ 5 =</div>
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0">₱</span>
                            <input type="text" class="form-control form-control-sm bg-light border-start-0 v-lots-plus5-total" readonly value="0.00">
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-md-5">
                        <label class="small fw-bold mb-0"># Number of Plans</label>
                        <input type="number" class="form-control form-control-sm v-plans-input" placeholder="Enter count" oninput="updateVerificationCalc(this)">
                    </div>
                    <div class="col-md-2 text-center small text-muted">x 6 =</div>
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0">₱</span>
                            <input type="text" class="form-control form-control-sm bg-light border-start-0 v-plans-total" readonly value="0.00">
                        </div>
                    </div>
                </div>

                <div class="v-extra-plans-container" style="display: none;">
                    <div class="row g-2 mb-2 align-items-center">
                        <div class="col-md-5">
                            <label class="small fw-bold mb-0"># Number of Lot Description (30+)</label>
                            <input type="number" class="form-control form-control-sm v-extra-input" placeholder="Enter count" oninput="updateVerificationCalc(this)">
                        </div>
                        <div class="col-md-2 text-center small text-muted">x 6 =</div>
                        <div class="col-md-5">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0">₱</span>
                                <input type="text" class="form-control form-control-sm bg-light border-start-0 v-extra-total" readonly value="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-2">
                <div class="row align-items-center">
                    <div class="col-md-7 text-end fw-bold small text-success">Unit Price (Calculated):</div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-success text-white border-success">₱</span>
                            <input type="number" name="items[Verification Fee][amt][]" class="form-control bg-success text-white fw-bold v-item-subtotal amount-input" readonly value="0.00" step="0.01">
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(itemBlock);
        calculateTotal();
    }

    function removeVerificationItem(itemId) {
        const item = document.getElementById(itemId);
        if (item) {
            item.remove();
            calculateTotal();
        }
    }

    window.updateVerificationCalc = updateVerificationCalc;

    // Remove description section
    function removeDescriptionSection(description) {
        const sectionId = 'section_' + description.replace(/[^a-zA-Z0-9]/g, '_');
        const section = document.getElementById(sectionId);
        if (section) {
            section.remove();
            calculateTotal();
        }

        const container = document.getElementById('namesContainer');
        if (container.children.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4 text-muted border rounded bg-light empty-names-message">
                    <i class="bi bi-info-circle fs-4"></i>
                    <p class="mb-0">Please select one or more descriptions above to add items.</p>
                </div>
            `;
        }
    }

    // Add new name row for a specific description
    function addNameRow(description) {
        const sectionId = 'section_' + description.replace(/[^a-zA-Z0-9]/g, '_');
        const container = document.querySelector(`#items_${sectionId}`);
        const rowId = 'itemRow_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
        
        // Find default amount from descriptionSelect options
        let defaultAmount = '';
        if (window.descriptionSelect) {
            const option = window.descriptionSelect.options[description];
            if (option && option.$option) {
                defaultAmount = option.$option.getAttribute('data-amount') || '';
            }
        }

        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 align-items-end item-row';
        row.id = rowId;
        row.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="items[${description}][name][]" class="form-control form-control-sm" placeholder="Enter item name" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${description}][qty][]" class="form-control form-control-sm quantity-input" placeholder="Qty" value="1" min="1" oninput="calculateTotal()">
            </div>
            <div class="col-md-4">
                <input type="number" name="items[${description}][amt][]" class="form-control form-control-sm amount-input" placeholder="Amount" step="0.01" min="0" value="${defaultAmount}" oninput="calculateTotal()">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="removeNameRow('${rowId}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        
        container.appendChild(row);
        calculateTotal();
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

    // Initialize Tom Select for the description dropdown
    window.descriptionSelect = new TomSelect('#description-select', {
        plugins: ['remove_button'],
        create: {{ auth()->user()->username === 'admin' ? 'true' : 'false' }},
        createFilter: function(input) {
            return input.length >= 2;
        },
        placeholder: '{{ auth()->user()->username === "admin" ? "Select or type to add descriptions..." : "Select one or more descriptions..." }}',
        onItemAdd:function(value){
            addDescriptionSection(value);
            this.setTextboxValue('');
            this.refreshOptions();
        },
        onItemRemove:function(value){
            removeDescriptionSection(value);
        },
        render:{
            option:function(data,escape){
                return `<div class="d-flex"><span>${escape(data.text)}</span></div>`;
            },
            item:function(data,escape){
                return `<div class="d-flex align-items-center">${escape(data.text)}</div>`;
            },
            option_create: function(data, escape) {
                return '<div class="create">Add <strong>' + escape(data.input) + '</strong>&hellip;</div>';
            }
        }
    });

    @if(auth()->user()->username === 'admin')
    // Manage Assessment Types Logic
    const addTypeForm = document.getElementById('addTypeForm');
    const updateTypeForm = document.getElementById('updateTypeForm');
    const typesTableBody = document.querySelector('#typesTable tbody');

    // Add New Type
    addTypeForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('new_type_name').value;
        const amount = document.getElementById('new_type_amount').value;

        fetch('{{ route("admin.assessment-types.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name, default_amount: amount })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add to table
                const newRow = document.createElement('tr');
                newRow.setAttribute('data-id', data.type.id);
                newRow.innerHTML = `
                    <td class="type-name">${data.type.name}</td>
                    <td class="type-amount">₱${parseFloat(data.type.default_amount).toFixed(2)}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-warning edit-type-btn" data-id="${data.type.id}" data-name="${data.type.name}" data-amount="${data.type.default_amount}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-type-btn" data-id="${data.type.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                typesTableBody.appendChild(newRow);
                
                // Add to TomSelect
                window.descriptionSelect.addOption({
                    value: data.type.name, 
                    text: data.type.name, 
                    amount: data.type.default_amount
                });
                
                // Also need to manually update the underlying <select> option data-amount attribute
                const newOpt = document.createElement('option');
                newOpt.value = data.type.name;
                newOpt.text = data.type.name;
                newOpt.setAttribute('data-amount', data.type.default_amount);
                document.getElementById('description-select').appendChild(newOpt);
                
                // Ensure TomSelect internal options are synced with the new data-amount
                window.descriptionSelect.options[data.type.name].$option = newOpt;
                
                window.descriptionSelect.sync();

                addTypeForm.reset();
                Swal.fire({ icon: 'success', title: 'Added!', text: data.message, timer: 1500, showConfirmButton: false });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message });
            }
        });
    });

    // Delegate Edit/Delete clicks
    document.addEventListener('click', function(e) {
        // Edit button
        if (e.target.closest('.edit-type-btn')) {
            const btn = e.target.closest('.edit-type-btn');
            document.getElementById('edit_type_id').value = btn.dataset.id;
            document.getElementById('edit_type_name').value = btn.dataset.name;
            document.getElementById('edit_type_amount').value = btn.dataset.amount;
            new bootstrap.Modal(document.getElementById('editTypeModal')).show();
        }

        // Delete button
        if (e.target.closest('.delete-type-btn')) {
            const btn = e.target.closest('.delete-type-btn');
            const id = btn.dataset.id;
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/admin/assessment-types/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`tr[data-id="${id}"]`).remove();
                            Swal.fire('Deleted!', data.message, 'success');
                        }
                    });
                }
            });
        }
    });

    // Update Type
    updateTypeForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('edit_type_id').value;
        const name = document.getElementById('edit_type_name').value;
        const amount = document.getElementById('edit_type_amount').value;

        fetch(`/admin/assessment-types/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name, default_amount: amount })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`tr[data-id="${id}"]`);
                row.querySelector('.type-name').textContent = data.type.name;
                row.querySelector('.type-amount').textContent = '₱' + parseFloat(data.type.default_amount).toFixed(2);
                
                // Update edit button data
                const editBtn = row.querySelector('.edit-type-btn');
                editBtn.dataset.name = data.type.name;
                editBtn.dataset.amount = data.type.default_amount;

                bootstrap.Modal.getInstance(document.getElementById('editTypeModal')).hide();
                Swal.fire({ icon: 'success', title: 'Updated!', text: data.message, timer: 1500, showConfirmButton: false });
                
                // Update underlying select option data-amount
                const select = document.getElementById('description-select');
                const opt = select.querySelector(`option[value="${data.type.name}"]`);
                if (opt) {
                    opt.setAttribute('data-amount', data.type.default_amount);
                }
                
                // Update TomSelect internal state
                if (window.descriptionSelect.options[data.type.name]) {
                    window.descriptionSelect.options[data.type.name].amount = data.type.default_amount;
                    if (opt) window.descriptionSelect.options[data.type.name].$option = opt;
                }
                
                window.descriptionSelect.sync();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message });
            }
        });
    });
    @endif
</script>
@endsection
