@extends('layouts.app')

@section('title', 'Section Staff Dashboard - DENR DQIMS')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-building text-success"></i> Section Staff Dashboard</h2>
                    <p class="text-muted mb-0">Manage all inquiries in your assigned section</p>
                </div>
                @if(isset($section))
                    <div class="badge bg-success" style="padding: 10px 20px; font-size: 1rem;">
                        <i class="bi bi-folder"></i> {{ $section }} ({{ $sectionCategories->count() }} categories)
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(isset($section))
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-warning shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-hourglass-split text-warning" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Waiting (All Section)</h6>
                        <h2 class="mb-0 fw-bold" id="waiting-count" style="color: var(--denr-green);">0</h2>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-info shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-check text-info" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Serving (All Section)</h6>
                        <h2 class="mb-0 fw-bold" id="serving-count" style="color: var(--denr-green);">0</h2>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-success shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Completed (All Section)</h6>
                        <h2 class="mb-0 fw-bold" id="completed-count" style="color: var(--denr-green);">0</h2>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-danger shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-x-circle text-danger" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Skipped (All Section)</h6>
                        <h2 class="mb-0 fw-bold" id="skipped-count" style="color: var(--denr-green);">0</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="row">
            <!-- Currently Serving -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, var(--denr-green) 0%, var(--denr-dark) 100%);">
                        <h5 class="mb-0 text-white"><i class="bi bi-person-video"></i> Currently Serving - All Categories</h5>
                        <span class="badge bg-light text-dark" id="serving-badge">-</span>
                    </div>
                    <div class="card-body" id="currently-serving">
                        <div class="text-center py-5">
                            <i class="bi bi-person-circle text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                            <p class="text-muted mt-3">No one is currently being served</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Waiting List -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, var(--denr-green) 0%, var(--denr-dark) 100%);">
                        <h5 class="mb-0 text-white"><i class="bi bi-list-ul"></i> Waiting List - All Categories</h5>
                        <span class="badge bg-light text-dark" id="waiting-badge">0</span>
                    </div>
                    <div class="card-body p-0">
                        <div id="waiting-list-container">
                            <div class="text-center py-5">
                                <i class="bi bi-hourglass text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="text-muted mt-3">Loading waiting list...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-success btn-lg" id="call-next-btn" onclick="callNext()">
                                <i class="bi bi-megaphone"></i> Call Next (Any Category)
                            </button>
                            <button type="button" class="btn btn-secondary btn-lg" id="skip-btn" onclick="skipCurrent()" disabled>
                                <i class="bi bi-skip-forward"></i> Skip
                            </button>
                            <button type="button" class="btn btn-primary btn-lg" id="complete-btn" onclick="completeService()" disabled>
                                <i class="bi bi-check-circle"></i> Complete
                            </button>
                            <button type="button" class="btn btn-warning btn-lg" id="forward-btn" onclick="showForwardModal()" disabled>
                                <i class="bi bi-arrow-right-circle"></i> Forward to Admin
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Section Categories Info -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6><i class="bi bi-info-circle"></i> Your Section Categories ({{ $sectionCategories->count() }})</h6>
                        <div class="row mt-2">
                            @foreach($sectionCategories as $secCat)
                            <div class="col-md-4 mb-2">
                                <div class="badge" style="background-color: {{ $secCat->color }}; color: {{ $secCat->contrast_color }}; padding: 8px 15px; font-size: 0.9rem;">
                                    {{ $secCat->code }} - {{ $secCat->name }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Section Assigned -->
        <div class="alert alert-warning" role="alert">
            <h5><i class="bi bi-exclamation-triangle"></i> No Section Assigned</h5>
            <p>Please contact the administrator to assign you a section.</p>
        </div>
    @endif
</div>

<!-- Forward to Admin Modal -->
<div class="modal fade" id="forwardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-arrow-right-circle"></i> Forward to Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Reason for forwarding</label>
                    <textarea class="form-control" id="forward-reason" rows="3" placeholder="Explain why this inquiry needs admin assistance..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="forwardToAdmin()">
                    <i class="bi bi-arrow-right-circle"></i> Forward
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentInquiry = null;
let currentInquiryId = null;

// Auto-refresh every 10 seconds
let refreshInterval;

function startAutoRefresh() {
    refreshInterval = setInterval(() => {
        loadWaitingList();
        if (currentInquiryId) {
            loadCurrentlyServing();
        }
        loadStatistics();
    }, 10000);
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
}

// Load on page load
document.addEventListener('DOMContentLoaded', function() {
    if ('{{ isset($section) ? $section : '' }}') {
        loadWaitingList();
        loadCurrentlyServing();
        loadStatistics();
        startAutoRefresh();
    }
});

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    stopAutoRefresh();
});

