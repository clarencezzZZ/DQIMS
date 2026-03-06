@extends('layouts.app')

@section('title', 'Edit Assessment')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1"><i class="bi bi-pencil-square text-warning"></i> Edit Assessment</h2>
                    <p class="text-muted mb-0">Update assessment details</p>
                </div>
                <a href="{{ route('admin.assessments') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Assessments
                </a>
            </div>

            <!-- Assessment Form -->
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="bi bi-calculator"></i> Assessment Details</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.assessments.update', $assessment) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Assessment Number -->
                        <div class="mb-4">
                            <label for="assessment_number" class="form-label fw-bold">
                                <i class="bi bi-tag text-warning"></i> Assessment Number
                            </label>
                            <input type="text" class="form-control bg-light" id="assessment_number" value="{{ $assessment->assessment_number }}" readonly>
                        </div>

                        <!-- Guest Name -->
                        <div class="mb-4">
                            <label for="guest_name" class="form-label fw-bold">
                                <i class="bi bi-person text-warning"></i> Guest Name
                            </label>
                            <input type="text" class="form-control" id="guest_name" name="guest_name" value="{{ old('guest_name', $assessment->guest_name) }}">
                        </div>

                        <!-- Address -->
                        <div class="mb-4">
                            <label for="address" class="form-label fw-bold">
                                <i class="bi bi-house text-warning"></i> Address
                            </label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $assessment->address) }}">
                        </div>

                        <!-- Description/Request Type -->
                        <div class="mb-4">
                            <label for="description_type" class="form-label fw-bold">
                                <i class="bi bi-folder text-warning"></i> Description/Request Type
                            </label>
                            <select name="description_type" class="form-select @error('description_type') is-invalid @enderror" 
                                    id="description_type" required>
                                <option value="">-- Select Description --</option>
                                <option value="Cadastral Cost" {{ old('description_type', $assessment->request_type) == 'Cadastral Cost' ? 'selected' : '' }}>Cadastral Cost</option>
                                <option value="Certification: A&D Status" {{ old('description_type', $assessment->request_type) == 'Certification: A&D Status' ? 'selected' : '' }}>Certification: A&D Status</option>
                                <option value="Certification: Cadastral Map" {{ old('description_type', $assessment->request_type) == 'Certification: Cadastral Map' ? 'selected' : '' }}>Certification: Cadastral Map</option>
                                <option value="Certification Cancellation Of Approved Plan" {{ old('description_type', $assessment->request_type) == 'Certification Cancellation Of Approved Plan' ? 'selected' : '' }}>Certification Cancellation Of Approved Plan</option>
                                <option value="Certification GPPC" {{ old('description_type', $assessment->request_type) == 'Certification GPPC' ? 'selected' : '' }}>Certification GPPC</option>
                                <option value="Certification Lot Data Computation" {{ old('description_type', $assessment->request_type) == 'Certification Lot Data Computation' ? 'selected' : '' }}>Certification Lot Data Computation</option>
                                <option value="Certification Lot Status" {{ old('description_type', $assessment->request_type) == 'Certification Lot Status' ? 'selected' : '' }}>Certification Lot Status</option>
                                <option value="Certification Rejection Order" {{ old('description_type', $assessment->request_type) == 'Certification Rejection Order' ? 'selected' : '' }}>Certification Rejection Order</option>
                                <option value="Certification Survey Plan" {{ old('description_type', $assessment->request_type) == 'Certification Survey Plan' ? 'selected' : '' }}>Certification Survey Plan</option>
                                <option value="Certification: Technical Description" {{ old('description_type', $assessment->request_type) == 'Certification: Technical Description' ? 'selected' : '' }}>Certification: Technical Description</option>
                                <option value="GE Credit" {{ old('description_type', $assessment->request_type) == 'GE Credit' ? 'selected' : '' }}>GE Credit</option>
                                <option value="Verification Fee" {{ old('description_type', $assessment->request_type) == 'Verification Fee' ? 'selected' : '' }}>Verification Fee</option>
                                <option value="Inspection Fee" {{ old('description_type', $assessment->request_type) == 'Inspection Fee' ? 'selected' : '' }}>Inspection Fee</option>
                            </select>
                            @error('description_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select the same description options as used in new assessment requests</div>
                        </div>

                        <!-- Fees -->
                        <div class="mb-4">
                            <label for="fees" class="form-label fw-bold">
                                <i class="bi bi-currency-peso text-warning"></i> Assessment Fees (₱) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">₱</span>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('fees') is-invalid @enderror" 
                                       id="fees" name="fees" placeholder="0.00" 
                                       value="{{ old('fees', $assessment->fees) }}" required>
                            </div>
                            @error('fees')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Enter the total assessment fees in Philippine Peso</div>
                        </div>

                        <!-- Officer incharge Selection -->
                        <div class="mb-4">
                            <label for="officer_in_charge" class="form-label fw-bold">
                                <i class="bi bi-person-badge text-warning"></i> Officer incharge <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('officer_in_charge') is-invalid @enderror" 
                                    id="officer_in_charge" name="officer_in_charge" required>
                                <option value="">-- Select Officer incharge --</option>
                                <option value="lota" {{ old('officer_in_charge', $assessment->officer_in_charge) === 'lota' || $assessment->officer_in_charge === 'lota' ? 'selected' : '' }}>Mr. Stanley M. Lota</option>
                            </select>
                            @error('officer_in_charge')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Names/Item -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-bold">Names/Item</label>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="addNameRowEdit()">
                                    <i class="bi bi-plus-lg"></i> Add Item
                                </button>
                            </div>
                            <div id="namesContainer">
                                <!-- Dynamic rows will be added here -->
                            </div>
                            <div class="text-end mt-3">
                                <h5 class="mb-0">Total: ₱<span id="totalAmount">{{ number_format($assessment->fees, 2) }}</span></h5>
                                <input type="hidden" name="fees" id="feesInput" value="{{ $assessment->fees }}">
                            </div>
                        </div>

                        <!-- Remarks -->
                        <div class="mb-4">
                            <label for="remarks" class="form-label fw-bold">
                                <i class="bi bi-chat-text text-warning"></i> Remarks / Notes
                            </label>
                            <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                      id="remarks" name="remarks" rows="4" 
                                      placeholder="Additional notes, requirements, or remarks...">{{ old('remarks', $assessment->remarks) }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-warning btn-lg flex-fill">
                                <i class="bi bi-check-circle"></i> Update Assessment
                            </button>
                            <a href="{{ route('admin.assessments') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialize names data from the assessment
    document.addEventListener('DOMContentLoaded', function() {
        const namesDetail = @json(json_decode($assessment->names_detail, true));
        if (namesDetail && Array.isArray(namesDetail)) {
            namesDetail.forEach(item => {
                // Skip template placeholders
                if (item.name && item.name !== '${name}' && item.name.trim() !== '') {
                    addNameRowEdit(item.name, item.quantity || 1, item.amount || 0);
                }
            });
        }
        // Add at least one row if no valid names exist
        if (document.getElementById('namesContainer').children.length === 0) {
            addNameRowEdit();
        }
    });

    // Add new name row for edit form
    function addNameRowEdit(name = '', quantity = 1, amount = 0) {
        const container = document.getElementById('namesContainer');
        const rowId = 'nameRow_' + Date.now();
        
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 align-items-end';
        row.id = rowId;
        row.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="names[]" class="form-control" placeholder="Enter item name" value="${name.replace(/\$/g, '\$\$')}" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="quantities[]" class="form-control quantity-input" placeholder="Qty" value="${quantity}" min="1" onchange="calculateTotalEdit()">
            </div>
            <div class="col-md-4">
                <input type="number" name="amounts[]" class="form-control amount-input" placeholder="Amount" step="0.01" min="0" value="${amount}" onchange="calculateTotalEdit()">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeNameRowEdit('${rowId}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        
        container.appendChild(row);
        calculateTotalEdit(); // Recalculate total after adding row
    }

    // Remove name row for edit form
    function removeNameRowEdit(rowId) {
        const row = document.getElementById(rowId);
        if (row) {
            row.remove();
            calculateTotalEdit();
        }
    }

    // Calculate total amount for edit form
    function calculateTotalEdit() {
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



    // Format fees input
    document.getElementById('fees')?.addEventListener('blur', function() {
        if (this.value) {
            this.value = parseFloat(this.value).toFixed(2);
        }
    });
</script>
@endsection