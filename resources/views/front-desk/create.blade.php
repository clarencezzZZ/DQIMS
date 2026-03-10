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
                    <p class="text-muted mb-0">Enter client information to generate a queue ticket</p>
                </div>
                <a href="{{ route('front-desk.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-clipboard-plus"></i> Client Information</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('front-desk.store') }}" method="POST" id="inquiryForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Client Name -->
                            <div class="col-md-6 mb-3">
                                <label for="guest_name" class="form-label fw-bold">
                                    <i class="bi bi-person text-success"></i> Client Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg @error('guest_name') is-invalid @enderror" 
                                       id="guest_name" name="guest_name" placeholder="Enter full name" 
                                       value="{{ old('guest_name') }}" required autofocus>
                                @error('guest_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label fw-bold">
                                    <i class="bi bi-geo-alt text-success"></i> Address
                                </label>
                                <input type="text" class="form-control form-control-lg @error('address') is-invalid @enderror" 
                                       id="address" name="address" placeholder="Enter address" 
                                       value="{{ old('address') }}">
                                @error('address')
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
                                <span id="section_label" class="fw-bold text-dark">Select a category to see section details</span>
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
                                      placeholder="Brief description of the client's inquiry or purpose of visit...">{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Priority Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-flag text-success"></i> Priority Level
                            </label>
                            <div class="row g-3 justify-content-center">
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

                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-success btn-lg flex-fill" id="submitBtn">
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

<!-- Notification Container -->
<div class="notification-container" id="notificationContainer"></div>

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
    
    .form-check-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    
    /* Animated Alert Styles */
    .notification-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        width: 380px;
    }
    
    .notification-alert {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
        transform: translateX(120%);
        opacity: 0;
        transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        position: relative;
        overflow: hidden;
        border: none;
    }
    
    .notification-alert.show {
        transform: translateX(0);
        opacity: 1;
    }
    
    .notification-alert.error {
        background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
        box-shadow: 0 10px 30px rgba(220, 53, 69, 0.3);
    }
    
    .notification-alert.warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        box-shadow: 0 10px 30px rgba(255, 193, 7, 0.3);
        color: #212529;
    }
    
    .notification-alert .alert-icon {
        font-size: 2.2rem;
        margin-right: 15px;
        animation: pulse 1.5s infinite;
    }
    
    .notification-alert .alert-content h5 {
        font-weight: 600;
        margin: 0 0 8px 0;
        font-size: 1.1rem;
    }
    
    .notification-alert .alert-content p {
        margin: 0;
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .notification-alert .close-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0.7;
        transition: all 0.3s ease;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    
    .notification-alert .close-btn:hover {
        opacity: 1;
        background: rgba(255, 255, 255, 0.2);
        transform: rotate(90deg);
    }
    
    .notification-alert.warning .close-btn {
        color: #212529;
    }
    
    .notification-alert .progress-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 4px;
        background: rgba(255, 255, 255, 0.3);
        width: 100%;
        transform-origin: left;
        transform: scaleX(0);
        animation: progress-decrease 5s linear forwards;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    @keyframes progress-decrease {
        0% { transform: scaleX(1); }
        100% { transform: scaleX(0); }
    }
    
    /* Loading spinner for submit button */
    .btn-loading .spinner-border {
        width: 1.2rem;
        height: 1.2rem;
        margin-right: 8px;
    }
    
    .btn-loading .btn-text {
        opacity: 0.8;
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
            // Show ONLY the section name meaning (without acronym, code, or description)
            const sectionFullNameMap = {
                'ACS': 'AGGREGATE AND CORRECTION',
                'OOSS': 'ORIGINAL AND OTHER SURVEYS',
                'LES': 'LAND EVALUATION',
                'SCS': 'SURVEYS AND CONTROL'
            };
            
            const sectionFullName = sectionFullNameMap[section] || section;
            sectionLabel.textContent = sectionFullName;
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
    
    // Test if JavaScript is working
    console.log('Front Desk Create page loaded - JavaScript is working!');
    
    // Notification System
    const notificationContainer = document.getElementById('notificationContainer');
    
    // Show notification function
    function showNotification(message, type = 'success', title = 'Success', autoClose = true) {
        const alert = document.createElement('div');
        alert.className = `notification-alert ${type}`;
        
        let icon = 'bi-check-circle-fill';
        if (type === 'error') icon = 'bi-x-circle-fill';
        if (type === 'warning') icon = 'bi-exclamation-triangle-fill';
        
        alert.innerHTML = `
            <div class="d-flex align-items-start">
                <i class="alert-icon bi ${icon}"></i>
                <div class="alert-content flex-grow-1">
                    <h5>${title}</h5>
                    <p>${message}</p>
                </div>
                <button class="close-btn" onclick="this.parentElement.parentElement.remove()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="progress-bar"></div>
        `;
        
        notificationContainer.appendChild(alert);
        
        // Trigger animation
        setTimeout(() => {
            alert.classList.add('show');
        }, 10);
        
        // Auto close after 5 seconds
        if (autoClose) {
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.classList.remove('show');
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, 500);
                }
            }, 5000);
        }
    }
    
    // AJAX Form Submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('inquiryForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submission processing...');
                
                // Show loading state
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                    <span class="btn-text">Processing...</span>
                `;
                submitBtn.disabled = true;
                
                // Get form data
                const formData = new FormData(form);
                
                // Submit via AJAX
                fetch('{{ route('front-desk.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success notification
                        alert(`SUCCESS!\nQueue Number: ${data.queue_number}\nGuest: ${data.guest_name}\nCategory: ${data.category}`);
                        
                        // Reset form
                        form.reset();
                        document.getElementById('section_label').textContent = 'Select a category to see section details';
                        document.getElementById('section_label').className = 'text-muted';
                        updatePriorityCards();
                    } else {
                        alert('Form submission failed. Please check the form and try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An unexpected error occurred. Please try again.');
                })
                .finally(() => {
                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });
        }
    });
</script>
@endsection
