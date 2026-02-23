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
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-0">Now Serving</h6>
                            <h3 class="mb-0 mt-2" id="servingCount">0</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-check" style="font-size: 2.5rem; opacity: 0.5;"></i>
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
                            <label for="guest_name" class="form-label fw-bold">Guest Name <span class="text-danger">*</span></label>
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

                        <!-- Contact Number -->
                        <div class="mb-3">
                            <label for="contact_number" class="form-label fw-bold">Contact Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" class="form-control @error('contact_number') is-invalid @enderror" 
                                       id="contact_number" name="contact_number" placeholder="09XX XXX XXXX" 
                                       value="{{ old('contact_number') }}">
                            </div>
                            @error('contact_number')
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
                                <span id="section_label" class="text-muted">Select a category to see section details</span>
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
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="priority" id="priority_urgent" value="urgent">
                                    <label class="form-check-label" for="priority_urgent">
                                        <span class="badge bg-danger">Urgent</span>
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
                    <a href="{{ route('monitor.lobby') }}" target="_blank" class="btn btn-light btn-sm">
                        <i class="bi bi-box-arrow-up-right"></i> Open Display
                    </a>
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
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Inquiries (Today)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Queue #</th>
                                    <th>Guest Name</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                    <th>Section</th>
                                </tr>
                            </thead>
                            <tbody id="recentInquiries">
                                @forelse($todayInquiries as $inquiry)
                                    <tr>
                                        <td><span class="badge bg-dark fs-6">{{ $inquiry->queue_number }}</span></td>
                                        <td>{{ $inquiry->guest_name }}</td>
                                        <td>
                                            @if($inquiry->category)
                                                <span class="badge" style="background-color: {{ $inquiry->category->color }}">
                                                    {{ $inquiry->category->code }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
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
                                        <td>{{ $inquiry->created_at->format('h:i A') }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $inquiry->category->section ?? 'N/A' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
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
            const displayNumber = info.now_serving || info.latest_waiting || '---';
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
                            ${info.latest_waiting_category ? `<br><small class="text-muted">${info.latest_waiting_category}</small>` : ''}
                        </div>
                    </div>
                </div>
            `;
        }
        html += '</div>';
        
        container.innerHTML = html;
    }

    // Load recent inquiries
    function loadRecentInquiries() {
        fetch('{{ route('front-desk.recent-inquiries') }}')
            .then(response => response.text())
            .then(html => {
                document.getElementById('recentInquiries').innerHTML = html;
            })
            .catch(error => console.error('Error loading recent inquiries:', error));
    }

    // Auto-refresh every 10 seconds
    setInterval(() => {
        loadQueueStatus();
        loadRecentInquiries();
    }, 10000);

    // Initial load
    loadQueueStatus();
</script>
@endsection