async function loadWaitingList() {
    try {
        const response = await fetch('{{ route("section-staff.waiting-list") }}');
        const data = await response.json();
        
        const container = document.getElementById('waiting-list-container');
        const badge = document.getElementById('waiting-badge');
        
        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="text-muted mt-3">No one in queue</p>
                </div>
            `;
            badge.textContent = '0';
            return;
        }
        
        badge.textContent = data.length;
        
        let html = '<div class="list-group list-group-flush">';
        data.forEach(inquiry => {
            const priorityClass = inquiry.priority === 'high' ? 'border-start border-danger border-4' : '';
            const priorityBadge = inquiry.priority === 'high' ? '<span class="badge bg-danger ms-2">High Priority</span>' : '';
            const categoryInfo = inquiry.category ? `<small class="text-muted d-block">${escapeHtml(inquiry.category.name)}</small>` : '';
            
            html += `
                <div class="list-group-item ${priorityClass}" style="cursor: pointer;" onclick="selectInquiry(${inquiry.id})">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${escapeHtml(inquiry.queue_number)}</h6>
                            <small class="text-muted">${escapeHtml(inquiry.name)}</small>
                            ${categoryInfo}
                            ${priorityBadge}
                        </div>
                        <small class="text-muted">${formatTime(inquiry.created_at)}</small>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        container.innerHTML = html;
    } catch (error) {
        console.error('Error loading waiting list:', error);
        document.getElementById('waiting-list-container').innerHTML = `
            <div class="text-center py-4">
                <p class="text-danger">Failed to load waiting list</p>
            </div>
        `;
    }
}

