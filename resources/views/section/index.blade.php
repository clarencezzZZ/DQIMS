@extends('layouts.app')

@section('title', 'Section Dashboard - DENR DQIMS')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-people text-success"></i> Section Dashboard</h2>
                    <p class="text-muted mb-0">Manage your section's queue and serve clients efficiently</p>
                </div>
                @if($category)
                    <div class="badge" style="background-color: {{ $category->color }}; color: {{ $category->contrast_color }}; padding: 10px 20px; font-size: 1rem;">
                        <i class="bi bi-tag"></i> {{ $category->code }} - {{ $category->name }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($category)
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-warning shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-hourglass-split text-warning" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Waiting</h6>
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
                        <h6 class="text-muted mb-2">Serving</h6>
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
                        <h6 class="text-muted mb-2">Completed</h6>
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
                        <h6 class="text-muted mb-2">Skipped</h6>
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
                        <h5 class="mb-0 text-white"><i class="bi bi-person-video"></i> Currently Serving</h5>
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
                        <h5 class="mb-0 text-white"><i class="bi bi-list-ul"></i> Waiting List</h5>
                        <span class="badge bg-light text-dark" id="waiting-badge">0</span>
                    </div>
                    <div class="card-body p-0" id="waiting-list">
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                            <p class="text-muted mt-3">No one is waiting</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="mb-3 text-muted"><i class="bi bi-gear"></i> Actions</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <button id="call-next-btn" class="btn btn-success btn-lg px-4">
                                <i class="bi bi-bell"></i> Call Next
                            </button>
                            <button id="complete-btn" class="btn btn-primary btn-lg px-4" disabled>
                                <i class="bi bi-check-circle"></i> Complete
                            </button>
                            <button id="skip-btn" class="btn btn-warning btn-lg px-4" disabled>
                                <i class="bi bi-skip-forward"></i> Skip
                            </button>
                            <button id="forward-btn" class="btn btn-danger btn-lg px-4" disabled>
                                <i class="bi bi-share"></i> Forward to Admin
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--denr-green) 0%, var(--denr-dark) 100%);">
                        <h5 class="mb-0 text-white"><i class="bi bi-clock-history"></i> Today's Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Queue #</th>
                                        <th>Guest Name</th>
                                        <th>Status</th>
                                        <th>Time</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-activity">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                                            <p class="mt-2">No activity yet today</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @elseif(auth()->user()->isAdmin())
        <!-- Admin Dashboard View -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="bi bi-info-circle me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <h5 class="alert-heading mb-1">Admin Access</h5>
                                <p class="mb-0">You're viewing the section dashboard as an administrator. You can monitor all categories and manage section activities across the system.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-warning shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-hourglass-split text-warning" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Total Waiting</h6>
                        <h2 class="mb-0 fw-bold" id="waiting-count" style="color: var(--denr-green);">-</h2>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-info shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-check text-info" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Total Serving</h6>
                        <h2 class="mb-0 fw-bold" id="serving-count" style="color: var(--denr-green);">-</h2>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-success shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Total Completed</h6>
                        <h2 class="mb-0 fw-bold" id="completed-count" style="color: var(--denr-green);">-</h2>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card border-danger shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-x-circle text-danger" style="font-size: 2.5rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Total Skipped</h6>
                        <h2 class="mb-0 fw-bold" id="skipped-count" style="color: var(--denr-green);">-</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Overview -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header" style="background: linear-gradient(135deg, var(--denr-green) 0%, var(--denr-dark) 100%);">
                        <h5 class="mb-0 text-white"><i class="bi bi-grid"></i> All Categories Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Code</th>
                                        <th>Waiting</th>
                                        <th>Serving</th>
                                        <th>Completed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="category-overview">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <div class="spinner-border text-success" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-2">Loading categories...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            @else
                <!-- No Category Assigned -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="alert alert-warning d-flex align-items-center" role="alert">
                                    <i class="bi bi-exclamation-triangle me-3" style="font-size: 1.5rem;"></i>
                                    <div>
                                        <h5 class="alert-heading mb-1">No Category Assigned</h5>
                                        <p class="mb-0">You don't have a category assigned to you. Please contact your administrator to assign you to a category before you can access section functions.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Category Selection Modal (Bootstrap 5) -->
    <div class="modal fade" id="categorySelectionModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--denr-green) 0%, var(--denr-dark) 100%);">
                    <h5 class="modal-title text-white" id="categoryModalLabel">
                        <i class="bi bi-tag"></i> Select Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category-select" class="form-label fw-bold">Choose a Category:</label>
                        <select id="category-select" class="form-select">
                            <option value="">Loading categories...</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirm-category-selection">
                        <i class="bi bi-check-circle"></i> Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Remarks Modal (Bootstrap 5) -->
    <div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--denr-green) 0%, var(--denr-dark) 100%);">
                    <h5 class="modal-title text-white" id="remarksModalLabel">
                        <i class="bi bi-chat-left-text"></i> Add Remarks
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <textarea id="remarks-text" class="form-control" rows="4" placeholder="Enter remarks or notes..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="save-remarks">
                        <i class="bi bi-save"></i> Save Remarks
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Details Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--denr-green) 0%, var(--denr-dark) 100%);">
                    <h5 class="modal-title text-white" id="categoryModalLabel">
                        <i class="bi bi-folder2-open me-2"></i>
                        <span id="modal-category-name">Category Name</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Category Info -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="text-muted mb-1">Category Code</h6>
                                    <h4 class="mb-0" id="modal-category-code">CODE</h4>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="badge bg-secondary" id="modal-category-section">Section</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card border-warning shadow-sm h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-hourglass-split text-warning mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="text-muted mb-1">Waiting</h6>
                                    <h2 class="mb-0 fw-bold text-success" id="modal-waiting-count">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-info shadow-sm h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-person-check text-info mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="text-muted mb-1">Serving</h6>
                                    <h2 class="mb-0 fw-bold text-info" id="modal-serving-count">0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success shadow-sm h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="text-muted mb-1">Completed</h6>
                                    <h2 class="mb-0 fw-bold text-success" id="modal-completed-count">0</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="text-center mt-4">
                        <a href="#" id="modal-view-section-btn" class="btn btn-primary btn-lg px-4">
                            <i class="bi bi-box-arrow-up-right me-2"></i>Go to Section Dashboard
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    /* Custom card hover effects */
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
    }
    
    /* Button animations */
    .btn {
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .btn:disabled {
        transform: none;
        opacity: 0.6;
    }
    
    /* Status badges */
    .status-waiting { 
        background-color: #ffc107 !important; 
        color: #000 !important; 
    }
    
    .status-serving { 
        background-color: #17a2b8 !important; 
        color: #fff !important; 
    }
    
    .status-completed { 
        background-color: #28a745 !important; 
        color: #fff !important; 
    }
    
    .status-skipped { 
        background-color: #dc3545 !important; 
        color: #fff !important; 
    }
    
    /* Loading spinner animation */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .loading-pulse {
        animation: pulse 1.5s ease-in-out infinite;
    }
    
    /* Table row hover effect */
    .table-hover tbody tr:hover {
        background-color: rgba(46, 125, 50, 0.05);
        cursor: pointer;
    }
    
    /* Modern badge styling */
    .badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    /* Card header gradient */
    .card-header {
        border-bottom: none;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn-lg {
            padding: 10px 20px;
            font-size: 0.9rem;
        }
        
        .display-4 {
            font-size: 2rem;
        }
    }
    
    /* Enhanced View button styling */
    .table td a.btn {
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        letter-spacing: 0.3px;
        border-radius: 6px;
        transition: background-color 0.2s ease;
    }
    
    .table td a.btn:hover {
        background-color: var(--denr-dark) !important;
        border-color: var(--denr-dark) !important;
    }
    
    .table td a.btn i {
        font-size: 0.9em;
    }
    
    /* Professional table styling */
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 16px 12px;
        border: none;
    }
    
    .table tbody td {
        padding: 16px 12px;
        vertical-align: middle;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    /* Category name styling */
    .table tbody strong {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--denr-dark);
    }
    
    [data-theme="dark"] .table tbody strong {
        color: #4caf50;
    }
    
    .table tbody small.text-muted {
        font-size: 0.8rem;
        opacity: 0.8;
    }
    
    /* Badge enhancements */
    .badge {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    
    .badge.bg-warning {
        background-color: #ff9800 !important;
        color: #fff !important;
    }
    
    .badge.bg-info {
        background-color: #03a9f4 !important;
        color: #fff !important;
    }
    
    .badge.bg-success {
        background-color: #4caf50 !important;
        color: #fff !important;
    }
    
    /* Code tag styling */
    .table td code {
        background-color: rgba(0,0,0,0.06);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        border: 1px solid rgba(0,0,0,0.1);
    }
    
    [data-theme="dark"] .table td code {
        background-color: rgba(255,255,255,0.08);
        border-color: rgba(255,255,255,0.15);
    }
</style>
@endsection

@section('scripts')
<script>
    let currentInquiry = null;
    const categoryId = {{ $category->id ?? 'null' }};
    const isAdmin = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
    const isSectionStaff = {{ auth()->user()->isSectionStaff() ? 'true' : 'false' }};
    
    // Show loading state
    if (categoryId || isAdmin || isSectionStaff) {
        if (categoryId) {
            document.getElementById('waiting-count').textContent = '...';
            document.getElementById('serving-count').textContent = '...';
            document.getElementById('completed-count').textContent = '...';
            document.getElementById('skipped-count').textContent = '...';
        }
        
        // Load initial data after a brief delay
        setTimeout(() => {
            if (!categoryId && isSectionStaff) {
                // Section staff without category - show message
                document.getElementById('currently-serving').innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-person-circle text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <p class="text-muted mt-3">Please select a category to work with</p>
                    </div>
                `;
                document.getElementById('waiting-list').innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <p class="text-muted mt-3">Please select a category to view waiting list</p>
                    </div>
                `;
                
                // Enable action buttons but they'll prompt for category
                document.getElementById('call-next-btn').disabled = false;
                document.getElementById('complete-btn').disabled = false;
                document.getElementById('skip-btn').disabled = false;
                document.getElementById('forward-btn').disabled = false;
            } else if (categoryId) {
                loadStatistics();
                loadCurrentlyServing();
                loadWaitingList();
                loadRecentActivity();
                
                // Auto-refresh every 5 seconds
                setInterval(() => {
                    loadStatistics();
                    loadCurrentlyServing();
                    loadWaitingList();
                }, 5000);

                // Set up event listeners
                setupEventListeners();
            }
            
            console.log('🔍 Page initialization check:');
            console.log('  - isAdmin:', isAdmin);
            console.log('  - categoryId:', categoryId);
            console.log('  - isSectionStaff:', isSectionStaff);
            console.log('  - Current URL:', window.location.href);
            console.log('  - Has category param:', window.location.href.includes('category='));
            
            // Only load admin overview if NO specific category is selected
            if (isAdmin && !categoryId) {
                console.log('✅ Admin viewing ALL categories - loading admin overview');
                loadAdminOverview();
                loadAdminTotalStatistics();
            } else if (categoryId) {
                console.log('✅ Viewing specific category (ID: ' + categoryId + ') - skipping admin overview');
            } else {
                console.log('❌ Not admin or has category assigned');
            }
        }, 100);
    }

    function setupEventListeners() {
        document.getElementById('call-next-btn').addEventListener('click', callNext);
        document.getElementById('complete-btn').addEventListener('click', () => showRemarksModal('complete'));
        document.getElementById('skip-btn').addEventListener('click', () => showRemarksModal('skip'));
        document.getElementById('forward-btn').addEventListener('click', () => showRemarksModal('forward'));
        document.getElementById('save-remarks').addEventListener('click', saveRemarks);
    }

    function loadStatistics() {
        axios.get('/section/statistics')
            .then(response => {
                const data = response.data;
                animateValue('waiting-count', parseInt(document.getElementById('waiting-count').textContent) || 0, data.waiting, 500);
                animateValue('serving-count', parseInt(document.getElementById('serving-count').textContent) || 0, data.serving, 500);
                animateValue('completed-count', parseInt(document.getElementById('completed-count').textContent) || 0, data.completed, 500);
                animateValue('skipped-count', parseInt(document.getElementById('skipped-count').textContent) || 0, data.skipped, 500);
            })
            .catch(error => console.error('Error loading statistics:', error));
    }

    function animateValue(id, start, end, duration) {
        const obj = document.getElementById(id);
        if (start === end) return;
        
        const range = end - start;
        let current = start;
        const increment = end > start ? 1 : -1;
        const stepTime = Math.abs(Math.floor(duration / (range !== 0 ? range : 1)));
        
        const timer = setInterval(() => {
            current += increment;
            obj.textContent = current;
            if (current === end) {
                clearInterval(timer);
            }
        }, stepTime);
    }

    function loadCurrentlyServing() {
        axios.get('/section/currently-serving')
            .then(response => {
                const data = response.data;
                const container = document.getElementById('currently-serving');
                const badge = document.getElementById('serving-badge');
                
                if (data) {
                    container.innerHTML = `
                        <div class="alert alert-success mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-1"><i class="bi bi-person-check-circle"></i> ${data.queue_number}</h4>
                                    <p class="mb-1 fw-bold">${data.name}</p>
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> Serving since: ${new Date(data.served_at).toLocaleTimeString()}
                                    </small>
                                </div>
                                <div>
                                    <span class="badge bg-success">Serving</span>
                                </div>
                            </div>
                        </div>
                    `;
                    badge.textContent = data.queue_number;
                    currentInquiry = data;
                    enableButtons();
                } else {
                    container.innerHTML = `
                        <div class="text-center py-5">
                            <i class="bi bi-person-circle text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                            <p class="text-muted mt-3">No one is currently being served</p>
                        </div>
                    `;
                    badge.textContent = '-';
                    currentInquiry = null;
                    disableButtons();
                }
            })
            .catch(error => console.error('Error loading currently serving:', error));
    }

    function loadWaitingList() {
        axios.get('/section/waiting-list')
            .then(response => {
                const data = response.data;
                const container = document.getElementById('waiting-list');
                const badge = document.getElementById('waiting-badge');
                
                badge.textContent = data.length;
                
                if (data.length > 0) {
                    container.innerHTML = `
                        <div class="list-group list-group-flush">
                            ${data.map((inquiry, index) => `
                                <div class="list-group-item d-flex justify-content-between align-items-center p-3 border-start ${index === 0 ? 'border-warning border-start-3' : ''}">
                                    <div>
                                        <h6 class="mb-1">
                                            <span class="badge ${index === 0 ? 'bg-warning text-dark' : 'bg-secondary'} me-2">
                                                ${inquiry.queue_number}
                                            </span>
                                            ${inquiry.name}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> ${new Date(inquiry.created_at).toLocaleTimeString()}
                                        </small>
                                    </div>
                                    ${index === 0 ? '<span class="badge bg-warning text-dark">Next</span>' : ''}
                                </div>
                            `).join('')}
                        </div>
                    `;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                            <p class="text-muted mt-3">No one is waiting</p>
                        </div>
                    `;
                }
            })
            .catch(error => console.error('Error loading waiting list:', error));
    }

    function loadRecentActivity() {
        // This would need a new endpoint for recent activity
        // For now, we'll show a placeholder
        document.getElementById('recent-activity').innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.3;"></i>
                    <p class="mt-2">No activity yet today</p>
                </td>
            </tr>
        `;
    }

    function loadAdminOverview() {
        console.log('🚀='.repeat(20));
        console.log('=== STARTING ADMIN OVERVIEW LOAD ===');
        console.log('🚀='.repeat(20));
        console.log('🔍 Function called: YES');
        console.log('🔍 Current page URL:', window.location.href);
        console.log('🔍 isAdmin variable:', isAdmin);
        console.log('🔍 categoryId variable:', categoryId);
        
        const tbody = document.getElementById('category-overview');
        console.log('🔍 tbody element found:', !!tbody);
        console.log('🔍 tbody innerHTML length:', tbody?.innerHTML?.length);
        
        if (!tbody) {
            console.error('❌ CRITICAL: tbody element NOT FOUND!');
            return;
        }
        
        // Show initial loading state
        tbody.innerHTML = `<tr><td colspan="6" class="text-center"><div class="spinner-border text-success" role="status"></div><p class="mt-2">Loading categories...</p></td></tr>`;
        
        // Step 1: Load categories first
        console.log('📡 Calling /api/categories...');
        
        axios.get('/api/categories')
            .then(response => {
                console.log('✅ Categories API response received');
                console.log('📦 Response status:', response.status);
                console.log('📦 Is Array?', Array.isArray(response.data));
                console.log('📦 Length:', response.data?.length);
                
                const categories = response.data;
                
                if (!Array.isArray(categories)) {
                    console.error('❌ ERROR: Response is not an array!', typeof response.data);
                    throw new Error('Categories response is not an array');
                }
                
                console.log('✅ Loaded', categories.length, 'categories');
                console.log('📋 Category IDs:', categories.map(c => c.id).join(', '));
                
                if (categories.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No categories found</td></tr>';
                    console.log('⚠️ Database is empty');
                    return;
                }
                
                // Show loading message
                console.log('🔄 Loading statistics for', categories.length, 'categories...');
                tbody.innerHTML = `<tr><td colspan="6" class="text-center"><div class="spinner-border text-success" role="status"></div><p class="mt-2">Loading statistics for ${categories.length} categories...</p></td></tr>`;
                
                // Step 2: Load stats for each category SEQUENTIALLY
                let html = '';
                let loadedCount = 0;
                
                // Define recursive loader function
                const loadNext = (index) => {
                    if (index >= categories.length) {
                        // All done!
                        console.log('✅ All', categories.length, 'categories loaded successfully');
                        console.log('📊 Final HTML length:', html.length);
                        tbody.innerHTML = html || '<tr><td colspan="6" class="text-center text-muted">No data available</td></tr>';
                        console.log('=== Admin Overview Load COMPLETE ===');
                        return;
                    }
                    
                    const cat = categories[index];
                    console.log(`📍 [${index + 1}/${categories.length}] Loading: ${cat.name} (ID: ${cat.id})`);
                    
                    // Update progress
                    if (index > 0) {
                        tbody.innerHTML = `<tr><td colspan="6" class="text-center"><div class="spinner-border text-success" role="status"></div><p class="mt-2">Loaded ${index}/${categories.length} categories...</p></td></tr>`;
                    }
                    
                    // Make the API call with timeout
                    axios.get('/section/statistics', { 
                        params: { category_id: cat.id },
                        timeout: 8000
                    })
                    .then(statsRes => {
                        const stats = statsRes.data;
                        console.log(`✅ [${index + 1}/${categories.length}] ${cat.name}: W=${stats.waiting}, S=${stats.serving}, C=${stats.completed}`);
                        
                        html += `
                            <tr>
                                <td>
                                    <strong>${cat.name}</strong>
                                    <br><small class="text-muted">${cat.section || ''}</small>
                                </td>
                                <td><code>${cat.code}</code></td>
                                <td><span class="badge bg-warning text-dark">${stats.waiting}</span></td>
                                <td><span class="badge bg-info">${stats.serving}</span></td>
                                <td><span class="badge bg-success">${stats.completed}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="openCategoryModal(${cat.id}, '${cat.name.replace(/'/g, "\\'")}', ${stats.waiting}, ${stats.serving}, ${stats.completed})" title="View category details" style="cursor: pointer;">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        `;
                        
                        loadedCount++;
                        
                        // Load next one
                        setTimeout(() => loadNext(index + 1), 100);
                    })
                    .catch(error => {
                        console.error(`❌ [${index + 1}/${categories.length}] Failed ${cat.name}:`, error.message);
                        
                        // Add error row
                        html += `
                            <tr>
                                <td>
                                    <strong>${cat.name}</strong>
                                    <br><small class="text-muted">${cat.section || ''}</small>
                                </td>
                                <td><code>${cat.code}</code></td>
                                <td colspan="3" class="text-danger">
                                    <i class="bi bi-exclamation-circle"></i> Error loading stats
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="openCategoryModal(${cat.id}, '${cat.name.replace(/'/g, "\\'")}', 0, 0, 0)" title="View category details" style="cursor: pointer;">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        `;
                        
                        // Continue to next anyway
                        setTimeout(() => loadNext(index + 1), 100);
                    });
                };
                
                // Start loading!
                console.log('🚀 Starting to load category statistics...');
                loadNext(0);
            })
            .catch(error => {
                console.error('❌ FATAL ERROR loading categories:', error);
                console.error('Error type:', error.constructor.name);
                console.error('Message:', error.message);
                console.error('Status:', error.response?.status);
                
                let errorMessage = 'Unable to connect to server';
                if (error.response) {
                    if (error.response.status === 401) errorMessage = 'Please log in again';
                    else if (error.response.status === 403) errorMessage = 'Access denied';
                    else if (error.response.status === 404) errorMessage = 'Endpoint not found';
                    else if (error.response.status === 500) errorMessage = 'Server error';
                    else errorMessage = `HTTP ${error.response.status}`;
                } else if (error.request) {
                    errorMessage = 'No response - check network';
                }
                
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-danger py-4">
                            <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                            <p class="mt-2 fw-bold">${errorMessage}</p>
                            <p class="text-muted">Status: ${error.response?.status || 'Unknown'}</p>
                            <button class="btn btn-sm btn-outline-danger mt-2" onclick="loadAdminOverview()">
                                <i class="bi bi-arrow-clockwise"></i> Retry
                            </button>
                        </td>
                    </tr>
                `;
            });
    }

    function loadAdminTotalStatistics() {
        // Get total statistics across all categories
        axios.get('/section/statistics')
            .then(response => {
                const data = response.data;
                animateValue('waiting-count', 0, data.waiting, 500);
                animateValue('serving-count', 0, data.serving, 500);
                animateValue('completed-count', 0, data.completed, 500);
                animateValue('skipped-count', 0, data.skipped, 500);
            })
            .catch(error => {
                console.error('Error loading admin total statistics:', error);
                document.getElementById('waiting-count').textContent = '0';
                document.getElementById('serving-count').textContent = '0';
                document.getElementById('completed-count').textContent = '0';
                document.getElementById('skipped-count').textContent = '0';
            });
    }

    function callNext() {
        const callNextBtn = document.getElementById('call-next-btn');
        callNextBtn.disabled = true;
        callNextBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Calling...';
        
        if (!categoryId && !isAdmin && isSectionStaff) {
            // Show category selection modal
            loadCategories().then(() => {
                const modal = new bootstrap.Modal(document.getElementById('categorySelectionModal'));
                modal.show();
                
                document.getElementById('confirm-category-selection').onclick = function() {
                    const selectedCategory = document.getElementById('category-select').value;
                    if (!selectedCategory) {
                        alert('Please select a category first');
                        setTimeout(() => {
                            callNextBtn.disabled = false;
                            callNextBtn.innerHTML = '<i class="bi bi-bell"></i> Call Next';
                        }, 500);
                        return;
                    }
                    
                    executeCallNext(selectedCategory, callNextBtn);
                    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('categorySelectionModal'));
                    modalInstance.hide();
                };
            });
        } else {
            executeCallNext(categoryId, callNextBtn);
        }
    }

    function executeCallNext(categoryId, btn) {
        axios.post('/section/call-next', { category_id: categoryId })
            .then(response => {
                const data = response.data;
                if (data.success) {
                    showNotification('success', 'Called Next', `${data.inquiry.queue_number} - ${data.inquiry.name}`);
                    loadCurrentlyServing();
                    loadWaitingList();
                    loadStatistics();
                } else {
                    showNotification('error', 'Error', data.error);
                }
            })
            .catch(error => {
                console.error('Error calling next:', error);
                const message = error.response?.data?.message || 'Error calling next inquiry';
                showNotification('error', 'Error', message);
            })
            .finally(() => {
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-bell"></i> Call Next';
                }, 500);
            });
    }

    // Open Category Details Modal
    function openCategoryModal(categoryId, categoryName, waiting, serving, completed) {
        console.log('Opening modal for category:', categoryId);
        
        // Populate modal with data
        document.getElementById('modal-category-name').textContent = categoryName;
        document.getElementById('modal-waiting-count').textContent = waiting;
        document.getElementById('modal-serving-count').textContent = serving;
        document.getElementById('modal-completed-count').textContent = completed;
        
        // Set the "Go to Section Dashboard" button link
        document.getElementById('modal-view-section-btn').href = `/section?category=${categoryId}`;
        
        // Find and display category code and section from the table
        const allCategories = [
            @foreach($categories ?? [] as $cat)
                { id: {{ $cat->id }}, code: '{{ $cat->code }}', section: '{{ $cat->section }}' },
            @endforeach
        ];
        
        const category = allCategories.find(c => c.id === categoryId);
        if (category) {
            document.getElementById('modal-category-code').textContent = category.code;
            document.getElementById('modal-category-section').textContent = category.section || 'N/A';
        }
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
        modal.show();
    }
    
    function showRemarksModal(action) {
        if (!currentInquiry) {
            showNotification('warning', 'No Active Inquiry', 'Please call next first');
            return;
        }
        
        document.getElementById('remarks-text').value = '';
        const modal = new bootstrap.Modal(document.getElementById('remarksModal'));
        modal.show();
        document.getElementById('remarksModal').dataset.action = action;
    }

    function saveRemarks() {
        const saveBtn = document.getElementById('save-remarks');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
        
        const action = document.getElementById('remarksModal').dataset.action;
        const remarks = document.getElementById('remarks-text').value;
        
        axios.post(`/section/${action}`, { remarks: remarks })
            .then(response => {
                const data = response.data;
                if (data.success) {
                    showNotification('success', 'Success', data.message);
                    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('remarksModal'));
                    modalInstance.hide();
                    loadCurrentlyServing();
                    loadWaitingList();
                    loadStatistics();
                } else {
                    showNotification('error', 'Error', data.error);
                }
            })
            .catch(error => {
                console.error('Error saving remarks:', error);
                const message = error.response?.data?.message || 'Error saving remarks';
                showNotification('error', 'Error', message);
            })
            .finally(() => {
                setTimeout(() => {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="bi bi-save"></i> Save Remarks';
                }, 500);
            });
    }

    function enableButtons() {
        document.getElementById('complete-btn').disabled = false;
        document.getElementById('skip-btn').disabled = false;
        document.getElementById('forward-btn').disabled = false;
    }

    function disableButtons() {
        document.getElementById('complete-btn').disabled = true;
        document.getElementById('skip-btn').disabled = true;
        document.getElementById('forward-btn').disabled = true;
    }

    async function loadCategories() {
        try {
            const response = await axios.get('/api/categories');
            const categories = response.data;
            const selectElement = document.getElementById('category-select');
            
            selectElement.innerHTML = '<option value="">Select a Category</option>';
            
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = `${category.name} (${category.code})`;
                selectElement.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading categories:', error);
            document.getElementById('category-select').innerHTML = '<option value="">Error loading categories</option>';
        }
    }

    function showNotification(type, title, message) {
        // You can integrate with your existing notification system from layouts.app
        alert(`${title}: ${message}`);
    }
</script>
@endsection