@extends('layouts.app')

@section('title', 'All Inquiries')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-list-check text-primary"></i> Queue Management</h2>
                    <p class="text-muted mb-0">Manage inquiries ({{ request('status') == '' ? 'Active' : ucfirst(request('status')) }})</p>
                </div>
                <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            
            <!-- Status Tabs -->
            <ul class="nav nav-tabs mt-3">
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == '' ? 'active' : '' }}" href="{{ route('admin.inquiries') }}">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'waiting' ? 'active' : '' }}" href="{{ route('admin.inquiries', ['status' => 'waiting']) }}">Waiting</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'serving' ? 'active' : '' }}" href="{{ route('admin.inquiries', ['status' => 'serving']) }}">Serving</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'skipped' ? 'active' : '' }}" href="{{ route('admin.inquiries', ['status' => 'skipped']) }}">Skipped</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}" href="{{ route('admin.inquiries', ['status' => 'completed']) }}">Completed</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('reports.index') }}" style="color: #6c757d;">
                        <i class="bi bi-file-text me-1"></i> Reports
                    </a>
                </li>
            </ul>
            
            <!-- Info message about completed records -->
            <div class="alert alert-info mt-3 mb-0">
                <small>
                    <i class="bi bi-info-circle"></i> 
                    The "All" view shows only active inquiries (waiting, serving, skipped). Completed inquiries are managed separately in the Reports section.
                    <a href="{{ route('reports.index') }}" class="alert-link">View completed records here</a>
                </small>
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
                        <option value="">Active Status Only</option>
                        <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>Waiting</option>
                        <option value="serving" {{ request('status') == 'serving' ? 'selected' : '' }}>Serving</option>
                        <option value="skipped" {{ request('status') == 'skipped' ? 'selected' : '' }}>Skipped</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
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

    <!-- Enhanced Individual Section Tables -->
    @foreach($sections as $section)
        @php
            $sectionInquiries = $inquiriesBySection->get($section['name'], collect());
            
            // Apply filters
            if(request('search')) {
                $search = request('search');
                $sectionInquiries = $sectionInquiries->filter(function($inquiry) use ($search) {
                    return stripos($inquiry->guest_name, $search) !== false || 
                           stripos($inquiry->queue_number, $search) !== false || 
                           stripos($inquiry->address, $search) !== false;
                });
            }
            
            if(request('status')) {
                $sectionInquiries = $sectionInquiries->where('status', request('status'));
            }
            
            $waitingCount = $sectionInquiries->where('status', 'waiting')->count();
            $servingCount = $sectionInquiries->where('status', 'serving')->count();
            $skippedCount = $sectionInquiries->where('status', 'skipped')->count();
            $completedCount = $sectionInquiries->where('status', 'completed')->count();
        @endphp
        
        @if($sectionInquiries->isNotEmpty() || !request()->hasAny(['status', 'search', 'section']) || request('section') == $section['name'])
        <div class="card border-0 shadow-lg mb-5" 
             style="border-left: 6px solid {{ $section['color'] }}; background: linear-gradient(135deg, {{ $section['color'] }}02 0%, white 100%);">
            
            <!-- Professional Section Header -->
            <div class="card-header border-0 py-4" 
                 style="background: linear-gradient(90deg, {{ $section['color'] }}15 0%, {{ $section['color'] }}05 100%);">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-4">
                    <div class="d-flex align-items-center">
                        <div class="me-4">
                            <div class="rounded-circle d-flex align-items-center justify-content-center shadow" 
                                 style="width: 70px; height: 70px; background: linear-gradient(135deg, {{ $section['color'] }} 0%, {{ $section['color'] }}cc 100%); border: 3px solid white;">
                                <i class="bi bi-folder2-open text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="mb-2" style="color: {{ $section['color'] }}; font-weight: 800; letter-spacing: -0.5px;">
                                {{ $section['name'] }} SERVICE QUEUE
                            </h2>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="badge bg-white text-dark border px-3 py-2" style="font-size: 1rem; font-weight: 500;">
                                    <i class="bi bi-people-fill me-1"></i> {{ $sectionInquiries->count() }} Active Cases
                                </span>
                                <span class="badge" style="background-color: {{ $section['color'] }}; color: white; font-size: 0.9rem;">
                                    <i class="bi bi-tags-fill me-1"></i> {{ $section['count'] }} Service Types
                                </span>
                            </div>
                            @if(request('status') || request('search'))
                                <div class="d-flex flex-wrap gap-2">
                                    @if(request('status'))
                                        <span class="badge bg-warning text-dark px-2 py-1">
                                            <i class="bi bi-funnel me-1"></i> {{ ucfirst(request('status')) }} Filter Active
                                        </span>
                                    @endif
                                    @if(request('search'))
                                        <span class="badge bg-info text-white px-2 py-1">
                                            <i class="bi bi-search me-1"></i> Search: "{{ request('search') }}"
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.inquiries', ['section' => $section['name']]) }}" 
                           class="btn btn-lg d-flex align-items-center px-4 py-2" 
                           style="background-color: {{ $section['color'] }}; border-color: {{ $section['color'] }}; color: white; font-weight: 600;">
                            <i class="bi bi-filter-circle me-2"></i> {{ $section['name'] }} View
                        </a>
                        @if(request('section') == $section['name'])
                            <a href="{{ route('admin.inquiries') }}" class="btn btn-lg btn-outline-secondary d-flex align-items-center px-4 py-2">
                                <i class="bi bi-x-circle me-2"></i> Clear Filter
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Enhanced Status Summary Cards -->
            <div class="px-4 pt-4">
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm h-100" 
                             style="background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%); border-left: 4px solid #ffc107;">
                            <div class="card-body text-center py-3">
                                <div class="text-warning mb-2">
                                    <i class="bi bi-clock-history" style="font-size: 2rem;"></i>
                                </div>
                                <h2 class="mb-1 text-warning fw-bold">{{ $waitingCount }}</h2>
                                <div class="small text-muted fw-semibold">WAITING</div>
                                <div class="small text-warning">In Queue</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm h-100" 
                             style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-left: 4px solid #2196f3;">
                            <div class="card-body text-center py-3">
                                <div class="text-info mb-2">
                                    <i class="bi bi-person-workspace" style="font-size: 2rem;"></i>
                                </div>
                                <h2 class="mb-1 text-info fw-bold">{{ $servingCount }}</h2>
                                <div class="small text-muted fw-semibold">SERVING</div>
                                <div class="small text-info">In Progress</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm h-100" 
                             style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); border-left: 4px solid #f44336;">
                            <div class="card-body text-center py-3">
                                <div class="text-danger mb-2">
                                    <i class="bi bi-skip-forward-circle" style="font-size: 2rem;"></i>
                                </div>
                                <h2 class="mb-1 text-danger fw-bold">{{ $skippedCount }}</h2>
                                <div class="small text-muted fw-semibold">SKIPPED</div>
                                <div class="small text-danger">Requests</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm h-100" 
                             style="background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%); border-left: 4px solid #4caf50;">
                            <div class="card-body text-center py-3">
                                <div class="text-success mb-2">
                                    <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                                </div>
                                <h2 class="mb-1 text-success fw-bold">{{ $completedCount }}</h2>
                                <div class="small text-muted fw-semibold">COMPLETED</div>
                                <div class="small text-success">Today</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Professional Queue Table -->
            <div class="card-body p-0 pb-4">
                <div class="table-responsive px-4">
                    <table class="table table-hover mb-0 align-middle">
                        <thead style="background: linear-gradient(180deg, {{ $section['color'] }}10 0%, {{ $section['color'] }}05 100%);">
                            <tr>
                                <th width="8%" class="text-center py-3" style="color: {{ $section['color'] }}; font-weight: 700; font-size: 0.95rem;">
                                    <i class="bi bi-hash me-1"></i>QUEUE #
                                </th>
                                <th width="16%" class="py-3" style="color: {{ $section['color'] }}; font-weight: 700; font-size: 0.95rem;">
                                    <i class="bi bi-person me-1"></i>GUEST NAME
                                </th>
                                <th width="12%" class="py-3" style="color: {{ $section['color'] }}; font-weight: 700; font-size: 0.95rem;">
                                    <i class="bi bi-geo-alt me-1"></i>ADDRESS
                                </th>
                                <th width="20%" class="py-3" style="color: {{ $section['color'] }}; font-weight: 700; font-size: 0.95rem;">
                                    <i class="bi bi-tag me-1"></i>SERVICE TYPE
                                </th>
                                <th width="10%" class="text-center py-3" style="color: {{ $section['color'] }}; font-weight: 700; font-size: 0.95rem;">
                                    <i class="bi bi-flag me-1"></i>STATUS
                                </th>
                                <th width="10%" class="text-center py-3" style="color: {{ $section['color'] }}; font-weight: 700; font-size: 0.95rem;">
                                    <i class="bi bi-exclamation-triangle me-1"></i>PRIORITY
                                </th>
                                <th width="10%" class="text-center py-3" style="color: {{ $section['color'] }}; font-weight: 700; font-size: 0.95rem;">
                                    <i class="bi bi-clock me-1"></i>TIME
                                </th>
                                <th width="14%" class="text-center py-3" style="color: {{ $section['color'] }}; font-weight: 700; font-size: 0.95rem;">
                                    <i class="bi bi-gear me-1"></i>ACTIONS
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sectionInquiries as $index => $inquiry)
                                <tr class="border-start border-4" 
                                    style="border-color: {{ $section['color'] }}30 !important; background-color: white; border-bottom: 1px solid #eee;">
                                    <td class="text-center py-3">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge bg-dark fs-6 px-3 py-2 fw-bold">#{{ $index + 1 }}</span>
                                            @if($inquiry->category && isset($nextInquiries[$inquiry->category_id]) && $nextInquiries[$inquiry->category_id] == $inquiry->id && $inquiry->status == 'waiting')
                                                <span class="badge bg-success mt-2 px-2 py-1">
                                                    <i class="bi bi-arrow-right-circle me-1"></i>NEXT
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-semibold" style="font-size: 1.05rem;">{{ $inquiry->guest_name }}</div>
                                        @if($inquiry->priority == 'priority')
                                            <div class="mt-1">
                                                <span class="badge bg-warning text-dark px-2 py-1">
                                                    <i class="bi bi-exclamation-circle me-1"></i>PRIORITY CASE
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-medium">{{ $inquiry->address ?? 'N/A' }}</div>
                                        <div class="small text-muted">
                                            <i class="bi bi-calendar-event me-1"></i>{{ $inquiry->created_at->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        @if($inquiry->category)
                                            <div class="small text-muted" style="line-height: 1.4;">{{ $inquiry->category->name }}</div>
                                        @else
                                            <span class="badge bg-secondary">No Category Assigned</span>
                                        @endif
                                    </td>
                                    <td class="text-center py-3">
                                        @if($inquiry->status == 'waiting')
                                            <span class="badge bg-warning text-dark fs-6 px-3 py-2 fw-semibold">
                                                <i class="bi bi-clock me-1"></i>WAITING
                                            </span>
                                        @elseif($inquiry->status == 'serving')
                                            <span class="badge bg-info text-white fs-6 px-3 py-2 fw-semibold">
                                                <i class="bi bi-person-workspace me-1"></i>SERVING
                                            </span>
                                        @elseif($inquiry->status == 'completed')
                                            <span class="badge bg-success text-white fs-6 px-3 py-2 fw-semibold">
                                                <i class="bi bi-check-circle me-1"></i>COMPLETED
                                            </span>
                                        @elseif($inquiry->status == 'skipped')
                                            <span class="badge bg-danger text-white fs-6 px-3 py-2 fw-semibold">
                                                <i class="bi bi-skip-forward me-1"></i>SKIPPED
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center py-3">
                                        @if($inquiry->priority == 'normal')
                                            <span class="badge bg-secondary fs-6 px-3 py-2">
                                                <i class="bi bi-circle me-1"></i>NORMAL
                                            </span>
                                        @elseif($inquiry->priority == 'priority')
                                            <span class="badge bg-warning text-dark fs-6 px-3 py-2 fw-semibold">
                                                <i class="bi bi-exclamation-triangle me-1"></i>PRIORITY
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center py-3">
                                        <div class="fw-semibold" style="font-size: 1.1rem;">{{ $inquiry->created_at->format('h:i A') }}</div>
                                        <div class="small text-muted">{{ $inquiry->created_at->format('M d') }}</div>
                                    </td>
                                    <td class="text-center py-3">
                                        <div class="d-flex justify-content-center gap-1">
                                            @if($inquiry->status == 'waiting')
                                                @php
                                                    $isNext = $inquiry->category && isset($nextInquiries[$inquiry->category_id]) && $nextInquiries[$inquiry->category_id] == $inquiry->id;
                                                @endphp
                                                <button type="button" 
                                                        class="btn {{ $isNext ? 'btn-success' : 'btn-outline-secondary' }} btn-sm d-flex align-items-center justify-content-center rounded-circle" 
                                                        style="width: 40px; height: 40px;"
                                                        onclick="{{ $isNext ? 'updateStatus(' . $inquiry->id . ', \'serving\')' : 'showQueueOrderWarning(\'' . $section['name'] . '\')' }}" 
                                                        title="{{ $isNext ? 'Start Serving This Case' : 'Not Next in Queue - Click for Queue Order' }}"
                                                        {{ $isNext ? '' : 'disabled' }}>
                                                    <i class="bi bi-person-badge"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-success btn-sm d-flex align-items-center justify-content-center rounded-circle" 
                                                        style="width: 40px; height: 40px;" 
                                                        disabled title="Must Serve First">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-warning btn-sm d-flex align-items-center justify-content-center rounded-circle" 
                                                        style="width: 40px; height: 40px;"
                                                        onclick="updateStatus({{ $inquiry->id }}, 'skipped')" title="Skip This Request">
                                                    <i class="bi bi-skip-forward-fill"></i>
                                                </button>
                                            @elseif($inquiry->status == 'serving')
                                                <button type="button" 
                                                        class="btn btn-success btn-sm d-flex align-items-center justify-content-center rounded-circle" 
                                                        style="width: 40px; height: 40px;"
                                                        onclick="updateStatus({{ $inquiry->id }}, 'completed')" title="Mark as Completed">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-secondary btn-sm d-flex align-items-center justify-content-center rounded-circle" 
                                                        style="width: 40px; height: 40px;"
                                                        onclick="updateStatus({{ $inquiry->id }}, 'waiting')" title="Return to Waiting Queue">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            @elseif($inquiry->status == 'completed')
                                                <span class="badge bg-success d-flex align-items-center px-3 py-2">
                                                    <i class="bi bi-check-circle-fill me-1"></i>DONE
                                                </span>
                                            @elseif($inquiry->status == 'skipped')
                                                <button type="button" 
                                                        class="btn btn-success btn-sm d-flex align-items-center justify-content-center rounded-circle" 
                                                        style="width: 40px; height: 40px;" 
                                                        onclick="updateStatus({{ $inquiry->id }}, 'serving')" 
                                                        title="Start Serving This Case">
                                                    <i class="bi bi-person-badge"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-secondary btn-sm d-flex align-items-center justify-content-center rounded-circle" 
                                                        style="width: 40px; height: 40px;"
                                                        onclick="updateStatus({{ $inquiry->id }}, 'waiting')" title="Return to Waiting Queue">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="mb-4">
                                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                                                 style="width: 100px; height: 100px; background-color: {{ $section['color'] }}10; color: {{ $section['color'] }};">
                                                <i class="bi bi-folder-x" style="font-size: 3rem;"></i>
                                            </div>
                                        </div>
                                        <h3 class="mb-3" style="color: {{ $section['color'] }};">No Cases in {{ $section['name'] }} Queue</h3>
                                        <p class="text-muted mb-4" style="font-size: 1.1rem;">
                                            @if(request('search'))
                                                <i class="bi bi-search me-2"></i>Try clearing your search filter
                                            @elseif(request('status'))
                                                <i class="bi bi-funnel me-2"></i>Try changing the status filter
                                            @else
                                                <i class="bi bi-info-circle me-2"></i>This {{ $section['name'] }} section is currently empty
                                            @endif
                                        </p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.inquiries') }}" class="btn btn-outline-primary">
                                                <i class="bi bi-arrow-left-circle me-1"></i>View All Sections
                                            </a>
                                            <a href="{{ route('admin.index') }}" class="btn btn-primary">
                                                <i class="bi bi-house-door me-1"></i>Dashboard
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    @endforeach

    <!-- Enhanced Empty State -->
    @if($sections->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 120px; height: 120px; background-color: #f8f9fa; color: #6c757d;">
                        <i class="bi bi-folder-x" style="font-size: 4rem;"></i>
                    </div>
                </div>
                <h2 class="mb-3 text-muted">No Active Service Sections</h2>
                <p class="mb-4 text-muted lead">Please create categories with sections to start managing queues</p>
                <a href="{{ route('admin.categories') }}" class="btn btn-primary btn-lg px-4 py-2">
                    <i class="bi bi-plus-circle me-2"></i>Create Service Categories
                </a>
            </div>
        </div>
    @endif

    <!-- No Sections Message -->
    @if($sections->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-folder-x" style="font-size: 3rem; color: #6c757d;"></i>
                <p class="mt-3 mb-0">No active sections found</p>
                <small class="text-muted">Please create categories with sections to start managing queues</small>
            </div>
        </div>
    @endif

    <!-- Pagination -->
    @if($paginatedInquiries->hasPages())
        <div class="card shadow-sm mt-4">
            <div class="card-body py-2">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mb-0">
                        <li class="page-item {{ $paginatedInquiries->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $paginatedInquiries->previousPageUrl() }}" aria-label="Previous">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        
                        @if($paginatedInquiries->currentPage() > 3)
                            <li class="page-item"><a class="page-link" href="{{ $paginatedInquiries->url(1) }}">1</a></li>
                            @if($paginatedInquiries->currentPage() > 4)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                        @endif
                        
                        @for($i = max(1, $paginatedInquiries->currentPage() - 2); $i <= min($paginatedInquiries->lastPage(), $paginatedInquiries->currentPage() + 2); $i++)
                            <li class="page-item {{ $i == $paginatedInquiries->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $paginatedInquiries->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor
                        
                        @if($paginatedInquiries->currentPage() < $paginatedInquiries->lastPage() - 2)
                            @if($paginatedInquiries->currentPage() < $paginatedInquiries->lastPage() - 3)
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                            <li class="page-item"><a class="page-link" href="{{ $paginatedInquiries->url($paginatedInquiries->lastPage()) }}">{{ $paginatedInquiries->lastPage() }}</a></li>
                        @endif
                        
                        <li class="page-item {{ $paginatedInquiries->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $paginatedInquiries->nextPageUrl() }}" aria-label="Next">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    @endif
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
    
    // Show queue order warning
    function showQueueOrderWarning(categoryCode) {
        const message = categoryCode 
            ? `You can only serve inquiries in ${categoryCode} queue order. The system enforces FIFO with priority rules.`
            : 'You can only serve inquiries in queue order. The system enforces FIFO with priority rules.';
        showAlert(message, 'warning');
    }
</script>
@endsection
