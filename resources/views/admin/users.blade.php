@extends('layouts.app')

@section('title', 'User Management')

@section('styles')
<style>
    /* Professional Dark Mode Overrides */
    [data-theme="dark"] .card {
        background-color: var(--dark-surface) !important;
        border-color: var(--dark-border) !important;
    }

    [data-theme="dark"] .card-body {
        background-color: var(--dark-surface) !important;
    }

    [data-theme="dark"] .modal-content {
        background-color: var(--dark-surface) !important;
        border-color: var(--dark-border) !important;
        color: var(--dark-on-surface) !important;
    }

    [data-theme="dark"] .modal-header {
        border-bottom-color: var(--dark-border) !important;
    }

    [data-theme="dark"] .modal-footer {
        border-top-color: var(--dark-border) !important;
    }

    [data-theme="dark"] .form-control, 
    [data-theme="dark"] .form-select {
        background-color: var(--dark-surface-secondary) !important;
        border-color: var(--dark-border) !important;
        color: var(--dark-on-surface) !important;
    }

    [data-theme="dark"] .table {
        color: var(--dark-on-surface) !important;
    }

    [data-theme="dark"] .table thead.table-light {
        background-color: var(--dark-surface-secondary) !important;
    }

    [data-theme="dark"] .table thead th {
        background-color: var(--dark-surface-secondary) !important;
        color: var(--dark-on-surface) !important;
        border-bottom-color: var(--dark-border) !important;
    }

    [data-theme="dark"] .table td {
        border-bottom-color: var(--dark-border) !important;
    }

    [data-theme="dark"] .text-muted {
        color: #adb5bd !important;
    }

    [data-theme="dark"] .pagination .page-link {
        background-color: var(--dark-surface-secondary);
        border-color: var(--dark-border);
        color: #adb5bd;
    }

    [data-theme="dark"] .pagination .page-item.active .page-link {
        background-color: var(--denr-green);
        border-color: var(--denr-green);
        color: white;
    }

    [data-theme="dark"] .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    [data-theme="dark"] .form-text {
        color: #adb5bd !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-people-fill text-primary"></i> User Management</h2>
                    <p class="text-muted mb-0">Manage system users and their roles</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus"></i> Create User
                </button>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Username</th>
                            <th width="20%">Name</th>
                            <th width="20%">Email</th>
                            <th width="12%">Role</th>
                            <th width="18%">Section</th>
                            <th width="8%">Status</th>
                            <th width="12%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $user->username }}</strong></td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role == 'front_desk')
                                    <span class="badge bg-info">Front Desk</span>
                                @elseif($user->role == 'section_staff')
                                    <span class="badge bg-success">Section Staff</span>
                                @elseif($user->role == 'section_officer')
                                    <span class="badge bg-primary">Section Officer</span>
                                @elseif($user->role == 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @endif
                            </td>
                            <td>
                                @if($user->assignedCategory && ($user->role == 'section_staff' || $user->role == 'section_officer'))
                                    <small>{{ $user->assignedCategory->section_name ?? $user->assignedCategory->section }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-3">No users found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
            <div class="card-footer bg-white border-0 py-4">
                <div class="d-flex flex-column align-items-center justify-content-center">
                    {{ $users->links('vendor.pagination.custom') }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title"><i class="bi bi-person-plus"></i> Create New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" id="add_role" class="form-select" required onchange="toggleSectionDropdown('add')">
                            <option value="">Select Role</option>
                            <option value="front_desk">Front Desk</option>
                            <option value="section_staff">Section Staff</option>
                            <option value="section_officer">Section Officer</option>
                            <option value="admin">Admin</option>
                        </select>
                        <div class="form-text mt-2">
                            <strong>Section Staff:</strong> Limited access to assigned section only (View & Queue Management)<br>
                            <strong>Section Officer:</strong> Full section access with category assignment capability
                        </div>
                    </div>
                    
                    <!-- Assigned Section Dropdown (Only for Section Staff & Section Officer) -->
                    <div class="mb-3" id="add_section_container" style="display: none;">
                        <label class="form-label">Assign Section <span class="text-danger">*</span></label>
                        <select name="assigned_section" id="add_assigned_section" class="form-select" onchange="filterCategoriesBySection()">
                            <option value="">Select Section</option>
                            <option value="ACS">Aggregate and Correction Section (ACS)</option>
                            <option value="OOSS">Original and Other Surveys Section (OOSS)</option>
                            <option value="LES">Land Evaluation Section (LES)</option>
                            <option value="SCS">Survey and Control Section (SCS)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modals -->
@foreach($users as $user)
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" id="edit_role_{{ $user->id }}" class="form-select" required onchange="toggleSectionDropdown('edit_{{ $user->id }}')">
                            <option value="">Select Role</option>
                            <option value="front_desk" {{ $user->role == 'front_desk' ? 'selected' : '' }}>Front Desk</option>
                            <option value="section_staff" {{ $user->role == 'section_staff' ? 'selected' : '' }}>Section Staff</option>
                            <option value="section_officer" {{ $user->role == 'section_officer' ? 'selected' : '' }}>Section Officer</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    
                    <!-- Assigned Section Dropdown -->
                    <div class="mb-3" id="edit_section_container_{{ $user->id }}" style="display: none;">
                        <label class="form-label">Assign Section <span class="text-danger">*</span></label>
                        <select name="assigned_section" id="edit_assigned_section_{{ $user->id }}" class="form-select" onchange="filterCategoriesBySection('edit_{{ $user->id }}_')">
                            <option value="">Select Section</option>
                            <option value="ACS" {{ ($user->assignedCategory && $user->assignedCategory->section == 'ACS') ? 'selected' : '' }}>Aggregate and Correction Section (ACS)</option>
                            <option value="OOSS" {{ ($user->assignedCategory && $user->assignedCategory->section == 'OOSS') ? 'selected' : '' }}>Original and Other Surveys Section (OOSS)</option>
                            <option value="LES" {{ ($user->assignedCategory && $user->assignedCategory->section == 'LES') ? 'selected' : '' }}>Land Evaluation Section (LES)</option>
                            <option value="SCS" {{ ($user->assignedCategory && $user->assignedCategory->section == 'SCS') ? 'selected' : '' }}>Survey and Control Section (SCS)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('scripts')
<script>
// Toggle section dropdown visibility based on role
function toggleSectionDropdown(prefix) {
    let sectionContainer;
    
    if (prefix === 'add') {
        sectionContainer = document.getElementById('add_section_container');
    } else {
        // For edit modals, prefix includes user ID
        const userId = prefix.replace('edit_', '');
        sectionContainer = document.getElementById('edit_section_container_' + userId);
    }
    
    let roleSelect;
    if (prefix === 'add') {
        roleSelect = document.getElementById('add_role');
    } else {
        roleSelect = document.getElementById('edit_role_' + prefix.replace('edit_', ''));
    }
    
    // Show section dropdown for both section_staff and section_officer
    if (roleSelect.value === 'section_staff' || roleSelect.value === 'section_officer') {
        sectionContainer.style.display = 'block';
    } else {
        sectionContainer.style.display = 'none';
    }
}

// Filter categories based on selected section
function filterCategoriesBySection(userPrefix = '') {
    let sectionSelect, categorySelect;
    
    if (userPrefix === '') {
        sectionSelect = document.getElementById('add_assigned_section');
        categorySelect = document.getElementById('add_assigned_category');
    } else {
        sectionSelect = document.getElementById(userPrefix + 'assigned_section');
        categorySelect = document.getElementById(userPrefix + 'assigned_category');
    }
    
    const selectedSection = sectionSelect.value;
    
    // Show all categories if no section selected
    if (!selectedSection) {
        Array.from(categorySelect.options).forEach(option => {
            option.style.display = '';
        });
        return;
    }
    
    // Filter categories by section
    Array.from(categorySelect.options).forEach(option => {
        if (option.value === '') {
            option.style.display = ''; // Always show "None" option
        } else if (option.dataset.section === selectedSection) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    });
    
    // Reset selected category when section changes
    categorySelect.value = '';
}

// Initialize edit modals on page load
document.addEventListener('DOMContentLoaded', function() {
    @foreach($users as $user)
        if ('{{ $user->role }}' === 'section_staff' || '{{ $user->role }}' === 'section_officer') {
            document.getElementById('edit_section_container_{{ $user->id }}').style.display = 'block';
        }
    @endforeach
});
</script>
@endsection
