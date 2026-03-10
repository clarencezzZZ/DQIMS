@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-people-fill text-warning"></i> User Management</h2>
                    <p class="text-muted mb-0">Manage system users and their roles</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus"></i> Add User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Assigned Section</th>
                            <th>Assigned Category</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="fw-bold">{{ $user->name }}</td>
                                <td><code>{{ $user->username }}</code></td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role == 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif($user->role == 'front_desk')
                                        <span class="badge bg-success">Front Desk</span>
                                    @elseif($user->role == 'section_officer')
                                        <span class="badge bg-info">Section Officer</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->assignedCategory)
                                        <span class="badge bg-secondary">{{ $user->assignedCategory->section }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->assignedCategory)
                                        <span class="badge" style="background-color: {{ $user->assignedCategory->color }}">
                                            {{ $user->assignedCategory->name }}
                                        </span>
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
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-people" style="font-size: 3rem;"></i>
                                    <p class="mt-3">No users found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
            <div class="card-footer">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-person-plus"></i> Add New User</h5>
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
                            <option value="section_officer">Section Officer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <!-- Assigned Section Dropdown (Only for Section Officers) -->
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
                        <label class="form-label">Assigned Category (Optional)</label>
                        <select name="assigned_category_id" id="add_assigned_category" class="form-select">
                            <option value="">None</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" data-section="{{ $category->section }}">{{ $category->name }} ({{ $category->section }})</option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted small">
                            <strong>For Section Officers:</strong> Select a category within your assigned section.<br>
                            <strong>For Others:</strong> Leave as "None" for general access.
                        </div>
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
                    <button type="submit" class="btn btn-warning">Add User</button>
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
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" minlength="6">
                        <div class="form-text">Leave blank to keep current password</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" id="edit_role_{{ $user->id }}" class="form-select" required onchange="toggleSectionDropdown('edit_{{ $user->id }}')">
                            <option value="">Select Role</option>
                            <option value="front_desk" {{ $user->role == 'front_desk' ? 'selected' : '' }}>Front Desk</option>
                            <option value="section_officer" {{ $user->role == 'section_officer' ? 'selected' : '' }}>Section Officer</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    
                    <!-- Assigned Section Dropdown (Only for Section Officers) -->
                    <div class="mb-3" id="edit_section_container_{{ $user->id }}" style="display: none;">
                        <label class="form-label">Assign Section <span class="text-danger">*</span></label>
                        <select name="assigned_section" id="edit_assigned_section_{{ $user->id }}" class="form-select" onchange="filterCategoriesBySection('edit_{{ $user->id }}')">
                            <option value="">Select Section</option>
                            <option value="ACS" {{ $user->assignedCategory && $user->assignedCategory->section == 'ACS' ? 'selected' : '' }}>Aggregate and Correction Section (ACS)</option>
                            <option value="OOSS" {{ $user->assignedCategory && $user->assignedCategory->section == 'OOSS' ? 'selected' : '' }}>Original and Other Surveys Section (OOSS)</option>
                            <option value="LES" {{ $user->assignedCategory && $user->assignedCategory->section == 'LES' ? 'selected' : '' }}>Land Evaluation Section (LES)</option>
                            <option value="SCS" {{ $user->assignedCategory && $user->assignedCategory->section == 'SCS' ? 'selected' : '' }}>Survey and Control Section (SCS)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Assigned Category (Optional)</label>
                        <select name="assigned_category_id" id="edit_assigned_category_{{ $user->id }}" class="form-select">
                            <option value="">None</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" data-section="{{ $category->section }}" 
                                    {{ $user->assigned_category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->section }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted small">
                            <strong>For Section Officers:</strong> Must select a category within assigned section.
                        </div>
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
    const roleSelect = document.getElementById(prefix + '_role');
    const sectionContainer = document.getElementById(prefix + '_section_container');
    
    if (roleSelect.value === 'section_officer') {
        sectionContainer.style.display = 'block';
    } else {
        sectionContainer.style.display = 'none';
    }
}

// Filter categories based on selected section
function filterCategoriesBySection(prefix = '') {
    const sectionSelect = document.getElementById(prefix + 'assigned_section');
    const categorySelect = document.getElementById(prefix + 'assigned_category');
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
        if ('{{ $user->role }}' === 'section_officer') {
            document.getElementById('edit_section_container_{{ $user->id }}').style.display = 'block';
        }
    @endforeach
});
</script>
@endsection
