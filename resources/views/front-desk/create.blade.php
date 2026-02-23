@extends('layouts.app')

@section('title', 'Create New Inquiry')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1"><i class="bi bi-person-plus text-success"></i> Create New Inquiry</h2>
                    <p class="text-muted mb-0">Enter guest information to generate a queue ticket</p>
                </div>
                <a href="{{ route('front-desk.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-clipboard-plus"></i> Guest Information</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('front-desk.store') }}" method="POST" id="inquiryForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Guest Name -->
                            <div class="col-md-6 mb-3">
                                <label for="guest_name" class="form-label fw-bold">
                                    <i class="bi bi-person text-success"></i> Guest Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('guest_name') is-invalid @enderror" 
                                       id="guest_name" name="guest_name" placeholder="Enter full name" 
                                       value="{{ old('guest_name') }}" required autofocus>
                                @error('guest_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Contact Number -->
                            <div class="col-md-6 mb-3">
                                <label for="contact_number" class="form-label fw-bold">
                                    <i class="bi bi-telephone text-success"></i> Contact Number
                                </label>
                                <input type="tel" class="form-control form-control-lg @error('contact_number') is-invalid @enderror" 
                                       id="contact_number" name="contact_number" placeholder="09XX XXX XXXX" 
                                       value="{{ old('contact_number') }}">
                                @error('contact_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Category Selection -->
                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-bold">
                                <i class="bi bi-tag text-success"></i> Category <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            data-section="{{ $category->section }}"
                                            data-code="{{ $category->code }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Section Label (Auto-populated from Category) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-building text-success"></i> Section
                            </label>
                            <div class="p-3 rounded border" style="background-color: #e9ecef;">
                                <span id="section_label" class="text-muted">Select a category to see section details</span>
                            </div>
                            <input type="hidden" id="section" name="section">
                        </div>

                        <!-- Purpose/Notes -->
                        <div class="mb-4">
                            <label for="purpose" class="form-label fw-bold">
                                <i class="bi bi-chat-text text-success"></i> Purpose / Notes
                            </label>
                            <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                      id="purpose" name="purpose" rows="4" 
                                      placeholder="Brief description of the guest's inquiry or purpose of visit...">{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Priority Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-flag text-success"></i> Priority Level
                            </label>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-check card priority-card" id="card_normal" data-priority="normal" onclick="selectPriority('normal')">
                                        <input class="form-check-input" type="radio" name="priority" 
                                               id="priority_normal" value="normal" checked>
                                        <label class="form-check-label w-100 p-4 text-center" for="priority_normal" onclick="event.stopPropagation()">
                                            <div class="check-indicator">
                                                <i class="bi bi-check-lg"></i>
                                            </div>
                                            <div class="icon-wrapper mb-3">
                                                <i class="bi bi-circle" style="font-size: 2.5rem; color: #6c757d;"></i>
                                            </div>
                                            <h5 class="mb-2">Normal</h5>
                                            <small class="text-muted">Standard queue</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check card priority-card" id="card_priority" data-priority="priority" onclick="selectPriority('priority')">
                                        <input class="form-check-input" type="radio" name="priority" 
                                               id="priority_priority" value="priority">
                                        <label class="form-check-label w-100 p-4 text-center" for="priority_priority" onclick="event.stopPropagation()">
                                            <div class="check-indicator">
                                                <i class="bi bi-check-lg"></i>
                                            </div>
                                            <div class="icon-wrapper mb-3">
                                                <i class="bi bi-exclamation-circle" style="font-size: 2.5rem; color: #ffc107;"></i>
                                            </div>
                                            <h5 class="mb-2">Priority</h5>
                                            <small class="text-muted">Senior/PWD/Pregnant</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check card priority-card" id="card_urgent" data-priority="urgent" onclick="selectPriority('urgent')">
                                        <input class="form-check-input" type="radio" name="priority" 
                                               id="priority_urgent" value="urgent">
                                        <label class="form-check-label w-100 p-4 text-center" for="priority_urgent" onclick="event.stopPropagation()">
                                            <div class="check-indicator">
                                                <i class="bi bi-check-lg"></i>
                                            </div>
                                            <div class="icon-wrapper mb-3">
                                                <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem; color: #dc3545;"></i>
                                            </div>
                                            <h5 class="mb-2">Urgent</h5>
                                            <small class="text-muted">Emergency cases</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-success btn-lg flex-fill">
                                <i class="bi bi-ticket-perforated"></i> Generate Queue Ticket
                            </button>
                            <a href="{{ route('front-desk.index') }}" class="btn btn-outline-secondary btn-lg">
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

@section('styles')
<style>
    .priority-card {
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid #dee2e6;
        position: relative;
        overflow: hidden;
        background: white;
        padding: 0 !important;
    }
    
    .priority-card:hover {
        border-color: #28a745;
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 12px 24px rgba(0,0,0,0.15), 0 4px 8px rgba(40, 167, 69, 0.2);
    }
    
    .priority-card .form-check-label {
        transition: all 0.3s ease;
        display: block;
        margin: 0;
        cursor: pointer;
        padding: 1.5rem !important;
        width: 100%;
        height: 100%;
        min-height: 180px;
    }
    
    /* Selected state - full card color */
    .priority-card.selected {
        border-color: #28a745;
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        transform: scale(1.02);
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
    
    .priority-card.selected .form-check-label {
        background: transparent !important;
        color: white;
    }
    
    .priority-card.selected .form-check-label .text-muted {
        color: rgba(255, 255, 255, 0.9) !important;
    }
    
    .priority-card.selected .form-check-label i {
        color: white !important;
        transform: scale(1.2);
    }
    
    /* Priority card specific colors when selected */
    .priority-card.selected[data-priority="priority"] {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        border-color: #ffc107;
    }
    
    .priority-card.selected[data-priority="urgent"] {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border-color: #dc3545;
    }
    
    .priority-card.selected[data-priority="normal"] {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-color: #28a745;
    }
    
    /* Icon animations */
    .priority-card i {
        transition: all 0.3s ease;
    }
    
    .priority-card:hover i {
        transform: scale(1.15);
    }
    
    /* Checkmark indicator */
    .priority-card .check-indicator {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.95);
        color: #28a745;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s ease;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        font-size: 1rem;
        font-weight: bold;
    }
    
    .priority-card.selected .check-indicator {
        opacity: 1;
        transform: scale(1);
    }
    
    .priority-card.selected[data-priority="priority"] .check-indicator {
        color: #ff9800;
    }
    
    .priority-card.selected[data-priority="urgent"] .check-indicator {
        color: #dc3545;
    }
    
    .form-check-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
</style>
@endsection

@section('scripts')
<script>
    // Auto-populate section label when category is selected
    document.getElementById('category_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const section = selectedOption.getAttribute('data-section');
        const code = selectedOption.getAttribute('data-code');
        const name = selectedOption.textContent.trim();
        
        const sectionLabel = document.getElementById('section_label');
        const sectionInput = document.getElementById('section');
        
        if (section && code) {
            // Show full section label: ACS - SECSIME NO.R4A-L_SMD-01. CANCELATION OF PREVIOUSLY APPROVED SURVEY PLANS(DAR)
            sectionLabel.textContent = section + ' - ' + code + '. ' + name;
            sectionLabel.className = 'fw-bold text-dark';
            sectionInput.value = section;
        } else {
            // Reset to default
            sectionLabel.textContent = 'Select a category to see section details';
            sectionLabel.className = 'text-muted';
            sectionInput.value = '';
        }
    });

    // Trigger change event on page load if category is already selected (old input)
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category_id');
        if (categorySelect.value) {
            categorySelect.dispatchEvent(new Event('change'));
        }
        
        // Initialize priority card selection
        updatePriorityCards();
    });

    // Priority card selection function
    function selectPriority(priority) {
        // Check the radio input
        const radioInput = document.getElementById('priority_' + priority);
        if (radioInput) {
            radioInput.checked = true;
        }
        
        // Update visual selection
        updatePriorityCards();
    }

    // Update priority card visual state
    function updatePriorityCards() {
        // Remove selected class from all cards
        document.querySelectorAll('.priority-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Add selected class to checked card
        const checkedInput = document.querySelector('input[name="priority"]:checked');
        if (checkedInput) {
            const cardId = 'card_' + checkedInput.value;
            const selectedCard = document.getElementById(cardId);
            if (selectedCard) {
                selectedCard.classList.add('selected');
            }
        }
    }
    
    // Make selectPriority globally accessible
    window.selectPriority = selectPriority;
    window.updatePriorityCards = updatePriorityCards;

    // Add change listener to radio inputs as backup
    const priorityInputs = document.querySelectorAll('input[name="priority"]');
    priorityInputs.forEach(input => {
        input.addEventListener('change', updatePriorityCards);
    });
</script>
@endsection
