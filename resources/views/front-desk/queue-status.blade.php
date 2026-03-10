@extends('layouts.app')

@section('title', 'Live Queue Status')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-eye text-primary"></i> Live Queue Status</h2>
                    <p class="text-muted mb-0">Real-time monitoring of queue status across all sections</p>
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
                            <h6 class="card-title mb-0">Serving</h6>
                            <h3 class="mb-0 mt-2" id="servingCount">0</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-bounding-box" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
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

    <!-- Queue Status by Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-grid-3x3-gap"></i> Queue Status by Section</h5>
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
        </div>
    </div>

    <!-- Recent Inquiries -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Inquiries (Today)</h5>
                    <a href="{{ route('front-desk.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-arrow-repeat"></i> Refresh
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="8%" class="text-center fw-bold">#</th>
                                    <th width="18%" class="fw-bold"><i class="bi bi-person me-1"></i>CLIENT NAME</th>
                                    <th width="32%" class="fw-bold"><i class="bi bi-tag me-1"></i>SERVICE TYPE</th>
                                    <th width="10%" class="text-center fw-bold"><i class="bi bi-hourglass-split me-1"></i>STATUS</th>
                                    <th width="10%" class="text-center fw-bold"><i class="bi bi-clock me-1"></i>TIME</th>
                                    <th width="22%" class="fw-bold"><i class="bi bi-geo-alt me-1"></i>SECTION</th>
                                    <th width="10%" class="text-center fw-bold"><i class="bi bi-printer me-1"></i>ACTION</th>
                                </tr>
                            </thead>
                            <tbody id="recentInquiries">
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2 text-muted">Loading recent inquiries...</p>
                                    </td>
                                </tr>
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
    // Format queue number to show only sequential number
    function formatQueueNumber(fullQueueNumber) {
        if (!fullQueueNumber) return '---';
        
        // Try to extract the last number after the last hyphen
        // e.g., "SECSIME NO.R4A-L_SMD-01-009" -> "#9"
        // e.g., "dsds-001" -> "#1"
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

    // Load queue status
    function loadQueueStatus() {
        fetch('{{ route('front-desk.queue-status') }}')
            .then(response => response.json())
            .then(data => {
                // Update stats
                document.getElementById('todayCount').textContent = data.today_count || 0;
                document.getElementById('waitingCount').textContent = data.waiting_count || 0;
                document.getElementById('servingCount').textContent = data.serving_count || 0;
                document.getElementById('completedCount').textContent = data.completed_count || 0;
                
                // Update queue display
                updateQueueDisplay(data);
            })
            .catch(error => {
                console.error('Error loading queue status:', error);
                document.getElementById('queueStatus').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> Error loading queue status. Please refresh the page.
                    </div>
                `;
            });
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
            // Format queue numbers
            const nowServing = info.now_serving ? formatQueueNumber(info.now_serving) : null;
            const latestWaiting = info.latest_waiting ? formatQueueNumber(info.latest_waiting) : null;
            const displayNumber = nowServing || latestWaiting || '---';
            const statusText = info.now_serving ? 'Now Serving' : (hasWaiting ? 'Next in Queue' : 'No Queue');
            const statusClass = info.now_serving ? 'text-success' : (hasWaiting ? 'text-warning' : 'text-muted');
            
            html += `
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card border-info h-100">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">${info.section_name || section}</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="display-4 fw-bold mb-2 text-info">
                                ${displayNumber}
                            </div>
                            <p class="mb-1">
                                <small class="${statusClass}">
                                    <i class="bi bi-${info.now_serving ? 'play-fill' : (hasWaiting ? 'hourglass-top' : 'pause-fill')}"></i>
                                    ${statusText}
                                </small>
                            </p>
                            <p class="mb-0">
                                <small class="text-muted">
                                    <i class="bi bi-people"></i> Waiting: ${info.waiting_count}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            `;
        }
        html += '</div>';
        
        container.innerHTML = html;
    }

    // Auto-refresh queue status only every 10 seconds (recent inquiries remain static)
    setInterval(() => {
        loadQueueStatus();
    }, 10000);

    // Initial load - queue status only (recent inquiries loaded once on page load)
    loadQueueStatus();
</script>
@endsection