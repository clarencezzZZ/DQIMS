@extends('layouts.app')

@section('title', 'Front Desk - Ground Floor')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-reception-4 text-success"></i> Front Desk (Ground Floor)</h2>
                    <p class="text-muted mb-0">Manage guest inquiries and queue system</p>
                </div>
                <a href="{{ route('front-desk.create') }}" class="btn btn-success btn-lg">
                    <i class="bi bi-plus-circle"></i> New Inquiry
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Today's Inquiries</h6>
                            <h3 class="mb-0 mt-2" id="todayCount">0</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-check" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Waiting</h6>
                            <h3 class="mb-0 mt-2" id="waitingCount">0</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-hourglass-split" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white" style="background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%) !important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0 fw-bold">Now Serving</h6>
                            <h3 class="mb-0 mt-2" id="servingCount">0</h3>
                            <small class="mt-1 d-block opacity-75">Currently being served</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-video3" style="font-size: 2.5rem; opacity: 0.8;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Completed</h6>
                            <h3 class="mb-0 mt-2" id="completedCount">0</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Create Inquiry Form -->
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-person-plus"></i> Create New Inquiry</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('front-desk.store') }}" method="POST" id="inquiryForm">
                        @csrf
                        
                        <!-- Guest Name -->
                        <div class="mb-3">
                            <label for="guest_name" class="form-label fw-bold">Guest Name<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control @error('guest_name') is-invalid @enderror" 
                                       id="guest_name" name="guest_name" placeholder="Enter guest full name" 
                                       value="{{ old('guest_name') }}" required>
                            </div>
                            @error('guest_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label fw-bold">Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       id="address" name="address" placeholder="Enter address" 
                                       value="{{ old('address') }}">
                            </div>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category Selection -->
                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                data-section="{{ $category->section }}"
                                                data-code="{{ $category->code }}">
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Section Label (Auto-populated from Category) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Section</label>
                            <div class="p-2 rounded" style="background-color: #e9ecef; border: 1px solid #ced4da;">
                                <span id="section_label" class="fw-bold text-dark">Select a category to see section details</span>
                            </div>
                            <input type="hidden" id="section" name="section">
                        </div>

                        <!-- Purpose/Notes -->
                        <div class="mb-3">
                            <label for="purpose" class="form-label fw-bold">Purpose / Notes</label>
                            <textarea class="form-control @error('purpose') is-invalid @enderror" 
                                      id="purpose" name="purpose" rows="3" 
                                      placeholder="Brief description of the inquiry...">{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Priority</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="priority" id="priority_normal" value="normal" checked>
                                    <label class="form-check-label" for="priority_normal">
                                        <span class="badge bg-secondary">Normal</span>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="priority" id="priority_priority" value="priority">
                                    <label class="form-check-label" for="priority_priority">
                                        <span class="badge bg-warning text-dark">Priority</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-ticket-perforated"></i> Generate Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Queue Status & Recent Inquiries -->
        <div class="col-lg-7">
            <!-- Queue Status -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-display"></i> Live Queue Status</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('monitor.lobby') }}" target="_blank" class="btn btn-light" title="Open Main Lobby Display">
                            <i class="bi bi-box-arrow-up-right"></i> Main Lobby
                        </a>
                        <a href="{{ route('monitor.lobby1') }}" target="_blank" class="btn btn-light" title="Open SCS & LES Display">
                            <i class="bi bi-box-arrow-up-right"></i> Lobby 1
                        </a>
                        <a href="{{ route('monitor.lobby2') }}" target="_blank" class="btn btn-light" title="Open ACS & OOSS Display">
                            <i class="bi bi-box-arrow-up-right"></i> Lobby 2
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="queueStatus">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading queue status...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Inquiries -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Recent Inquiries (Today)</h5>
                        <span class="badge bg-light text-dark">{{ count($todayInquiries) }} total</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="8%" class="text-center py-3 fw-bold">#</th>
                                    <th width="18%" class="py-3 fw-bold"><i class="bi bi-person me-1"></i>GUEST NAME</th>
                                    <th width="32%" class="py-3 fw-bold"><i class="bi bi-tag me-1"></i>SERVICE TYPE</th>
                                    <th width="10%" class="text-center py-3 fw-bold"><i class="bi bi-hourglass-split me-1"></i>STATUS</th>
                                    <th width="10%" class="text-center py-3 fw-bold"><i class="bi bi-clock me-1"></i>TIME</th>
                                    <th width="22%" class="py-3 fw-bold"><i class="bi bi-geo-alt me-1"></i>SECTION</th>
                                    <th width="10%" class="text-center py-3 fw-bold"><i class="bi bi-printer me-1"></i>ACTION</th>
                                </tr>
                            </thead>
                            <tbody id="recentInquiries">
                                @forelse($todayInquiries as $index => $inquiry)
                                    <tr>
                                        <td><span class="badge bg-dark fs-6">#{{ $index + 1 }}</span></td>
                                        <td>{{ $inquiry->guest_name }}</td>
                                        <td>
                                            @if($inquiry->category)
                                                <span class="badge" style="background-color: {{ $inquiry->category->color }}">
                                                    {{ $inquiry->category->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($inquiry->status == 'waiting')
                                                <span class="badge bg-warning text-dark">Waiting</span>
                                            @elseif($inquiry->status == 'serving')
                                                <span class="badge bg-info">Serving</span>
                                            @elseif($inquiry->status == 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif($inquiry->status == 'skipped')
                                                <span class="badge bg-danger">Skipped</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $inquiry->created_at->format('h:i A') }}</td>
                                        <td>
                                            @if($inquiry->category)
                                                @php
                                                    $sectionNames = [
                                                        'ACS' => 'AGGREGATE AND CORRECTION SECTION',
                                                        'OOSS' => 'ORIGINAL AND OTHER SURVEYS SECTION',
                                                        'LES' => 'LAND EVALUATION SECTION',
                                                        'SCS' => 'SURVEYS AND CONTROL SECTION',
                                                    ];
                                                    $sectionAcronym = $inquiry->category->section;
                                                @endphp
                                                <span class="badge bg-secondary">{{ $sectionNames[$sectionAcronym] ?? $sectionAcronym }}</span>
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('front-desk.ticket', $inquiry) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Print Ticket">
                                                <i class="bi bi-printer"></i> Print
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">No inquiries yet today</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
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
            // Show ONLY the full section name meaning (without acronym, code, or description)
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

    // Load queue status
    function loadQueueStatus() {
        fetch('{{ route('front-desk.queue-status') }}')
            .then(response => response.json())
            .then(data => {
                updateStats(data);
                updateQueueDisplay(data);
            })
            .catch(error => console.error('Error loading queue status:', error));
    }

    // Update statistics
    function updateStats(data) {
        document.getElementById('todayCount').textContent = data.today_count || 0;
        document.getElementById('waitingCount').textContent = data.waiting_count || 0;
        document.getElementById('servingCount').textContent = data.serving_count || 0;
        document.getElementById('completedCount').textContent = data.completed_count || 0;
    }

    // Update queue display
    function updateQueueDisplay(data) {
        const container = document.getElementById('queueStatus');
        
        if (!data.sections || Object.keys(data.sections).length === 0) {
            container.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-pause-circle" style="font-size: 3rem;"></i>
                    <p class="mt-2">No queue data available</p>
                </div>
            `;
            return;
        }

        let html = '<div class="row">';
        for (const [section, info] of Object.entries(data.sections)) {
            const hasWaiting = info.waiting_count > 0;
            // Format queue number to show only #N format
            let displayNumber = '---';
            if (info.now_serving) {
                displayNumber = formatQueueNumber(info.now_serving);
            } else if (info.latest_waiting) {
                displayNumber = formatQueueNumber(info.latest_waiting);
            }
            const statusText = info.now_serving ? 'Now Serving' : (hasWaiting ? 'Next in Queue' : 'No Queue');
            const statusClass = info.now_serving ? 'text-success' : (hasWaiting ? 'text-warning' : 'text-muted');
            
            html += `
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card border-info h-100">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <span class="fw-bold">${section}</span>
                            <span class="badge bg-dark">Waiting: ${info.waiting_count}</span>
                        </div>
                        <div class="card-body text-center">
                            <h2 class="${statusClass} mb-0 fw-bold">${displayNumber}</h2>
                            <small class="text-muted">${statusText}</small>
                            ${'' /* Removed category code display */}
                        </div>
                    </div>
                </div>
            `;
        }
        html += '</div>';
        
        container.innerHTML = html;
    }

    // Format queue number to show only sequential number as #N
    function formatQueueNumber(fullQueueNumber) {
        if (!fullQueueNumber) return '---';
        
        // Try to extract the last number after the last hyphen
        // e.g., "SECSIME NO.R4A-L_SMD-01-009" -> "#9"
        const parts = fullQueueNumber.split('-');
        if (parts.length > 0) {
            const lastPart = parts[parts.length - 1];
            const num = parseInt(lastPart.replace(/^0+/, '')); // Remove leading zeros
            if (!isNaN(num)) {
                return '#' + num;
            }
        }
        // Fallback: if no number found, return the original
        return fullQueueNumber;
    }

    // Load queue status only (recent inquiries static - no auto-refresh)
    function loadQueueStatusOnly() {
        fetch('{{ route('front-desk.queue-status') }}')
            .then(response => response.json())
            .then(data => {
                updateStats(data);
                updateQueueDisplay(data);
            })
            .catch(error => console.error('Error loading queue status:', error));
    }

    // Auto-refresh queue status only every 10 seconds (recent inquiries remain static)
    setInterval(() => {
        loadQueueStatusOnly();
    }, 10000);

    // Initial load
    loadQueueStatusOnly();
    
    // Add notification container
    const notificationContainer = document.createElement('div');
    notificationContainer.className = 'notification-container';
    notificationContainer.id = 'notificationContainer';
    document.body.appendChild(notificationContainer);
    
    // Notification System
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
    
    // Professional Contact Validation Debugging - Remove all contact validation
    // Contact field has been replaced with Address field
    
    // AJAX Form Submission for index page
    const form = document.getElementById('inquiryForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
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
                    showNotification(
                        `Queue Number: <strong>${data.queue_number}</strong><br>Guest: ${data.guest_name}<br>Category: ${data.category}`,
                        'success',
                        'Generated Ticket Submitted!'
                    );
                    
                    // Reset form
                    form.reset();
                    document.getElementById('section_label').textContent = 'Select a category to see section details';
                    document.getElementById('section_label').className = 'text-muted';
                    
                    // Reload queue status only (recent inquiries remain static)
                    loadQueueStatusOnly();
                } else {
                    // Show error notification
                    showNotification('Please check the form for errors and try again.', 'error', 'Submission Failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An unexpected error occurred. Please try again.', 'error', 'Error');
            })
            .finally(() => {
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
    
    // Make functions globally accessible
    window.showNotification = showNotification;
</script>
@endsection
