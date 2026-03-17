@extends('layouts.app')

@section('title', 'Category Management')

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

    [data-theme="dark"] .card-footer {
        background-color: var(--dark-surface-secondary) !important;
        border-top-color: var(--dark-border) !important;
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
    [data-theme="dark"] .form-select,
    [data-theme="dark"] .form-control-color {
        background-color: var(--dark-surface-secondary) !important;
        border-color: var(--dark-border) !important;
        color: var(--dark-on-surface) !important;
    }

    [data-theme="dark"] .text-muted {
        color: #adb5bd !important;
    }

    [data-theme="dark"] hr {
        border-color: var(--dark-border) !important;
        opacity: 0.2;
    }

    [data-theme="dark"] .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="bi bi-tags text-primary"></i> Category Management</h2>
                    <p class="text-muted mb-0">Manage service categories and queue prefixes</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="row">
        @forelse($categories as $category)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header" style="background-color: {{ $category->color }}; color: white;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-tag"></i> {{ $category->section_name ?? 'No Section' }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            
                        </div>
                        <h6 class="card-subtitle mb-2 text-muted"><strong>Category:</strong> {{ $category->name }}</h6>
                        <p class="card-text text-muted">{{ $category->description ?? 'No description' }}</p>
                        
                        <hr>
                        
                        <div class="row text-center">
                            <div class="col-6">
                                <h6 class="text-muted mb-1">Inquiries</h6>
                                <h4>{{ $category->inquiries_count ?? 0 }}</h4>
                            </div>
                            <div class="col-6">
                                <h6 class="text-muted mb-1">Staff</h6>
                                <h4>{{ $category->assigned_users_count ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-grid gap-2">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}">
                                <i class="bi bi-pencil"></i> Edit Category
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-tags" style="font-size: 3rem;"></i>
                    <p class="mt-3">No categories found</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Edit Category Modals -->
@foreach($categories as $category)
<div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: {{ $category->color }}; color: white;">
                <h5 class="modal-title"><i class="bi bi-pencil"></i> Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Section <span class="text-danger">*</span></label>
                        <input type="text" name="section_name" class="form-control" value="{{ $category->section_name }}" placeholder="e.g., Administrative and Client Services" required maxlength="100">
                        <div class="form-text">Full section name (this will be displayed in the card header)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lobby</label>
                        <select name="lobby" class="form-select">
                            <option value="">Select Lobby</option>
                            <option value="lobby1" {{ old('lobby', $category->lobby) == 'lobby1' ? 'selected' : '' }}>Lobby 1</option>
                            <option value="lobby2" {{ old('lobby', $category->lobby) == 'lobby2' ? 'selected' : '' }}>Lobby 2</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ $category->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color <span class="text-danger">*</span></label>
                        <input type="color" name="color" class="form-control form-control-color" value="{{ $category->color }}" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active{{ $category->id }}" value="1" {{ $category->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active{{ $category->id }}">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel </button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Delete Category Modals -->
@foreach($categories as $category)
<div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Delete Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the category <strong>{{ $category->name }}</strong>?</p>
                <p class="text-danger">This action cannot be undone and will permanently delete this category.</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> This category cannot be deleted if it has associated inquiries, assessments, or assigned users.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Delete Category
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection


