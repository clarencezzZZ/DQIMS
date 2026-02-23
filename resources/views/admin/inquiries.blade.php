@extends('layouts.app')

@section('title', 'All Inquiries')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-list-check text-primary"></i> All Inquiries</h2>
                    <p class="text-muted mb-0">View and manage all guest inquiries</p>
                </div>
                <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.inquiries') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>Waiting</option>
                        <option value="serving" {{ request('status') == 'serving' ? 'selected' : '' }}>Serving</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="skipped" {{ request('status') == 'skipped' ? 'selected' : '' }}>Skipped</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Name, Queue #..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Inquiries Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Queue #</th>
                            <th>Guest Name</th>
                            <th>Contact</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inquiries as $inquiry)
                            <tr>
                                <td><span class="badge bg-dark fs-6">{{ $inquiry->queue_number }}</span></td>
                                <td>{{ $inquiry->guest_name }}</td>
                                <td>{{ $inquiry->contact_number ?? 'N/A' }}</td>
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
                                <td>
                                    @if($inquiry->priority == 'normal')
                                        <span class="badge bg-secondary">Normal</span>
                                    @elseif($inquiry->priority == 'priority')
                                        <span class="badge bg-warning text-dark">Priority</span>
                                    @elseif($inquiry->priority == 'urgent')
                                        <span class="badge bg-danger">Urgent</span>
                                    @endif
                                </td>
                                <td>{{ $inquiry->created_at->format('h:i A') }}</td>
                                <td>
                                    <div class="btn-group">
                                        {{-- Status Action Buttons Only --}}
                                        @if($inquiry->status == 'waiting')
                                            {{-- Serve Button --}}
                                            <button type="button" class="btn btn-sm btn-info" onclick="updateStatus({{ $inquiry->id }}, 'serving')" title="Start Serving">
                                                <i class="bi bi-play-fill"></i> Serve
                                            </button>
                                            {{-- Complete Button --}}
                                            <button type="button" class="btn btn-sm btn-success" onclick="updateStatus({{ $inquiry->id }}, 'completed')" title="Complete">
                                                <i class="bi bi-check-lg"></i> Complete
                                            </button>
                                            {{-- Skip Button --}}
                                            <button type="button" class="btn btn-sm btn-warning" onclick="updateStatus({{ $inquiry->id }}, 'skipped')" title="Skip">
                                                <i class="bi bi-skip-forward-fill"></i> Skip
                                            </button>
                                        @elseif($inquiry->status == 'serving')
                                            {{-- Complete Button --}}
                                            <button type="button" class="btn btn-sm btn-success" onclick="updateStatus({{ $inquiry->id }}, 'completed')" title="Complete">
                                                <i class="bi bi-check-lg"></i> Complete
                                            </button>
                                            {{-- Back to Waiting --}}
                                            <button type="button" class="btn btn-sm btn-secondary" onclick="updateStatus({{ $inquiry->id }}, 'waiting')" title="Return to Waiting">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        @elseif($inquiry->status == 'completed')
                                            <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Completed</span>
                                        @elseif($inquiry->status == 'skipped')
                                            {{-- Serve Button for Skipped --}}
                                            <button type="button" class="btn btn-sm btn-info" onclick="updateStatus({{ $inquiry->id }}, 'serving')" title="Serve Now">
                                                <i class="bi bi-play-fill"></i> Serve
                                            </button>
                                            {{-- Back to Waiting --}}
                                            <button type="button" class="btn btn-sm btn-secondary" onclick="updateStatus({{ $inquiry->id }}, 'waiting')" title="Return to Waiting">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-3">No inquiries found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($inquiries->hasPages())
            <div class="card-footer">
                {{ $inquiries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Update inquiry status via AJAX
    function updateStatus(inquiryId, status) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('{{ route('admin.inquiries.update-status') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                inquiry_id: inquiryId,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showAlert('Status updated successfully!', 'success');
                // Reload page after 1 second
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showAlert(data.message || 'Failed to update status', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while updating status', 'danger');
        });
    }
    
    // Show alert message
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        // Auto dismiss after 3 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
</script>
@endsection
