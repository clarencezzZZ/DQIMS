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

                        <!-- Client Name -->
                        <div class="mb-4">
                            <label for="guest_name" class="form-label fw-bold">
                                <i class="bi bi-person text-warning"></i> Client Name
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
                            <select id="description-select" name="description_type[]" multiple required>
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
                            @error('description_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                            <label class="form-label fw-bold mb-3">Names/Item (Grouped by Description)</label>
                            <div id="namesContainer">
                                <!-- Dynamic sections will be added here -->
                                <div class="text-center py-4 text-muted border rounded bg-light empty-names-message">
                                    <i class="bi bi-info-circle fs-4"></i>
                                    <p class="mb-0">Please select one or more descriptions above to add items.</p>
                                </div>
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
        const currentDescriptions = @json(json_decode($assessment->request_type, true) ?: [$assessment->request_type]);
        
        // Initialize Tom Select
        window.descriptionSelect = new TomSelect('#description-select', {
            plugins: ['remove_button'],
            create: true,
            placeholder: 'Select one or more descriptions...',
            onItemAdd: function(value) {
                addDescriptionSection(value);
            },
            onItemRemove: function(value) {
                removeDescriptionSection(value);
            }
        });

        // Load existing descriptions and items
        if (Array.isArray(currentDescriptions)) {
            currentDescriptions.forEach(desc => {
                if (desc) {
                    window.descriptionSelect.addItem(desc);
                    // Clear the automatically added row so we can add existing ones
                    const sectionId = 'section_' + desc.replace(/[^a-zA-Z0-9]/g, '_');
                    const itemsContainer = document.getElementById('items_' + sectionId);
                    if (itemsContainer) itemsContainer.innerHTML = '';
                }
            });
        }

        // Add existing items to their respective sections
        if (namesDetail && Array.isArray(namesDetail)) {
            namesDetail.forEach(item => {
                if (item.name && item.name !== '${name}' && item.name.trim() !== '') {
                    const desc = item.description || 'GENERAL';
                    addNameRowEdit(desc, item.name, item.quantity || 1, item.amount || 0);
                }
            });
        }
        
        calculateTotalEdit();
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
        section.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-success fw-bold">
                    <i class="bi bi-tag-fill me-2"></i>${description}
                </h6>
                <button type="button" class="btn btn-sm btn-success rounded-pill" onclick="addNameRowEdit('${description}')">
                    <i class="bi bi-plus-lg"></i> Add Item
                </button>
            </div>
            <div class="items-container" id="items_${sectionId}">
                <!-- Item rows for this description -->
            </div>
        `;
        
        container.appendChild(section);
        addNameRowEdit(description); // Add first row automatically
    }

    // Remove description section
    function removeDescriptionSection(description) {
        const sectionId = 'section_' + description.replace(/[^a-zA-Z0-9]/g, '_');
        const section = document.getElementById(sectionId);
        if (section) {
            section.remove();
            calculateTotalEdit();
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

    // Add new name row for a specific description (Edit)
    function addNameRowEdit(description, name = '', quantity = 1, amount = null) {
        const sectionId = 'section_' + description.replace(/[^a-zA-Z0-9]/g, '_');
        let container = document.querySelector(`#items_${sectionId}`);
        
        // Fallback for old items without a description
        if (!container && description === 'GENERAL') {
            addDescriptionSection('GENERAL');
            container = document.querySelector(`#items_section_GENERAL`);
        }
        
        if (!container) return;

        const rowId = 'itemRow_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
        
        // Default amount logic: 50 if description starts with 'Certification' and amount is null (meaning it's a new row)
        let finalAmount = amount;
        if (finalAmount === null) {
            finalAmount = description.toLowerCase().startsWith('certification') ? 50 : 0;
        }
        
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 align-items-end item-row';
        row.id = rowId;
        row.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="items[${description}][name][]" class="form-control form-control-sm" placeholder="Enter item name" value="${name.replace(/\$/g, '\$\$')}" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${description}][qty][]" class="form-control form-control-sm quantity-input" placeholder="Qty" value="${quantity}" min="1" oninput="calculateTotalEdit()">
            </div>
            <div class="col-md-4">
                <input type="number" name="items[${description}][amt][]" class="form-control form-control-sm amount-input" placeholder="Amount" step="0.01" min="0" value="${finalAmount}" oninput="calculateTotalEdit()">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="removeNameRowEdit('${rowId}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        
        container.appendChild(row);
        calculateTotalEdit();
    }

    // Remove name row (Edit)
    function removeNameRowEdit(rowId) {
        const row = document.getElementById(rowId);
        if (row) {
            row.remove();
            calculateTotalEdit();
        }
    }

    // Calculate total amount (Edit)
    function calculateTotalEdit() {
        let total = 0;
        const amounts = document.querySelectorAll('.amount-input');
        const quantities = document.querySelectorAll('.quantity-input');
        
        amounts.forEach((amount, index) => {
            const qty = quantities[index] ? parseFloat(quantities[index].value) || 0 : 0;
            const amt = parseFloat(amount.value) || 0;
            total += (qty * amt);
        });
        
        document.getElementById('totalAmount').textContent = total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('feesInput').value = total.toFixed(2);
    }
</script>
@endsection