async function loadCurrentlyServing() {
    try {
        const response = await fetch('{{ route("section-staff.currently-serving") }}');
        const data = await response.json();
        
        const container = document.getElementById('currently-serving');
        const badge = document.getElementById('serving-badge');
        
        if (!data || !data.id) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-person-circle text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                    <p class="text-muted mt-3">No one is currently being served</p>
                </div>
            `;
            badge.textContent = '-';
            currentInquiry = null;
            currentInquiryId = null;
            disableActionButtons();
            return;
        }
        
        currentInquiry = data;
        currentInquiryId = data.id;
        badge.textContent = data.queue_number;
        
        container.innerHTML = `
            <div class="text-center">
                <h3 class="mb-2">${escapeHtml(data.queue_number)}</h3>
                <h5 class="text-success mb-3">${escapeHtml(data.name)}</h5>
                ${data.category ? `<p class="text-muted mb-2"><small>Category: ${escapeHtml(data.category.name)}</small></p>` : ''}
                <div class="bg-light rounded p-3 text-start">
                    <p class="mb-2"><strong>Purpose:</strong> ${escapeHtml(data.purpose)}</p>
                    <p class="mb-2"><strong>Contact:</strong> ${escapeHtml(data.contact_number || 'N/A')}</p>
                    <p class="mb-0"><strong>Since:</strong> ${formatDateTime(data.served_at)}</p>
                </div>
            </div>
        `;
        
        enableActionButtons();
    } catch (error) {
        console.error('Error loading currently serving:', error);
    }
}

async function loadStatistics() {
    try {
        const response = await fetch('{{ route("section-staff.statistics") }}');
        const data = await response.json();
        
        document.getElementById('waiting-count').textContent = data.waiting || 0;
        document.getElementById('serving-count').textContent = data.serving || 0;
        document.getElementById('completed-count').textContent = data.served || 0;
        document.getElementById('skipped-count').textContent = data.skipped || 0;
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

async function callNext() {
    if (!confirm('Call the next person in queue?')) return;
    
    const btn = document.getElementById('call-next-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass"></i> Calling...';
    
    try {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        
        const response = await fetch('{{ route("section-staff.call-next") }}', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            await loadWaitingList();
            await loadCurrentlyServing();
            await loadStatistics();
        } else {
            alert(data.error || 'Failed to call next');
        }
    } catch (error) {
        console.error('Error calling next:', error);
        alert('Failed to call next');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-megaphone"></i> Call Next';
    }
}

function skipCurrent() {
    if (!currentInquiryId || !confirm('Skip current inquiry? The person will go back to the queue.')) return;
    
    skipInquiry(currentInquiryId);
}

async function skipInquiry(inquiryId) {
    const btn = document.getElementById('skip-btn');
    btn.disabled = true;
    
    try {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('inquiry_id', inquiryId);
        
        const response = await fetch('{{ route("section-staff.skip") }}', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            await loadWaitingList();
            await loadCurrentlyServing();
            await loadStatistics();
        } else {
            alert(data.error || 'Failed to skip');
        }
    } catch (error) {
        console.error('Error skipping:', error);
        alert('Failed to skip');
    } finally {
        btn.disabled = false;
    }
}

async function completeService() {
    if (!currentInquiryId || !confirm('Mark service as completed?')) return;
    
    const btn = document.getElementById('complete-btn');
    btn.disabled = true;
    
    try {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('inquiry_id', currentInquiryId);
        
        const response = await fetch('{{ route("section-staff.complete") }}', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            await loadWaitingList();
            await loadCurrentlyServing();
            await loadStatistics();
        } else {
            alert(data.error || 'Failed to complete');
        }
    } catch (error) {
        console.error('Error completing:', error);
        alert('Failed to complete');
    } finally {
        btn.disabled = false;
    }
}

function showForwardModal() {
    if (!currentInquiryId) return;
    const modal = new bootstrap.Modal(document.getElementById('forwardModal'));
    modal.show();
}

async function forwardToAdmin() {
    const reason = document.getElementById('forward-reason').value.trim();
    if (!reason) {
        alert('Please provide a reason for forwarding');
        return;
    }
    
    const btn = event.target.closest('button');
    btn.disabled = true;
    
    try {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('inquiry_id', currentInquiryId);
        formData.append('forward_reason', reason);
        
        const response = await fetch('{{ route("section-staff.forward") }}', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Close modal
            const modalEl = document.getElementById('forwardModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
            
            // Clear reason
            document.getElementById('forward-reason').value = '';
            
            alert('Inquiry forwarded to admin successfully');
            await loadWaitingList();
            await loadCurrentlyServing();
            await loadStatistics();
        } else {
            alert(data.error || 'Failed to forward');
        }
    } catch (error) {
        console.error('Error forwarding:', error);
        alert('Failed to forward');
    } finally {
        btn.disabled = false;
    }
}

function selectInquiry(inquiryId) {
    // For section staff, they can only work with their assigned category
    // This function could be used to view details if needed
    console.log('Selected inquiry:', inquiryId);
}

function enableActionButtons() {
    document.getElementById('skip-btn').disabled = false;
    document.getElementById('complete-btn').disabled = false;
    document.getElementById('forward-btn').disabled = false;
}

function disableActionButtons() {
    document.getElementById('skip-btn').disabled = true;
    document.getElementById('complete-btn').disabled = true;
    document.getElementById('forward-btn').disabled = true;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric',
        hour: '2-digit', 
        minute: '2-digit' 
    });
}
</script>
@endsection
